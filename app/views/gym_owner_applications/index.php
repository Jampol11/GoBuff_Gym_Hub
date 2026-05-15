<?php
/**
 * Gym Owner Applications — Reviewer list
 */
?>
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                <i class="bi bi-building-fill-gear text-danger fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Gym Owner Applications</h4>
                <p class="text-muted mb-0 small">Review ownership credential submissions.</p>
            </div>
        </div>
        <?php if ($pendingCount > 0): ?>
        <span class="badge bg-danger fs-6 px-3 py-2">
            <i class="bi bi-hourglass-split me-1"></i><?= $pendingCount ?> Pending
        </span>
        <?php endif; ?>
    </div>

    <!-- Status Filter Tabs -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-0">
            <ul class="nav nav-tabs border-0">
                <li class="nav-item">
                    <a class="nav-link <?= $statusFilter === '' ? 'active' : '' ?>"
                       href="<?= base_url('/gym-owner-applications') ?>">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $statusFilter === 'pending' ? 'active' : '' ?>"
                       href="<?= base_url('/gym-owner-applications?status=pending') ?>">
                        Pending
                        <?php if ($pendingCount > 0): ?>
                        <span class="badge bg-danger ms-1"><?= $pendingCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $statusFilter === 'approved' ? 'active' : '' ?>"
                       href="<?= base_url('/gym-owner-applications?status=approved') ?>">Approved</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $statusFilter === 'rejected' ? 'active' : '' ?>"
                       href="<?= base_url('/gym-owner-applications?status=rejected') ?>">Rejected</a>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <?php if (empty($applications)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No applications found.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Applicant</th>
                            <th>Business Name</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Reviewed By</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td class="text-muted small"><?= $app['id'] ?></td>
                            <td>
                                <div class="fw-semibold"><?= e($app['user_name']) ?></div>
                                <div class="text-muted small"><?= e($app['user_email']) ?></div>
                            </td>
                            <td><?= e($app['business_name']) ?></td>
                            <td>
                                <?php
                                $badgeClass = match($app['status']) {
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    default    => 'warning',
                                };
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($app['status']) ?></span>
                            </td>
                            <td class="text-muted small"><?= date('M d, Y g:i A', strtotime($app['created_at'])) ?></td>
                            <td class="text-muted small"><?= e($app['reviewer_name'] ?? '—') ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('/gym-owner-applications/' . $app['id']) ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>Review
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="card-footer bg-transparent d-flex justify-content-end">
            <?= pagination_links($pagination, base_url('/gym-owner-applications')) ?>
        </div>
        <?php endif; ?>
    </div>

</div>
