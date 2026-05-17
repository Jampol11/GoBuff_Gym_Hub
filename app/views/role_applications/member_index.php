<?php
/**
 * Membership Applications — Admin Officer review list
 */
?>
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                <i class="bi bi-card-checklist text-primary fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">Membership Applications</h4>
                <p class="text-muted mb-0 small">Review and approve membership applications from gym enthusiasts.</p>
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
                       href="<?= base_url('/member-applications') ?>">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $statusFilter === 'pending' ? 'active' : '' ?>"
                       href="<?= base_url('/member-applications?status=pending') ?>">
                        Pending
                        <?php if ($pendingCount > 0): ?>
                        <span class="badge bg-danger ms-1"><?= $pendingCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $statusFilter === 'approved' ? 'active' : '' ?>"
                       href="<?= base_url('/member-applications?status=approved') ?>">Approved</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $statusFilter === 'rejected' ? 'active' : '' ?>"
                       href="<?= base_url('/member-applications?status=rejected') ?>">Rejected</a>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <?php if (empty($applications)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No membership applications found.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Applicant</th>
                            <th>Plan Preference</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Reviewed By</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <?php
                            $formData = [];
                            if (!empty($app['membership_form_data'])) {
                                $formData = json_decode($app['membership_form_data'], true) ?? [];
                            }
                            $planLabels = [
                                'monthly'     => 'Monthly',
                                'quarterly'   => 'Quarterly',
                                'semi_annual' => 'Semi-Annual',
                                'annual'      => 'Annual',
                            ];
                            $planLabel = $planLabels[$formData['plan_preference'] ?? ''] ?? '—';
                        ?>
                        <tr>
                            <td class="text-muted small"><?= $app['id'] ?></td>
                            <td>
                                <div class="fw-semibold"><?= e($app['user_name']) ?></div>
                                <div class="text-muted small"><?= e($app['user_email']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                    <?= e($planLabel) ?>
                                </span>
                            </td>
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
                                <a href="<?= base_url('/member-applications/' . $app['id']) ?>"
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
            <?= pagination_links($pagination, base_url('/member-applications')) ?>
        </div>
        <?php endif; ?>
    </div>

</div>
