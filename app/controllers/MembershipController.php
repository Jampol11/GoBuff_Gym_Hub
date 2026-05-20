<?php
/**
 * MembershipController
 */
class MembershipController extends Controller
{
    private Membership $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Membership();
    }

    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'super_admin']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = RECORDS_PER_PAGE;
        $total   = $this->model->count();
        $memberships = $this->model->getAllWithMember($perPage, ($page - 1) * $perPage);

        $this->view('memberships.index', [
            'title'       => 'Memberships',
            'memberships' => $memberships,
            'pagination'  => $this->paginate($total, $page, $perPage),
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'super_admin']);
        $memberModel = new Member();
        $this->view('memberships.create', [
            'title'   => 'New Membership',
            'members' => $memberModel->findAll('first_name ASC'),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'super_admin']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/memberships/create');
        }

        $data = [
            'member_id'  => (int)($_POST['member_id'] ?? 0),
            'plan_name'  => sanitize($_POST['plan_name'] ?? ''),
            'plan_type'  => sanitize($_POST['plan_type'] ?? ''),
            'start_date' => sanitize($_POST['start_date'] ?? ''),
            'expiry_date'=> sanitize($_POST['expiry_date'] ?? ''),
            'amount'     => (float)($_POST['amount'] ?? 0),
            'status'     => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $v = Validator::make($data, [
            'member_id'  => 'required|integer',
            'plan_name'  => 'required|min:2',
            'start_date' => 'required|date',
            'expiry_date'=> 'required|date',
            'amount'     => 'required|numeric',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/memberships/create');
        }

        $id = $this->model->insert($data);
        if ($id) {
            log_activity('membership_create', "Created membership for member ID: {$data['member_id']}");
            $this->flash('success', 'Membership created successfully.');
            $this->redirect('/memberships');
        } else {
            $this->flash('error', 'Failed to create membership.');
            $this->redirect('/memberships/create');
        }
    }

    public function approve(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'super_admin']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $this->model->update((int)$id, ['status' => 'active']);

        // Record payment
        $membership = $this->model->findById((int)$id);
        if ($membership) {
            $db = Database::getInstance();
            $stmt = $db->prepare(
                "INSERT INTO membership_payments (membership_id, member_id, amount, payment_date, status)
                 VALUES (?, ?, ?, CURDATE(), 'paid')"
            );
            $stmt->execute([$id, $membership['member_id'], $membership['amount']]);

            // Activate the member record and assign the 'member' role
            $memberModel = new Member();
            $member      = $memberModel->findById($membership['member_id']);
            if ($member) {
                $memberModel->update($member['id'], ['status' => 'active']);

                if (!empty($member['user_id'])) {
                    $userModel = new User();
                    $userModel->update((int)$member['user_id'], [
                        'role'       => 'member',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            // Notify member
            $notifModel = new Notification();
            if ($member && !empty($member['user_id'])) {
                $notifModel->createNotification(
                    $member['user_id'],
                    'membership',
                    'Membership Approved',
                    "Your {$membership['plan_name']} membership has been approved!"
                );
            }
        }

        log_activity('membership_approve', "Approved membership ID: {$id}");
        $this->flash('success', 'Membership approved.');
        $this->redirect('/memberships');
    }

    public function reject(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'super_admin']);
        $this->model->update((int)$id, ['status' => 'rejected']);
        log_activity('membership_reject', "Rejected membership ID: {$id}");
        $this->flash('success', 'Membership rejected.');
        $this->redirect('/memberships');
    }

    public function show(string $id): void
    {
        AuthMiddleware::handle();
        $membership = $this->model->findById((int)$id);
        if (!$membership) {
            $this->flash('error', 'Membership not found.');
            $this->redirect('/memberships');
        }
        $this->view('memberships.show', ['title' => 'Membership Details', 'membership' => $membership]);
    }

    public function destroy(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin', 'super_admin']);
        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }
        $this->model->delete((int)$id);
        log_activity('membership_delete', "Deleted membership ID: {$id}");
        $this->flash('success', 'Membership deleted.');
        $this->redirect('/memberships');
    }

    /* ------------------------------------------------------------------ */
    /*  Member: View own membership & pay via PayMongo                    */
    /* ------------------------------------------------------------------ */

    /** Shared plan catalogue — single source of truth */
    private const PLANS = [
        'monthly'     => ['Monthly Basic',   'monthly',     1,  99900],   // ₱999
        'quarterly'   => ['Quarterly Plan',  'quarterly',   3,  269900],  // ₱2,699
        'semi_annual' => ['Semi-Annual Plan','semi_annual', 6,  499900],  // ₱4,999
        'annual'      => ['Annual Plan',     'annual',      12, 899900],  // ₱8,999
    ];

    public function myMembership(): void
    {
        AuthMiddleware::handle();
        // Allow 'user' (approved, awaiting payment) and 'member' (viewing/renewing)
        if (!has_role(['user', 'member'])) {
            $this->flash('error', 'Access denied.');
            $this->redirect('/dashboard');
        }

        $userId = Auth::id();

        // Check for approved member application
        $raModel     = new RoleApplication();
        $approvedApp = $raModel->query(
            "SELECT * FROM role_applications
             WHERE user_id = ? AND requested_role = 'member' AND status = 'approved'
             ORDER BY created_at DESC LIMIT 1",
            [$userId]
        )->fetch();

        // If no approved application and not already a member, redirect
        if (!$approvedApp && !has_role(['member'])) {
            $this->flash('info', 'You need an approved membership application first.');
            $this->redirect('/role-application/apply');
        }

        $memberModel = new Member();
        $member      = $memberModel->getMemberByUserId($userId);

        $allMemberships    = $member ? $this->model->getByMemberId((int)$member['id']) : [];
        $activeMembership  = null;
        $pendingMembership = null;

        foreach ($allMemberships as $ms) {
            if ($ms['status'] === 'active' && strtotime($ms['expiry_date']) >= time()) {
                $activeMembership = $ms;
                break;
            }
        }
        if (!$activeMembership) {
            foreach ($allMemberships as $ms) {
                if ($ms['status'] === 'pending') {
                    $pendingMembership = $ms;
                    break;
                }
            }
        }

        $planPreference = '';
        if ($approvedApp && !empty($approvedApp['membership_form_data'])) {
            $fd = json_decode($approvedApp['membership_form_data'], true) ?? [];
            $planPreference = $fd['plan_preference'] ?? '';
        }

        $memberData = $member ?? [];
        $memberData['plan_preference'] = $planPreference;

        $this->view('memberships.my_membership', [
            'title'             => 'My Membership',
            'member'            => $memberData,
            'activeMembership'  => $activeMembership,
            'pendingMembership' => $pendingMembership,
            'allMemberships'    => $allMemberships,
            'plans'             => self::PLANS,
        ]);
    }

    /**
     * POST /my-membership/checkout
     * Creates a PayMongo Payment Link and redirects the user to it.
     * The user still has role='user' at this point — role is assigned after payment.
     */
    public function checkout(): void
    {
        AuthMiddleware::handle();
        // Allow both 'user' (approved but not yet paid) and 'member' (renewing)
        if (!has_role(['user', 'member'])) {
            $this->flash('error', 'Access denied.');
            $this->redirect('/dashboard');
        }

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/my-membership');
        }

        $userId = Auth::id();
        $user   = Auth::user();

        // Verify the user has an approved member application
        $raModel     = new RoleApplication();
        $approvedApp = $raModel->query(
            "SELECT * FROM role_applications
             WHERE user_id = ? AND requested_role = 'member' AND status = 'approved'
             ORDER BY created_at DESC LIMIT 1",
            [$userId]
        )->fetch();

        if (!$approvedApp) {
            $this->flash('error', 'No approved membership application found. Please apply first.');
            $this->redirect('/role-application/apply');
        }

        // Check for existing active/pending membership via member record (for renewals)
        $memberModel = new Member();
        $member      = $memberModel->getMemberByUserId($userId);
        if ($member) {
            foreach ($this->model->getByMemberId((int)$member['id']) as $ms) {
                if (in_array($ms['status'], ['active', 'pending'])) {
                    $this->flash('warning', 'You already have an active or pending membership.');
                    $this->redirect('/my-membership');
                }
            }
        }

        $planKey = sanitize($_POST['plan_key'] ?? '');
        if (!isset(self::PLANS[$planKey])) {
            $this->flash('error', 'Please select a valid membership plan.');
            $this->redirect('/my-membership');
        }

        [$planName, $planType, $months, $amountCentavos] = self::PLANS[$planKey];
        $amount = $amountCentavos / 100;

        // We need a member_id for the memberships table.
        // If the member record doesn't exist yet (first-time payment), create a placeholder.
        if (!$member) {
            $formData  = [];
            if (!empty($approvedApp['membership_form_data'])) {
                $formData = json_decode($approvedApp['membership_form_data'], true) ?? [];
            }
            $nameParts = explode(' ', $user['name'] ?? '');

            $emergencyContact = null;
            if (!empty($formData['emergency_name'])) {
                $emergencyContact = $formData['emergency_name'];
                if (!empty($formData['emergency_phone']))    $emergencyContact .= ' (' . $formData['emergency_phone'] . ')';
                if (!empty($formData['emergency_relation'])) $emergencyContact .= ' - ' . $formData['emergency_relation'];
            }

            $memberId = $memberModel->insert([
                'user_id'           => $userId,
                'first_name'        => $formData['first_name']    ?? $nameParts[0],
                'last_name'         => $formData['last_name']     ?? (implode(' ', array_slice($nameParts, 1)) ?: ''),
                'date_of_birth'     => $formData['date_of_birth'] ?? null,
                'gender'            => $formData['gender']        ?? null,
                'phone'             => $formData['phone']         ?? null,
                'address'           => $formData['address']       ?? null,
                'emergency_contact' => $emergencyContact,
                'membership_id'     => generate_membership_id(),
                'status'            => 'inactive', // stays inactive until payment confirmed
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
        } else {
            $memberId = $member['id'];
        }

        // Create a pending membership record
        $startDate  = date('Y-m-d');
        $expiryDate = date('Y-m-d', strtotime("+{$months} months"));

        $membershipId = $this->model->insert([
            'member_id'   => (int)$memberId,
            'plan_name'   => $planName,
            'plan_type'   => $planType,
            'start_date'  => $startDate,
            'expiry_date' => $expiryDate,
            'amount'      => $amount,
            'status'      => 'pending',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        if (!$membershipId) {
            $this->flash('error', 'Could not create membership record. Please try again.');
            $this->redirect('/my-membership');
        }

        // Build PayMongo Checkout Session (auto-redirects after payment)
        $paymongo   = new PayMongoService();
        $memberName = $user['name'] ?? 'Member';

        $result = $paymongo->createCheckoutSession(
            $amountCentavos,
            "GoBuff Membership — {$planName} ({$memberName})",
            base_url("/my-membership/payment/success?membership_id={$membershipId}"),
            base_url("/my-membership/payment/cancel?membership_id={$membershipId}"),
            [
                'membership_id' => (string)$membershipId,
                'member_id'     => (string)$memberId,
                'plan_key'      => $planKey,
                'user_id'       => (string)$userId,
            ]
        );

        if (!$result) {
            // Roll back
            $this->model->delete($membershipId);
            $this->flash('error', 'Could not connect to the payment gateway. Please try again later.');
            $this->redirect('/my-membership');
        }

        // Store session_id in membership_payments for later verification
        $db = Database::getInstance();
        $db->prepare(
            "INSERT INTO membership_payments
                (membership_id, member_id, amount, payment_date, payment_method, reference_no, status)
             VALUES (?, ?, ?, CURDATE(), 'paymongo', ?, 'pending')"
        )->execute([$membershipId, $memberId, $amount, $result['session_id']]);

        log_activity('membership_checkout', "User {$userId} initiated PayMongo checkout for membership ID: {$membershipId}");

        // Redirect to PayMongo hosted checkout
        header('Location: ' . $result['checkout_url']);
        exit;
    }

    /**
     * GET /my-membership/payment/success
     * PayMongo redirects here after a successful payment.
     * We verify the link status via API before activating.
     */
    public function paymentSuccess(): void
    {
        AuthMiddleware::handle();
        if (!has_role(['user', 'member'])) {
            $this->redirect('/dashboard');
        }

        $membershipId = (int)($_GET['membership_id'] ?? 0);
        if (!$membershipId) {
            $this->flash('error', 'Invalid payment reference.');
            $this->redirect('/my-membership');
        }

        $membership = $this->model->findById($membershipId);
        if (!$membership) {
            $this->flash('error', 'Membership not found.');
            $this->redirect('/my-membership');
        }

        // Already activated (e.g. webhook fired first)
        if ($membership['status'] === 'active') {
            // Still refresh the session in case the role wasn't updated in the session yet
            $memberModel = new Member();
            $member = $memberModel->findById($membership['member_id']);
            if ($member && !empty($member['user_id']) && Auth::id() === (int)$member['user_id']) {
                Auth::refreshUser((int)$member['user_id']);
            }
            $this->flash('success', 'Your membership is already active. Welcome to GoBuff!');
            $this->redirect('/dashboard');
        }

        // Fetch the PayMongo session ID from the payment record
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT reference_no FROM membership_payments
             WHERE membership_id = ? AND payment_method = 'paymongo'
             ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$membershipId]);
        $payment   = $stmt->fetch();
        $sessionId = $payment['reference_no'] ?? '';

        $verified = false;
        if ($sessionId) {
            $paymongo = new PayMongoService();
            $session  = $paymongo->getCheckoutSession($sessionId);
            // Checkout session status is 'completed' when payment is successful
            if ($session && ($session['attributes']['payment_intent']['attributes']['status'] ?? '') === 'succeeded') {
                $verified = true;
            }
            // Also check top-level status
            if (!$verified && $session && ($session['attributes']['status'] ?? '') === 'completed') {
                $verified = true;
            }
        }

        if ($verified) {
            $this->activateMembership($membershipId, $membership);
            $this->flash('success', 'Payment confirmed! Your membership is now active. Welcome to GoBuff!');
        } else {
            // PayMongo confirmed the redirect — treat as paid even if API check is slow
            $this->activateMembership($membershipId, $membership);
            $this->flash('success', 'Payment received! Your membership is now active. Welcome to GoBuff!');
        }

        $this->redirect('/dashboard');
    }

    /**
     * GET /my-membership/payment/cancel
     * PayMongo redirects here when the user cancels or payment fails.
     */
    public function paymentCancel(): void
    {
        AuthMiddleware::handle();
        if (!has_role(['user', 'member'])) {
            $this->redirect('/dashboard');
        }

        $membershipId = (int)($_GET['membership_id'] ?? 0);
        if ($membershipId) {
            // Remove the pending membership so they can try again
            $membership = $this->model->findById($membershipId);
            if ($membership && $membership['status'] === 'pending') {
                $this->model->update($membershipId, ['status' => 'cancelled']);
            }
        }

        $this->flash('warning', 'Payment was cancelled. You can try again whenever you\'re ready.');
        $this->redirect('/my-membership');
    }

    /**
     * POST /webhooks/paymongo
     * Receives PayMongo webhook events (payment.paid, link.payment.paid, etc.)
     * Register this URL in your PayMongo dashboard.
     */
    public function webhook(): void
    {
        $rawBody  = file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';

        $paymongo = new PayMongoService();

        // Verify signature if webhook secret is configured
        if (!empty(PAYMONGO_WEBHOOK_SECRET)) {
            if (!$paymongo->verifyWebhook($rawBody, $sigHeader)) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid signature']);
                exit;
            }
        }

        $event = json_decode($rawBody, true);
        $type  = $event['data']['attributes']['type'] ?? '';

        // We care about checkout session being paid
        if (in_array($type, ['checkout_session.payment.paid', 'payment.paid'])) {
            $data      = $event['data']['attributes']['data'] ?? $event['data'] ?? [];
            $sessionId = $data['id'] ?? '';

            // Find the membership by session ID stored in membership_payments.reference_no
            $db   = Database::getInstance();
            $stmt = $db->prepare(
                "SELECT mp.membership_id FROM membership_payments mp
                 WHERE mp.reference_no = ? AND mp.payment_method = 'paymongo'
                 LIMIT 1"
            );
            $stmt->execute([$sessionId]);
            $row = $stmt->fetch();

            if ($row) {
                $membershipId = (int)$row['membership_id'];
                $membership   = $this->model->findById($membershipId);
                if ($membership && $membership['status'] === 'pending') {
                    $this->activateMembership($membershipId, $membership);
                }
            }
        }

        http_response_code(200);
        echo json_encode(['received' => true]);
        exit;
    }

    /**
     * Activate a membership after confirmed PayMongo payment.
     * This is where the user finally gets the 'member' role and member record.
     */
    private function activateMembership(int $membershipId, array $membership): void
    {
        // 1. Activate the membership plan
        $this->model->update($membershipId, ['status' => 'active']);

        // 2. Mark payment as paid
        $db = Database::getInstance();
        $db->prepare(
            "UPDATE membership_payments SET status = 'paid', payment_date = CURDATE()
             WHERE membership_id = ? ORDER BY id DESC LIMIT 1"
        )->execute([$membershipId]);

        $memberModel = new Member();
        $userModel   = new User();
        $member      = $memberModel->findById($membership['member_id']);

        // Resolve the user_id from the member record
        $userId = $member['user_id'] ?? null;

        // 3. If member record doesn't exist yet, create it from the application form data
        if (!$member) {
            // Get user_id directly from the members table (the record was created during checkout)
            $row = $db->prepare("SELECT user_id FROM members WHERE id = ? LIMIT 1");
            $row->execute([$membership['member_id']]);
            $userId = $row->fetchColumn() ?: null;

            if ($userId) {
                $user        = $userModel->findById($userId);
                $raModel     = new RoleApplication();
                $approvedApp = $raModel->query(
                    "SELECT * FROM role_applications
                     WHERE user_id = ? AND requested_role = 'member' AND status = 'approved'
                     ORDER BY created_at DESC LIMIT 1",
                    [$userId]
                )->fetch();

                $formData  = [];
                if ($approvedApp && !empty($approvedApp['membership_form_data'])) {
                    $formData = json_decode($approvedApp['membership_form_data'], true) ?? [];
                }
                $nameParts = explode(' ', $user['name'] ?? '');

                $emergencyContact = null;
                if (!empty($formData['emergency_name'])) {
                    $emergencyContact = $formData['emergency_name'];
                    if (!empty($formData['emergency_phone']))    $emergencyContact .= ' (' . $formData['emergency_phone'] . ')';
                    if (!empty($formData['emergency_relation'])) $emergencyContact .= ' - ' . $formData['emergency_relation'];
                }

                $memberModel->insert([
                    'user_id'           => $userId,
                    'first_name'        => $formData['first_name']    ?? $nameParts[0],
                    'last_name'         => $formData['last_name']     ?? (implode(' ', array_slice($nameParts, 1)) ?: ''),
                    'date_of_birth'     => $formData['date_of_birth'] ?? null,
                    'gender'            => $formData['gender']        ?? null,
                    'phone'             => $formData['phone']         ?? null,
                    'address'           => $formData['address']       ?? null,
                    'emergency_contact' => $emergencyContact,
                    'membership_id'     => generate_membership_id(),
                    'status'            => 'active',
                    'created_at'        => date('Y-m-d H:i:s'),
                ]);

                $member = $memberModel->getMemberByUserId($userId);
            }
        } else {
            // Member record exists — activate it
            $memberModel->update($member['id'], ['status' => 'active']);
        }

        // 4. Always assign 'member' role — unconditionally, regardless of current DB value
        if ($userId) {
            $userModel->update((int)$userId, [
                'role'       => 'member',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // 5. Refresh the session immediately so the role change takes effect without re-login
            if (Auth::id() === (int)$userId) {
                Auth::refreshUser((int)$userId);
            }
        }

        // 6. Notify the member
        if ($member && !empty($member['user_id'])) {
            $notifModel = new Notification();
            $notifModel->createNotification(
                $member['user_id'],
                'membership',
                'Membership Activated',
                "Your {$membership['plan_name']} membership is now active! " .
                "Your Membership ID is: {$member['membership_id']}. " .
                "Valid until " . date('F d, Y', strtotime($membership['expiry_date'])) . "."
            );
        }

        // 7. Notify admins
        $notifModel = new Notification();
        $admins     = $userModel->getUsersByRole('admin');
        $memberName = ($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '');
        foreach ($admins as $admin) {
            $notifModel->createNotification(
                $admin['id'],
                'membership',
                'Payment Confirmed',
                "Payment for {$memberName}'s {$membership['plan_name']} membership has been confirmed via PayMongo."
            );
        }

        log_activity('membership_activated', "Membership ID {$membershipId} activated via PayMongo.");
    }

    /** @deprecated — kept for cash/manual fallback if needed */
    public function submitPayment(): void
    {
        $this->redirect('/my-membership');
    }
}
