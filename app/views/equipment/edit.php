<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Edit Equipment</h2></div>
        <a href="<?= base_url('/equipment/' . $equipment['id']) ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark"><h6 class="mb-0">Edit Equipment Details</h6></div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/equipment/' . $equipment['id'] . '/update') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Name</label>
                                <input type="text" class="form-control" name="name" required value="<?= e($equipment['name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Brand</label>
                                <input type="text" class="form-control" name="brand" value="<?= e($equipment['brand'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Model</label>
                                <input type="text" class="form-control" name="model" value="<?= e($equipment['model'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Location</label>
                                <input type="text" class="form-control" name="location" value="<?= e($equipment['location'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Condition Status</label>
                                <select class="form-select" name="condition_status">
                                    <option value="good" <?= $equipment['condition_status'] === 'good' ? 'selected' : '' ?>>Good</option>
                                    <option value="needs_repair" <?= $equipment['condition_status'] === 'needs_repair' ? 'selected' : '' ?>>Needs Repair</option>
                                    <option value="under_maintenance" <?= $equipment['condition_status'] === 'under_maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea class="form-control" name="notes" rows="2"><?= e($equipment['notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/equipment/' . $equipment['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
