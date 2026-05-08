<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">New Campaign</h2></div>
        <a href="<?= base_url('/campaigns') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white"><h6 class="mb-0">Campaign Details</h6></div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/campaigns') ?>" method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Campaign Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Target Audience</label>
                                <select class="form-select" name="target_audience">
                                    <option value="All Members">All Members</option>
                                    <option value="New Members">New Members</option>
                                    <option value="Existing Members">Existing Members</option>
                                    <option value="Expired Members">Expired Members</option>
                                    <option value="General Public">General Public</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" name="status">
                                    <option value="scheduled">Scheduled</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="end_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Budget (₱)</label>
                                <input type="number" class="form-control" name="budget" min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Discount (%)</label>
                                <input type="number" class="form-control" name="discount_pct" min="0" max="100" step="0.1">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Banner Image</label>
                                <input type="file" class="form-control" name="banner" accept="image/*">
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/campaigns') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-megaphone me-1"></i>Create Campaign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
