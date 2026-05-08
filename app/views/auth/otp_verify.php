<div class="auth-form-header">
    <h2 class="auth-form-title">Verify your identity</h2>
    <p class="auth-form-subtitle">
        <?php if ($purpose === 'register'): ?>
            Confirm your email to activate your account
        <?php else: ?>
            Enter the code sent to your email
        <?php endif; ?>
    </p>
</div>

<?php
// ── DEV MODE: show OTP on screen ──────────────────────────────
$devCode = (new Session())->get('otp_dev_code');
if (APP_ENV === 'development' && $devCode):
    (new Session())->remove('otp_dev_code'); // show once
?>
<div class="alert alert-warning border-warning d-flex gap-2 align-items-start mb-3"
     style="font-size:0.85rem;border-left:4px solid #ffc107!important">
    <i class="bi bi-bug-fill flex-shrink-0 mt-1 text-warning"></i>
    <div>
        <strong>Dev Mode — OTP Code:</strong>
        <span class="badge bg-dark fs-5 ms-2 font-monospace letter-spacing-2"
              style="letter-spacing:6px"><?= e($devCode) ?></span>
        <div class="text-muted mt-1" style="font-size:0.75rem">
            This banner only appears when <code>APP_ENV = 'development'</code>.
            It will not show in production.
        </div>
    </div>
</div>
<?php endif; ?>

<div class="alert alert-info d-flex gap-2 align-items-start mb-4" style="font-size:0.85rem">
    <i class="bi bi-envelope-fill flex-shrink-0 mt-1 text-primary"></i>
    <div>
        We sent a <strong><?= OTP_LENGTH ?>-digit code</strong> to
        <strong><?= e($email) ?></strong>.<br>
        It expires in <strong><?= OTP_EXPIRY_MINS ?> minutes</strong>.
    </div>
</div>

<form action="<?= base_url('/otp/verify') ?>" method="POST" class="auth-form" id="otpForm" novalidate>
    <?= csrf_field() ?>

    <div class="mb-4">
        <label class="form-label fw-semibold text-center d-block mb-3">
            Verification Code
        </label>

        <!-- Individual digit boxes -->
        <div class="d-flex justify-content-center gap-2 mb-3" id="otpBoxes">
            <?php for ($i = 0; $i < OTP_LENGTH; $i++): ?>
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                   class="form-control text-center fw-bold otp-digit"
                   style="width:52px;height:58px;font-size:1.6rem;border-radius:10px;"
                   autocomplete="off" data-index="<?= $i ?>">
            <?php endfor; ?>
        </div>

        <!-- Hidden actual input submitted -->
        <input type="hidden" name="otp" id="otpHidden">
    </div>

    <button type="submit" class="btn btn-primary btn-auth w-100 mb-3" id="verifyBtn" disabled>
        <i class="bi bi-shield-check me-2"></i>Verify Code
    </button>
</form>

<!-- Resend -->
<div class="text-center">
    <p class="text-muted small mb-2">Didn't receive the code?</p>
    <form action="<?= base_url('/otp/resend') ?>" method="POST" class="d-inline">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-outline-secondary btn-sm" id="resendBtn">
            <i class="bi bi-arrow-clockwise me-1"></i>Resend Code
            <span id="resendTimer" class="ms-1 text-muted"></span>
        </button>
    </form>
</div>

<div class="auth-footer">
    <a href="<?= base_url('/login') ?>" class="text-muted small">
        <i class="bi bi-arrow-left me-1"></i>Back to Sign In
    </a>
</div>

<script>
const digits    = document.querySelectorAll('.otp-digit');
const hidden    = document.getElementById('otpHidden');
const verifyBtn = document.getElementById('verifyBtn');
const otpLen    = <?= OTP_LENGTH ?>;

// Auto-focus first box
digits[0].focus();

digits.forEach((input, idx) => {
    input.addEventListener('input', function () {
        // Allow only digits
        this.value = this.value.replace(/\D/g, '').slice(-1);

        updateHidden();

        // Move to next
        if (this.value && idx < otpLen - 1) {
            digits[idx + 1].focus();
        }
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace' && !this.value && idx > 0) {
            digits[idx - 1].focus();
            digits[idx - 1].value = '';
            updateHidden();
        }
    });

    // Handle paste on any digit
    input.addEventListener('paste', function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData)
            .getData('text').replace(/\D/g, '').slice(0, otpLen);
        pasted.split('').forEach((ch, i) => {
            if (digits[i]) digits[i].value = ch;
        });
        updateHidden();
        const next = Math.min(pasted.length, otpLen - 1);
        digits[next].focus();
    });
});

function updateHidden() {
    const code = Array.from(digits).map(d => d.value).join('');
    hidden.value = code;
    verifyBtn.disabled = code.length < otpLen;
}

// Resend cooldown (60 seconds)
let cooldown = 60;
const resendBtn   = document.getElementById('resendBtn');
const resendTimer = document.getElementById('resendTimer');
resendBtn.disabled = true;

const interval = setInterval(() => {
    cooldown--;
    resendTimer.textContent = '(' + cooldown + 's)';
    if (cooldown <= 0) {
        clearInterval(interval);
        resendBtn.disabled = false;
        resendTimer.textContent = '';
    }
}, 1000);
</script>
