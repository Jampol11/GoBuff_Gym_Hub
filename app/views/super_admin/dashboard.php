<div class="container-fluid px-4 py-3">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-shield-fill-check me-2 text-dark"></i>Super Admin Dashboard
            </h1>
            <p class="text-muted mb-0">Platform-level control center — full system authority.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('/super-admin/owner-applications') ?>" class="btn btn-warning position-relative">
                <i class="bi bi-building-fill-gear me-1"></i>Owner Applications
                <?php if ($pendingOwnerApps > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $pendingOwnerApps ?>
                    </span>
                <?php endif; ?>
            </a>
            <a href="<?= base_url('/super-admin/create-super-admin') ?>" class="btn btn-dark">
                <i class="bi bi-shield-plus me-1"></i>Add Super Admin
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-dark border-4">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-dark bg-opacity-10 p-3">
                        <i class="bi bi-building fs-3 text-dark"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold"><?= (int)$totalGyms ?></div>
                        <div class="text-muted small">Total Gyms</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?= base_url('/super-admin/gyms') ?>" class="btn btn-sm btn-outline-dark w-100">Manage Gyms</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-danger border-4">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-danger bg-opacity-10 p-3">
                        <i class="bi bi-person-badge-fill fs-3 text-danger"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold"><?= count($gymOwners) ?></div>
                        <div class="text-muted small">Gym Owners</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?= base_url('/super-admin/gym-owners') ?>" class="btn btn-sm btn-outline-danger w-100">View Owners</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-primary border-4">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-people-fill fs-3 text-primary"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold"><?= (int)$totalUsers ?></div>
                        <div class="text-muted small">Total Users</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?= base_url('/super-admin/users') ?>" class="btn btn-sm btn-outline-primary w-100">View All Users</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-warning bg-opacity-10 p-3">
                        <i class="bi bi-hourglass-split fs-3 text-warning"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold"><?= (int)$pendingOwnerApps ?></div>
                        <div class="text-muted small">Pending Owner Apps</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?= base_url('/super-admin/owner-applications?status=pending') ?>" class="btn btn-sm btn-outline-warning w-100">Review Now</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- Pending Owner Applications -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-building-fill-gear text-warning fs-5"></i>
                        <strong>Recent Gym Owner Applications</strong>
                        <?php if ($pendingOwnerApps > 0): ?>
                            <span class="badge bg-danger"><?= $pendingOwnerApps ?> pending</span>
                        <?php endif; ?>
                    </div>
                    <a href="<?= base_url('/super-admin/owner-applications') ?>" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentApps)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            No applications yet.
                        </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Applicant</th>
                                    <th>Business</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentApps as $app): ?>
                                <tr>
                                    <td>
                                        <div class="fw-medium"><?= e($app['user_name']) ?></div>
                                        <div class="text-muted small"><?= e($app['user_email']) ?></div>
                                    </td>
                                    <td><?= e($app['business_name']) ?></td>
                                    <td>
                                        <?php
                                        $badge = match($app['status']) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default    => 'warning text-dark',
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= ucfirst($app['status']) ?></span>
                                    </td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($app['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('/super-admin/owner-applications/' . $app['id']) ?>"
                                           class="btn btn-sm btn-outline-primary">Review</a>
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

        <!-- Users by Role -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart-fill text-primary fs-5"></i>
                    <strong>Users by Role</strong>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($usersByRole as $row): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3">
                            <?= role_badge($row['role']) ?>
                            <span class="badge bg-secondary rounded-pill"><?= (int)$row['count'] ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Gym Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-building text-dark fs-5"></i>
                        <strong>Gym Overview</strong>
                    </div>
                    <a href="<?= base_url('/super-admin/gyms/create') ?>" class="btn btn-sm btn-dark">
                        <i class="bi bi-plus-lg"></i> Add Gym
                    </a>
                </div>
                <div class="card-body">
                    <div class="row text-center g-2">
                        <div class="col-4">
                            <div class="fs-3 fw-bold"><?= (int)$gymStats['total'] ?></div>
                            <div class="text-muted small">Total</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-3 fw-bold text-success"><?= (int)$gymStats['active'] ?></div>
                            <div class="text-muted small">Active</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-3 fw-bold text-secondary"><?= (int)$gymStats['inactive'] ?></div>
                            <div class="text-muted small">Inactive</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent text-end">
                    <a href="<?= base_url('/super-admin/gyms') ?>" class="btn btn-sm btn-outline-dark">Manage Gyms</a>
                </div>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus-fill text-success fs-5"></i>
                        <strong>Recent Registrations</strong>
                    </div>
                    <a href="<?= base_url('/super-admin/users') ?>" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentUsers as $u): ?>
                                <tr>
                                    <td class="fw-medium"><?= e($u['name']) ?></td>
                                    <td class="text-muted small"><?= e($u['email']) ?></td>
                                    <td class="text-muted small"><?= e($u['username']) ?></td>
                                    <td><?= role_badge($u['role']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $u['status'] === 'active' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst($u['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
