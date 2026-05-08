<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Create Fitness Plan</h2></div>
        <a href="<?= base_url('/trainers/fitness-plans') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white"><h6 class="mb-0">Plan Details</h6></div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/trainers/fitness-plans') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
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
                                <input type="text" class="form-control" name="plan_name" required placeholder="e.g. Weight Loss Program">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Goal <span class="text-danger">*</span></label>
                                <select class="form-select" name="goal" required>
                                    <option value="">Select goal</option>
                                    <option value="Weight Loss">Weight Loss</option>
                                    <option value="Muscle Gain">Muscle Gain</option>
                                    <option value="Endurance">Endurance</option>
                                    <option value="Flexibility">Flexibility</option>
                                    <option value="General Fitness">General Fitness</option>
                                    <option value="Rehabilitation">Rehabilitation</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Duration (weeks)</label>
                                <input type="number" class="form-control" name="duration_weeks" value="4" min="1" max="52">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Frequency</label>
                                <input type="text" class="form-control" name="frequency" placeholder="e.g. 3x per week">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Exercises</label>
                                <textarea class="form-control" name="exercises" rows="4" placeholder="List exercises, sets, reps..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea class="form-control" name="notes" rows="2"></textarea>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/trainers/fitness-plans') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Create Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
