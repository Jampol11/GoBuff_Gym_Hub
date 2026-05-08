<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Fitness Plans</h2></div>
        <a href="<?= base_url('/trainers/fitness-plans/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-1"></i>Create Plan</a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr><th>#</th><th>Member</th><th>Plan Name</th><th>Goal</th><th>Trainer</th><th>Duration</th><th>Status</th><th>Created</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($plans)): ?>
                            <tr><td colspan="8" class="text-center py-5 text-muted">No fitness plans found</td></tr>
                        <?php else: ?>
                            <?php foreach ($plans as $i => $p): ?>
                                <tr>
                                    <td><?= $pagination['offset'] + $i + 1 ?></td>
                                    <td><?= e($p['member_name']) ?></td>
                                    <td class="fw-semibold"><?= e($p['plan_name']) ?></td>
                                    <td><?= e($p['goal']) ?></td>
                                    <td><?= e($p['trainer_name'] ?? 'N/A') ?></td>
                                    <td><?= e($p['duration_weeks']) ?> weeks</td>
                                    <td><?= status_badge($p['status']) ?></td>
                                    <td><?= format_date($p['created_at']) ?></td>
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
            <?= pagination_links($pagination, base_url('/trainers/fitness-plans')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
