<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Maintenance Reports</h2><p class="page-subtitle">Equipment maintenance tracking</p></div>
        <a href="<?= base_url('/maintenance/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-1"></i>Report Issue</a>
    </div>

    <!-- Pending Reports Alert -->
    <?php if (!empty($pending)): ?>
    <div class="alert alert-warning d-flex align-items-center mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div><strong><?= count($pending) ?> pending</strong> maintenance report(s) require attention.</div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr><th>#</th><th>Equipment</th><th>Issue Type</th><th>Priority</th><th>Reported By</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reports)): ?>
                            <tr><td colspan="8" class="text-center py-5 text-muted">No maintenance reports</td></tr>
                        <?php else: ?>
                            <?php foreach ($reports as $i => $r): ?>
                                <tr>
                                    <td><?= $pagination['offset'] + $i + 1 ?></td>
                                    <td class="fw-semibold"><?= e($r['equipment_name']) ?></td>
                                    <td><?= e($r['issue_type']) ?></td>
                                    <td><?= status_badge($r['priority'] ?? 'medium') ?></td>
                                    <td><?= e($r['reported_by_name'] ?? 'N/A') ?></td>
                                    <td><?= status_badge($r['status']) ?></td>
                                    <td><?= format_date($r['created_at']) ?></td>
                                    <td>
                                        <?php if (has_role(['gym_owner','admin','maintenance'])): ?>
                                        <div class="btn-group btn-group-sm">
                                            <?php if ($r['status'] === 'pending'): ?>
                                                <form method="POST" action="<?= base_url('/maintenance/' . $r['id'] . '/verify') ?>">
                                                    <?= csrf_field() ?>
                                                    <button class="btn btn-outline-info" title="Verify"><i class="bi bi-check-circle"></i></button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if (in_array($r['status'], ['pending','in_progress'])): ?>
                                                <form method="POST" action="<?= base_url('/maintenance/' . $r['id'] . '/complete') ?>">
                                                    <?= csrf_field() ?>
                                                    <button class="btn btn-outline-success" title="Complete"><i class="bi bi-check-lg"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
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
            <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?></small>
            <?= pagination_links($pagination, base_url('/maintenance')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
