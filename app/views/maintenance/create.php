<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Report Maintenance Issue</h2></div>
        <a href="<?= base_url('/maintenance') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white"><h6 class="mb-0"><i class="bi bi-wrench me-2"></i>Maintenance Report</h6></div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/maintenance') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Equipment <span class="text-danger">*</span></label>
                                <select class="form-select" name="equipment_id" required>
                                    <option value="">Select equipment</option>
                                    <?php foreach ($equipment as $eq): ?>
                                        <option value="<?= $eq['id'] ?>"><?= e($eq['name']) ?> (<?= e($eq['condition_status']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Issue Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="issue_type" required>
                                    <option value="">Select issue type</option>
                                    <option value="Mechanical Failure">Mechanical Failure</option>
                                    <option value="Electrical Issue">Electrical Issue</option>
                                    <option value="Wear and Tear">Wear and Tear</option>
                                    <option value="Safety Hazard">Safety Hazard</option>
                                    <option value="Routine Maintenance">Routine Maintenance</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Priority</label>
                                <select class="form-select" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" rows="4" required
                                          placeholder="Describe the issue in detail..."></textarea>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/maintenance') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-danger"><i class="bi bi-send me-1"></i>Submit Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
