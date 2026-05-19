<?php
/**
 * SuperAdminController
 *
 * Handles all Super Admin platform-level operations:
 *  - Platform dashboard
 *  - Gym Owner application approval/rejection
 *  - Gym Owner account management (create, activate, deactivate)
 *  - Multi-gym management (create, assign owner, edit, view)
 *  - System-wide user overview
 */
class SuperAdminController extends Controller
{
    private User               $userModel;
    private GymOwnerApplication $goaModel;
    private Gym                $gymModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->goaModel  = new GymOwnerApplication();
        $this->gymModel  = new Gym();
    }

    /* ------------------------------------------------------------------ */
    /*  Middleware shortcut                                                 */
    /* ------------------------------------------------------------------ */

    private function requireSuperAdmin(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['super_admin']);
    }

    /* ================================================================== */
    /*  DASHBOARD                                                          */
    /* ================================================================== */

    public function dashboard(): void
    {
        $this->requireSuperAdmin();

        $memberModel     = new Member();
        $membershipModel = new Membership();
        $notifModel      = new Notification();

        // Platform-level stats
        $totalUsers       = $this->userModel->count();
        $totalMembers     = $memberModel->count();
        $totalGyms        = $this->gymModel->count();
        $pendingOwnerApps = $this->goaModel->countByStatus('pending');
        $gymOwners        = $this->userModel->getUsersByRole('gym_owner');
        $recentUsers      = $this->userModel->findAll('created_at DESC', 10);
        $usersByRole      = $this->userModel->countByRole();
        $unreadNotifs     = $notifModel->getUnreadCount(Auth::id());

        // Recent gym owner applications
        $recentApps = $this->goaModel->getAllWithUser(5, 0);

        // Gym stats
        $gymStats = $this->gymModel->getStats();

        $this->view('super_admin.dashboard', [
            'title'           => 'Super Admin Dashboard',
            'totalUsers'      => $totalUsers,
            'totalMembers'    => $totalMembers,
            'totalGyms'       => $totalGyms,
            'pendingOwnerApps'=> $pendingOwnerApps,
            'gymOwners'       => $gymOwners,
            'recentUsers'     => $recentUsers,
            'usersByRole'     => $usersByRole,
            'recentApps'      => $recentApps,
            'gymStats'        => $gymStats,
            'unread_notifs'   => $unreadNotifs,
        ]);
    }

    /* ================================================================== */
    /*  SUPER ADMIN ACCOUNT MANAGEMENT                                    */
    /* ================================================================== */

    public function createSuperAdminForm(): void
    {
        $this->requireSuperAdmin();
        $this->view('super_admin.super_admin_create', ['title' => 'Create Super Admin Account']);
    }

    public function createSuperAdmin(): void
    {
        $this->requireSuperAdmin();

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/super-admin/create-super-admin');
        }

        $data = [
            'name'     => sanitize($_POST['name'] ?? ''),
            'email'    => sanitize($_POST['email'] ?? ''),
            'username' => sanitize($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
        ];

        $v = Validator::make($data, [
            'name'     => 'required|min:2|max:100',
            'email'    => 'required|email|max:150',
            'username' => 'required|min:3|max:50',
            'password' => 'required|min:8|strong_password|confirmed',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/super-admin/create-super-admin');
        }

        if ($this->userModel->findByEmail($data['email'])) {
            $this->flash('error', 'Email address is already registered.');
            $this->redirect('/super-admin/create-super-admin');
        }
        if ($this->userModel->findByUsername($data['username'])) {
            $this->flash('error', 'Username is already taken.');
            $this->redirect('/super-admin/create-super-admin');
        }

        $userId = $this->userModel->createUser([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'username' => $data['username'],
            'password' => $data['password'],
            'role'     => 'super_admin',
            'status'   => 'active',
        ]);

        if (!$userId) {
            $this->flash('error', 'Failed to create Super Admin account.');
            $this->redirect('/super-admin/create-super-admin');
        }

        log_activity('super_admin_create_sa', "Super Admin created new Super Admin account: {$data['email']}");
        $this->flash('success', 'New Super Admin account created successfully.');
        $this->redirect('/super-admin/users?role=super_admin');
    }

    /* ================================================================== */
    /*  GYM OWNER APPLICATION MANAGEMENT                                  */
    /* ================================================================== */

    public function ownerApplications(): void
    {
        $this->requireSuperAdmin();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $status  = sanitize($_GET['status'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        $total        = $this->goaModel->countByStatus($status ?: '');
        $applications = $this->goaModel->getAllWithUser($perPage, ($page - 1) * $perPage);

        if ($status) {
            $applications = array_values(array_filter($applications, fn($a) => $a['status'] === $status));
        }

        $pendingCount = $this->goaModel->countByStatus('pending');

        $this->view('super_admin.owner_applications', [
            'title'        => 'Gym Owner Applications',
            'applications' => $applications,
            'pagination'   => $this->paginate($total, $page, $perPage),
            'statusFilter' => $status,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function showOwnerApplication(string $id): void
    {
        $this->requireSuperAdmin();

        $application = $this->goaModel->getWithDetails((int)$id);
        if (!$application) {
            $this->flash('error', 'Application not found.');
            $this->redirect('/super-admin/owner-applications');
        }

        $documentTypes = [
            'business_permit'    => 'Business Permit / DTI Registration',
            'government_id'      => 'Government-Issued ID',
            'proof_of_ownership' => 'Proof of Gym Ownership / Lease Contract',
            'bir_certificate'    => 'BIR Certificate of Registration',
            'other'              => 'Other Supporting Document',
        ];

        $this->view('super_admin.owner_application_show', [
            'title'         => 'Review Gym Owner Application',
            'application'   => $application,
            'documentTypes' => $documentTypes,
        ]);
    }

    public function approveOwnerApplication(string $id): void
    {
        $this->requireSuperAdmin();

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $notes = sanitize($_POST['review_notes'] ?? '');
        $app   = $this->goaModel->findById((int)$id);

        if (!$app) {
            $this->flash('error', 'Application not found.');
            $this->redirect('/super-admin/owner-applications');
        }

        if ($app['status'] !== 'pending') {
            $this->flash('warning', 'This application has already been reviewed.');
            $this->redirect('/super-admin/owner-applications/' . $id);
        }

        $success = $this->goaModel->approve((int)$id, Auth::id(), $notes);

        if ($success) {
            $notifModel = new Notification();
            $notifModel->createNotification(
                $app['user_id'],
                'system',
                'Gym Owner Application Approved',
                'Congratulations! Your application to become Gym Owner has been approved by the Super Admin. Please log out and log back in to access your new privileges.'
            );

            log_activity('super_admin_approve_owner', "Super Admin approved Gym Owner application #{$id} for user #{$app['user_id']}");
            $this->flash('success', 'Application approved. The user has been granted Gym Owner access.');
        } else {
            $this->flash('error', 'Failed to approve application.');
        }

        $this->redirect('/super-admin/owner-applications');
    }

    public function rejectOwnerApplication(string $id): void
    {
        $this->requireSuperAdmin();

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $notes = sanitize($_POST['review_notes'] ?? '');
        $app   = $this->goaModel->findById((int)$id);

        if (!$app) {
            $this->flash('error', 'Application not found.');
            $this->redirect('/super-admin/owner-applications');
        }

        if ($app['status'] !== 'pending') {
            $this->flash('warning', 'This application has already been reviewed.');
            $this->redirect('/super-admin/owner-applications/' . $id);
        }

        $this->goaModel->reject((int)$id, Auth::id(), $notes);

        $notifModel = new Notification();
        $notifModel->createNotification(
            $app['user_id'],
            'system',
            'Gym Owner Application Not Approved',
            'Your application to become Gym Owner was not approved by the Super Admin.' .
            ($notes ? ' Note: ' . $notes : ' Please contact the platform administrator for more information.')
        );

        log_activity('super_admin_reject_owner', "Super Admin rejected Gym Owner application #{$id}");
        $this->flash('success', 'Application rejected.');
        $this->redirect('/super-admin/owner-applications');
    }

    public function downloadApplicationDocument(string $docId): void
    {
        $this->requireSuperAdmin();

        $doc = $this->goaModel->query(
            "SELECT * FROM gym_owner_application_documents WHERE id = ?",
            [(int)$docId]
        )->fetch();

        if (!$doc) {
            $this->flash('error', 'Document not found.');
            $this->redirect('/super-admin/owner-applications');
        }

        $filePath = UPLOAD_PATH . '/gym_owner_docs/' . $doc['file_name'];
        if (!file_exists($filePath)) {
            $this->flash('error', 'File not found on server.');
            $this->redirect('/super-admin/owner-applications');
        }

        header('Content-Type: ' . $doc['file_type']);
        header('Content-Disposition: attachment; filename="' . $doc['file_original'] . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, no-cache');
        readfile($filePath);
        exit;
    }

    /* ================================================================== */
    /*  GYM OWNER ACCOUNT MANAGEMENT                                      */
    /* ================================================================== */

    public function gymOwners(): void
    {
        $this->requireSuperAdmin();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $search  = sanitize($_GET['search'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        if ($search) {
            $owners = array_values(array_filter(
                $this->userModel->searchUsers($search),
                fn($u) => $u['role'] === 'gym_owner'
            ));
            $total = count($owners);
        } else {
            $owners = $this->userModel->getUsersByRole('gym_owner');
            $total  = count($owners);
            // Manual pagination since getUsersByRole doesn't support limit/offset
            $owners = array_slice($owners, ($page - 1) * $perPage, $perPage);
        }

        $this->view('super_admin.gym_owners', [
            'title'      => 'Gym Owner Accounts',
            'owners'     => $owners,
            'pagination' => $this->paginate($total, $page, $perPage),
            'search'     => $search,
        ]);
    }

    public function createGymOwnerForm(): void
    {
        $this->requireSuperAdmin();
        $this->view('super_admin.gym_owner_create', ['title' => 'Create Gym Owner Account']);
    }

    public function createGymOwner(): void
    {
        $this->requireSuperAdmin();

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/super-admin/gym-owners/create');
        }

        $data = [
            'name'     => sanitize($_POST['name'] ?? ''),
            'email'    => sanitize($_POST['email'] ?? ''),
            'username' => sanitize($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
        ];

        $v = Validator::make($data, [
            'name'     => 'required|min:2|max:100',
            'email'    => 'required|email|max:150',
            'username' => 'required|min:3|max:50',
            'password' => 'required|min:8|strong_password|confirmed',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/super-admin/gym-owners/create');
        }

        if ($this->userModel->findByEmail($data['email'])) {
            $this->flash('error', 'Email address is already registered.');
            $this->redirect('/super-admin/gym-owners/create');
        }
        if ($this->userModel->findByUsername($data['username'])) {
            $this->flash('error', 'Username is already taken.');
            $this->redirect('/super-admin/gym-owners/create');
        }

        $userId = $this->userModel->createUser([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'username' => $data['username'],
            'password' => $data['password'],
            'role'     => 'gym_owner',
            'status'   => 'active',
        ]);

        if (!$userId) {
            $this->flash('error', 'Failed to create Gym Owner account.');
            $this->redirect('/super-admin/gym-owners/create');
        }

        // Create employee record
        $employeeModel = new Employee();
        $nameParts = explode(' ', $data['name']);
        $employeeModel->insert([
            'user_id'    => $userId,
            'first_name' => $nameParts[0],
            'last_name'  => implode(' ', array_slice($nameParts, 1)) ?: '',
            'job_role'   => 'gym_owner',
            'department' => 'Management',
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        log_activity('super_admin_create_owner', "Super Admin created Gym Owner account: {$data['email']}");
        $this->flash('success', 'Gym Owner account created successfully.');
        $this->redirect('/super-admin/gym-owners');
    }

    public function toggleGymOwnerStatus(string $id): void
    {
        $this->requireSuperAdmin();

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        $user = $this->userModel->findById((int)$id);
        if (!$user || $user['role'] !== 'gym_owner') {
            $this->flash('error', 'Gym Owner not found.');
            $this->redirect('/super-admin/gym-owners');
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        $this->userModel->update((int)$id, [
            'status'     => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $notifModel = new Notification();
        $notifModel->createNotification(
            (int)$id,
            'system',
            'Account Status Updated',
            "Your Gym Owner account has been {$newStatus} by the Super Admin."
        );

        log_activity('super_admin_toggle_owner', "Super Admin set Gym Owner #{$id} status to {$newStatus}");
        $this->flash('success', "Gym Owner account has been {$newStatus}.");
        $this->redirect('/super-admin/gym-owners');
    }

    /* ================================================================== */
    /*  GYM MANAGEMENT                                                     */
    /* ================================================================== */

    public function gyms(): void
    {
        $this->requireSuperAdmin();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = RECORDS_PER_PAGE;
        $total   = $this->gymModel->count();
        $gyms    = $this->gymModel->getAllWithOwner($perPage, ($page - 1) * $perPage);

        $this->view('super_admin.gyms', [
            'title'      => 'Gym Management',
            'gyms'       => $gyms,
            'pagination' => $this->paginate($total, $page, $perPage),
            'gymStats'   => $this->gymModel->getStats(),
        ]);
    }

    public function createGymForm(): void
    {
        $this->requireSuperAdmin();

        $owners = $this->userModel->getUsersByRole('gym_owner');
        $this->view('super_admin.gym_create', [
            'title'  => 'Create Gym',
            'owners' => $owners,
        ]);
    }

    public function createGym(): void
    {
        $this->requireSuperAdmin();

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/super-admin/gyms/create');
        }

        $data = [
            'name'        => sanitize($_POST['name'] ?? ''),
            'address'     => sanitize($_POST['address'] ?? ''),
            'contact'     => sanitize($_POST['contact'] ?? ''),
            'email'       => sanitize($_POST['email'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'owner_id'    => (int)($_POST['owner_id'] ?? 0) ?: null,
            'status'      => sanitize($_POST['status'] ?? 'active'),
            'created_by'  => Auth::id(),
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $v = Validator::make($data, [
            'name'    => 'required|min:2|max:255',
            'address' => 'required|min:5',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/super-admin/gyms/create');
        }

        $gymId = $this->gymModel->insert($data);
        if (!$gymId) {
            $this->flash('error', 'Failed to create gym.');
            $this->redirect('/super-admin/gyms/create');
        }

        log_activity('super_admin_create_gym', "Super Admin created gym: {$data['name']}");
        $this->flash('success', 'Gym created successfully.');
        $this->redirect('/super-admin/gyms');
    }

    public function showGym(string $id): void
    {
        $this->requireSuperAdmin();

        $gym = $this->gymModel->getWithOwner((int)$id);
        if (!$gym) {
            $this->flash('error', 'Gym not found.');
            $this->redirect('/super-admin/gyms');
        }

        $this->view('super_admin.gym_show', [
            'title' => 'Gym Details',
            'gym'   => $gym,
        ]);
    }

    public function editGymForm(string $id): void
    {
        $this->requireSuperAdmin();

        $gym = $this->gymModel->getWithOwner((int)$id);
        if (!$gym) {
            $this->flash('error', 'Gym not found.');
            $this->redirect('/super-admin/gyms');
        }

        $owners = $this->userModel->getUsersByRole('gym_owner');
        $this->view('super_admin.gym_edit', [
            'title'  => 'Edit Gym',
            'gym'    => $gym,
            'owners' => $owners,
        ]);
    }

    public function updateGym(string $id): void
    {
        $this->requireSuperAdmin();

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/super-admin/gyms/' . $id . '/edit');
        }

        $gym = $this->gymModel->findById((int)$id);
        if (!$gym) {
            $this->flash('error', 'Gym not found.');
            $this->redirect('/super-admin/gyms');
        }

        $data = [
            'name'        => sanitize($_POST['name'] ?? ''),
            'address'     => sanitize($_POST['address'] ?? ''),
            'contact'     => sanitize($_POST['contact'] ?? ''),
            'email'       => sanitize($_POST['email'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'owner_id'    => (int)($_POST['owner_id'] ?? 0) ?: null,
            'status'      => sanitize($_POST['status'] ?? 'active'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $v = Validator::make($data, [
            'name'    => 'required|min:2|max:255',
            'address' => 'required|min:5',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/super-admin/gyms/' . $id . '/edit');
        }

        $this->gymModel->update((int)$id, $data);
        log_activity('super_admin_update_gym', "Super Admin updated gym #{$id}: {$data['name']}");
        $this->flash('success', 'Gym updated successfully.');
        $this->redirect('/super-admin/gyms/' . $id);
    }

    /* ================================================================== */
    /*  SYSTEM USER OVERVIEW                                               */
    /* ================================================================== */

    public function users(): void
    {
        $this->requireSuperAdmin();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $search  = sanitize($_GET['search'] ?? '');
        $role    = sanitize($_GET['role'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        if ($search) {
            $users = $this->userModel->searchUsers($search);
            if ($role) {
                $users = array_values(array_filter($users, fn($u) => $u['role'] === $role));
            }
            $total = count($users);
            $users = array_slice($users, ($page - 1) * $perPage, $perPage);
        } else {
            $total = $this->userModel->count($role ? "role = '{$role}'" : '');
            $users = $role
                ? array_slice($this->userModel->getUsersByRole($role), ($page - 1) * $perPage, $perPage)
                : $this->userModel->findAll('created_at DESC', $perPage, ($page - 1) * $perPage);
        }

        $usersByRole = $this->userModel->countByRole();

        $this->view('super_admin.users', [
            'title'      => 'System Users',
            'users'      => $users,
            'pagination' => $this->paginate($total, $page, $perPage),
            'search'     => $search,
            'roleFilter' => $role,
            'usersByRole'=> $usersByRole,
        ]);
    }

    public function toggleUserStatus(string $id): void
    {
        $this->requireSuperAdmin();

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        // Prevent deactivating own account
        if ((int)$id === Auth::id()) {
            $this->flash('error', 'You cannot deactivate your own account.');
            $this->redirect('/super-admin/users');
        }

        $user = $this->userModel->findById((int)$id);
        if (!$user) {
            $this->flash('error', 'User not found.');
            $this->redirect('/super-admin/users');
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        $this->userModel->update((int)$id, [
            'status'     => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        log_activity('super_admin_toggle_user', "Super Admin set user #{$id} status to {$newStatus}");
        $this->flash('success', "User account has been {$newStatus}.");
        $this->redirect('/super-admin/users');
    }
}
