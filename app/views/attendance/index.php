<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">Staff Attendance</h2>
            <p class="page-subtitle">Employee time tracking</p>
        </div>
        <div class="page-actions">
            <?php if ($isManager): ?>
            <a href="<?= base_url('/employees/schedule') ?>" class="btn btn-outline-primary">
                <i class="bi bi-calendar2-week me-1"></i>Schedule Sheet
            </a>
            <a href="<?= base_url('/attendance/export') ?>" class="btn btn-outline-success">
                <i class="bi bi-download me-1"></i>Export
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($myEmployee): ?>
    <div class="row g-4 mb-4">

        <!-- ── Clock In/Out Panel ── -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex align-items-center gap-2">
                    <i class="bi bi-clock-fill text-primary"></i>
                    <strong>My Attendance — <?= date('l, F j, Y') ?></strong>
                </div>
                <div class="card-body">

                    <?php
                    $mySched = $scheduleMap[$myEmployee['id']] ?? null;
                    ?>

                    <!-- Scheduled shift -->
                    <div class="mb-3 p-3 rounded-3 <?= $mySched ? 'bg-primary bg-opacity-10' : 'bg-light' ?>">
                        <div class="text-muted small mb-1 fw-semibold">
                            <i class="bi bi-calendar-check me-1"></i>Today's Scheduled Shift
                        </div>
                        <?php if ($mySched): ?>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <span class="fs-5 fw-bold text-primary">
                                    <?= date('h:i A', strtotime($mySched['time_in'])) ?>
                                </span>
                                <i class="bi bi-arrow-right text-muted"></i>
                                <span class="fs-5 fw-bold text-primary">
                                    <?= date('h:i A', strtotime($mySched['time_out'])) ?>
                                </span>
                                <?php
                                $shiftSecs = strtotime($mySched['time_out']) - strtotime($mySched['time_in']);
                                $shiftHrs  = round($shiftSecs / 3600, 1);
                                ?>
                                <span class="badge bg-primary"><?= $shiftHrs ?> hrs</span>
                            </div>
                            <?php if ($mySched['notes']): ?>
                                <div class="text-muted small mt-1"><?= e($mySched['notes']) ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">No schedule assigned for today.</span>
                        <?php endif; ?>
                    </div>

                    <!-- Clock-in / Clock-out status row -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="text-muted small mb-1 fw-semibold">
                                <i class="bi bi-box-arrow-in-right me-1 text-success"></i>Time In
                            </div>
                            <?php if ($myAttendance && $myAttendance['time_in']): ?>
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <?= date('h:i A', strtotime($myAttendance['time_in'])) ?>
                                </span>
                                <?php if ($myAttendance['status'] === 'late'): ?>
                                    <div class="mt-1">
                                        <span class="badge bg-danger">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Late
                                        </span>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Not yet clocked in</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small mb-1 fw-semibold">
                                <i class="bi bi-box-arrow-right me-1 text-secondary"></i>Time Out
                            </div>
                            <?php if ($myAttendance && $myAttendance['time_out']): ?>
                                <span class="badge bg-secondary fs-6 px-3 py-2">
                                    <?= date('h:i A', strtotime($myAttendance['time_out'])) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">Not yet clocked out</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="d-flex gap-2 flex-wrap">
                        <?php if (!$myAttendance): ?>
                            <form method="POST" action="<?= base_url('/attendance/clock-in') ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-success btn-lg px-4">
                                    <i class="bi bi-clock me-2"></i>Clock In
                                </button>
                            </form>
                            <?php if ($mySched): ?>
                                <?php
                                $nowTs       = time();
                                $schedInTs   = strtotime(date('Y-m-d') . ' ' . $mySched['time_in']);
                                $minsToShift = (int)round(($schedInTs - $nowTs) / 60);
                                if ($minsToShift > 0 && $minsToShift <= 60):
                                ?>
                                <div class="align-self-center text-muted small">
                                    <i class="bi bi-hourglass-split me-1 text-warning"></i>
                                    Shift starts in <?= $minsToShift ?> min<?= $minsToShift !== 1 ? 's' : '' ?>
                                </div>
                                <?php elseif ($minsToShift < 0): ?>
                                <div class="align-self-center">
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Shift started <?= abs($minsToShift) ?> min<?= abs($minsToShift) !== 1 ? 's' : '' ?> ago
                                    </span>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php elseif (!$myAttendance['time_out']): ?>
                            <span class="btn btn-success btn-lg disabled">
                                <i class="bi bi-check-circle me-2"></i>Clocked In
                            </span>
                            <form method="POST" action="<?= base_url('/attendance/' . $myAttendance['id'] . '/clock-out') ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="bi bi-clock-history me-2"></i>Clock Out
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="bi bi-check2-all me-1"></i>Shift Complete
                                </span>
                                <?php
                                if ($myAttendance['time_in'] && $myAttendance['time_out']) {
                                    $worked = strtotime($myAttendance['time_out']) - strtotime($myAttendance['time_in']);
                                    $hrs    = floor($worked / 3600);
                                    $mins   = floor(($worked % 3600) / 60);
                                    echo "<span class='text-muted small'>Worked: {$hrs}h {$mins}m</span>";
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- ── Upcoming Schedule ── -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex align-items-center gap-2">
                    <i class="bi bi-calendar2-week text-success"></i>
                    <strong>My Upcoming Schedule</strong>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($myUpcoming)): ?>
                        <div class="text-center text-muted py-4 px-3">
                            <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                            No upcoming shifts scheduled.<br>
                            <span class="small">Check with your Gym Owner.</span>
                        </div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($myUpcoming as $s): ?>
                            <?php
                                $isToday   = $s['schedule_date'] === date('Y-m-d');
                                $shiftSecs = strtotime($s['time_out']) - strtotime($s['time_in']);
                                $shiftHrs  = round($shiftSecs / 3600, 1);
                            ?>
                            <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2
                                        <?= $isToday ? 'bg-success bg-opacity-10 border-start border-success border-3' : '' ?>">
                                <div>
                                    <div class="fw-semibold small">
                                        <?= $isToday ? '<span class="badge bg-success me-1">Today</span>' : '' ?>
                                        <?= date('D, M j', strtotime($s['schedule_date'])) ?>
                                    </div>
                                    <div class="text-muted small">
                                        <?= date('h:i A', strtotime($s['time_in'])) ?>
                                        &ndash;
                                        <?= date('h:i A', strtotime($s['time_out'])) ?>
                                    </div>
                                    <?php if ($s['notes']): ?>
                                        <div class="text-muted" style="font-size:.75rem;"><?= e($s['notes']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-light text-dark border"><?= $shiftHrs ?> hrs</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- ── My Attendance History ── -->
    <?php if (!empty($myHistory)): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex align-items-center gap-2">
            <i class="bi bi-journal-text text-primary"></i>
            <strong>My Recent Attendance (Last 14 days)</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Scheduled</th>
                            <th>Clocked In</th>
                            <th>Clocked Out</th>
                            <th>Hours Worked</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($myHistory as $h): ?>
                        <?php
                            $workedHrs = '—';
                            if ($h['time_in'] && $h['time_out']) {
                                $secs      = strtotime($h['time_out']) - strtotime($h['time_in']);
                                $workedHrs = floor($secs / 3600) . 'h ' . floor(($secs % 3600) / 60) . 'm';
                            }
                            $isLate = $h['status'] === 'late';
                        ?>
                        <tr class="<?= $isLate ? 'table-warning' : '' ?>">
                            <td class="fw-semibold small">
                                <?= date('D, M j', strtotime($h['date'])) ?>
                                <?php if ($h['date'] === date('Y-m-d')): ?>
                                    <span class="badge bg-success ms-1">Today</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?php if ($h['sched_time_in']): ?>
                                    <?= date('h:i A', strtotime($h['sched_time_in'])) ?>
                                    &ndash;
                                    <?= date('h:i A', strtotime($h['sched_time_out'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($h['time_in']): ?>
                                    <span class="badge bg-<?= $isLate ? 'warning text-dark' : 'success' ?>">
                                        <?= date('h:i A', strtotime($h['time_in'])) ?>
                                    </span>
                                    <?php if ($isLate && $h['sched_time_in']): ?>
                                        <?php
                                        $minsLate = (int)round(
                                            (strtotime($h['time_in']) - strtotime($h['sched_time_in'])) / 60
                                        );
                                        if ($minsLate > 0):
                                        ?>
                                        <div class="text-danger" style="font-size:.75rem;">
                                            +<?= $minsLate ?> min late
                                        </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $h['time_out']
                                    ? '<span class="badge bg-secondary">' . date('h:i A', strtotime($h['time_out'])) . '</span>'
                                    : '<span class="text-muted">—</span>' ?>
                            </td>
                            <td class="small"><?= $workedHrs ?></td>
                            <td>
                                <?php
                                $badge = match($h['status']) {
                                    'present'  => '<span class="badge bg-success">Present</span>',
                                    'late'     => '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Late</span>',
                                    'absent'   => '<span class="badge bg-danger">Absent</span>',
                                    'half_day' => '<span class="badge bg-info text-dark">Half Day</span>',
                                    'leave'    => '<span class="badge bg-secondary">On Leave</span>',
                                    default    => '<span class="badge bg-light text-dark">' . ucfirst($h['status']) . '</span>',
                                };
                                echo $badge;
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; /* end $myEmployee */ ?>

    <?php if ($isManager): ?>

    <!-- ── Today's Full Attendance (managers only) ── -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-calendar-day me-2 text-success"></i>Today's Attendance
            </h6>
            <span class="badge bg-success"><?= count($today) ?></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Role</th>
                            <th>Scheduled</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($today)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No attendance recorded today</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($today as $a): ?>
                                <?php $sched = $scheduleMap[$a['employee_id']] ?? null; ?>
                                <tr class="<?= $a['status'] === 'late' ? 'table-warning' : '' ?>">
                                    <td class="fw-semibold"><?= e($a['employee_name']) ?></td>
                                    <td><?= ucfirst(e($a['job_role'])) ?></td>
                                    <td class="small">
                                        <?php if ($sched): ?>
                                            <span class="badge bg-primary bg-opacity-75">
                                                <?= date('h:i A', strtotime($sched['time_in'])) ?>
                                                &ndash; <?= date('h:i A', strtotime($sched['time_out'])) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($a['time_in']): ?>
                                            <span class="badge bg-<?= $a['status'] === 'late' ? 'warning text-dark' : 'success' ?>">
                                                <?= date('h:i A', strtotime($a['time_in'])) ?>
                                            </span>
                                            <?php if ($a['status'] === 'late' && $sched): ?>
                                                <?php
                                                $minsLate = (int)round(
                                                    (strtotime($a['time_in']) - strtotime($sched['time_in'])) / 60
                                                );
                                                ?>
                                                <div class="text-danger" style="font-size:.75rem;">+<?= $minsLate ?> min</div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $a['time_out'] ? date('h:i A', strtotime($a['time_out'])) : '—' ?></td>
                                    <td>
                                        <?php
                                        $badge = match($a['status']) {
                                            'present'  => '<span class="badge bg-success">Present</span>',
                                            'late'     => '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Late</span>',
                                            default    => status_badge($a['status']),
                                        };
                                        echo $badge;
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (!$a['time_out']): ?>
                                            <form method="POST" action="<?= base_url('/attendance/' . $a['id'] . '/clock-out') ?>">
                                                <?= csrf_field() ?>
                                                <button class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-clock-history me-1"></i>Clock Out
                                                </button>
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

    <!-- ── All Records (managers only) ── -->
    <div class="card">
        <div class="card-header"><h6 class="mb-0 fw-semibold">All Attendance Records</h6></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Role</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $i => $r): ?>
                            <tr class="<?= $r['status'] === 'late' ? 'table-warning' : '' ?>">
                                <td><?= $pagination['offset'] + $i + 1 ?></td>
                                <td><?= e($r['employee_name']) ?></td>
                                <td><?= ucfirst(e($r['job_role'])) ?></td>
                                <td><?= format_date($r['date']) ?></td>
                                <td><?= $r['time_in'] ? date('h:i A', strtotime($r['time_in'])) : '—' ?></td>
                                <td><?= $r['time_out'] ? date('h:i A', strtotime($r['time_out'])) : '—' ?></td>
                                <td>
                                    <?php
                                    $badge = match($r['status']) {
                                        'present'  => '<span class="badge bg-success">Present</span>',
                                        'late'     => '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Late</span>',
                                        default    => status_badge($r['status']),
                                    };
                                    echo $badge;
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?= $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?>
                of <?= $pagination['total'] ?>
            </small>
            <?= pagination_links($pagination, base_url('/attendance')) ?>
        </div>
        <?php endif; ?>
    </div>

    <?php endif; ?>
</div>
