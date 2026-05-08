<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-wallet2 me-2"></i>Budget Plans</h2>
            <p class="page-subtitle">Plan and track gym financial budgets by period</p>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('/owner/budgets/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>New Budget Plan
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Fiscal Year</th>
                            <th>Period</th>
                            <th>Total Budget</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($plans)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-wallet2 fs-1 d-block mb-2"></i>
                                    No budget plans yet
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($plans as $i => $plan): ?>
                            <tr>
                                <td><?= $pagination['offset'] + $i + 1 ?></td>
                                <td>
                                    <a href="<?= base_url('/owner/budgets/' . $plan['id']) ?>" class="fw-semibold text-decoration-none">
                                        <?= e($plan['title']) ?>
                                    </a>
                                    <?php if ($plan['period_label']): ?>
                                        <br><small class="text-muted"><?= e($plan['period_label']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($plan['fiscal_year']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= ucfirst(str_replace('_', '-', $plan['period'])) ?></span></td>
                                <td class="fw-semibold text-success">₱<?= number_format($plan['total_budget'], 2) ?></td>
                                <td>
                                    <?php
                                    $badge = match($plan['status']) {
                                        'approved' => 'success',
                                        'active'   => 'primary',
                                        'closed'   => 'secondary',
                                        default    => 'warning text-dark',
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($plan['status']) ?></span>
                                </td>
                                <td><?= e($plan['creator_name'] ?? '—') ?></td>
                                <td><small class="text-muted"><?= format_date($plan['created_at']) ?></small></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('/owner/budgets/' . $plan['id']) ?>"
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('/owner/budgets/' . $plan['id'] . '/edit') ?>"
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="<?= base_url('/owner/budgets/' . $plan['id'] . '/delete') ?>"
                                              class="d-inline" onsubmit="return confirm('Delete this budget plan?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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
                of <?= $pagination['total'] ?> plans
            </small>
            <?= pagination_links($pagination, base_url('/owner/budgets')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
