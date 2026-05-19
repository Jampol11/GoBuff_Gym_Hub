<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-person-badge-fill me-2 text-danger"></i>Gym Owner Accounts
            </h1>
            <p class="text-muted mb-0">Manage all Gym Owner accounts on the platform.</p>
        </div>
        <a href="<?= base_url('/super-admin/gym-owners/create') ?>" class="btn btn-danger">
            <i class="bi bi-plus-lg me-1"></i>Create Gym Owner
        </a>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search by name, email, or username..."
                       value="<?= e($search) ?>">
                <button type="submit" class="btn btn-sm btn-primary">Search</button>
                <?php if ($search): ?>
                    <a href="<?= base_url('/super-admin/gym-owners') ?>" class="btn btn-sm btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($owners)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-person-badge fs-2 d-block mb-2"></i>
                    No Gym Owner accounts found.
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($owners as $owner): ?>
                        <tr>
                            <td class="fw-medium"><?= e($owner['name']) ?></td>
                            <td class="text-muted small"><?= e($owner['email']) ?></td>
                            <td class="text-muted small"><?= e($owner['username']) ?></td>
                            <td>
                                <span class="badge bg-<?= $owner['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($owner['status']) ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= date('M d, Y', strtotime($owner['created_at'])) ?></td>
                            <td>
                                <form method="POST"
                                      action="<?= base_url('/super-admin/gym-owners/' . $owner['id'] . '/toggle-status') ?>"
                                      class="d-inline"
                                      onsubmit="return confirm('Toggle status for <?= e(addslashes($owner['name'])) ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                            class="btn btn-sm btn-<?= $owner['status'] === 'active' ? 'outline-warning' : 'outline-success' ?>">
                                        <i class="bi bi-<?= $owner['status'] === 'active' ? 'pause-circle' : 'play-circle' ?>"></i>
                                        <?= $owner['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
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
