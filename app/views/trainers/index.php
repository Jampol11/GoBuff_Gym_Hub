<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Fitness Trainers</h2><p class="page-subtitle">Our certified fitness professionals</p></div>
        <a href="<?= base_url('/bookings/create') ?>" class="btn btn-primary"><i class="bi bi-calendar-plus me-1"></i>Book a Trainer</a>
    </div>
    <div class="row g-4">
        <?php if (empty($trainers)): ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-person-badge fs-1 d-block mb-2"></i>No trainers found
            </div>
        <?php else: ?>
            <?php foreach ($trainers as $t): ?>
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <div class="card trainer-card h-100">
                        <div class="card-body text-center py-4">
                            <div class="trainer-avatar mx-auto mb-3">
                                <div class="avatar-lg bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto fs-2 fw-bold">
                                    <?= strtoupper(substr($t['first_name'], 0, 1)) ?>
                                </div>
                            </div>
                            <h6 class="fw-bold mb-1"><?= e($t['first_name'] . ' ' . $t['last_name']) ?></h6>
                            <p class="text-muted small mb-2"><?= e($t['specialization'] ?? 'General Fitness') ?></p>
                            <p class="text-muted small mb-3"><?= e($t['email'] ?? '') ?></p>
                            <?= status_badge($t['status'] ?? 'active') ?>
                        </div>
                        <div class="card-footer text-center">
                            <a href="<?= base_url('/bookings/create') ?>" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-calendar-plus me-1"></i>Book Session
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Quick Links -->
    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <a href="<?= base_url('/trainers/fitness-plans') ?>" class="card text-decoration-none hover-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-primary text-white"><i class="bi bi-clipboard2-pulse-fill"></i></div>
                    <div><h6 class="mb-0 fw-semibold">Fitness Plans</h6><small class="text-muted">View & manage plans</small></div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= base_url('/trainers/nutrition-plans') ?>" class="card text-decoration-none hover-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-success text-white"><i class="bi bi-egg-fried"></i></div>
                    <div><h6 class="mb-0 fw-semibold">Nutrition Plans</h6><small class="text-muted">Dietary guidance</small></div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= base_url('/trainers/progress') ?>" class="card text-decoration-none hover-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-info text-white"><i class="bi bi-graph-up-arrow"></i></div>
                    <div><h6 class="mb-0 fw-semibold">Progress Tracking</h6><small class="text-muted">Monitor member progress</small></div>
                </div>
            </a>
        </div>
    </div>
</div>
