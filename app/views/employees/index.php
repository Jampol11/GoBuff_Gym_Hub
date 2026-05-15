<div class="container-fluid px-4 py-3">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1"><i class="bi bi-people-fill me-2 text-primary"></i>Employee Profiles</h1>
            <p class="text-muted mb-0">Manage staff job roles, departments, and work details.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('/employees/schedule') ?>" class="btn btn-outline-success">
                <i class="bi bi-calendar2-week me-1"></i>Schedule Sheet
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" action="<?= base_url('/employees') ?>" class="row g-2 align-items-center">
                <div class="col-sm-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by name, role, department…"
                               value="<?= e($search) ?>">
                    </div>
                </div>
                <div class="col-sm-3">
                    <select name="role" class="form-select form-select-sm">
                        <option value="">All Roles</option>
                        <option value="admin"       <?= $roleFilter === 'admin'       ? 'selected' : '' ?>>Administrative Officer</option>
                        <option value="trainer"     <?= $roleFilter === 'trainer'     ? 'selected' : '' ?>>Fitness Trainer</option>
                        <option value="maintenance" <?= $roleFilter === 'maintenance' ? 'selected' : '' ?>>Maintenance Supervisor</option>
                        <option value="marketing"   <?= $roleFilter === 'marketing'   ? 'selected' : '' ?>>Marketing Officer</option>
                        <option value="gym_owner"   <?= $roleFilter === 'gym_owner'   ? 'selected' : '' ?>>Gym Owner</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <?php if ($search || $roleFilter): ?>
                        <a href="<?= base_url('/employees') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Employee Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
            <span class="fw-semibold"><i class="bi bi-person-badge me-2 text-primary"></i>Staff Directory</span>
            <span class="badge bg-primary"><?= count($employees) ?> employee<?= count($employees) !== 1 ? 's' : '' ?></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Job Role</th>
                            <th>Department</th>
                            <th>Phone</th>
                            <th>Hire Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-2 d-block mb-2"></i>
                                    No employees found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employees as $i => $emp): ?>
                            <tr>
                                <td class="text-muted small"><?= $pagination['offset'] + $i + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                             style="width:36px;height:36px;flex-shrink:0;">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?= e($emp['first_name'] . ' ' . $emp['last_name']) ?></div>
                                            <div class="text-muted small"><?= e($emp['email'] ?? '—') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= role_badge($emp['job_role']) ?></td>
                                <td><?= e($emp['department'] ?? '—') ?></td>
                                <td><?= e($emp['phone'] ?? '—') ?></td>
                                <td class="text-muted small">
                                    <?= $emp['hire_date'] ? date('M d, Y', strtotime($emp['hire_date'])) : '—' ?>
                                </td>
                                <td>
                                    <?php
                                    $statusColor = match($emp['status']) {
                                        'active'   => 'success',
                                        'inactive' => 'warning',
                                        'resigned' => 'danger',
                                        default    => 'secondary',
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statusColor ?>"><?= ucfirst($emp['status']) ?></span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= base_url('/employees/' . $emp['id']) ?>"
                                       class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= base_url('/employees/' . $emp['id'] . '/edit') ?>"
                                       class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?= $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?>
                of <?= $pagination['total'] ?>
            </small>
            <?= pagination_links($pagination, base_url('/employees')) ?>
        </div>
        <?php endif; ?>
    </div>

</div>
