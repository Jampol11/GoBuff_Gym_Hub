<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title"><?= e($campaign['title']) ?></h2></div>
        <div class="page-actions">
            <a href="<?= base_url('/campaigns/' . $campaign['id'] . '/edit') ?>" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
            <a href="<?= base_url('/campaigns') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <?php if (!empty($campaign['banner_image'])): ?>
                    <img src="<?= asset('uploads/campaigns/' . $campaign['banner_image']) ?>" class="card-img-top" style="max-height:250px;object-fit:cover">
                <?php endif; ?>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="fw-bold"><?= e($campaign['title']) ?></h5>
                        <?= status_badge($campaign['status']) ?>
                    </div>
                    <p><?= e($campaign['description'] ?? '') ?></p>
                    <dl class="row">
                        <dt class="col-5">Target Audience</dt><dd class="col-7"><?= e($campaign['target_audience'] ?? 'All') ?></dd>
                        <dt class="col-5">Start Date</dt><dd class="col-7"><?= format_date($campaign['start_date']) ?></dd>
                        <dt class="col-5">End Date</dt><dd class="col-7"><?= format_date($campaign['end_date']) ?></dd>
                        <dt class="col-5">Budget</dt><dd class="col-7"><?= format_currency($campaign['budget'] ?? 0) ?></dd>
                        <dt class="col-5">Discount</dt><dd class="col-7"><?= $campaign['discount_pct'] > 0 ? $campaign['discount_pct'] . '%' : 'None' ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
