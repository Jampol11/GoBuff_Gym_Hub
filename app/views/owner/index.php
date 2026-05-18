<div class="container-fluid px-4 py-3">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1"><i class="bi bi-building-fill-gear me-2 text-primary"></i>Owner Hub</h1>
            <p class="text-muted mb-0">Manage legal documents, budget plans, and operational expenses.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-file-earmark-lock2-fill fs-3 text-primary"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold"><?= (int)$totalDocs ?></div>
                        <div class="text-muted small">Legal Documents</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?= base_url('/owner/documents') ?>" class="btn btn-sm btn-outline-primary w-100">View All</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-success bg-opacity-10 p-3">
                        <i class="bi bi-wallet2 fs-3 text-success"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold"><?= (int)$totalBudgets ?></div>
                        <div class="text-muted small">Budget Plans</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?= base_url('/owner/budgets') ?>" class="btn btn-sm btn-outline-success w-100">View All</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-warning bg-opacity-10 p-3">
                        <i class="bi bi-receipt-cutoff fs-3 text-warning"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold">₱<?= number_format($totalExpenses, 2) ?></div>
                        <div class="text-muted small">Expenses This Year</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?= base_url('/owner/expenses') ?>" class="btn btn-sm btn-outline-warning w-100">View All</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-danger bg-opacity-10 p-3">
                        <i class="bi bi-exclamation-triangle-fill fs-3 text-danger"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-bold"><?= count($expiringSoon) ?></div>
                        <div class="text-muted small">Expiring Soon (30 days)</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?= base_url('/owner/documents') ?>" class="btn btn-sm btn-outline-danger w-100">Review</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Process 8: Services & Rates Quick Panel -->
    <?php
    $svcModel = new GymService();
    $totalSvc = $svcModel->count();
    $pendingSvc = $svcModel->count('submitted_to_marketing = 0 AND is_active = 1');
    $submittedSvc = $svcModel->count('submitted_to_marketing = 1 AND is_active = 1');
    ?>
    <div class="card border-0 shadow-sm border-start border-primary border-4 mb-4">
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-tags-fill text-primary fs-5"></i>
                <strong>Services &amp; Membership Rates</strong>
                <span class="badge bg-primary"><?= $totalSvc ?> total</span>
            </div>
            <a href="<?= base_url('/owner/services') ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-tags-fill me-1"></i>Manage Services
            </a>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-sm-4 text-center">
                    <div class="fs-1 fw-bold text-primary"><?= $totalSvc ?></div>
                    <div class="text-muted small">Total Services</div>
                </div>
                <div class="col-sm-4 text-center">
                    <div class="fs-1 fw-bold text-warning"><?= $pendingSvc ?></div>
                    <div class="text-muted small">Pending Submission</div>
                </div>
                <div class="col-sm-4 text-center">
                    <div class="fs-1 fw-bold text-success"><?= $submittedSvc ?></div>
                    <div class="text-muted small">Submitted to Marketing</div>
                </div>
            </div>
            <?php if ($pendingSvc > 0): ?>
            <div class="alert alert-warning d-flex align-items-center gap-3 mt-3 mb-0 py-2">
                <i class="bi bi-send-fill text-warning fs-5"></i>
                <div class="flex-grow-1 small">
                    <strong><?= $pendingSvc ?> service(s)</strong> are ready to be submitted to the Marketing Officer for campaign creation.
                </div>
                <a href="<?= base_url('/owner/services') ?>" class="btn btn-warning btn-sm text-nowrap">
                    Submit Now
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">

        <!-- Expiring Documents -->
        <?php if (!empty($expiringSoon)): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm border-start border-danger border-4">
                <div class="card-header bg-transparent d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                    <strong>Documents Expiring Within 30 Days</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Expiry Date</th>
                                    <th>Days Left</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expiringSoon as $doc): ?>
                                <?php $daysLeft = (int)ceil((strtotime($doc['expiry_date']) - time()) / 86400); ?>
                                <tr>
                                    <td><?= e($doc['title']) ?></td>
                                    <td><span class="badge bg-secondary"><?= e(ucwords(str_replace('_', ' ', $doc['category']))) ?></span></td>
                                    <td><?= date('M d, Y', strtotime($doc['expiry_date'])) ?></td>
                                    <td>
                                        <span class="badge <?= $daysLeft <= 7 ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                            <?= $daysLeft ?> day<?= $daysLeft !== 1 ? 's' : '' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('/owner/documents/' . $doc['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Active Budget Plans -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-wallet2 text-success"></i>
                        <strong>Active Budget Plans</strong>
                    </div>
                    <a href="<?= base_url('/owner/budgets/create') ?>" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-lg"></i> New
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($activePlans)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-wallet2 fs-2 d-block mb-2"></i>
                            No active budget plans.
                        </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Year</th>
                                    <th>Total Budget</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activePlans as $plan): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('/owner/budgets/' . $plan['id']) ?>" class="text-decoration-none fw-medium">
                                            <?= e($plan['title']) ?>
                                        </a>
                                    </td>
                                    <td><?= e($plan['fiscal_year']) ?></td>
                                    <td class="fw-semibold text-success">₱<?= number_format($plan['total_budget'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-receipt-cutoff text-warning"></i>
                        <strong>Recent Expenses</strong>
                    </div>
                    <a href="<?= base_url('/owner/expenses/create') ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-plus-lg"></i> Record
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentExpenses)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-receipt fs-2 d-block mb-2"></i>
                            No expenses recorded yet.
                        </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentExpenses as $exp): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('/owner/expenses/' . $exp['id']) ?>" class="text-decoration-none">
                                            <?= e($exp['description']) ?>
                                        </a>
                                    </td>
                                    <td class="text-muted small"><?= date('M d', strtotime($exp['expense_date'])) ?></td>
                                    <td class="fw-semibold">₱<?= number_format($exp['amount'], 2) ?></td>
                                    <td>
                                        <?php
                                        $badge = match($exp['status']) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default    => 'warning text-dark',
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= ucfirst($exp['status']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent text-end">
                    <a href="<?= base_url('/owner/expenses') ?>" class="btn btn-sm btn-outline-secondary">View All Expenses</a>
                </div>
            </div>
        </div>

    </div>
</div>
