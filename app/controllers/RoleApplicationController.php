<?php
/**
 * RoleApplicationController
 * - Users (role='user') can apply for a role
 * - Member applications are reviewed by the Administrative Officer (admin)
 * - Employee applications (trainer/marketing/maintenance/admin) are reviewed by the Gym Owner
 */
class RoleApplicationController extends Controller
{
    private RoleApplication $model;

    // Roles reviewed by the gym owner (employee positions)
    private const OWNER_ROLES = ['trainer', 'marketing', 'maintenance', 'admin'];
    // Roles reviewed by the admin officer (membership)
    private const ADMIN_ROLES = ['member'];

    public function __construct()
    {
        parent::__construct();
        $this->model = new RoleApplication();
    }

    /* ------------------------------------------------------------------ */
    /*  User: Apply for a role                                             */
    /* ------------------------------------------------------------------ */

    public function applyForm(): void
    {
        AuthMiddleware::handle();

        // Only 'user' role can apply; others already have a role
        if (!has_role(['user'])) {
            $this->flash('info', 'You already have an assigned role.');
            $this->redirect('/dashboard');
        }

        $myApplications = $this->model->getForUser(Auth::id());
        $hasPending     = $this->model->getPendingForUser(Auth::id());

        $availableRoles = [
            'member'      => 'Member (Gym Enthusiast)',
            'trainer'     => 'Fitness Trainer',
            'marketing'   => 'Marketing Officer',
            'maintenance' => 'Maintenance Supervisor',
            'admin'       => 'Administrative Officer',
        ];

        $this->view('role_applications.apply', [
            'title'          => 'Apply for a Role',
            'availableRoles' => $availableRoles,
            'myApplications' => $myApplications,
            'hasPending'     => $hasPending,
        ]);
    }

    public function apply(): void
    {
        AuthMiddleware::handle();

        if (!has_role(['user'])) {
            $this->flash('info', 'You already have an assigned role.');
            $this->redirect('/dashboard');
        }

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/role-application/apply');
        }

        // Prevent duplicate pending applications
        if ($this->model->getPendingForUser(Auth::id())) {
            $this->flash('warning', 'You already have a pending application. Please wait for it to be reviewed.');
            $this->redirect('/role-application/apply');
        }

        $requestedRole = sanitize($_POST['requested_role'] ?? '');
        $allowedRoles  = ['member', 'trainer', 'marketing', 'maintenance', 'admin'];

        if (!in_array($requestedRole, $allowedRoles)) {
            $this->flash('error', 'Invalid role selected.');
            $this->redirect('/role-application/apply');
        }

        // ── Member application: collect membership form data ──────────────
        $membershipData = null;
        if ($requestedRole === 'member') {
            $membershipData = $this->collectMembershipFormData();
            if (isset($membershipData['_error'])) {
                $this->flash('error', $membershipData['_error']);
                $this->redirect('/role-application/apply');
            }
            $reason = 'Membership application submitted via membership form.';
        } else {
            // Employee roles: require a reason text
            $reason = sanitize($_POST['reason'] ?? '');
            if (empty($reason) || strlen($reason) < 10) {
                $this->flash('error', 'Please provide a reason (at least 10 characters).');
                $this->redirect('/role-application/apply');
            }
        }

        $insertData = [
            'user_id'        => Auth::id(),
            'requested_role' => $requestedRole,
            'reason'         => $reason,
            'status'         => 'pending',
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        // Store membership form data as JSON if present
        if ($membershipData !== null) {
            $insertData['membership_form_data'] = json_encode($membershipData);
        }

        $id = $this->model->insert($insertData);

        if ($id) {
            $notifModel = new Notification();
            $userModel  = new User();
            $applicant  = Auth::user();

            if ($requestedRole === 'member') {
                // Notify all admin officers
                $admins = $userModel->getUsersByRole('admin');
                foreach ($admins as $admin) {
                    $notifModel->createNotification(
                        $admin['id'],
                        'system',
                        'New Membership Application',
                        "{$applicant['name']} has submitted a membership application and is awaiting your review."
                    );
                }
                $this->flash('success', 'Your membership application has been submitted. The Administrative Office will review it shortly.');
            } else {
                // Notify all gym owners
                $owners = $userModel->getUsersByRole('gym_owner');
                foreach ($owners as $owner) {
                    $notifModel->createNotification(
                        $owner['id'],
                        'system',
                        'New Role Application',
                        "{$applicant['name']} has applied for the role: " . role_label($requestedRole)
                    );
                }
                $this->flash('success', 'Your application has been submitted. The Gym Owner will review it shortly.');
            }

            log_activity('role_application', "User applied for role: {$requestedRole}");
        } else {
            $this->flash('error', 'Failed to submit application. Please try again.');
        }

        $this->redirect('/role-application/apply');
    }

