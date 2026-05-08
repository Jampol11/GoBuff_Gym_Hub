<div class="container-fluid py-4">

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
        <!-- Expiring Memberships -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Expiring Soon (7 days)</h6>
                    <span class="badge bg-warning text-dark"><?= count($expiring_soon) ?></span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($expiring_soon)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>
                            No memberships expiring soon
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr><th>Member</th><th>Plan</th><th>Expires</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expiring_soon as $m): ?>
                                        <tr>
                                            <td><?= e($m['member_name']) ?></td>
                                            <td><?= e($m['plan_name']) ?></td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    <?= format_date($m['expiry_date']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Upcoming Bookings -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-calendar-check-fill me-2 text-primary"></i>Upcoming Bookings</h6>
                    <a href="<?= base_url('/bookings') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($upcoming_bookings)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                            No upcoming bookings
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr><th>Member</th><th>Trainer</th><th>Date & Time</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcoming_bookings as $b): ?>
                                        <tr>
                                            <td><?= e($b['member_name']) ?></td>
                                            <td><?= e($b['trainer_name']) ?></td>
                                            <td>
                                                <small><?= format_date($b['booking_date']) ?></small><br>
                                                <small class="text-muted"><?= e($b['booking_time']) ?></small>
                                            </td>
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

    <!-- Recent Members & Today's Check-ins -->
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
                            <thead class="table-light">
                                <tr><th>Name</th><th>ID</th><th>Joined</th></tr>
                            </thead>
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
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-door-closed fs-3 d-block mb-2"></i>
                            No check-ins today
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr><th>Member</th><th>Time In</th><th>Status</th></tr>
                                </thead>
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
</div>

<script>
// Weekly Check-In Chart
const checkinData = <?= json_encode($weekly_checkins) ?>;
const labels = checkinData.map(d => {
    const date = new Date(d.date);
    return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
});
const counts = checkinData.map(d => parseInt(d.count));

new Chart(document.getElementById('checkinChart'), {
    type: 'bar',
    data: {
        labels: labels.length ? labels : ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
        datasets: [{
            label: 'Check-Ins',
            data: counts.length ? counts : [0,0,0,0,0,0,0],
            backgroundColor: 'rgba(13, 110, 253, 0.7)',
            borderColor: 'rgba(13, 110, 253, 1)',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Equipment Status Chart
const eqData = <?= json_encode($equipment_status) ?>;
const eqLabels = eqData.map(d => d.condition_status.replace(/_/g,' ').replace(/\b\w/g, l => l.toUpperCase()));
const eqCounts = eqData.map(d => parseInt(d.count));
const eqColors = eqData.map(d => {
    if (d.condition_status === 'good') return '#198754';
    if (d.condition_status === 'needs_repair') return '#ffc107';
    return '#0dcaf0';
});

if (eqData.length > 0) {
    new Chart(document.getElementById('equipmentChart'), {
        type: 'doughnut',
        data: {
            labels: eqLabels,
            datasets: [{ data: eqCounts, backgroundColor: eqColors, borderWidth: 2 }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }
        }
    });
}
</script>
