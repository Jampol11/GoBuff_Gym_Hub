<div class="container-fluid px-4 py-3">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-building-fill-gear me-2 text-warning"></i>Gym Owner Applications
            </h1>
            <p class="text-muted mb-0">Review and approve or reject Gym Owner registration requests.</p>
        </div>
        <?php if ($pendingCount > 0): ?>
        <span class="badge bg-danger fs-6 px-3 py-2">
            <i class="bi bi-hourglass-split me-1"></i><?= $pendingCount ?> Pending
        </span>
        <?php endif; ?>
    </div>

    <!-- Status Filter Tabs -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link <?= $statusFilter === '' ? 'active' : '' ?>"
                       href="<?= base_url('/super-admin/owner-applications') ?>">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $statusFilter === 'pending' ? 'active' : '' ?>"
                       href="<?= base_url('/super-admin/owner-applications?status=pending') ?>">
                        Pending
                        <?php if ($pendingCount > 0): ?>
                            <span class="badge bg-danger ms-1"><?= $pendingCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $statusFilter === 'approved' ? 'active' : '' ?>"
                       href="<?= base_url('/super-admin/owner-applications?status=approved') ?>">Approved</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $statusFilter === 'rejected' ? 'active' : '' ?>"
                       href="<?= base_url('/super-admin/owner-applications?status=rejected') ?>">Rejected</a>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <?php if (empty($applications)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    No applications found.
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Applicant</th>
                            <th>Business Name</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td class="text-muted small"><?= (int)$app['id'] ?></td>
                            <td>
                                <div class="fw-medium"><?= e($app['user_name']) ?></div>
                                <div class="text-muted small"><?= e($app['user_email']) ?></div>
                            </td>
                            <td><?= e($app['business_name']) ?></td>
                            <td class="text-muted small"><?= e($app['contact_number']) ?></td>
                            <td>
                                <?php
                                $badge = match($app['status']) {
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    default    => 'warning text-dark',
                                };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst($app['status']) ?></span>
                            </td>
                            <td class="text-muted small"><?= date('M d, Y', strtotime($app['created_at'])) ?></td>
                            <td>
                                <a href="<?= base_url('/super-admin/owner-applications/' . $app['id']) ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye-fill"></i> Review
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
