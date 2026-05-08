<?php
/**
 * GoogleAuthController — handles Google OAuth 2.0 login flow
 *
 * Flow:
 *   1. GET  /auth/google          → redirect to Google consent screen
 *   2. GET  /auth/google/callback → handle callback, find/create user,
 *                                   store pending session, send OTP, redirect to /otp
 */

use League\OAuth2\Client\Provider\Google;

class GoogleAuthController extends Controller
{
    private User       $userModel;
    private OtpService $otpService;

    public function __construct()
    {
        parent::__construct();
        $this->userModel  = new User();
        $this->otpService = new OtpService();
    }

    /** Step 1 — redirect to Google */
    public function redirectToGoogle(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        $provider = $this->makeProvider();
        $authUrl  = $provider->getAuthorizationUrl([
            'scope' => ['openid', 'email', 'profile'],
        ]);

        // Store state for CSRF protection
        $this->session->set('oauth2_state', $provider->getState());
        header('Location: ' . $authUrl);
        exit;
    }

    /** Step 2 — handle callback from Google */
    public function callback(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        // Validate state
        $state         = $_GET['state']          ?? '';
        $storedState   = $this->session->get('oauth2_state', '');
        $this->session->remove('oauth2_state');

        if (empty($state) || !hash_equals($storedState, $state)) {
            $this->flash('error', 'Invalid OAuth state. Please try again.');
            $this->redirect('/login');
        }

        if (isset($_GET['error'])) {
            $this->flash('error', 'Google sign-in was cancelled or denied.');
            $this->redirect('/login');
        }

        $code = $_GET['code'] ?? '';
        if (empty($code)) {
            $this->flash('error', 'No authorization code received from Google.');
            $this->redirect('/login');
        }

        try {
            $provider    = $this->makeProvider();
            $token       = $provider->getAccessToken('authorization_code', ['code' => $code]);
            $googleUser  = $provider->getResourceOwner($token);

            $googleId    = $googleUser->getId();
            $email       = $googleUser->getEmail();
            $name        = $googleUser->getName();
            $avatarUrl   = $googleUser->getAvatar();

            if (empty($email)) {
                $this->flash('error', 'Could not retrieve email from Google. Please ensure your Google account has a verified email.');
                $this->redirect('/login');
            }

            // Find existing user by Google ID or email
            $user = $this->userModel->findByGoogleId($googleId);

            if (!$user) {
                $user = $this->userModel->findByEmail($email);
                if ($user) {
                    // Link Google ID to existing local account
                    $this->userModel->update($user['id'], [
                        'google_id'     => $googleId,
                        'avatar_url'    => $avatarUrl,
                        'auth_provider' => 'google',
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ]);
                    $user = $this->userModel->findById($user['id']);
                } else {
                    // Create new member account from Google profile
                    $username = $this->generateUsername($email);
                    $userId   = $this->userModel->createGoogleUser([
                        'name'       => $name,
                        'email'      => $email,
                        'username'   => $username,
                        'role'       => 'member',
                        'google_id'  => $googleId,
                        'avatar_url' => $avatarUrl,
                    ]);

                    if (!$userId) {
                        $this->flash('error', 'Failed to create account. Please try again.');
                        $this->redirect('/login');
                    }

                    // Create member record
                    $memberModel = new Member();
                    $nameParts   = explode(' ', $name);
                    $memberModel->insert([
                        'user_id'       => $userId,
                        'first_name'    => $nameParts[0],
                        'last_name'     => implode(' ', array_slice($nameParts, 1)) ?: '',
                        'membership_id' => generate_membership_id(),
                        'status'        => 'active',
                        'created_at'    => date('Y-m-d H:i:s'),
                    ]);

                    log_activity('google_register', "New member via Google: {$email}", $userId);
                    $user = $this->userModel->findById($userId);
                }
            }

            if ($user['status'] !== 'active') {
                $this->flash('error', 'Your account is inactive. Please contact the administrator.');
                $this->redirect('/login');
            }

            // Store pending user in session, send OTP
            $this->session->set('otp_pending_user', [
                'id'    => $user['id'],
                'email' => $user['email'],
                'name'  => $user['name'],
            ]);
            $this->session->set('otp_purpose', 'login');

            $sent = $this->otpService->sendOtp($user['id'], $user['email'], $user['name'], 'login');
            if (APP_ENV === 'development') {
                $this->session->set('otp_dev_code', $sent);
            }

            $this->flash('info', 'A verification code has been sent to ' . $user['email']);
            $this->redirect('/otp/verify');

        } catch (\Exception $e) {
            error_log('Google OAuth error: ' . $e->getMessage());
            $this->flash('error', 'Google sign-in failed. Please try again.');
            $this->redirect('/login');
        }
    }

    private function makeProvider(): Google
    {
        return new Google([
            'clientId'     => GOOGLE_CLIENT_ID,
            'clientSecret' => GOOGLE_CLIENT_SECRET,
            'redirectUri'  => GOOGLE_REDIRECT_URI,
        ]);
    }

    private function generateUsername(string $email): string
    {
        $base = strtolower(explode('@', $email)[0]);
        $base = preg_replace('/[^a-z0-9_]/', '', $base);
        $base = $base ?: 'user';

        $username = $base;
        $i        = 1;
        while ($this->userModel->findByUsername($username)) {
            $username = $base . $i++;
        }
        return $username;
    }
}
