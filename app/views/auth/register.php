<div class="auth-form-header">
    <h2 class="auth-form-title">Create your account</h2>
    <p class="auth-form-subtitle">Join <?= APP_NAME ?> and start your fitness journey</p>
</div>

<?php if (!empty($errors['general'])): ?>
<div class="alert alert-danger d-flex gap-2 align-items-center mb-3" style="font-size:0.82rem; border-radius:10px; border-left:3px solid #dc3545;">
    <i class="bi bi-x-circle-fill flex-shrink-0"></i>
    <div><?= e($errors['general']) ?></div>
</div>
<?php endif; ?>

<!-- Member-only notice -->
<div class="alert alert-info d-flex gap-2 align-items-start mb-3" style="font-size:0.78rem; border-radius:10px; border-left:3px solid #1e88e5; background:#eff6ff; color:#1e3a5f; border-top:none; border-right:none; border-bottom:none;">
    <i class="bi bi-info-circle-fill flex-shrink-0 mt-1" style="color:#1e88e5;"></i>
    <div>
        <strong>Members only.</strong> Staff accounts must be created by an administrator via <strong>User Management</strong>.
    </div>
</div>

<form action="<?= base_url('/register') ?>" method="POST" class="auth-form" novalidate>
    <?= csrf_field() ?>

    <!-- Full Name -->
    <div class="auth-field">
        <label for="name">Full Name</label>
        <div class="auth-underline-wrap">
            <input type="text"
                   class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
                   id="name" name="name"
                   placeholder="Enter your full name" required
                   value="<?= e($old['name'] ?? '') ?>">
            <i class="bi bi-check2 auth-field-icon"></i>
        </div>
        <?php if (!empty($errors['name'])): ?>
            <div style="font-size:0.75rem; color:#dc3545; margin-top:4px;"><?= e($errors['name']) ?></div>
        <?php endif; ?>
    </div>

    <!-- Email -->
    <div class="auth-field">
        <label for="email">Email Address</label>
        <div class="auth-underline-wrap">
            <input type="email"
                   class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                   id="email" name="email"
                   placeholder="Enter your email" required
                   value="<?= e($old['email'] ?? '') ?>">
            <i class="bi bi-check2 auth-field-icon"></i>
        </div>
        <?php if (!empty($errors['email'])): ?>
            <div style="font-size:0.75rem; color:#dc3545; margin-top:4px;"><?= e($errors['email']) ?></div>
        <?php endif; ?>
    </div>

    <!-- Username -->
    <div class="auth-field">
        <label for="username">Username</label>
        <div class="auth-underline-wrap">
            <input type="text"
                   class="form-control <?= !empty($errors['username']) ? 'is-invalid' : '' ?>"
                   id="username" name="username"
                   placeholder="Choose a username" required
                   value="<?= e($old['username'] ?? '') ?>">
            <i class="bi bi-check2 auth-field-icon"></i>
        </div>
        <?php if (!empty($errors['username'])): ?>
            <div style="font-size:0.75rem; color:#dc3545; margin-top:4px;"><?= e($errors['username']) ?></div>
        <?php endif; ?>
    </div>

    <!-- Password -->
    <div class="auth-field">
        <label for="password">Password</label>
        <div class="auth-underline-wrap">
            <input type="password"
                   class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                   id="password" name="password"
                   placeholder="Min. 8 chars, 1 uppercase, 1 special char" required minlength="8">
            <button class="auth-toggle-btn" type="button" id="togglePassword" tabindex="-1">
                <i class="bi bi-eye" id="togglePasswordIcon"></i>
            </button>
        </div>
        <?php if (!empty($errors['password'])): ?>
            <div style="font-size:0.75rem; color:#dc3545; margin-top:4px;"><?= e($errors['password']) ?></div>
        <?php endif; ?>
        <!-- Strength bar -->
        <div class="mt-2">
            <div class="progress" style="height:4px; background:#e2e8f0; border-radius:4px;">
                <div id="strengthBar" class="progress-bar" style="width:0%; border-radius:4px; transition:width .3s,background .3s"></div>
            </div>
            <div id="strengthLabel" class="mt-1" style="font-size:0.75rem;"></div>
        </div>
        <!-- Requirements checklist -->
        <ul class="list-unstyled mt-1 mb-0" style="font-size:0.75rem" id="pwReqs">
            <li id="req-len"     class="text-muted"><i class="bi bi-circle me-1"></i>At least 8 characters</li>
            <li id="req-upper"   class="text-muted"><i class="bi bi-circle me-1"></i>At least one uppercase letter</li>
            <li id="req-special" class="text-muted"><i class="bi bi-circle me-1"></i>At least one special character</li>
        </ul>
    </div>

    <!-- Confirm Password -->
    <div class="auth-field">
        <label for="password_confirmation">Confirm Password</label>
        <div class="auth-underline-wrap">
            <input type="password"
                   class="form-control"
                   id="password_confirmation" name="password_confirmation"
                   placeholder="Repeat your password" required>
            <button class="auth-toggle-btn" type="button" id="toggleConfirm" tabindex="-1">
                <i class="bi bi-eye" id="toggleConfirmIcon"></i>
            </button>
        </div>
        <div id="passwordMatchFeedback" style="font-size:0.75rem; margin-top:4px;"></div>
    </div>

    <!-- Actions -->
    <div class="auth-actions">
        <button type="submit" class="btn-auth-primary" id="submitBtn">
            Sign Up
        </button>
        <a href="<?= base_url('/login') ?>" class="btn-auth-secondary">
            Sign In
        </a>
    </div>
