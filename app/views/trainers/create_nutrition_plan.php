<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Create Nutrition Plan</h2></div>
        <a href="<?= base_url('/trainers/nutrition-plans') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white"><h6 class="mb-0">Nutrition Plan Details</h6></div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/trainers/nutrition-plans') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Member</label>
                                <select class="form-select" name="member_id" required>
                                    <option value="">Select member</option>
                                    <?php foreach ($members as $m): ?>
                                        <option value="<?= $m['id'] ?>"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Plan Name</label>
                                <input type="text" class="form-control" name="plan_name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Daily Calories</label>
                                <input type="number" class="form-control" name="daily_calories" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Protein (g)</label>
                                <input type="number" class="form-control" name="protein_grams" min="0" step="0.1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Carbs (g)</label>
                                <input type="number" class="form-control" name="carbs_grams" min="0" step="0.1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Fat (g)</label>
                                <input type="number" class="form-control" name="fat_grams" min="0" step="0.1">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Meal Plan</label>
                                <textarea class="form-control" name="meal_plan" rows="5" placeholder="Breakfast, Lunch, Dinner, Snacks..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea class="form-control" name="notes" rows="2"></textarea>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/trainers/nutrition-plans') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Create Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
