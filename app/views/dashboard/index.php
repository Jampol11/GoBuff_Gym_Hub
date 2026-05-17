<div class="container-fluid py-4">

    <?php if (has_role(['user'])): ?>
    <!-- ── User: No Role Yet ─────────────────────────────────────────── -->
    <?php
    // Check if user has an approved member application awaiting payment
    $raCheck = new RoleApplication();
    $approvedMemberApp = $raCheck->query(
        "SELECT id FROM role_applications WHERE user_id = ? AND requested_role = 'member' AND status = 'approved' LIMIT 1",
        [auth_id()]
    )->fetch();
    ?>
    <?php if ($approvedMemberApp): ?>
    <div class="alert alert-success alert-dismissible d-flex align-items-start gap-3 mb-4 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill fs-4 mt-1 text-success"></i>
        <div class="flex-grow-1">
            <strong>Membership Application Approved!</strong>
            Your application has been approved by the Administrative Office.
            Please complete your payment to activate your membership and access all gym features.
        </div>
        <a href="<?= base_url('/my-membership') ?>" class="btn btn-success btn-sm text-nowrap">
            <i class="bi bi-credit-card-2-front me-1"></i>Pay Now
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php else: ?>
    <div class="alert alert-warning alert-dismissible d-flex align-items-start gap-3 mb-4 shadow-sm" role="alert">
        <i class="bi bi-person-badge-fill fs-4 mt-1 text-warning"></i>
        <div class="flex-grow-1">
            <strong>Welcome to GoBuff!</strong> Your account is active but you don't have a role yet.
            To access gym features, please apply for a role.
        </div>
        <a href="<?= base_url('/role-application/apply') ?>" class="btn btn-warning btn-sm text-nowrap">
            <i class="bi bi-send me-1"></i>Apply for a Role
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">Dashboard</h2>
            <p class="page-subtitle">Welcome back, <?= e(auth_user()['name'] ?? 'User') ?>! Here's what's happening today.</p>
        </div>
        <div class="page-actions">
            <span class="badge bg-light text-dark border">
                <i class="bi bi-calendar3 me-1"></i><?= date('l, F j, Y') ?>
            </span>
        </div>
    </div>

    <?php if (has_role(['gym_owner', 'admin'])): ?>
    <!-- ══════════════════════════════════════════════════════════════════
         GYM OWNER & ADMIN DASHBOARD
    ══════════════════════════════════════════════════════════════════ -->

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= number_format($total_members) ?></div>
                    <div class="stat-label">Total Members</div>
                </div>
                <a href="<?= base_url('/members') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-success">
                <div class="stat-icon"><i class="bi bi-card-checklist"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= number_format($active_memberships) ?></div>
                    <div class="stat-label">Active Memberships</div>
                </div>
                <a href="<?= base_url('/memberships') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="bi bi-door-open-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= number_format($today_checkins) ?></div>
                    <div class="stat-label">Today's Check-Ins</div>
                </div>
                <a href="<?= base_url('/checkins') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= format_currency($monthly_revenue) ?></div>
                    <div class="stat-label">Monthly Revenue</div>
                </div>
                <span class="stat-link text-muted">Total: <?= format_currency($total_revenue) ?></span>
            </div>
        </div>
    </div>

    <!-- Pending Memberships Alert -->
    <?php if (($pending_memberships ?? 0) > 0): ?>
    <div class="alert alert-info d-flex align-items-center gap-3 mb-4">
        <i class="bi bi-hourglass-split fs-5"></i>
        <div class="flex-grow-1">
            <strong><?= $pending_memberships ?> membership application<?= $pending_memberships > 1 ? 's' : '' ?></strong> awaiting verification.
        </div>
        <a href="<?= base_url('/memberships') ?>" class="btn btn-sm btn-info text-white">Review Now</a>
    </div>
    <?php endif; ?>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Weekly Check-Ins</h6>
                    <a href="<?= base_url('/checkins/stats') ?>" class="btn btn-sm btn-outline-primary">Full Stats</a>
                </div>
                <div class="card-body">
                    <canvas id="checkinChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-tools me-2 text-warning"></i>Equipment Status</h6>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <canvas id="equipmentChart" height="180"></canvas>
                    <div class="mt-3 w-100">
                        <?php foreach ($equipment_status as $es): ?>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small"><?= ucwords(str_replace('_', ' ', $es['condition_status'])) ?></span>
                            <span class="badge bg-<?= $es['condition_status'] === 'good' ? 'success' : ($es['condition_status'] === 'needs_repair' ? 'warning' : 'info') ?>">
                                <?= $es['count'] ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Expiring Soon (7 days)</h6>
                    <span class="badge bg-warning text-dark"><?= count($expiring_soon) ?></span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($expiring_soon)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>No memberships expiring soon
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light"><tr><th>Member</th><th>Plan</th><th>Expires</th></tr></thead>
                            <tbody>
                                <?php foreach ($expiring_soon as $m): ?>
                                <tr>
                                    <td><?= e($m['member_name']) ?></td>
                                    <td><?= e($m['plan_name']) ?></td>
                                    <td><span class="badge bg-warning text-dark"><?= format_date($m['expiry_date']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-calendar-check-fill me-2 text-primary"></i>Upcoming Bookings</h6>
                    <a href="<?= base_url('/bookings') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($upcoming_bookings)): ?>
                    <div class="text-center py-4 text-muted"><i class="bi bi-calendar-x fs-3 d-block mb-2"></i>No upcoming bookings</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light"><tr><th>Member</th><th>Trainer</th><th>Date & Time</th></tr></thead>
                            <tbody>
                                <?php foreach ($upcoming_bookings as $b): ?>
                                <tr>
                                    <td><?= e($b['member_name']) ?></td>
                                    <td><?= e($b['trainer_name']) ?></td>
                                    <td><small><?= format_date($b['booking_date']) ?><br><span class="text-muted"><?= e($b['booking_time']) ?></span></small></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-person-plus-fill me-2 text-success"></i>Recent Members</h6>
                    <a href="<?= base_url('/members') ?>" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light"><tr><th>Name</th><th>ID</th><th>Joined</th></tr></thead>
                            <tbody>
                                <?php foreach ($recent_members as $m): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xs bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                                <?= strtoupper(substr($m['first_name'], 0, 1)) ?>
                                            </div>
                                            <?= e($m['first_name'] . ' ' . $m['last_name']) ?>
                                        </div>
                                    </td>
                                    <td><code><?= e($m['membership_id']) ?></code></td>
                                    <td><small><?= format_date($m['created_at']) ?></small></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-door-open-fill me-2 text-info"></i>Today's Check-Ins</h6>
                    <a href="<?= base_url('/checkins') ?>" class="btn btn-sm btn-outline-info">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recent_checkins)): ?>
                    <div class="text-center py-4 text-muted"><i class="bi bi-door-closed fs-3 d-block mb-2"></i>No check-ins today</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light"><tr><th>Member</th><th>Time In</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php foreach (array_slice($recent_checkins, 0, 5) as $c): ?>
                                <tr>
                                    <td><?= e($c['member_name']) ?></td>
                                    <td><small><?= date('h:i A', strtotime($c['check_in_time'])) ?></small></td>
                                    <td><?= status_badge($c['status']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    const checkinData = <?= json_encode($weekly_checkins) ?>;
    const ciLabels = checkinData.map(d => new Date(d.date).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }));
    const ciCounts = checkinData.map(d => parseInt(d.count));
    new Chart(document.getElementById('checkinChart'), {
        type: 'bar',
        data: {
            labels: ciLabels.length ? ciLabels : ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
            datasets: [{ label: 'Check-Ins', data: ciCounts.length ? ciCounts : [0,0,0,0,0,0,0],
                backgroundColor: 'rgba(13,110,253,0.7)', borderColor: 'rgba(13,110,253,1)',
                borderWidth: 2, borderRadius: 6 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
    const eqData   = <?= json_encode($equipment_status) ?>;
    const eqLabels = eqData.map(d => d.condition_status.replace(/_/g,' ').replace(/\b\w/g, l => l.toUpperCase()));
    const eqCounts = eqData.map(d => parseInt(d.count));
    const eqColors = eqData.map(d => d.condition_status === 'good' ? '#198754' : d.condition_status === 'needs_repair' ? '#ffc107' : '#0dcaf0');
    if (eqData.length > 0) {
        new Chart(document.getElementById('equipmentChart'), {
            type: 'doughnut',
            data: { labels: eqLabels, datasets: [{ data: eqCounts, backgroundColor: eqColors, borderWidth: 2 }] },
            options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
        });
    }
    </script>
    <?php endif; ?>

    <?php if (has_role(['trainer'])): ?>
    <!-- ══════════════════════════════════════════════════════════════════
         TRAINER DASHBOARD
    ══════════════════════════════════════════════════════════════════ -->

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= count($assigned_members) ?></div>
                    <div class="stat-label">Assigned Members</div>
                </div>
                <a href="<?= base_url('/members') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-success">
                <div class="stat-icon"><i class="bi bi-clipboard2-pulse-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= $active_fitness_plans ?></div>
                    <div class="stat-label">Active Fitness Plans</div>
                </div>
                <a href="<?= base_url('/trainers/fitness-plans') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="bi bi-egg-fried"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= $active_nutrition_plans ?></div>
                    <div class="stat-label">Active Nutrition Plans</div>
                </div>
                <a href="<?= base_url('/trainers/nutrition-plans') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= count($today_bookings) ?></div>
                    <div class="stat-label">Today's Sessions</div>
                </div>
                <a href="<?= base_url('/bookings') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Today's Sessions -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-calendar-day me-2 text-warning"></i>Today's Sessions</h6>
                    <span class="badge bg-warning text-dark"><?= count($today_bookings) ?></span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($today_bookings)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>No sessions scheduled for today
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($today_bookings as $b): ?>
                        <div class="list-group-item d-flex align-items-center gap-3 py-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 flex-shrink-0">
                                <i class="bi bi-person-fill text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?= e($b['member_name']) ?></div>
                                <div class="text-muted small"><?= date('h:i A', strtotime($b['booking_time'])) ?> &bull; <?= $b['duration'] ?> min</div>
                            </div>
                            <?= status_badge($b['status']) ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Upcoming Bookings -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-calendar-week me-2 text-primary"></i>Upcoming Sessions</h6>
                    <a href="<?= base_url('/bookings') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($upcoming_bookings)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-check fs-2 d-block mb-2"></i>No upcoming sessions
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Member</th><th>Date</th><th>Time</th><th>Duration</th></tr></thead>
                            <tbody>
                                <?php foreach ($upcoming_bookings as $b): ?>
                                <tr>
                                    <td class="fw-semibold"><?= e($b['member_name']) ?></td>
                                    <td><small><?= format_date($b['booking_date']) ?></small></td>
                                    <td><small><?= date('h:i A', strtotime($b['booking_time'])) ?></small></td>
                                    <td><span class="badge bg-light text-dark border"><?= $b['duration'] ?>m</span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Assigned Members -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-people-fill me-2 text-success"></i>My Members</h6>
                    <a href="<?= base_url('/members') ?>" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($assigned_members)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-person-x fs-2 d-block mb-2"></i>No members assigned yet
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Member</th><th>ID</th><th>Plan</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php foreach ($assigned_members as $m): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xs bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                                                <?= strtoupper(substr($m['first_name'], 0, 1)) ?>
                                            </div>
                                            <?= e($m['first_name'] . ' ' . $m['last_name']) ?>
                                        </div>
                                    </td>
                                    <td><code class="small"><?= e($m['membership_id']) ?></code></td>
                                    <td class="small text-muted"><?= e($m['plan_name']) ?></td>
                                    <td><?= status_badge($m['plan_status']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Fitness Plans -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-clipboard2-pulse-fill me-2 text-info"></i>Recent Fitness Plans</h6>
                    <a href="<?= base_url('/trainers/fitness-plans') ?>" class="btn btn-sm btn-outline-info">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recent_fitness_plans)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard-x fs-2 d-block mb-2"></i>No fitness plans yet
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_fitness_plans as $fp): ?>
                        <div class="list-group-item py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold small"><?= e($fp['plan_name']) ?></div>
                                    <div class="text-muted" style="font-size:.75rem"><?= e($fp['member_name']) ?></div>
                                </div>
                                <?= status_badge($fp['status']) ?>
                            </div>
                            <?php if ($fp['goal']): ?>
                            <div class="text-muted mt-1" style="font-size:.75rem"><i class="bi bi-bullseye me-1"></i><?= e($fp['goal']) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (has_role(['maintenance'])): ?>
    <!-- ══════════════════════════════════════════════════════════════════
         MAINTENANCE SUPERVISOR DASHBOARD
    ══════════════════════════════════════════════════════════════════ -->

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="bi bi-tools"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= $total_equipment ?></div>
                    <div class="stat-label">Total Equipment</div>
                </div>
                <a href="<?= base_url('/equipment') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <?php
            $needsRepairCount = 0;
            foreach ($equipment_status as $es) {
                if ($es['condition_status'] === 'needs_repair') $needsRepairCount = $es['count'];
            }
            ?>
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= $needsRepairCount ?></div>
                    <div class="stat-label">Needs Repair</div>
                </div>
                <a href="<?= base_url('/equipment') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-danger">
                <div class="stat-icon"><i class="bi bi-clipboard-x-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= count($pending_reports) ?></div>
                    <div class="stat-label">Pending Reports</div>
                </div>
                <a href="<?= base_url('/maintenance') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <?php
            $completedCount = 0;
            foreach ($report_status_counts as $sc) {
                if ($sc['status'] === 'completed') $completedCount = $sc['count'];
            }
            ?>
            <div class="stat-card stat-card-success">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= $completedCount ?></div>
                    <div class="stat-label">Completed Repairs</div>
                </div>
                <a href="<?= base_url('/maintenance') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Equipment Needing Attention -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Equipment Needing Attention</h6>
                    <span class="badge bg-warning text-dark"><?= count($needing_maintenance) ?></span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($needing_maintenance)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle-fill text-success fs-2 d-block mb-2"></i>All equipment in good condition
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($needing_maintenance as $eq): ?>
                        <div class="list-group-item d-flex align-items-center gap-3 py-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 flex-shrink-0">
                                <i class="bi bi-tools text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?= e($eq['name']) ?></div>
                                <div class="text-muted small"><?= e($eq['location'] ?? '—') ?> &bull; <?= e($eq['brand'] ?? '') ?></div>
                            </div>
                            <span class="badge bg-<?= $eq['condition_status'] === 'needs_repair' ? 'warning text-dark' : 'info' ?>">
                                <?= ucwords(str_replace('_', ' ', $eq['condition_status'])) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="<?= base_url('/maintenance/create') ?>" class="btn btn-sm btn-warning w-100">
                        <i class="bi bi-plus-circle me-1"></i>Log New Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Pending Maintenance Reports -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-clipboard-fill me-2 text-danger"></i>Pending Reports</h6>
                    <a href="<?= base_url('/maintenance') ?>" class="btn btn-sm btn-outline-danger">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($pending_reports)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard-check fs-2 d-block mb-2"></i>No pending reports
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($pending_reports, 0, 5) as $r): ?>
                        <div class="list-group-item py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold small"><?= e($r['issue_type']) ?></div>
                                    <div class="text-muted" style="font-size:.75rem"><i class="bi bi-tools me-1"></i><?= e($r['equipment_name']) ?></div>
                                </div>
                                <span class="badge bg-<?= $r['priority'] === 'critical' ? 'danger' : ($r['priority'] === 'high' ? 'warning text-dark' : 'secondary') ?>">
                                    <?= ucfirst($r['priority']) ?>
                                </span>
                            </div>
                            <div class="text-muted mt-1" style="font-size:.75rem">
                                <i class="bi bi-clock me-1"></i><?= format_date($r['created_at']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Status Overview -->
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Equipment Status Overview</h6>
                </div>
                <div class="card-body d-flex flex-column align-items-center">
                    <canvas id="eqStatusChart" height="200"></canvas>
                    <div class="mt-3 w-100">
                        <?php foreach ($equipment_status as $es): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small"><?= ucwords(str_replace('_', ' ', $es['condition_status'])) ?></span>
                            <span class="badge bg-<?= $es['condition_status'] === 'good' ? 'success' : ($es['condition_status'] === 'needs_repair' ? 'warning text-dark' : 'info') ?>">
                                <?= $es['count'] ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-secondary"></i>Recent Maintenance Activity</h6>
                    <a href="<?= base_url('/maintenance') ?>" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recent_reports)): ?>
                    <div class="text-center py-4 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No reports yet</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Issue</th><th>Equipment</th><th>Priority</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php foreach ($recent_reports as $r): ?>
                                <tr>
                                    <td class="small fw-semibold"><?= e($r['issue_type']) ?></td>
                                    <td class="small text-muted"><?= e($r['equipment_name']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $r['priority'] === 'critical' ? 'danger' : ($r['priority'] === 'high' ? 'warning text-dark' : ($r['priority'] === 'medium' ? 'info' : 'secondary')) ?>">
                                            <?= ucfirst($r['priority']) ?>
                                        </span>
                                    </td>
                                    <td><?= status_badge($r['status']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    const eqStatusData = <?= json_encode($equipment_status) ?>;
    if (eqStatusData.length > 0) {
        new Chart(document.getElementById('eqStatusChart'), {
            type: 'doughnut',
            data: {
                labels: eqStatusData.map(d => d.condition_status.replace(/_/g,' ').replace(/\b\w/g, l => l.toUpperCase())),
                datasets: [{ data: eqStatusData.map(d => parseInt(d.count)),
                    backgroundColor: eqStatusData.map(d => d.condition_status === 'good' ? '#198754' : d.condition_status === 'needs_repair' ? '#ffc107' : '#0dcaf0'),
                    borderWidth: 2 }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
        });
    }
    </script>
    <?php endif; ?>

    <?php if (has_role(['marketing'])): ?>
    <!-- ══════════════════════════════════════════════════════════════════
         MARKETING OFFICER DASHBOARD
    ══════════════════════════════════════════════════════════════════ -->

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <?php
        $totalCampaigns    = 0; $activeCnt = 0; $scheduledCnt = 0; $completedCnt = 0;
        foreach ($campaign_status_counts as $sc) {
            $totalCampaigns += $sc['count'];
            if ($sc['status'] === 'active')    $activeCnt    = $sc['count'];
            if ($sc['status'] === 'scheduled') $scheduledCnt = $sc['count'];
            if ($sc['status'] === 'completed') $completedCnt = $sc['count'];
        }
        ?>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="bi bi-megaphone-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= $totalCampaigns ?></div>
                    <div class="stat-label">Total Campaigns</div>
                </div>
                <a href="<?= base_url('/campaigns') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-success">
                <div class="stat-icon"><i class="bi bi-broadcast"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= $activeCnt ?></div>
                    <div class="stat-label">Active Campaigns</div>
                </div>
                <a href="<?= base_url('/campaigns') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="bi bi-calendar-event-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= $scheduledCnt ?></div>
                    <div class="stat-label">Scheduled</div>
                </div>
                <a href="<?= base_url('/campaigns') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= format_currency($total_campaign_budget) ?></div>
                    <div class="stat-label">Total Budget Used</div>
                </div>
                <span class="stat-link text-muted"><?= $completedCnt ?> completed</span>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Active Campaigns -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-broadcast me-2 text-success"></i>Active Campaigns</h6>
                    <span class="badge bg-success"><?= count($active_campaigns) ?></span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($active_campaigns)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-megaphone fs-2 d-block mb-2"></i>No active campaigns
                        <div class="mt-2"><a href="<?= base_url('/campaigns/create') ?>" class="btn btn-sm btn-success">Create Campaign</a></div>
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($active_campaigns as $c): ?>
                        <div class="list-group-item py-3">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="fw-semibold"><?= e($c['title']) ?></div>
                                <?php if ($c['discount_pct'] > 0): ?>
                                <span class="badge bg-danger"><?= $c['discount_pct'] ?>% OFF</span>
                                <?php endif; ?>
                            </div>
                            <div class="text-muted small"><?= e($c['target_audience'] ?? 'All') ?></div>
                            <div class="d-flex gap-3 mt-1" style="font-size:.75rem">
                                <span class="text-muted"><i class="bi bi-calendar me-1"></i><?= format_date($c['start_date']) ?> – <?= format_date($c['end_date']) ?></span>
                                <?php if ($c['budget'] > 0): ?>
                                <span class="text-muted"><i class="bi bi-wallet me-1"></i><?= format_currency($c['budget']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="<?= base_url('/campaigns/create') ?>" class="btn btn-sm btn-success w-100">
                        <i class="bi bi-plus-circle me-1"></i>New Campaign
                    </a>
                </div>
            </div>
        </div>

        <!-- Upcoming Campaigns -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-calendar-event-fill me-2 text-info"></i>Upcoming Campaigns</h6>
                    <span class="badge bg-info text-dark"><?= count($upcoming_campaigns) ?></span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($upcoming_campaigns)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-plus fs-2 d-block mb-2"></i>No upcoming campaigns scheduled
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($upcoming_campaigns as $c): ?>
                        <div class="list-group-item py-3">
                            <div class="fw-semibold"><?= e($c['title']) ?></div>
                            <div class="text-muted small mt-1">
                                <i class="bi bi-calendar-check me-1"></i>Starts <?= format_date($c['start_date']) ?>
                                &bull; Ends <?= format_date($c['end_date']) ?>
                            </div>
                            <?php if ($c['target_audience']): ?>
                            <div class="text-muted" style="font-size:.75rem"><i class="bi bi-people me-1"></i><?= e($c['target_audience']) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Status Chart + Recent Campaigns -->
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Campaign Status</h6>
                </div>
                <div class="card-body d-flex flex-column align-items-center">
                    <canvas id="campaignChart" height="200"></canvas>
                    <div class="mt-3 w-100">
                        <?php foreach ($campaign_status_counts as $sc): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-capitalize"><?= $sc['status'] ?></span>
                            <span class="badge bg-secondary"><?= $sc['count'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-secondary"></i>All Campaigns</h6>
                    <a href="<?= base_url('/campaigns') ?>" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Title</th><th>Dates</th><th>Discount</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php foreach ($recent_campaigns as $c): ?>
                                <tr>
                                    <td class="fw-semibold small"><?= e($c['title']) ?></td>
                                    <td class="small text-muted"><?= format_date($c['start_date']) ?> – <?= format_date($c['end_date']) ?></td>
                                    <td><?= $c['discount_pct'] > 0 ? '<span class="badge bg-danger">' . $c['discount_pct'] . '%</span>' : '<span class="text-muted small">—</span>' ?></td>
                                    <td><?= status_badge($c['status']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    const campaignStatusData = <?= json_encode($campaign_status_counts) ?>;
    if (campaignStatusData.length > 0) {
        const csColors = { active: '#198754', scheduled: '#0dcaf0', completed: '#6c757d', inactive: '#adb5bd' };
        new Chart(document.getElementById('campaignChart'), {
            type: 'doughnut',
            data: {
                labels: campaignStatusData.map(d => d.status.charAt(0).toUpperCase() + d.status.slice(1)),
                datasets: [{ data: campaignStatusData.map(d => parseInt(d.count)),
                    backgroundColor: campaignStatusData.map(d => csColors[d.status] || '#6c757d'),
                    borderWidth: 2 }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
        });
    }
    </script>
    <?php endif; ?>

    <?php if (has_role(['member'])): ?>
    <!-- ══════════════════════════════════════════════════════════════════
         MEMBER DASHBOARD
    ══════════════════════════════════════════════════════════════════ -->

    <?php if (!$active_membership): ?>
    <?php
    // Check if member has a pending membership (payment submitted, awaiting admin)
    $memberModel = new Member();
    $memberRecord = $memberModel->getMemberByUserId(auth_id());
    $membershipModel = new Membership();
    $hasPendingPayment = false;
    if ($memberRecord) {
        foreach ($membershipModel->getByMemberId((int)$memberRecord['id']) as $ms) {
            if ($ms['status'] === 'pending') { $hasPendingPayment = true; break; }
        }
    }
    ?>
    <?php if ($hasPendingPayment): ?>
    <div class="alert alert-warning d-flex align-items-start gap-3 mb-4">
        <i class="bi bi-hourglass-split fs-5 mt-1 text-warning"></i>
        <div class="flex-grow-1">
            <strong>Payment Under Review</strong> — Your payment has been submitted and is awaiting verification by the Administrative Office.
            You will be notified once your membership is activated.
        </div>
        <a href="<?= base_url('/my-membership') ?>" class="btn btn-sm btn-warning text-nowrap">View Status</a>
    </div>
    <?php else: ?>
    <div class="alert alert-info d-flex align-items-start gap-3 mb-4">
        <i class="bi bi-cash-coin fs-5 mt-1"></i>
        <div class="flex-grow-1">
            <strong>Payment Required</strong> — Your membership application was approved! Please submit your payment to activate your membership.
        </div>
        <a href="<?= base_url('/my-membership') ?>" class="btn btn-sm btn-primary text-nowrap">
            <i class="bi bi-send me-1"></i>Submit Payment
        </a>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card <?= $active_membership ? 'stat-card-success' : 'stat-card-warning' ?>">
                <div class="stat-icon"><i class="bi bi-card-checklist"></i></div>
                <div class="stat-content">
                    <div class="stat-value" style="font-size:1.1rem">
                        <?= $active_membership ? e($active_membership['plan_name']) : 'None' ?>
                    </div>
                    <div class="stat-label">Membership Plan</div>
                </div>
                <?php if ($active_membership): ?>
                <span class="stat-link text-muted small">Expires <?= format_date($active_membership['expiry_date']) ?></span>
                <?php else: ?>
                <a href="<?= base_url('/memberships') ?>" class="stat-link">Apply now <i class="bi bi-arrow-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="bi bi-door-open-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= count($recent_checkins) ?></div>
                    <div class="stat-label">Recent Check-Ins</div>
                </div>
                <a href="<?= base_url('/checkins') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= count($upcoming_bookings) ?></div>
                    <div class="stat-label">Upcoming Sessions</div>
                </div>
                <a href="<?= base_url('/bookings') ?>" class="stat-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="bi bi-fire"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><?= number_format($today_calories) ?></div>
                    <div class="stat-label">Calories Today</div>
                </div>
                <a href="<?= base_url('/diet') ?>" class="stat-link">Log diet <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Membership Card -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-person-badge-fill me-2 text-primary"></i>My Membership</h6>
                </div>
                <div class="card-body">
                    <?php if ($member): ?>
                    <div class="text-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:64px;height:64px">
                            <i class="bi bi-person-fill text-primary fs-2"></i>
                        </div>
                        <div class="fw-bold fs-5"><?= e(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')) ?></div>
                        <div class="text-muted small">Member ID: <code><?= e($member['membership_id'] ?? '—') ?></code></div>
                    </div>
                    <?php if ($active_membership): ?>
                    <div class="list-group list-group-flush rounded">
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted small">Plan</span>
                            <span class="fw-semibold small"><?= e($active_membership['plan_name']) ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted small">Type</span>
                            <span class="fw-semibold small text-capitalize"><?= e($active_membership['plan_type']) ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted small">Start Date</span>
                            <span class="fw-semibold small"><?= format_date($active_membership['start_date']) ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted small">Expiry</span>
                            <?php
                            $daysLeft = (int)ceil((strtotime($active_membership['expiry_date']) - time()) / 86400);
                            $expiryClass = $daysLeft <= 7 ? 'text-danger fw-bold' : ($daysLeft <= 30 ? 'text-warning fw-semibold' : 'text-success fw-semibold');
                            ?>
                            <span class="small <?= $expiryClass ?>"><?= format_date($active_membership['expiry_date']) ?> (<?= $daysLeft ?>d left)</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted small">Amount</span>
                            <span class="fw-semibold small"><?= format_currency($active_membership['amount']) ?></span>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-card-checklist fs-3 d-block mb-2"></i>No active membership
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Active Plans -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-clipboard2-pulse-fill me-2 text-success"></i>My Active Plans</h6>
                </div>
                <div class="card-body">
                    <!-- Fitness Plan -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-clipboard2-pulse-fill text-success"></i>
                            <span class="fw-semibold small">Fitness Plan</span>
                        </div>
                        <?php if ($active_fitness_plan): ?>
                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <div class="fw-semibold"><?= e($active_fitness_plan['plan_name']) ?></div>
                            <?php if ($active_fitness_plan['goal']): ?>
                            <div class="text-muted small mt-1"><i class="bi bi-bullseye me-1"></i><?= e($active_fitness_plan['goal']) ?></div>
                            <?php endif; ?>
                            <?php if ($active_fitness_plan['trainer_name']): ?>
                            <div class="text-muted small"><i class="bi bi-person-badge me-1"></i><?= e($active_fitness_plan['trainer_name']) ?></div>
                            <?php endif; ?>
                            <div class="text-muted small"><i class="bi bi-calendar me-1"></i><?= $active_fitness_plan['duration_weeks'] ?> weeks &bull; <?= e($active_fitness_plan['frequency'] ?? '') ?></div>
                        </div>
                        <?php else: ?>
                        <div class="text-center text-muted py-2 small">No active fitness plan</div>
                        <?php endif; ?>
                    </div>
                    <!-- Nutrition Plan -->
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-egg-fried text-warning"></i>
                            <span class="fw-semibold small">Nutrition Plan</span>
                        </div>
                        <?php if ($active_nutrition_plan): ?>
                        <div class="bg-warning bg-opacity-10 rounded p-3">
                            <div class="fw-semibold"><?= e($active_nutrition_plan['plan_name']) ?></div>
                            <div class="row g-2 mt-1">
                                <div class="col-6"><div class="text-muted" style="font-size:.7rem">Calories</div><div class="fw-semibold small"><?= number_format($active_nutrition_plan['daily_calories']) ?> kcal</div></div>
                                <div class="col-6"><div class="text-muted" style="font-size:.7rem">Protein</div><div class="fw-semibold small"><?= $active_nutrition_plan['protein_grams'] ?>g</div></div>
                                <div class="col-6"><div class="text-muted" style="font-size:.7rem">Carbs</div><div class="fw-semibold small"><?= $active_nutrition_plan['carbs_grams'] ?>g</div></div>
                                <div class="col-6"><div class="text-muted" style="font-size:.7rem">Fat</div><div class="fw-semibold small"><?= $active_nutrition_plan['fat_grams'] ?>g</div></div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="text-center text-muted py-2 small">No active nutrition plan</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer bg-transparent d-flex gap-2">
                    <a href="<?= base_url('/trainers/fitness-plans') ?>" class="btn btn-sm btn-outline-success flex-fill">Fitness Plans</a>
                    <a href="<?= base_url('/trainers/nutrition-plans') ?>" class="btn btn-sm btn-outline-warning flex-fill">Nutrition Plans</a>
                </div>
            </div>
        </div>

        <!-- Upcoming Bookings -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-calendar-check-fill me-2 text-primary"></i>Upcoming Sessions</h6>
                    <a href="<?= base_url('/bookings') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($upcoming_bookings)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>No upcoming sessions
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($upcoming_bookings as $b): ?>
                        <div class="list-group-item py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 flex-shrink-0">
                                    <i class="bi bi-person-badge-fill text-primary small"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small"><?= e($b['trainer_name']) ?></div>
                                    <div class="text-muted" style="font-size:.75rem">
                                        <i class="bi bi-calendar me-1"></i><?= format_date($b['booking_date']) ?>
                                        &bull; <?= date('h:i A', strtotime($b['booking_time'])) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="<?= base_url('/bookings/create') ?>" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-plus-circle me-1"></i>Book a Trainer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Check-Ins & Today's Diet -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-door-open-fill me-2 text-info"></i>Recent Check-Ins</h6>
                    <a href="<?= base_url('/checkins') ?>" class="btn btn-sm btn-outline-info">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recent_checkins)): ?>
                    <div class="text-center py-4 text-muted"><i class="bi bi-door-closed fs-2 d-block mb-2"></i>No check-ins yet</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Date</th><th>Time In</th><th>Time Out</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php foreach ($recent_checkins as $c): ?>
                                <tr>
                                    <td class="small"><?= format_date($c['check_in_time']) ?></td>
                                    <td class="small"><?= date('h:i A', strtotime($c['check_in_time'])) ?></td>
                                    <td class="small"><?= $c['check_out_time'] ? date('h:i A', strtotime($c['check_out_time'])) : '<span class="text-muted">—</span>' ?></td>
                                    <td><?= status_badge($c['status']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-journal-medical me-2 text-warning"></i>Today's Diet Log</h6>
                    <a href="<?= base_url('/diet') ?>" class="btn btn-sm btn-outline-warning">Log Food</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($today_diet)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-journal-plus fs-2 d-block mb-2"></i>Nothing logged today
                        <div class="mt-2"><a href="<?= base_url('/diet') ?>" class="btn btn-sm btn-outline-warning">Add Entry</a></div>
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($today_diet as $d): ?>
                        <div class="list-group-item d-flex align-items-center gap-3 py-2">
                            <span class="badge bg-<?= ['breakfast'=>'warning text-dark','lunch'=>'success','dinner'=>'primary','snack'=>'info text-dark'][$d['meal_type']] ?? 'secondary' ?> text-capitalize" style="min-width:70px">
                                <?= $d['meal_type'] ?>
                            </span>
                            <div class="flex-grow-1 small"><?= e(mb_strimwidth($d['food_items'], 0, 40, '…')) ?></div>
                            <span class="text-muted small text-nowrap"><?= number_format($d['calories']) ?> kcal</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="p-3 border-top d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Total today</span>
                        <span class="fw-bold"><?= number_format($today_calories) ?> kcal</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
