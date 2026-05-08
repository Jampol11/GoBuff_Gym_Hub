<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Edit Campaign</h2></div>
        <a href="<?= base_url('/campaigns/' . $campaign['id']) ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark"><h6 class="mb-0">Edit Campaign</h6></div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/campaigns/' . $campaign['id'] . '/update') ?>" method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Title</label>
                                <input type="text" class="form-control" name="title" required value="<?= e($campaign['title']) ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" name="description" rows="3"><?= e($campaign['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Target Audience</label>
                                <input type="text" class="form-control" name="target_audience" value="<?= e($campaign['target_audience'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" name="status">
                                    <?php foreach (['scheduled','active','inactive','completed'] as $s): ?>
                                        <option value="<?= $s ?>" <?= $campaign['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="<?= e($campaign['start_date']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date</label>
                                <input type="date" class="form-control" name="end_date" value="<?= e($campaign['end_date']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Budget (₱)</label>
                                <input type="number" class="form-control" name="budget" value="<?= e($campaign['budget'] ?? 0) ?>" min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Discount (%)</label>
                                <input type="number" class="form-control" name="discount_pct" value="<?= e($campaign['discount_pct'] ?? 0) ?>" min="0" max="100">
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/campaigns/' . $campaign['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
