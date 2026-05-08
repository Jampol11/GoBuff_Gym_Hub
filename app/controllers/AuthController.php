<?php
/**
 * AuthController - Handles login, logout, registration
 */
class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth.login', ['title' => 'Login'], 'auth');
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        // CSRF check
        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/login');
        }

        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $v = Validator::make(['email' => $email, 'password' => $password], [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($v->fails()) {
            $this->flash('error', $v->firstError());
            $this->redirect('/login');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !verify_password($password, $user['password'])) {
            $this->userModel->logLoginAttempt($email, false, $ip);
            $this->flash('error', 'Invalid email or password.');
            $this->redirect('/login');
        }

        if ($user['status'] !== 'active') {
            $this->flash('error', 'Your account is inactive. Please contact the administrator.');
            $this->redirect('/login');
        }

        // Log attempt (success confirmed after OTP)
        $this->userModel->logLoginAttempt($email, true, $ip);

        // Store pending user and send OTP
        $this->session->set('otp_pending_user', [
            'id'    => $user['id'],
            'email' => $user['email'],
            'name'  => $user['name'],
        ]);
        $this->session->set('otp_purpose', 'login');

        $otpService = new OtpService();
        $otp  = $otpService->sendOtp($user['id'], $user['email'], $user['name'], 'login');

        // In dev mode, store OTP in session so it can be shown on the verify page
        if (APP_ENV === 'development') {
            $this->session->set('otp_dev_code', $otp);
        }

        $this->flash('info', 'A verification code has been sent to ' . e($user['email']));
        $this->redirect('/otp/verify');
    }

    public function logout(): void
    {
        log_activity('logout', 'User logged out');
        Auth::logout();
        $this->flash('success', 'You have been logged out successfully.');
        $this->redirect('/login');
    }

    public function registerForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth.register', ['title' => 'Register', 'errors' => [], 'old' => []], 'auth');
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/register');
        }

        $old = [
            'name'     => sanitize($_POST['name'] ?? ''),
            'email'    => sanitize($_POST['email'] ?? ''),
            'username' => sanitize($_POST['username'] ?? ''),
        ];

        $data = array_merge($old, [
            'password'              => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
        ]);

        $v = Validator::make($data, [
            'name'     => 'required|min:2|max:100',
            'email'    => 'required|email|max:150',
            'username' => 'required|min:3|max:50',
            'password' => 'required|min:8|strong_password|confirmed',
        ]);

        if ($v->fails()) {
            $this->view('auth.register', [
                'title'  => 'Register',
                'errors' => $v->errors(),
                'old'    => $old,
            ], 'auth');
            return;
        }

        // Check duplicates
        if ($this->userModel->findByEmail($data['email'])) {
            $this->view('auth.register', [
                'title'  => 'Register',
                'errors' => ['email' => 'Email address is already registered.'],
                'old'    => $old,
            ], 'auth');
            return;
        }
        if ($this->userModel->findByUsername($data['username'])) {
            $this->view('auth.register', [
                'title'  => 'Register',
                'errors' => ['username' => 'Username is already taken.'],
                'old'    => $old,
            ], 'auth');
            return;
        }

        $userId = $this->userModel->createUser([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'username' => $data['username'],
            'password' => $data['password'],
            'role'     => 'member',
            'status'   => 'active',
        ]);

        if ($userId) {
            // Create member record
            $memberModel = new Member();
            $memberModel->insert([
                'user_id'       => $userId,
                'first_name'    => explode(' ', $data['name'])[0],
                'last_name'     => implode(' ', array_slice(explode(' ', $data['name']), 1)) ?: '',
                'membership_id' => generate_membership_id(),
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
            ]);

            log_activity('register', 'New member registered', $userId);

            // Send OTP to verify email before completing registration login
            $newUser = $this->userModel->findById($userId);
            $this->session->set('otp_pending_user', [
                'id'    => $userId,
                'email' => $newUser['email'],
                'name'  => $newUser['name'],
            ]);
            $this->session->set('otp_purpose', 'register');

            $otpService = new OtpService();
            $otp  = $otpService->sendOtp($userId, $newUser['email'], $newUser['name'], 'register');

            if (APP_ENV === 'development') {
                $this->session->set('otp_dev_code', $otp);
            }

            $this->flash('info', 'Account created! A verification code has been sent to ' . e($newUser['email']));
            $this->redirect('/otp/verify');
        } else {
            $this->view('auth.register', [
                'title'  => 'Register',
                'errors' => ['general' => 'Registration failed. Please try again.'],
                'old'    => $old,
            ], 'auth');
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Admin: User Management                                            */
    /* ------------------------------------------------------------------ */

    public function userList(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $search  = sanitize($_GET['search'] ?? '');
        $perPage = RECORDS_PER_PAGE;

        if ($search) {
            $users = $this->userModel->searchUsers($search);
            $total = count($users);
        } else {
            $total = $this->userModel->count();
            $users = $this->userModel->findAll('created_at DESC', $perPage, ($page - 1) * $perPage);
        }

        $this->view('auth.user_list', [
            'title'      => 'User Management',
            'users'      => $users,
            'pagination' => $this->paginate($total, $page, $perPage),
            'search'     => $search,
        ]);
    }

    public function createUserForm(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);
        $this->view('auth.create_user', ['title' => 'Create User Account']);
    }

    public function createUser(): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/admin/users/create');
        }

        $allowedRoles = ['gym_owner', 'admin', 'marketing', 'trainer', 'maintenance', 'member'];
        $role         = sanitize($_POST['role'] ?? 'member');

        // Only gym_owner can create another gym_owner
        if ($role === 'gym_owner' && Auth::role() !== 'gym_owner') {
            $this->flash('error', 'Only the Gym Owner can create another Gym Owner account.');
            $this->redirect('/admin/users/create');
        }

        if (!in_array($role, $allowedRoles)) {
            $this->flash('error', 'Invalid role selected.');
            $this->redirect('/admin/users/create');
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
            $this->redirect('/admin/users/create');
        }

        if ($this->userModel->findByEmail($data['email'])) {
            $this->flash('error', 'Email address is already registered.');
            $this->redirect('/admin/users/create');
        }
        if ($this->userModel->findByUsername($data['username'])) {
            $this->flash('error', 'Username is already taken.');
            $this->redirect('/admin/users/create');
        }

        $userId = $this->userModel->createUser([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'username' => $data['username'],
            'password' => $data['password'],
            'role'     => $role,
            'status'   => sanitize($_POST['status'] ?? 'active'),
        ]);

        if (!$userId) {
            $this->flash('error', 'Failed to create user account.');
            $this->redirect('/admin/users/create');
        }

        // If role is member, also create a member record
        if ($role === 'member') {
            $memberModel = new Member();
            $memberModel->insert([
                'user_id'       => $userId,
                'first_name'    => explode(' ', $data['name'])[0],
                'last_name'     => implode(' ', array_slice(explode(' ', $data['name']), 1)) ?: '',
                'membership_id' => generate_membership_id(),
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        // If role is a staff role, also create an employee record
        $staffRoles = ['trainer', 'maintenance', 'marketing', 'admin', 'gym_owner'];
        if (in_array($role, $staffRoles)) {
            $employeeModel = new Employee();
            $nameParts = explode(' ', $data['name']);
            $employeeModel->insert([
                'user_id'    => $userId,
                'first_name' => $nameParts[0],
                'last_name'  => implode(' ', array_slice($nameParts, 1)) ?: '',
                'job_role'   => $role,
                'department' => ucfirst($role),
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        log_activity('user_create', "Admin created user: {$data['email']} with role: {$role}");
        $this->flash('success', "User account created successfully with role: " . role_label($role));
        $this->redirect('/admin/users');
    }

    public function editUserForm(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

        $user = $this->userModel->findById((int)$id);
        if (!$user) {
            $this->flash('error', 'User not found.');
            $this->redirect('/admin/users');
        }
        $this->view('auth.edit_user', ['title' => 'Edit User', 'user' => $user]);
    }

    public function updateUser(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner', 'admin']);

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/admin/users/' . $id . '/edit');
        }

        $role = sanitize($_POST['role'] ?? 'member');

        // Only gym_owner can assign gym_owner role
        if ($role === 'gym_owner' && Auth::role() !== 'gym_owner') {
            $this->flash('error', 'Only the Gym Owner can assign the Gym Owner role.');
            $this->redirect('/admin/users/' . $id . '/edit');
        }

        $updateData = [
            'name'       => sanitize($_POST['name'] ?? ''),
            'role'       => $role,
            'status'     => sanitize($_POST['status'] ?? 'active'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Optionally reset password
        $newPassword = $_POST['new_password'] ?? '';
        if ($newPassword !== '') {
            if (strlen($newPassword) < 8) {
                $this->flash('error', 'New password must be at least 8 characters.');
                $this->redirect('/admin/users/' . $id . '/edit');
            }
            if (!preg_match('/[A-Z]/', $newPassword)) {
                $this->flash('error', 'New password must contain at least one uppercase letter.');
                $this->redirect('/admin/users/' . $id . '/edit');
            }
            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?`~]/', $newPassword)) {
                $this->flash('error', 'New password must contain at least one special character (!@#$%^&* etc.).');
                $this->redirect('/admin/users/' . $id . '/edit');
            }
            $updateData['password'] = hash_password($newPassword);
        }

        $this->userModel->update((int)$id, $updateData);
        log_activity('user_update', "Updated user ID: {$id}, role: {$role}");
        $this->flash('success', 'User account updated successfully.');
        $this->redirect('/admin/users');
    }

    public function deleteUser(string $id): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(['gym_owner']);

        if (!verify_csrf()) {
            $this->json(['error' => 'Invalid token'], 403);
        }

        // Prevent self-deletion
        if ((int)$id === Auth::id()) {
            $this->flash('error', 'You cannot delete your own account.');
            $this->redirect('/admin/users');
        }

        $this->userModel->delete((int)$id);
        log_activity('user_delete', "Deleted user ID: {$id}");
        $this->flash('success', 'User account deleted.');
        $this->redirect('/admin/users');
    }

    /* ------------------------------------------------------------------ */
    /*  Change Password                                                     */
    /* ------------------------------------------------------------------ */

    public function changePasswordForm(): void
    {
        AuthMiddleware::handle();
        $this->view('auth.change_password', ['title' => 'Change Password']);
    }

    public function changePassword(): void
    {
        AuthMiddleware::handle();
        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/change-password');
        }

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['new_password_confirmation'] ?? '';

        if ($new !== $confirm) {
            $this->flash('error', 'New passwords do not match.');
            $this->redirect('/change-password');
        }
        if (strlen($new) < 8) {
            $this->flash('error', 'Password must be at least 8 characters.');
            $this->redirect('/change-password');
        }
        if (!preg_match('/[A-Z]/', $new)) {
            $this->flash('error', 'New password must contain at least one uppercase letter.');
            $this->redirect('/change-password');
        }
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?`~]/', $new)) {
            $this->flash('error', 'New password must contain at least one special character (!@#$%^&* etc.).');
            $this->redirect('/change-password');
        }

        $user = $this->userModel->findById(Auth::id());
        if (!verify_password($current, $user['password'])) {
            $this->flash('error', 'Current password is incorrect.');
            $this->redirect('/change-password');
        }

        $this->userModel->updatePassword(Auth::id(), $new);
        log_activity('password_change', 'User changed password');
        $this->flash('success', 'Password changed successfully.');
        $this->redirect('/dashboard');
    }
}
