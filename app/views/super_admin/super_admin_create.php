<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= base_url('/super-admin/users?role=super_admin') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <div>
            <h1 class="h3 fw-bold mb-0">
                <i class="bi bi-shield-fill-check me-2 text-dark"></i>Create Super Admin Account
            </h1>
            <p class="text-muted mb-0 small">Only existing Super Admins can create new Super Admin accounts.</p>
        </div>
    </div>

    <!-- Security notice -->
    <div class="alert alert-warning d-flex gap-3 align-items-start mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0 mt-1"></i>
        <div>
            <strong>High-Privilege Action</strong><br>
            <small>
                Super Admin accounts have <strong>full platform authority</strong> — they can approve Gym Owners,
                manage all gyms, and access every module. Only create this account for a trusted platform administrator.
                This action is logged.
            </small>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm border-top border-dark border-4">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-person-plus-fill me-2"></i>New Super Admin Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('/super-admin/create-super-admin') ?>" novalidate>
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= e($_POST['name'] ?? '') ?>"
                                   placeholder="e.g. Platform Administrator"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= e($_POST['email'] ?? '') ?>"
                                   placeholder="admin@platform.com"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="username" class="form-control"
                                   value="<?= e($_POST['username'] ?? '') ?>"
                                   placeholder="e.g. superadmin2"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control"
                                       id="saPassword"
                                       placeholder="Min. 8 chars, 1 uppercase, 1 special"
                                       required>
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePwd('saPassword','saEye1')">
                                    <i class="bi bi-eye-fill" id="saEye1"></i>
                                </button>
                            </div>
                            <!-- Strength bar -->
                            <div class="mt-2">
                                <div class="progress" style="height:5px;">
                                    <div id="saStrengthBar" class="progress-bar"
                                         style="width:0%;transition:width .3s,background .3s"></div>
                                </div>
                                <div id="saStrengthLabel" class="form-text mt-1"></div>
                            </div>
                            <ul class="list-unstyled mt-1 mb-0" style="font-size:0.75rem">
                                <li id="sa-req-len"    class="text-muted"><i class="bi bi-circle me-1"></i>8+ characters</li>
                                <li id="sa-req-upper"  class="text-muted"><i class="bi bi-circle me-1"></i>One uppercase letter</li>
                                <li id="sa-req-special"class="text-muted"><i class="bi bi-circle me-1"></i>One special character</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Confirm Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" class="form-control"
                                       id="saConfirm"
                                       placeholder="Repeat password"
                                       required>
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePwd('saConfirm','saEye2')">
                                    <i class="bi bi-eye-fill" id="saEye2"></i>
                                </button>
                            </div>
                            <div id="saMatchFeedback" class="form-text mt-1"></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-dark"
                                    onclick="return confirm('Create a new Super Admin account? This grants full platform authority.')">
                                <i class="bi bi-shield-fill-check me-1"></i>Create Super Admin
                            </button>
                            <a href="<?= base_url('/super-admin/users?role=super_admin') ?>"
                               class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info panel -->
        <div class="col-lg-4 d-none d-lg-block">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <strong><i class="bi bi-info-circle me-1 text-primary"></i>Super Admin Capabilities</strong>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex gap-2 align-items-start">
                            <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                            <span class="small">Access all modules and dashboards</span>
                        </li>
                        <li class="list-group-item d-flex gap-2 align-items-start">
                            <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                            <span class="small">Approve or reject Gym Owner applications</span>
                        </li>
                        <li class="list-group-item d-flex gap-2 align-items-start">
                            <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                            <span class="small">Create and manage Gym Owner accounts</span>
                        </li>
                        <li class="list-group-item d-flex gap-2 align-items-start">
                            <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                            <span class="small">Create and manage multiple gyms</span>
                        </li>
                        <li class="list-group-item d-flex gap-2 align-items-start">
                            <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                            <span class="small">View and manage all system users</span>
                        </li>
                        <li class="list-group-item d-flex gap-2 align-items-start">
                            <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                            <span class="small">Activate or deactivate any account</span>
                        </li>
                        <li class="list-group-item d-flex gap-2 align-items-start">
                            <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                            <span class="small">Create other Super Admin accounts</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
}

const saPw    = document.getElementById('saPassword');
const saConf  = document.getElementById('saConfirm');
const saBar   = document.getElementById('saStrengthBar');
const saLabel = document.getElementById('saStrengthLabel');
const saMatch = document.getElementById('saMatchFeedback');

function saSetReq(id, met) {
    const el = document.getElementById(id);
    el.className = met ? 'text-success' : 'text-muted';
    el.querySelector('i').className = met ? 'bi bi-check-circle-fill me-1' : 'bi bi-circle me-1';
}

saPw.addEventListener('input', function () {
    const pw = this.value;
    const hasLen     = pw.length >= 8;
    const hasUpper   = /[A-Z]/.test(pw);
    const hasSpecial = /[!@#$%^&*()\-_=+\[\]{};':"\\|,.<>\/?`~]/.test(pw);

    saSetReq('sa-req-len',     hasLen);
    saSetReq('sa-req-upper',   hasUpper);
    saSetReq('sa-req-special', hasSpecial);

    const score = [hasLen, hasUpper, hasSpecial].filter(Boolean).length;
    const levels = [
        { pct: 0,   color: '',        label: '' },
        { pct: 33,  color: '#dc3545', label: '<span class="text-danger">Weak</span>' },
        { pct: 66,  color: '#fd7e14', label: '<span class="text-warning">Fair</span>' },
        { pct: 100, color: '#198754', label: '<span class="text-success">Strong ✓</span>' },
    ];
    saBar.style.width           = levels[score].pct + '%';
    saBar.style.backgroundColor = levels[score].color;
    saLabel.innerHTML           = levels[score].label;
    saCheckMatch();
});

function saCheckMatch() {
    const cfm = saConf.value;
    if (!cfm) { saMatch.textContent = ''; saConf.classList.remove('is-valid','is-invalid'); return; }
    if (saPw.value === cfm) {
        saMatch.innerHTML = '<span class="text-success">✓ Passwords match</span>';
        saConf.classList.add('is-valid'); saConf.classList.remove('is-invalid');
    } else {
        saMatch.innerHTML = '<span class="text-danger">✗ Passwords do not match</span>';
        saConf.classList.add('is-invalid'); saConf.classList.remove('is-valid');
    }
}
saConf.addEventListener('input', saCheckMatch);
</script>
