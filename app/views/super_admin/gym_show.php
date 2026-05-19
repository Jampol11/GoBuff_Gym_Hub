<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= base_url('/super-admin/gyms') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h1 class="h3 fw-bold mb-0">
            <i class="bi bi-building me-2 text-dark"></i><?= e($gym['name']) ?>
        </h1>
        <span class="badge bg-<?= $gym['status'] === 'active' ? 'success' : 'secondary' ?> fs-6">
            <?= ucfirst($gym['status']) ?>
        </span>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <strong>Gym Information</strong>
                    <a href="<?= base_url('/super-admin/gyms/' . $gym['id'] . '/edit') ?>"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil-fill me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name</dt>
                        <dd class="col-sm-8"><?= e($gym['name']) ?></dd>

                        <dt class="col-sm-4">Address</dt>
                        <dd class="col-sm-8"><?= e($gym['address'] ?? '—') ?></dd>

                        <dt class="col-sm-4">Contact</dt>
                        <dd class="col-sm-8"><?= e($gym['contact'] ?? '—') ?></dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8"><?= e($gym['email'] ?? '—') ?></dd>

                        <dt class="col-sm-4">Description</dt>
                        <dd class="col-sm-8"><?= e($gym['description'] ?? '—') ?></dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-<?= $gym['status'] === 'active' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($gym['status']) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-4">Created</dt>
                        <dd class="col-sm-8"><?= date('F d, Y', strtotime($gym['created_at'])) ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <strong><i class="bi bi-person-badge-fill text-danger me-1"></i>Assigned Gym Owner</strong>
                </div>
                <div class="card-body">
                    <?php if ($gym['owner_name']): ?>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Name</dt>
                            <dd class="col-sm-8"><?= e($gym['owner_name']) ?></dd>

                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8"><?= e($gym['owner_email']) ?></dd>
                        </dl>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                            No Gym Owner assigned.
                            <br>
                            <a href="<?= base_url('/super-admin/gyms/' . $gym['id'] . '/edit') ?>"
                               class="btn btn-sm btn-outline-danger mt-2">Assign Owner</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
