<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">Create User Account</h2>
            <p class="page-subtitle">Create accounts for staff and members with specific roles</p>
        </div>
        <a href="<?= base_url('/admin/users') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Users
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">

            <!-- Role Info Banner -->
            <div class="alert alert-info d-flex gap-3 mb-4">
                <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 mt-1"></i>
                <div>
                    <strong>Role-Based Access</strong><br>
                    <small>
                        The role you assign determines what this user can access in the system.
                        Public self-registration is restricted to <strong>Member</strong> only.
                        Use this form to create staff accounts.
                    </small>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>New User Details</h6>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/admin/users') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>

                        <!-- Role Selector — shown prominently first -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold fs-6">
                                Select Role <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2" id="roleCards">
                                <?php
                                $roles = [
                                    'gym_owner'   => ['icon' => 'building-fill',          'color' => 'danger',    'desc' => 'Full system access'],
                                    'admin'       => ['icon' => 'shield-fill-check',       'color' => 'primary',   'desc' => 'Manage members & operations'],
                                    'marketing'   => ['icon' => 'megaphone-fill',          'color' => 'info',      'desc' => 'Campaigns & promotions'],
                                    'trainer'     => ['icon' => 'person-badge-fill',       'color' => 'success',   'desc' => 'Fitness plans & bookings'],
                                    'maintenance' => ['icon' => 'wrench-adjustable-circle-fill', 'color' => 'warning', 'desc' => 'Equipment & maintenance'],
                                    'member'      => ['icon' => 'person-fill',             'color' => 'secondary', 'desc' => 'Gym member access'],
                                    'user'        => ['icon' => 'person-dash-fill',        'color' => 'dark',      'desc' => 'Pending role assignment'],
                                ];
                                foreach ($roles as $roleKey => $roleInfo):
                                    // Only gym_owner can see the gym_owner option
                                    if ($roleKey === 'gym_owner' && !has_role(['gym_owner'])) continue;
                                ?>
                                    <div class="col-6 col-md-4">
                                        <input type="radio" class="btn-check" name="role"
                                               id="role_<?= $roleKey ?>" value="<?= $roleKey ?>"
                                               <?= ($roleKey === 'member') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-<?= $roleInfo['color'] ?> w-100 h-100 d-flex flex-column align-items-center py-3 role-card-label"
                                               for="role_<?= $roleKey ?>">
                                            <i class="bi bi-<?= $roleInfo['icon'] ?> fs-3 mb-1"></i>
                                            <span class="fw-semibold small"><?= role_label($roleKey) ?></span>
                                            <span class="text-muted" style="font-size:0.7rem"><?= $roleInfo['desc'] ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required
                                       placeholder="e.g. Maria Santos"
                                       value="<?= e($_POST['name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required
                                       placeholder="user@example.com"
                                       value="<?= e($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="username" required
                                       placeholder="e.g. maria.santos"
                                       value="<?= e($_POST['username'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password"
                                           id="createPassword" required minlength="8"
                                           placeholder="Min. 8 chars, 1 uppercase, 1 special">
                                    <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePwd('createPassword', 'eyeIcon1')">
                                        <i class="bi bi-eye-fill" id="eyeIcon1"></i>
                                    </button>
                                </div>
                                <!-- Strength bar -->
                                <div class="mt-2">
                                    <div class="progress" style="height:5px;">
                                        <div id="cuStrengthBar" class="progress-bar"
                                             style="width:0%;transition:width .3s,background .3s"></div>
                                    </div>
                                    <div id="cuStrengthLabel" class="form-text mt-1"></div>
                                </div>
                                <ul class="list-unstyled mt-1 mb-0" style="font-size:0.75rem">
                                    <li id="cu-req-len"    class="text-muted"><i class="bi bi-circle me-1"></i>8+ characters</li>
                                    <li id="cu-req-upper"  class="text-muted"><i class="bi bi-circle me-1"></i>One uppercase letter</li>
                                    <li id="cu-req-special"class="text-muted"><i class="bi bi-circle me-1"></i>One special character</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password_confirmation"
                                           id="confirmPassword" required
                                           placeholder="Repeat password">
                                    <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePwd('confirmPassword', 'eyeIcon2')">
                                        <i class="bi bi-eye-fill" id="eyeIcon2"></i>
                                    </button>
                                </div>
                                <div id="cuMatchFeedback" class="form-text mt-1"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Account Status</label>
                                <select class="form-select" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Role-specific note -->
                        <div id="roleNote" class="alert alert-secondary mt-3 small mb-0" style="display:none"></div>

                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/admin/users') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-check-fill me-1"></i>Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Role Reference Card -->
        <div class="col-lg-4 d-none d-lg-block">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-2 text-primary"></i>Role Permissions</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($roles as $roleKey => $roleInfo):
                            if ($roleKey === 'gym_owner' && !has_role(['gym_owner'])) continue;
                        ?>
                            <div class="list-group-item">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-<?= $roleInfo['icon'] ?> text-<?= $roleInfo['color'] ?>"></i>
                                    <span class="fw-semibold small"><?= role_label($roleKey) ?></span>
                                </div>
                                <small class="text-muted">
                                    <?php
                                    $perms = [
                                        'gym_owner'   => 'Full access to all modules, settings, and user management.',
                                        'admin'       => 'Manage members, memberships, staff, attendance, and notifications.',
                                        'marketing'   => 'Create and manage campaigns and promotions.',
                                        'trainer'     => 'Manage fitness plans, nutrition plans, bookings, and progress tracking.',
                                        'maintenance' => 'Report and resolve equipment maintenance issues.',
                                        'member'      => 'View dashboard, check-in, book trainers, log diet, and view own plans.',
                                        'user'        => 'Newly registered user. Must apply for a role to access features.',
                                    ];
                                    echo $perms[$roleKey];
                                    ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
        icon.className = 'bi bi-eye-slash-fill';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye-fill';
    }
}

