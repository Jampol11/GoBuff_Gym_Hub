<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Marketing Campaigns</h2><p class="page-subtitle">Promotions and membership advertisements</p></div>
        <a href="<?= base_url('/campaigns/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-1"></i>New Campaign</a>
    </div>

    <!-- Active Campaigns -->
    <?php if (!empty($active)): ?>
    <div class="row g-4 mb-4">
        <?php foreach ($active as $c): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card campaign-card border-success h-100">
                    <?php if (!empty($c['banner_image'])): ?>
                        <img src="<?= asset('uploads/campaigns/' . $c['banner_image']) ?>" class="card-img-top" style="height:150px;object-fit:cover">
                    <?php else: ?>
                        <div class="campaign-banner-placeholder bg-success text-white d-flex align-items-center justify-content-center" style="height:100px">
                            <i class="bi bi-megaphone-fill fs-1"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="fw-bold"><?= e($c['title']) ?></h6>
                            <?= status_badge($c['status']) ?>
                        </div>
                        <p class="text-muted small mb-2"><?= e(substr($c['description'] ?? '', 0, 100)) ?></p>
                        <?php if ($c['discount_pct'] > 0): ?>
                            <span class="badge bg-danger fs-6"><?= $c['discount_pct'] ?>% OFF</span>
                        <?php endif; ?>
                        <div class="mt-2 small text-muted">
                            <i class="bi bi-calendar3 me-1"></i><?= format_date($c['start_date']) ?> – <?= format_date($c['end_date']) ?>
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="<?= base_url('/campaigns/' . $c['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
                        <a href="<?= base_url('/campaigns/' . $c['id'] . '/edit') ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- All Campaigns Table -->
    <div class="card">
        <div class="card-header"><h6 class="mb-0 fw-semibold">All Campaigns</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr><th>#</th><th>Title</th><th>Target</th><th>Start</th><th>End</th><th>Budget</th><th>Discount</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($campaigns)): ?>
                            <tr><td colspan="9" class="text-center py-5 text-muted">No campaigns found</td></tr>
                        <?php else: ?>
                            <?php foreach ($campaigns as $i => $c): ?>
                                <tr>
                                    <td><?= $pagination['offset'] + $i + 1 ?></td>
                                    <td class="fw-semibold"><?= e($c['title']) ?></td>
                                    <td><?= e($c['target_audience'] ?? 'All') ?></td>
                                    <td><?= format_date($c['start_date']) ?></td>
                                    <td><?= format_date($c['end_date']) ?></td>
                                    <td><?= format_currency($c['budget'] ?? 0) ?></td>
                                    <td><?= $c['discount_pct'] > 0 ? $c['discount_pct'] . '%' : '—' ?></td>
                                    <td><?= status_badge($c['status']) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('/campaigns/' . $c['id']) ?>" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                            <a href="<?= base_url('/campaigns/' . $c['id'] . '/edit') ?>" class="btn btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                            <form method="POST" action="<?= base_url('/campaigns/' . $c['id'] . '/delete') ?>"
                                                  class="d-inline" onsubmit="return confirm('Delete this campaign?')">
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
            <?= pagination_links($pagination, base_url('/campaigns')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
