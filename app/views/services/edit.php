<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Edit Service</h2></div>
        <a href="<?= base_url('/owner/services') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-pencil-fill me-2"></i>Edit Service Details</h6>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/owner/services/' . $service['id'] . '/update') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Service / Rate Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required
                                       value="<?= e($service['name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category</label>
                                <select class="form-select" name="category">
                                    <?php foreach (['membership','class','personal_training','amenity','other'] as $cat): ?>
                                    <option value="<?= $cat ?>" <?= $service['category'] === $cat ? 'selected' : '' ?>>
                                        <?= ucwords(str_replace('_', ' ', $cat)) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Price (₱)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" name="price" min="0" step="0.01"
                                           value="<?= e($service['price']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Duration / Period</label>
                                <input type="text" class="form-control" name="duration"
                                       value="<?= e($service['duration'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                           <?= $service['is_active'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="isActive">Active</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" name="description" rows="3"><?= e($service['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Internal Notes</label>
                                <textarea class="form-control" name="notes" rows="2"><?= e($service['notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/owner/services') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
