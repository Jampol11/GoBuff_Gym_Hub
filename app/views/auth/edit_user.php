<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">Edit User Account</h2>
            <p class="page-subtitle"><?= e($user['name']) ?> — <?= role_badge($user['role']) ?></p>
        </div>
        <a href="<?= base_url('/admin/users') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Users
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-pencil-fill me-2"></i>Edit User: <?= e($user['email']) ?></h6>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/admin/users/' . $user['id'] . '/update') ?>"
                          method="POST" novalidate>
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required
                                       value="<?= e($user['name']) ?>">
                            </div>

                            <!-- Role Selector -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <?php
                                    $roles = [
                                        'gym_owner'   => ['icon' => 'building-fill',                'color' => 'danger'],
                                        'admin'       => ['icon' => 'shield-fill-check',            'color' => 'primary'],
                                        'marketing'   => ['icon' => 'megaphone-fill',               'color' => 'info'],
                                        'trainer'     => ['icon' => 'person-badge-fill',            'color' => 'success'],
                                        'maintenance' => ['icon' => 'wrench-adjustable-circle-fill','color' => 'warning'],
                                        'member'      => ['icon' => 'person-fill',                  'color' => 'secondary'],
                                        'user'        => ['icon' => 'person-dash-fill',             'color' => 'dark'],
                                    ];
                                    foreach ($roles as $roleKey => $roleInfo):
                                        if ($roleKey === 'gym_owner' && !has_role(['gym_owner'])) continue;
                                    ?>
                                        <div class="col-6 col-md-4">
                                            <input type="radio" class="btn-check" name="role"
                                                   id="role_<?= $roleKey ?>" value="<?= $roleKey ?>"
                                                   <?= $user['role'] === $roleKey ? 'checked' : '' ?>>
                                            <label class="btn btn-outline-<?= $roleInfo['color'] ?> w-100 d-flex flex-column align-items-center py-2"
                                                   for="role_<?= $roleKey ?>">
                                                <i class="bi bi-<?= $roleInfo['icon'] ?> fs-4 mb-1"></i>
                                                <span class="small fw-semibold"><?= role_label($roleKey) ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Account Status</label>
                                <select class="form-select" name="status">
                                    <option value="active"    <?= $user['status'] === 'active'    ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive"  <?= $user['status'] === 'inactive'  ? 'selected' : '' ?>>Inactive</option>
                                    <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Optional Password Reset -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Reset Password
                                <span class="text-muted fw-normal small">(leave blank to keep current)</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="new_password"
                                       id="newPassword" placeholder="New password (min. 8 characters)"
                                       minlength="8">
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePwd('newPassword','eyeIcon')">
                                    <i class="bi bi-eye-fill" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Account Info (read-only) -->
                        <div class="bg-light rounded p-3 small text-muted mb-3">
                            <div class="row">
                                <div class="col-6"><strong>Email:</strong> <?= e($user['email']) ?></div>
                                <div class="col-6"><strong>Username:</strong> <?= e($user['username']) ?></div>
                                <div class="col-6 mt-1"><strong>Created:</strong> <?= format_datetime($user['created_at']) ?></div>
                                <div class="col-6 mt-1"><strong>Last Login:</strong> <?= $user['last_login'] ? format_datetime($user['last_login']) : 'Never' ?></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/admin/users') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i>Save Changes
                            </button>
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
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
}
</script>
