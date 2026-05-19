<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-building me-2 text-dark"></i>Gym Management
            </h1>
            <p class="text-muted mb-0">Create and manage gym branches on the platform.</p>
        </div>
        <a href="<?= base_url('/super-admin/gyms/create') ?>" class="btn btn-dark">
            <i class="bi bi-plus-lg me-1"></i>Add Gym
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold"><?= (int)$gymStats['total'] ?></div>
                <div class="text-muted small">Total Gyms</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success"><?= (int)$gymStats['active'] ?></div>
                <div class="text-muted small">Active</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-secondary"><?= (int)$gymStats['inactive'] ?></div>
                <div class="text-muted small">Inactive</div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($gyms)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-building fs-2 d-block mb-2"></i>
                    No gyms found. <a href="<?= base_url('/super-admin/gyms/create') ?>">Add the first gym.</a>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Gym Name</th>
                            <th>Address</th>
                            <th>Owner</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gyms as $gym): ?>
                        <tr>
                            <td class="text-muted small"><?= (int)$gym['id'] ?></td>
                            <td class="fw-medium"><?= e($gym['name']) ?></td>
                            <td class="text-muted small"><?= e($gym['address'] ?? '—') ?></td>
                            <td>
                                <?php if ($gym['owner_name']): ?>
                                    <span class="badge bg-danger"><?= e($gym['owner_name']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= e($gym['contact'] ?? '—') ?></td>
                            <td>
                                <span class="badge bg-<?= $gym['status'] === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($gym['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('/super-admin/gyms/' . $gym['id']) ?>"
                                   class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="<?= base_url('/super-admin/gyms/' . $gym['id'] . '/edit') ?>"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
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
