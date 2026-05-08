<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">User Management</h2>
            <p class="page-subtitle">Create and manage all system user accounts and roles</p>
        </div>
        <a href="<?= base_url('/admin/users/create') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i>Create User Account
        </a>
    </div>

    <!-- Role Legend -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted small fw-semibold me-1">Roles:</span>
                <?php
                $roles = ['gym_owner','admin','marketing','trainer','maintenance','member'];
                foreach ($roles as $r): ?>
                    <?= role_badge($r) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="<?= base_url('/admin/users') ?>" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Search by name, email or username..."
                               value="<?= e($search) ?>">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search): ?>
                        <a href="<?= base_url('/admin/users') ?>" class="btn btn-outline-secondary">Clear</a>
                    <?php endif; ?>
                </div>
                <div class="col-auto ms-auto">
                    <span class="text-muted small"><?= number_format($pagination['total']) ?> user(s)</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>No users found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $i => $u): ?>
                                <tr <?= $u['id'] == auth_id() ? 'class="table-primary"' : '' ?>>
                                    <td><?= $pagination['offset'] + $i + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm bg-<?= $u['role'] === 'gym_owner' ? 'danger' : ($u['role'] === 'admin' ? 'primary' : 'secondary') ?> text-white rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                                <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?= e($u['name']) ?></div>
                                                <?php if ($u['id'] == auth_id()): ?>
                                                    <small class="text-primary">(You)</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= e($u['email']) ?></td>
                                    <td><code><?= e($u['username']) ?></code></td>
                                    <td><?= role_badge($u['role']) ?></td>
                                    <td><?= status_badge($u['status']) ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?= $u['last_login'] ? format_datetime($u['last_login']) : 'Never' ?>
                                        </small>
                                    </td>
                                    <td><small><?= format_date($u['created_at']) ?></small></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('/admin/users/' . $u['id'] . '/edit') ?>"
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if ($u['id'] != auth_id() && has_role(['gym_owner'])): ?>
                                                <form method="POST"
                                                      action="<?= base_url('/admin/users/' . $u['id'] . '/delete') ?>"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Delete user <?= e($u['name']) ?>? This cannot be undone.')">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?= $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?>
                of <?= $pagination['total'] ?> users
            </small>
            <?= pagination_links($pagination, base_url('/admin/users')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
