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
        RoleMiddleware::handle(['gym_owner', 'admin']);

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
        RoleMiddleware::handle(['gym_owner', 'admin']);
        $memberModel = new Member();
        $this->view('memberships.create', [
            'title'   => 'New Membership',
            'members' => $memberModel->findAll('first_name ASC'),
        ]);
    }

    public function store(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

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
        RoleMiddleware::handle(['gym_owner', 'admin']);

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

            // Notify member
            $notifModel = new Notification();
            $memberModel = new Member();
            $member = $memberModel->findById($membership['member_id']);
            if ($member) {
                $notifModel->createNotification(
                    $member['user_id'] ?? null,
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
        RoleMiddleware::handle(['gym_owner', 'admin']);
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
        RoleMiddleware::handle(['gym_owner', 'admin']);
        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }
        $this->model->delete((int)$id);
        log_activity('membership_delete', "Deleted membership ID: {$id}");
        $this->flash('success', 'Membership deleted.');
        $this->redirect('/memberships');
    }
}
