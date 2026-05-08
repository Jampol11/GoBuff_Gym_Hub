<div class="auth-form-header">
    <h2 class="auth-form-title">Welcome back</h2>
    <p class="auth-form-subtitle">Sign in to your account to continue</p>
</div>

<form action="<?= base_url('/login') ?>" method="POST" class="auth-form" novalidate>
    <?= csrf_field() ?>

    <!-- Email -->
    <div class="auth-field">
        <label for="email">Email Address</label>
        <div class="auth-underline-wrap">
            <input type="email" class="form-control" id="email" name="email"
                   placeholder="Enter your email" required
                   value="<?= e($_POST['email'] ?? '') ?>">
            <i class="bi bi-check2 auth-field-icon"></i>
        </div>
    </div>

    <!-- Password -->
    <div class="auth-field">
        <label for="password">Password</label>
        <div class="auth-underline-wrap">
            <input type="password" class="form-control" id="password" name="password"
                   placeholder="Enter your password" required>
            <button class="auth-toggle-btn" type="button" id="togglePassword" tabindex="-1">
                <i class="bi bi-eye-fill" id="toggleIcon"></i>
            </button>
        </div>
    </div>

    <!-- Actions -->
    <div class="auth-actions">
        <button type="submit" class="btn-auth-primary">
            Sign In
        </button>
    </div>
</form>

<p class="mt-3 mb-0" style="color:#8a94a6; font-size:0.875rem;">
    Don't have an account? <a href="<?= base_url('/register') ?>" style="color:#1a1a2e; font-weight:700; text-decoration:none;">Sign Up</a>
</p>

<!-- Divider -->
<div class="auth-divider mt-3">
    <span>or continue with</span>
</div>

<!-- Google Sign-In -->
<a href="<?= base_url('/auth/google') ?>"
   class="btn btn-google w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
    <svg width="18" height="18" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
        <path fill="none" d="M0 0h48v48H0z"/>
    </svg>
    Sign in with Google
</a>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const pwd  = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'bi bi-eye-slash-fill';
    } else {
        pwd.type = 'password';
        icon.className = 'bi bi-eye-fill';
    }
});
</script>