// Password strength meter for create user form
const cuPw      = document.getElementById('createPassword');
const cuConfirm = document.getElementById('confirmPassword');
const cuBar     = document.getElementById('cuStrengthBar');
const cuLabel   = document.getElementById('cuStrengthLabel');
const cuMatch   = document.getElementById('cuMatchFeedback');

function cuSetReq(id, met) {
    const el = document.getElementById(id);
    if (met) {
        el.className = 'text-success';
        el.querySelector('i').className = 'bi bi-check-circle-fill me-1';
    } else {
        el.className = 'text-muted';
        el.querySelector('i').className = 'bi bi-circle me-1';
    }
}

cuPw.addEventListener('input', function () {
    const pw = this.value;
    const hasLen     = pw.length >= 8;
    const hasUpper   = /[A-Z]/.test(pw);
    const hasSpecial = /[!@#$%^&*()\-_=+\[\]{};':"\\|,.<>\/?`~]/.test(pw);

    cuSetReq('cu-req-len',     hasLen);
    cuSetReq('cu-req-upper',   hasUpper);
    cuSetReq('cu-req-special', hasSpecial);

    const score = [hasLen, hasUpper, hasSpecial].filter(Boolean).length;
    const levels = [
        { pct: 0,   color: '',        label: '' },
        { pct: 33,  color: '#dc3545', label: '<span class="text-danger">Weak</span>' },
        { pct: 66,  color: '#fd7e14', label: '<span class="text-warning">Fair</span>' },
        { pct: 100, color: '#198754', label: '<span class="text-success">Strong ✓</span>' },
    ];
    cuBar.style.width           = levels[score].pct + '%';
    cuBar.style.backgroundColor = levels[score].color;
    cuLabel.innerHTML           = levels[score].label;
    cuCheckMatch();
});

function cuCheckMatch() {
    const cfm = cuConfirm.value;
    if (!cfm) { cuMatch.textContent = ''; cuConfirm.classList.remove('is-valid','is-invalid'); return; }
    if (cuPw.value === cfm) {
        cuMatch.innerHTML = '<span class="text-success">✓ Passwords match</span>';
        cuConfirm.classList.add('is-valid'); cuConfirm.classList.remove('is-invalid');
    } else {
        cuMatch.innerHTML = '<span class="text-danger">✗ Passwords do not match</span>';
        cuConfirm.classList.add('is-invalid'); cuConfirm.classList.remove('is-valid');
    }
}

cuConfirm.addEventListener('input', cuCheckMatch);

// Show role-specific notes
const roleNotes = {
    gym_owner:   '⚠️ Gym Owner has unrestricted access to the entire system including user deletion.',
    admin:       'ℹ️ Admin can manage most operations but cannot delete users or assign the Gym Owner role.',
    marketing:   'ℹ️ Marketing Officer can only access campaigns and promotions.',
    trainer:     'ℹ️ Trainer can manage fitness/nutrition plans and view assigned members.',
    maintenance: 'ℹ️ Maintenance Supervisor can report and resolve equipment issues.',
    member:      'ℹ️ Member has limited access: dashboard, check-in, bookings, diet log, and own plans.',
    user:        'ℹ️ User has no role yet. They must apply for a role through the Role Application system.',
};

document.querySelectorAll('input[name="role"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const note = document.getElementById('roleNote');
        note.textContent = roleNotes[this.value] || '';
        note.style.display = this.value ? 'block' : 'none';
    });
});

// Trigger on load for default selected
const defaultRole = document.querySelector('input[name="role"]:checked');
if (defaultRole) defaultRole.dispatchEvent(new Event('change'));
</script>
