<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">Member Profile</h2>
            <p class="page-subtitle"><?= e($member['first_name'] . ' ' . $member['last_name']) ?></p>
        </div>
        <div class="page-actions">
            <?php if (has_role(['gym_owner','admin'])): ?>
            <a href="<?= base_url('/members/' . $member['id'] . '/edit') ?>" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <?php endif; ?>
            <a href="<?= base_url('/members') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="card text-center">
                <div class="card-body py-4">
                    <div class="member-avatar mx-auto mb-3">
                        <?php if (!empty($member['photo'])): ?>
                            <img src="<?= asset('uploads/members/' . $member['photo']) ?>"
                                 class="rounded-circle" width="100" height="100" style="object-fit:cover">
                        <?php else: ?>
                            <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto fs-1 fw-bold">
                                <?= strtoupper(substr($member['first_name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h5 class="fw-bold"><?= e($member['first_name'] . ' ' . $member['last_name']) ?></h5>
                    <p class="text-muted mb-2"><?= e($member['email'] ?? '') ?></p>
                    <code class="fs-6"><?= e($member['membership_id']) ?></code>
                    <div class="mt-3">
                        <?= status_badge($member['status'] ?? 'active') ?>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="fw-bold text-primary"><?= count($checkins) ?></div>
                            <small class="text-muted">Check-ins</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-success"><?= count($memberships) ?></div>
                            <small class="text-muted">Plans</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-info"><?= count($bookings) ?></div>
                            <small class="text-muted">Bookings</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Info -->
            <div class="card mt-4">
                <div class="card-header"><h6 class="mb-0 fw-semibold">Personal Information</h6></div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">Phone</dt>
                        <dd class="col-7"><?= e($member['phone'] ?? 'N/A') ?></dd>
                        <dt class="col-5 text-muted">Gender</dt>
                        <dd class="col-7"><?= ucfirst(e($member['gender'] ?? 'N/A')) ?></dd>
                        <dt class="col-5 text-muted">Birthday</dt>
                        <dd class="col-7"><?= format_date($member['date_of_birth']) ?></dd>
                        <dt class="col-5 text-muted">Address</dt>
                        <dd class="col-7"><?= e($member['address'] ?? 'N/A') ?></dd>
                        <dt class="col-5 text-muted">Emergency</dt>
                        <dd class="col-7"><?= e($member['emergency_contact'] ?? 'N/A') ?></dd>
                        <dt class="col-5 text-muted">Joined</dt>
                        <dd class="col-7"><?= format_date($member['created_at']) ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="memberTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#memberships-tab">
                                <i class="bi bi-card-checklist me-1"></i>Memberships
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#checkins-tab">
                                <i class="bi bi-door-open me-1"></i>Check-Ins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#bookings-tab">
                                <i class="bi bi-calendar-check me-1"></i>Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#fitness-tab">
                                <i class="bi bi-clipboard2-pulse me-1"></i>Fitness Plans
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content">
                    <!-- Memberships Tab -->
                    <div class="tab-pane fade show active" id="memberships-tab">
                        <?php if (empty($memberships)): ?>
                            <p class="text-muted text-center py-3">No memberships found.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead><tr><th>Plan</th><th>Start</th><th>Expiry</th><th>Amount</th><th>Status</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($memberships as $ms): ?>
                                            <tr>
                                                <td><?= e($ms['plan_name']) ?></td>
                                                <td><?= format_date($ms['start_date']) ?></td>
                                                <td><?= format_date($ms['expiry_date']) ?></td>
                                                <td><?= format_currency($ms['amount']) ?></td>
                                                <td><?= status_badge($ms['status']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Check-Ins Tab -->
                    <div class="tab-pane fade" id="checkins-tab">
                        <?php if (empty($checkins)): ?>
                            <p class="text-muted text-center py-3">No check-ins recorded.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead><tr><th>Date</th><th>Time In</th><th>Time Out</th><th>Method</th><th>Status</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($checkins as $c): ?>
                                            <tr>
                                                <td><?= format_date($c['check_in_time']) ?></td>
                                                <td><?= date('h:i A', strtotime($c['check_in_time'])) ?></td>
                                                <td><?= $c['check_out_time'] ? date('h:i A', strtotime($c['check_out_time'])) : '—' ?></td>
                                                <td><?= ucfirst(e($c['method'])) ?></td>
                                                <td><?= status_badge($c['status']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Bookings Tab -->
                    <div class="tab-pane fade" id="bookings-tab">
                        <?php if (empty($bookings)): ?>
                            <p class="text-muted text-center py-3">No bookings found.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead><tr><th>Trainer</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($bookings as $b): ?>
                                            <tr>
                                                <td><?= e($b['trainer_name']) ?></td>
                                                <td><?= format_date($b['booking_date']) ?></td>
                                                <td><?= e($b['booking_time']) ?></td>
                                                <td><?= status_badge($b['status']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Fitness Plans Tab -->
                    <div class="tab-pane fade" id="fitness-tab">
                        <?php if (empty($fitness_plans)): ?>
                            <p class="text-muted text-center py-3">No fitness plans assigned.</p>
                        <?php else: ?>
                            <?php foreach ($fitness_plans as $fp): ?>
                                <div class="card mb-3 border-start border-primary border-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="fw-semibold"><?= e($fp['plan_name']) ?></h6>
                                            <?= status_badge($fp['status']) ?>
                                        </div>
                                        <p class="text-muted small mb-1"><strong>Goal:</strong> <?= e($fp['goal']) ?></p>
                                        <p class="text-muted small mb-1"><strong>Trainer:</strong> <?= e($fp['trainer_name'] ?? 'N/A') ?></p>
                                        <p class="text-muted small mb-0"><strong>Duration:</strong> <?= e($fp['duration_weeks']) ?> weeks</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
