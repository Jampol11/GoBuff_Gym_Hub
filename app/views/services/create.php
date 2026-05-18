<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Add Service / Rate</h2></div>
        <a href="<?= base_url('/owner/services') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-tags-fill me-2"></i>Service Details</h6>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/owner/services') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Service / Rate Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required
                                       placeholder="e.g. Monthly Membership, Group Yoga Class, Personal Training Session">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select class="form-select" name="category" required>
                                    <option value="membership">Membership</option>
                                    <option value="class">Group Class</option>
                                    <option value="personal_training">Personal Training</option>
                                    <option value="amenity">Amenity / Add-on</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Price (₱) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" name="price" min="0" step="0.01" required placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Duration / Period</label>
                                <input type="text" class="form-control" name="duration"
                                       placeholder="e.g. per month, per session, 1 year">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" name="description" rows="3"
                                          placeholder="What's included in this service?"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Internal Notes</label>
                                <textarea class="form-control" name="notes" rows="2"
                                          placeholder="Notes for internal reference (not shown publicly)"></textarea>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/owner/services') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Add Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
