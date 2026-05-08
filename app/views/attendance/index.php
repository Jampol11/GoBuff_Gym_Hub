<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Staff Attendance</h2><p class="page-subtitle">Employee time tracking</p></div>
        <div class="page-actions">
            <a href="<?= base_url('/attendance/export') ?>" class="btn btn-outline-success"><i class="bi bi-download me-1"></i>Export</a>
            <form method="POST" action="<?= base_url('/attendance/clock-in') ?>" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success"><i class="bi bi-clock me-1"></i>Clock In</button>
            </form>
        </div>
    </div>

    <!-- Today's Attendance -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-calendar-day me-2 text-success"></i>Today's Attendance</h6>
            <span class="badge bg-success"><?= count($today) ?></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Employee</th><th>Role</th><th>Time In</th><th>Time Out</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($today)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No attendance recorded today</td></tr>
                        <?php else: ?>
                            <?php foreach ($today as $a): ?>
                                <tr>
                                    <td class="fw-semibold"><?= e($a['employee_name']) ?></td>
                                    <td><?= ucfirst(e($a['job_role'])) ?></td>
                                    <td><?= $a['time_in'] ? date('h:i A', strtotime($a['time_in'])) : '—' ?></td>
                                    <td><?= $a['time_out'] ? date('h:i A', strtotime($a['time_out'])) : '—' ?></td>
                                    <td><?= status_badge($a['status']) ?></td>
                                    <td>
                                        <?php if (!$a['time_out']): ?>
                                            <form method="POST" action="<?= base_url('/attendance/' . $a['id'] . '/clock-out') ?>">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-clock-history me-1"></i>Clock Out</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small">Completed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- All Records -->
    <div class="card">
        <div class="card-header"><h6 class="mb-0 fw-semibold">All Attendance Records</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr><th>#</th><th>Employee</th><th>Role</th><th>Date</th><th>Time In</th><th>Time Out</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $i => $r): ?>
                            <tr>
                                <td><?= $pagination['offset'] + $i + 1 ?></td>
                                <td><?= e($r['employee_name']) ?></td>
                                <td><?= ucfirst(e($r['job_role'])) ?></td>
                                <td><?= format_date($r['date']) ?></td>
                                <td><?= $r['time_in'] ? date('h:i A', strtotime($r['time_in'])) : '—' ?></td>
                                <td><?= $r['time_out'] ? date('h:i A', strtotime($r['time_out'])) : '—' ?></td>
                                <td><?= status_badge($r['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?></small>
            <?= pagination_links($pagination, base_url('/attendance')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
