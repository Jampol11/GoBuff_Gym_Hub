<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-shield-lock-fill me-2"></i>Change Password</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/change-password') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" name="current_password"
                                       id="currentPassword" required placeholder="Enter current password">
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePwd('currentPassword','eyeCurrent')">
                                    <i class="bi bi-eye" id="eyeCurrent"></i>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" name="new_password"
                                       id="newPassword" required minlength="8"
                                       placeholder="Min. 8 chars, 1 uppercase, 1 special char">
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePwd('newPassword','eyeNew')">
                                    <i class="bi bi-eye" id="eyeNew"></i>
                                </button>
                            </div>
                            <!-- Strength bar -->
                            <div class="mt-2">
                                <div class="progress" style="height:5px;">
                                    <div id="strengthBar" class="progress-bar"
                                         style="width:0%;transition:width .3s,background .3s"></div>
                                </div>
                                <div id="strengthLabel" class="form-text mt-1"></div>
                            </div>
                            <!-- Requirements checklist -->
                            <ul class="list-unstyled mt-2 mb-0" style="font-size:0.78rem">
                                <li id="req-len"    class="text-muted"><i class="bi bi-circle me-1"></i>At least 8 characters</li>
                                <li id="req-upper"  class="text-muted"><i class="bi bi-circle me-1"></i>At least one uppercase letter (A–Z)</li>
                                <li id="req-special"class="text-muted"><i class="bi bi-circle me-1"></i>At least one special character (!@#$%^&amp;* etc.)</li>
                            </ul>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" name="new_password_confirmation"
                                       id="confirmPassword" required placeholder="Repeat new password">
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePwd('confirmPassword','eyeConfirm')">
                                    <i class="bi bi-eye" id="eyeConfirm"></i>
                                </button>
                            </div>
                            <div id="matchFeedback" class="form-text mt-1"></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Update Password
                            </button>
                            <a href="<?= base_url('/dashboard') ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

const newPw      = document.getElementById('newPassword');
const confirmPw  = document.getElementById('confirmPassword');
const bar        = document.getElementById('strengthBar');
const barLabel   = document.getElementById('strengthLabel');
const matchFb    = document.getElementById('matchFeedback');

function setReq(id, met) {
    const el = document.getElementById(id);
    if (met) {
        el.className = 'text-success';
        el.querySelector('i').className = 'bi bi-check-circle-fill me-1';
    } else {
        el.className = 'text-muted';
        el.querySelector('i').className = 'bi bi-circle me-1';
    }
}

newPw.addEventListener('input', function () {
    const pw = this.value;
    const hasLen     = pw.length >= 8;
    const hasUpper   = /[A-Z]/.test(pw);
    const hasSpecial = /[!@#$%^&*()\-_=+\[\]{};':"\\|,.<>\/?`~]/.test(pw);

    setReq('req-len',     hasLen);
    setReq('req-upper',   hasUpper);
    setReq('req-special', hasSpecial);

    const score = [hasLen, hasUpper, hasSpecial].filter(Boolean).length;
    const levels = [
        { pct: 0,   color: '',        label: '' },
        { pct: 33,  color: '#dc3545', label: '<span class="text-danger">Weak</span>' },
        { pct: 66,  color: '#fd7e14', label: '<span class="text-warning">Fair</span>' },
        { pct: 100, color: '#198754', label: '<span class="text-success">Strong ✓</span>' },
    ];
    bar.style.width           = levels[score].pct + '%';
    bar.style.backgroundColor = levels[score].color;
    barLabel.innerHTML        = levels[score].label;

    checkMatch();
});

function checkMatch() {
    const cfm = confirmPw.value;
    if (!cfm) { matchFb.textContent = ''; confirmPw.classList.remove('is-valid','is-invalid'); return; }
    if (newPw.value === cfm) {
        matchFb.innerHTML = '<span class="text-success">✓ Passwords match</span>';
        confirmPw.classList.add('is-valid'); confirmPw.classList.remove('is-invalid');
    } else {
        matchFb.innerHTML = '<span class="text-danger">✗ Passwords do not match</span>';
        confirmPw.classList.add('is-invalid'); confirmPw.classList.remove('is-valid');
    }
}

confirmPw.addEventListener('input', checkMatch);
</script>