    /**
     * Collect and validate membership form fields from $_POST.
     * Returns an array of sanitized data, or ['_error' => '...'] on failure.
     */
    private function collectMembershipFormData(): array
    {
        $required = [
            'first_name'       => 'First name',
            'last_name'        => 'Last name',
            'date_of_birth'    => 'Date of birth',
            'gender'           => 'Gender',
            'phone'            => 'Phone number',
            'address'          => 'Address',
            'emergency_name'   => 'Emergency contact name',
            'emergency_phone'  => 'Emergency contact phone',
            'emergency_relation' => 'Emergency contact relationship',
            'plan_preference'  => 'Membership plan preference',
        ];

        foreach ($required as $field => $label) {
            if (empty(trim($_POST[$field] ?? ''))) {
                return ['_error' => "{$label} is required."];
            }
        }

        $gender = sanitize($_POST['gender'] ?? '');
        if (!in_array($gender, ['male', 'female', 'other'])) {
            return ['_error' => 'Please select a valid gender.'];
        }

        $plan = sanitize($_POST['plan_preference'] ?? '');
        if (!in_array($plan, ['monthly', 'quarterly', 'semi_annual', 'annual'])) {
            return ['_error' => 'Please select a valid membership plan.'];
        }

        return [
            'first_name'          => sanitize($_POST['first_name']),
            'last_name'           => sanitize($_POST['last_name']),
            'date_of_birth'       => sanitize($_POST['date_of_birth']),
            'gender'              => $gender,
            'phone'               => sanitize($_POST['phone']),
            'address'             => sanitize($_POST['address']),
            'emergency_name'      => sanitize($_POST['emergency_name']),
            'emergency_phone'     => sanitize($_POST['emergency_phone']),
            'emergency_relation'  => sanitize($_POST['emergency_relation']),
            'plan_preference'     => $plan,
            'health_conditions'   => sanitize($_POST['health_conditions'] ?? ''),
            'fitness_goals'       => sanitize($_POST['fitness_goals'] ?? ''),
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Admin Officer: Review member applications                          */
    /* ------------------------------------------------------------------ */

    public function memberApplications(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $status  = sanitize($_GET['status'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        $total        = $this->model->countByStatusAndRole($status, self::ADMIN_ROLES);
        $applications = $this->model->getByRolesWithUser(self::ADMIN_ROLES, $perPage, ($page - 1) * $perPage);

        if ($status) {
            $applications = array_values(array_filter($applications, fn($a) => $a['status'] === $status));
        }

        $pendingCount = $this->model->countByStatusAndRole('pending', self::ADMIN_ROLES);

        $this->view('role_applications.member_index', [
            'title'        => 'Membership Applications',
            'applications' => $applications,
            'pagination'   => $this->paginate($total, $page, $perPage),
            'statusFilter' => $status,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function memberApplicationShow(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin']);

        $application = $this->model->getWithUser((int)$id);
        if (!$application || $application['requested_role'] !== 'member') {
            $this->flash('error', 'Application not found.');
            $this->redirect('/member-applications');
        }

        // Decode membership form data
        $membershipData = [];
        if (!empty($application['membership_form_data'])) {
            $membershipData = json_decode($application['membership_form_data'], true) ?? [];
        }

        $this->view('role_applications.member_show', [
            'title'          => 'Review Membership Application',
            'application'    => $application,
            'membershipData' => $membershipData,
        ]);
    }

    public function memberApprove(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $notes = sanitize($_POST['review_notes'] ?? '');
        $app   = $this->model->findById((int)$id);

        if (!$app || $app['requested_role'] !== 'member') {
            $this->flash('error', 'Application not found.');
            $this->redirect('/member-applications');
        }

        if ($app['status'] !== 'pending') {
            $this->flash('warning', 'This application has already been reviewed.');
            $this->redirect('/member-applications');
        }

        $success = $this->model->approve((int)$id, Auth::id(), $notes);

        if ($success) {
            $notifModel = new Notification();
            $notifModel->createNotification(
                $app['user_id'],
                'membership',
                'Membership Application Approved',
                'Your membership application has been approved by the Administrative Office! Please go to "My Membership" to submit your payment and activate your membership.'
            );

            log_activity('member_app_approve', "Admin approved membership application ID: {$id}");
            $this->flash('success', 'Membership application approved. User has been assigned the Member role.');
        } else {
            $this->flash('error', 'Failed to approve application.');
        }

        $this->redirect('/member-applications');
    }

    public function memberReject(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['admin']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $notes = sanitize($_POST['review_notes'] ?? '');
        $app   = $this->model->findById((int)$id);

        if (!$app || $app['requested_role'] !== 'member') {
            $this->flash('error', 'Application not found.');
            $this->redirect('/member-applications');
        }

        if ($app['status'] !== 'pending') {
            $this->flash('warning', 'This application has already been reviewed.');
            $this->redirect('/member-applications');
        }

        $this->model->reject((int)$id, Auth::id(), $notes);

        $notifModel = new Notification();
        $notifModel->createNotification(
            $app['user_id'],
            'system',
            'Membership Application Rejected',
            'Your membership application was not approved by the Administrative Office.' .
            ($notes ? ' Note: ' . $notes : ' Please contact the office for more information.')
        );

        log_activity('member_app_reject', "Admin rejected membership application ID: {$id}");
        $this->flash('success', 'Membership application rejected.');
        $this->redirect('/member-applications');
    }

    /* ------------------------------------------------------------------ */
    /*  Owner: Review employee role applications                           */
    /* ------------------------------------------------------------------ */

    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $status  = sanitize($_GET['status'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        $total        = $this->model->countByStatusAndRole($status, self::OWNER_ROLES);
        $applications = $this->model->getByRolesWithUser(self::OWNER_ROLES, $perPage, ($page - 1) * $perPage);

        if ($status) {
            $applications = array_values(array_filter($applications, fn($a) => $a['status'] === $status));
        }

        $pendingCount = $this->model->countByStatusAndRole('pending', self::OWNER_ROLES);

        $this->view('role_applications.index', [
            'title'        => 'Role Applications',
            'applications' => $applications,
            'pagination'   => $this->paginate($total, $page, $perPage),
            'statusFilter' => $status,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function show(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $application = $this->model->getWithUser((int)$id);
        if (!$application || !in_array($application['requested_role'], self::OWNER_ROLES)) {
            $this->flash('error', 'Application not found.');
            $this->redirect('/role-applications');
        }

        $this->view('role_applications.show', [
            'title'       => 'Review Application',
            'application' => $application,
        ]);
    }

    public function approve(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $notes = sanitize($_POST['review_notes'] ?? '');
        $app   = $this->model->findById((int)$id);

        if (!$app) {
            $this->flash('error', 'Application not found.');
            $this->redirect('/role-applications');
        }

        if ($app['status'] !== 'pending') {
            $this->flash('warning', 'This application has already been reviewed.');
            $this->redirect('/role-applications');
        }

        $success = $this->model->approve((int)$id, Auth::id(), $notes);

        if ($success) {
            $notifModel = new Notification();
            $notifModel->createNotification(
                $app['user_id'],
                'system',
                'Role Application Approved',
                'Your application for the role "' . role_label($app['requested_role']) . '" has been approved! Please log out and log back in to access your new features.'
            );

            log_activity('role_approve', "Approved role application ID: {$id}, role: {$app['requested_role']}");
            $this->flash('success', 'Application approved. User role has been updated to: ' . role_label($app['requested_role']));
        } else {
            $this->flash('error', 'Failed to approve application.');
        }

        $this->redirect('/role-applications');
    }

    public function reject(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $notes = sanitize($_POST['review_notes'] ?? '');
        $app   = $this->model->findById((int)$id);

        if (!$app) {
            $this->flash('error', 'Application not found.');
            $this->redirect('/role-applications');
        }

        if ($app['status'] !== 'pending') {
            $this->flash('warning', 'This application has already been reviewed.');
            $this->redirect('/role-applications');
        }

        $this->model->reject((int)$id, Auth::id(), $notes);

        $notifModel = new Notification();
        $notifModel->createNotification(
            $app['user_id'],
            'system',
            'Role Application Rejected',
            'Your application for the role "' . role_label($app['requested_role']) . '" was not approved.' .
            ($notes ? ' Note: ' . $notes : ' Please contact the gym owner for more information.')
        );

        log_activity('role_reject', "Rejected role application ID: {$id}");
        $this->flash('success', 'Application rejected.');
        $this->redirect('/role-applications');
    }
}
