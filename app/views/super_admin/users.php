<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-people-fill me-2 text-primary"></i>System Users
            </h1>
            <p class="text-muted mb-0">View and manage all users across the platform.</p>
        </div>
        <a href="<?= base_url('/admin/users/create') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i>Create User
        </a>
    </div>

    <!-- Role Summary -->
    <div class="row g-2 mb-4">
        <?php foreach ($usersByRole as $row): ?>
        <div class="col-auto">
            <a href="<?= base_url('/super-admin/users?role=' . $row['role']) ?>"
               class="btn btn-sm <?= $roleFilter === $row['role'] ? 'btn-dark' : 'btn-outline-secondary' ?>">
                <?= role_label($row['role']) ?>
                <span class="badge bg-secondary ms-1"><?= (int)$row['count'] ?></span>
            </a>
        </div>
        <?php endforeach; ?>
        <?php if ($roleFilter): ?>
        <div class="col-auto">
            <a href="<?= base_url('/super-admin/users') ?>" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-x-lg"></i> Clear Filter
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" class="d-flex gap-2">
                <?php if ($roleFilter): ?>
                    <input type="hidden" name="role" value="<?= e($roleFilter) ?>">
                <?php endif; ?>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search by name, email, or username..."
                       value="<?= e($search) ?>">
                <button type="submit" class="btn btn-sm btn-primary">Search</button>
                <?php if ($search): ?>
                    <a href="<?= base_url('/super-admin/users' . ($roleFilter ? '?role=' . $roleFilter : '')) ?>"
                       class="btn btn-sm btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($users)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-people fs-2 d-block mb-2"></i>
                    No users found.
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="fw-medium"><?= e($u['name']) ?></td>
                            <td class="text-muted small"><?= e($u['email']) ?></td>
                            <td class="text-muted small"><?= e($u['username']) ?></td>
                            <td><?= role_badge($u['role']) ?></td>
                            <td>
                                <span class="badge bg-<?= $u['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($u['status']) ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <a href="<?= base_url('/admin/users/' . $u['id'] . '/edit') ?>"
                                   class="btn btn-sm btn-outline-secondary me-1">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <?php if ($u['id'] !== auth_id()): ?>
                                <form method="POST"
                                      action="<?= base_url('/super-admin/users/' . $u['id'] . '/toggle-status') ?>"
                                      class="d-inline"
                                      onsubmit="return confirm('Toggle status for <?= e(addslashes($u['name'])) ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                            class="btn btn-sm btn-<?= $u['status'] === 'active' ? 'outline-warning' : 'outline-success' ?>">
                                        <i class="bi bi-<?= $u['status'] === 'active' ? 'pause-circle' : 'play-circle' ?>"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer bg-transparent">
            <?php include VIEWS_PATH . '/layouts/pagination.php'; ?>
        </div>
        <?php endif; ?>
    </div>

</div>
