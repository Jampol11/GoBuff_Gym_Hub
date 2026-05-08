<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">Memberships</h2>
            <p class="page-subtitle">Manage membership plans and approvals</p>
        </div>
        <a href="<?= base_url('/memberships/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill me-1"></i>New Membership
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th><th>Member</th><th>Plan</th><th>Start</th>
                            <th>Expiry</th><th>Amount</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($memberships)): ?>
                            <tr><td colspan="8" class="text-center py-5 text-muted">No memberships found</td></tr>
                        <?php else: ?>
                            <?php foreach ($memberships as $i => $ms): ?>
                                <tr>
                                    <td><?= $pagination['offset'] + $i + 1 ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= e($ms['member_name']) ?></div>
                                        <small class="text-muted"><?= e($ms['member_code']) ?></small>
                                    </td>
                                    <td><?= e($ms['plan_name']) ?><br><small class="text-muted"><?= e($ms['plan_type'] ?? '') ?></small></td>
                                    <td><?= format_date($ms['start_date']) ?></td>
                                    <td>
                                        <?= format_date($ms['expiry_date']) ?>
                                        <?php if ($ms['status'] === 'active' && strtotime($ms['expiry_date']) < strtotime('+7 days')): ?>
                                            <span class="badge bg-warning text-dark ms-1">Expiring</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= format_currency($ms['amount']) ?></td>
                                    <td><?= status_badge($ms['status']) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('/memberships/' . $ms['id']) ?>" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                            <?php if ($ms['status'] === 'pending'): ?>
                                                <form method="POST" action="<?= base_url('/memberships/' . $ms['id'] . '/approve') ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button class="btn btn-outline-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                                                </form>
                                                <form method="POST" action="<?= base_url('/memberships/' . $ms['id'] . '/reject') ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button class="btn btn-outline-danger" title="Reject"><i class="bi bi-x-lg"></i></button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" action="<?= base_url('/memberships/' . $ms['id'] . '/delete') ?>"
                                                  class="d-inline" onsubmit="return confirm('Delete this membership?')">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
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
            <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?></small>
            <?= pagination_links($pagination, base_url('/memberships')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
