<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">New Membership</h2></div>
        <a href="<?= base_url('/memberships') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white"><h6 class="mb-0">Membership Details</h6></div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/memberships') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Member <span class="text-danger">*</span></label>
                                <select class="form-select" name="member_id" required>
                                    <option value="">Select member</option>
                                    <?php foreach ($members as $m): ?>
                                        <option value="<?= $m['id'] ?>"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Plan Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="plan_name" required placeholder="e.g. Monthly Basic">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Plan Type</label>
                                <select class="form-select" name="plan_type">
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="semi_annual">Semi-Annual</option>
                                    <option value="annual">Annual</option>
                                    <option value="daily">Daily</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" required value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="expiry_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Amount (₱) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="amount" required min="0" step="0.01">
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/memberships') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Create Membership</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
