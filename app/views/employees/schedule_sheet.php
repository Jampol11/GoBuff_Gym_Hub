<div class="container-fluid px-4 py-3">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-calendar2-week me-2 text-success"></i>Work Schedule Sheet
            </h1>
            <p class="text-muted mb-0">
                Assign daily work schedules for staff — mirrors the physical attendance book.
            </p>
        </div>
        <a href="<?= base_url('/employees') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-people me-1"></i>Employee List
        </a>
    </div>

    <!-- Date Picker -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" action="<?= base_url('/employees/schedule') ?>" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0 fw-semibold small">Schedule Date</label>
                </div>
                <div class="col-auto">
                    <input type="date" name="date" class="form-control form-control-sm"
                           value="<?= e($date) ?>" max="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-calendar-check me-1"></i>Load Sheet
                    </button>
                </div>
                <div class="col-auto ms-auto">
                    <span class="badge bg-success fs-6 px-3 py-2">
                        <?= date('l, F j, Y', strtotime($date)) ?>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedule Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-journal-text text-success"></i>
                <strong>Daily Schedule — <?= date('m/d/y', strtotime($date)) ?></strong>
            </div>
            <span class="badge bg-secondary"><?= count($employees) ?> staff</span>
        </div>

        <form method="POST" action="<?= base_url('/employees/schedule/save') ?>" id="scheduleForm">
            <?= csrf_field() ?>
            <input type="hidden" name="schedule_date" value="<?= e($date) ?>">

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="scheduleTable">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Role</th>
                                <th>Name</th>
                                <th style="width:140px;">Time In</th>
                                <th style="width:140px;">Time Out</th>
                                <th>Notes</th>
                                <th style="width:80px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleBody">
                            <?php foreach ($employees as $i => $emp): ?>
                            <?php $existing = $sheetMap[$emp['id']] ?? null; ?>
                            <tr class="schedule-row" data-index="<?= $i ?>">
                                <td class="text-muted small"><?= $i + 1 ?></td>
                                <td>
                                    <input type="hidden" name="employee_id[]" value="<?= $emp['id'] ?>">
                                    <?= role_badge($emp['job_role']) ?>
                                </td>
                                <td class="fw-semibold">
                                    <?= e($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                    <?php if ($emp['status'] !== 'active'): ?>
                                        <span class="badge bg-warning text-dark ms-1 small">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <input type="time" name="time_in[]" class="form-control form-control-sm time-in"
                                           value="<?= $existing ? e($existing['time_in']) : '' ?>"
                                           placeholder="06:00">
                                </td>
                                <td>
                                    <input type="time" name="time_out[]" class="form-control form-control-sm time-out"
                                           value="<?= $existing ? e($existing['time_out']) : '' ?>"
                                           placeholder="21:00">
                                </td>
                                <td>
                                    <input type="text" name="notes[]" class="form-control form-control-sm"
                                           value="<?= $existing ? e($existing['notes'] ?? '') : '' ?>"
                                           placeholder="Optional notes">
                                </td>
                                <td class="text-center">
                                    <?php if ($existing): ?>
                                    <form method="POST" action="<?= base_url('/employees/schedule/' . $existing['id'] . '/delete') ?>"
                                          class="d-inline" onsubmit="return confirm('Remove this schedule entry?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="redirect_date" value="<?= e($date) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-transparent d-flex align-items-center justify-content-between">
                <div class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Fill in Time In and Time Out for each employee on duty. Leave blank to skip.
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clearAllBtn">
                        <i class="bi bi-eraser me-1"></i>Clear All Times
                    </button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save me-1"></i>Save Schedule Sheet
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Today's Summary (if entries exist) -->
    <?php if (!empty($sheet)): ?>
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent">
            <strong><i class="bi bi-list-check me-2 text-primary"></i>Saved Schedule for <?= date('M d, Y', strtotime($date)) ?></strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Role</th>
                            <th>Name</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sheet as $row): ?>
                        <?php
                            $inSec  = strtotime($row['time_in']);
                            $outSec = strtotime($row['time_out']);
                            $hours  = $outSec > $inSec ? round(($outSec - $inSec) / 3600, 1) : '—';
                        ?>
                        <tr>
                            <td><?= role_badge($row['job_role']) ?></td>
                            <td class="fw-semibold"><?= e($row['employee_name']) ?></td>
                            <td>
                                <span class="badge bg-success bg-opacity-75 fs-6">
                                    <?= date('h:i A', strtotime($row['time_in'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-75 fs-6">
                                    <?= date('h:i A', strtotime($row['time_out'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (is_numeric($hours)): ?>
                                    <span class="badge bg-info text-dark"><?= $hours ?> hrs</span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= e($row['notes'] ?? '—') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Clear all time inputs
    document.getElementById('clearAllBtn').addEventListener('click', function () {
        document.querySelectorAll('.time-in, .time-out').forEach(function (input) {
            input.value = '';
        });
    });

    // Highlight rows that have time filled in
    document.querySelectorAll('.schedule-row').forEach(function (row) {
        const timeIn  = row.querySelector('.time-in');
        const timeOut = row.querySelector('.time-out');

        function updateHighlight() {
            if (timeIn.value && timeOut.value) {
                row.classList.add('table-success');
            } else {
                row.classList.remove('table-success');
            }
        }

        timeIn.addEventListener('change', updateHighlight);
        timeOut.addEventListener('change', updateHighlight);
        updateHighlight(); // run on load for pre-filled rows
    });

    // Validate time_out > time_in on submit
    document.getElementById('scheduleForm').addEventListener('submit', function (e) {
        let hasEntry = false;
        let valid    = true;

        document.querySelectorAll('.schedule-row').forEach(function (row) {
            const timeIn  = row.querySelector('.time-in').value;
            const timeOut = row.querySelector('.time-out').value;

            if (timeIn || timeOut) {
                hasEntry = true;
                if (!timeIn || !timeOut) {
                    valid = false;
                    row.querySelector('.time-in').classList.toggle('is-invalid', !timeIn);
                    row.querySelector('.time-out').classList.toggle('is-invalid', !timeOut);
                } else {
                    row.querySelector('.time-in').classList.remove('is-invalid');
                    row.querySelector('.time-out').classList.remove('is-invalid');
                }
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Please fill in both Time In and Time Out for each row you are scheduling.');
        }
    });
});
</script>
