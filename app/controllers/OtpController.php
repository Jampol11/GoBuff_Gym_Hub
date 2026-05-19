<?php
/**
 * OtpController — OTP verification and resend
 */
class OtpController extends Controller
{
    private OtpService $otpService;
    private User       $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->otpService = new OtpService();
        $this->userModel  = new User();
    }

    /** Show OTP entry form */
    public function showForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $pending = $this->session->get('otp_pending_user');
        if (!$pending) {
            $this->flash('error', 'Session expired. Please sign in again.');
            $this->redirect('/login');
        }

        $this->view('auth.otp_verify', [
            'title'   => 'Verify Your Identity',
            'email'   => $pending['email'],
            'purpose' => $this->session->get('otp_purpose', 'login'),
        ], 'auth');
    }

    /** Handle OTP submission */
    public function verify(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        if (!verify_csrf()) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/otp/verify');
        }

        $pending = $this->session->get('otp_pending_user');
        if (!$pending) {
            $this->flash('error', 'Session expired. Please sign in again.');
            $this->redirect('/login');
        }

        $submitted = trim($_POST['otp'] ?? '');
        $purpose   = $this->session->get('otp_purpose', 'login');

        if (empty($submitted)) {
            $this->flash('error', 'Please enter the verification code.');
            $this->redirect('/otp/verify');
        }

        $result = $this->otpService->verify($pending['id'], $submitted, $purpose);

        switch ($result) {
            case 'ok':
                // OTP passed — complete login
                $user = $this->userModel->findById($pending['id']);
                if (!$user || $user['status'] !== 'active') {
                    $this->flash('error', 'Account not found or inactive.');
                    $this->redirect('/login');
                }

                // Clean up pending session keys
                $this->session->remove('otp_pending_user');
                $this->session->remove('otp_purpose');

                // Update last login
                $this->userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
                $this->userModel->logLoginAttempt($user['email'], true, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
                log_activity('login', 'User logged in (OTP verified)', $user['id']);

                unset($user['password']);
                Auth::login($user);

                $this->flash('success', 'Welcome back, ' . e($user['name']) . '!');
                $this->redirect('/dashboard');
                break;

            case 'expired':
                $this->flash('error', 'Your verification code has expired. Please request a new one.');
                $this->redirect('/otp/verify');
                break;

            case 'locked':
                $this->flash('error', 'Too many incorrect attempts. Please request a new code.');
                $this->redirect('/otp/verify');
                break;

            default: // 'invalid'
                $this->flash('error', 'Incorrect verification code. Please try again.');
                $this->redirect('/otp/verify');
                break;
        }
    }

    /** Resend OTP */
    public function resend(): void
    {
        $pending = $this->session->get('otp_pending_user');
        if (!$pending) {
            $this->flash('error', 'Session expired. Please sign in again.');
            $this->redirect('/login');
        }

        $purpose = $this->session->get('otp_purpose', 'login');
        $this->otpService->sendOtp(
            $pending['id'],
            $pending['email'],
            $pending['name'],
            $purpose
        );

        $this->flash('success', 'A new verification code has been sent to ' . $pending['email']);

        $this->redirect('/otp/verify');
    }
}
