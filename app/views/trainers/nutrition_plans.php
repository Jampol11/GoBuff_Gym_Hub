<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Nutrition Plans</h2></div>
        <a href="<?= base_url('/trainers/nutrition-plans/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-1"></i>Create Plan</a>
    </div>
    <div class="row g-4">
        <?php if (empty($plans)): ?>
            <div class="col-12 text-center py-5 text-muted"><i class="bi bi-egg-fried fs-1 d-block mb-2"></i>No nutrition plans found</div>
        <?php else: ?>
            <?php foreach ($plans as $p): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-start border-success border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="fw-bold"><?= e($p['plan_name']) ?></h6>
                                <?= status_badge($p['status']) ?>
                            </div>
                            <div class="row g-2 text-center mb-3">
                                <div class="col-3">
                                    <div class="fw-bold text-danger"><?= e($p['daily_calories']) ?></div>
                                    <small class="text-muted">kcal</small>
                                </div>
                                <div class="col-3">
                                    <div class="fw-bold text-primary"><?= e($p['protein_grams']) ?>g</div>
                                    <small class="text-muted">Protein</small>
                                </div>
                                <div class="col-3">
                                    <div class="fw-bold text-warning"><?= e($p['carbs_grams']) ?>g</div>
                                    <small class="text-muted">Carbs</small>
                                </div>
                                <div class="col-3">
                                    <div class="fw-bold text-info"><?= e($p['fat_grams']) ?>g</div>
                                    <small class="text-muted">Fat</small>
                                </div>
                            </div>
                            <?php if (!empty($p['meal_plan'])): ?>
                                <p class="text-muted small mb-0"><?= e(substr($p['meal_plan'], 0, 100)) ?>...</p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer text-muted small">
                            Created: <?= format_date($p['created_at']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
