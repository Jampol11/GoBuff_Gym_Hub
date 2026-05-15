<?php
/**
 * RoleApplicationController
 * - Users (role='user') can apply for a role
 * - Gym Owner can review and approve/reject applications
 */
class RoleApplicationController extends Controller
{
    private RoleApplication $model;

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
        $reason        = sanitize($_POST['reason'] ?? '');

        $allowedRoles = ['member', 'trainer', 'marketing', 'maintenance', 'admin'];
        if (!in_array($requestedRole, $allowedRoles)) {
            $this->flash('error', 'Invalid role selected.');
            $this->redirect('/role-application/apply');
        }

        if (empty($reason) || strlen($reason) < 10) {
            $this->flash('error', 'Please provide a reason (at least 10 characters).');
            $this->redirect('/role-application/apply');
        }

        $id = $this->model->insert([
            'user_id'        => Auth::id(),
            'requested_role' => $requestedRole,
            'reason'         => $reason,
            'status'         => 'pending',
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        if ($id) {
            // Notify the gym owner
            $notifModel = new Notification();
            $userModel  = new User();
            $owners     = $userModel->getUsersByRole('gym_owner');
            $applicant  = Auth::user();
            foreach ($owners as $owner) {
                $notifModel->createNotification(
                    $owner['id'],
                    'system',
                    'New Role Application',
                    "{$applicant['name']} has applied for the role: " . role_label($requestedRole)
                );
            }

            log_activity('role_application', "User applied for role: {$requestedRole}");
            $this->flash('success', 'Your application has been submitted. The Gym Owner will review it shortly.');
        } else {
            $this->flash('error', 'Failed to submit application. Please try again.');
        }

        $this->redirect('/role-application/apply');
    }

    /* ------------------------------------------------------------------ */
    /*  Owner: Review applications                                         */
    /* ------------------------------------------------------------------ */

    public function index(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $status  = sanitize($_GET['status'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        $total        = $this->model->countByStatus($status);
        $applications = $this->model->getAllWithUser($perPage, ($page - 1) * $perPage);

        // Filter by status if provided
        if ($status) {
            $applications = array_filter($applications, fn($a) => $a['status'] === $status);
            $applications = array_values($applications);
        }

        $pendingCount = $this->model->countByStatus('pending');

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
        if (!$application) {
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
            // Notify the applicant
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

        // Notify the applicant
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
