<div class="container-fluid px-4 py-3">

    <!-- Back -->
    <?php if (has_role(['gym_owner'])): ?>
    <a href="<?= base_url('/employees') ?>" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left me-1"></i>Back to Employees
    </a>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:80px;height:80px;">
                        <i class="bi bi-person-fill fs-1 text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-1"><?= e($employee['first_name'] . ' ' . $employee['last_name']) ?></h5>
                    <div class="mb-2"><?= role_badge($employee['job_role']) ?></div>
                    <p class="text-muted small mb-0"><?= e($employee['department'] ?? 'No department assigned') ?></p>
                </div>
                <div class="card-body border-top pt-3">
                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7">
                            <?php
                            $sc = match($employee['status']) {
                                'active'   => 'success',
                                'inactive' => 'warning',
                                'resigned' => 'danger',
                                default    => 'secondary',
                            };
                            ?>
                            <span class="badge bg-<?= $sc ?>"><?= ucfirst($employee['status']) ?></span>
                        </dd>

                        <dt class="col-5 text-muted">Phone</dt>
                        <dd class="col-7"><?= e($employee['phone'] ?? '—') ?></dd>

                        <dt class="col-5 text-muted">Hire Date</dt>
                        <dd class="col-7">
                            <?= $employee['hire_date'] ? date('M d, Y', strtotime($employee['hire_date'])) : '—' ?>
                        </dd>

                        <dt class="col-5 text-muted">Specialization</dt>
                        <dd class="col-7"><?= e($employee['specialization'] ?? '—') ?></dd>

                        <?php if ($employee['address']): ?>
                        <dt class="col-5 text-muted">Address</dt>
                        <dd class="col-7"><?= e($employee['address']) ?></dd>
                        <?php endif; ?>

                        <dt class="col-5 text-muted">Member Since</dt>
                        <dd class="col-7"><?= date('M d, Y', strtotime($employee['created_at'])) ?></dd>
                    </dl>
                </div>
                <?php if (has_role(['gym_owner'])): ?>
                <div class="card-footer bg-transparent">
                    <a href="<?= base_url('/employees/' . $employee['id'] . '/edit') ?>"
                       class="btn btn-warning w-100">
                        <i class="bi bi-pencil-fill me-1"></i>Assign / Edit Job Role
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Schedule History -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar2-week text-success"></i>
                        <strong>Recent Work Schedule (Last 14 entries)</strong>
                    </div>
                    <?php if (has_role(['gym_owner', 'admin'])): ?>
                    <a href="<?= base_url('/employees/schedule') ?>" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-plus-lg me-1"></i>Manage Schedule
                    </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($schedules)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                            No schedule entries yet.
                        </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Hours</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedules as $s): ?>
                                <?php
                                    $inSec  = strtotime($s['time_in']);
                                    $outSec = strtotime($s['time_out']);
                                    $hours  = $outSec > $inSec ? round(($outSec - $inSec) / 3600, 1) : '—';
                                ?>
                                <tr>
                                    <td class="fw-semibold"><?= date('M d, Y (D)', strtotime($s['schedule_date'])) ?></td>
                                    <td><span class="badge bg-success bg-opacity-75"><?= date('h:i A', strtotime($s['time_in'])) ?></span></td>
                                    <td><span class="badge bg-secondary bg-opacity-75"><?= date('h:i A', strtotime($s['time_out'])) ?></span></td>
                                    <td><?= is_numeric($hours) ? $hours . ' hrs' : $hours ?></td>
                                    <td class="text-muted small"><?= e($s['notes'] ?? '—') ?></td>
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
