<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Add Equipment</h2></div>
        <a href="<?= base_url('/equipment') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white"><h6 class="mb-0">Equipment Details</h6></div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/equipment') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Equipment Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select category</option>
                                    <option value="Cardio">Cardio</option>
                                    <option value="Strength">Strength</option>
                                    <option value="Free Weights">Free Weights</option>
                                    <option value="Flexibility">Flexibility</option>
                                    <option value="Functional">Functional</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Brand</label>
                                <input type="text" class="form-control" name="brand">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Model</label>
                                <input type="text" class="form-control" name="model">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Serial Number</label>
                                <input type="text" class="form-control" name="serial_number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Location</label>
                                <input type="text" class="form-control" name="location" placeholder="e.g. Main Floor, Room A">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Purchase Date</label>
                                <input type="date" class="form-control" name="purchase_date">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Purchase Price (₱)</label>
                                <input type="number" class="form-control" name="purchase_price" min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Condition Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="condition_status" required>
                                    <option value="good">Good</option>
                                    <option value="needs_repair">Needs Repair</option>
                                    <option value="under_maintenance">Under Maintenance</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea class="form-control" name="notes" rows="2"></textarea>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/equipment') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Add Equipment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