</form>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function () {
    const input = document.getElementById('password');
    const icon  = document.getElementById('togglePasswordIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

document.getElementById('toggleConfirm').addEventListener('click', function () {
    const input = document.getElementById('password_confirmation');
    const icon  = document.getElementById('toggleConfirmIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

// Password strength meter
const pwInput      = document.getElementById('password');
const confirmInput = document.getElementById('password_confirmation');
const feedback     = document.getElementById('passwordMatchFeedback');
const strengthBar  = document.getElementById('strengthBar');
const strengthLabel= document.getElementById('strengthLabel');

const reqLen     = document.getElementById('req-len');
const reqUpper   = document.getElementById('req-upper');
const reqSpecial = document.getElementById('req-special');

function setReq(el, met) {
    if (met) {
        el.className = 'text-success';
        el.querySelector('i').className = 'bi bi-check-circle-fill me-1';
    } else {
        el.className = 'text-muted';
        el.querySelector('i').className = 'bi bi-circle me-1';
    }
}

pwInput.addEventListener('input', function () {
    const pw = this.value;
    const hasLen     = pw.length >= 8;
    const hasUpper   = /[A-Z]/.test(pw);
    const hasSpecial = /[!@#$%^&*()\-_=+\[\]{};':"\\|,.<>\/?`~]/.test(pw);

    setReq(reqLen,     hasLen);
    setReq(reqUpper,   hasUpper);
    setReq(reqSpecial, hasSpecial);

    const score = [hasLen, hasUpper, hasSpecial].filter(Boolean).length;
    const levels = [
        { pct: 0,   color: '',          label: '' },
        { pct: 33,  color: '#dc3545',   label: '<span style="color:#dc3545">Weak</span>' },
        { pct: 66,  color: '#fd7e14',   label: '<span style="color:#fd7e14">Fair</span>' },
        { pct: 100, color: '#198754',   label: '<span style="color:#198754">Strong ✓</span>' },
    ];
    const lvl = levels[score];
    strengthBar.style.width           = lvl.pct + '%';
    strengthBar.style.backgroundColor = lvl.color;
    strengthLabel.innerHTML           = lvl.label;

    checkMatch();
});

function checkMatch() {
    const pw  = pwInput.value;
    const cfm = confirmInput.value;
    if (!cfm) {
        feedback.textContent = '';
        confirmInput.classList.remove('is-valid', 'is-invalid');
        return;
    }
    if (pw === cfm) {
        feedback.textContent = '✓ Passwords match';
        feedback.style.color = '#198754';
        confirmInput.classList.add('is-valid');
        confirmInput.classList.remove('is-invalid');
    } else {
        feedback.textContent = '✗ Passwords do not match';
        feedback.style.color = '#dc3545';
        confirmInput.classList.add('is-invalid');
        confirmInput.classList.remove('is-valid');
    }
}

confirmInput.addEventListener('input', checkMatch);
</script>
