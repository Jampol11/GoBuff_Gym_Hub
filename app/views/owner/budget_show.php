<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-wallet2 me-2"></i><?= e($plan['title']) ?></h2>
            <p class="page-subtitle">
                Fiscal Year <?= e($plan['fiscal_year']) ?> &bull;
                <?= ucfirst(str_replace('_', '-', $plan['period'])) ?>
                <?= $plan['period_label'] ? '&bull; ' . e($plan['period_label']) : '' ?>
            </p>
        </div>
        <div class="page-actions">
            <?php if ($plan['status'] === 'draft'): ?>
            <form method="POST" action="<?= base_url('/owner/budgets/' . $plan['id'] . '/approve') ?>" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this budget plan?')">
                    <i class="bi bi-check-circle me-1"></i>Approve
                </button>
            </form>
            <?php endif; ?>
            <a href="<?= base_url('/owner/budgets/' . $plan['id'] . '/edit') ?>" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <a href="<?= base_url('/owner/budgets') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <?php if ($utilization['over_budget']): ?>
    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
        <div>
            <strong>Over Budget!</strong>
            This plan has exceeded its total budget by
            <strong>₱<?= number_format(abs($utilization['remaining']), 2) ?></strong>
            (<?= number_format($utilization['raw_pct'], 1) ?>% utilized).
        </div>
    </div>
    <?php elseif ($utilization['raw_pct'] >= 90): ?>
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill fs-5 me-2"></i>
        <div>
            <strong>Near Budget Limit.</strong>
            <?= number_format($utilization['raw_pct'], 1) ?>% of the budget has been spent.
            Only <strong>₱<?= number_format($utilization['remaining'], 2) ?></strong> remaining.
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Left Column: Summary + Utilization -->
        <div class="col-lg-4">

            <!-- Plan Summary -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-1"></i>Plan Summary</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-6 text-muted">Status</dt>
                        <dd class="col-6">
                            <?php
                            $badge = match($plan['status']) {
                                'approved' => 'success',
                                'active'   => 'primary',
                                'closed'   => 'secondary',
                                default    => 'warning text-dark',
                            };
                            ?>
                            <span class="badge bg-<?= $badge ?>"><?= ucfirst($plan['status']) ?></span>
                        </dd>

                        <dt class="col-6 text-muted">Total Budget</dt>
                        <dd class="col-6 fw-bold text-success fs-5">₱<?= number_format($plan['total_budget'], 2) ?></dd>

                        <dt class="col-6 text-muted">Created By</dt>
                        <dd class="col-6"><?= e($plan['creator_name'] ?? '—') ?></dd>

                        <?php if ($plan['approved_by']): ?>
                        <dt class="col-6 text-muted">Approved By</dt>
                        <dd class="col-6"><?= e($plan['approver_name'] ?? '—') ?></dd>
                        <dt class="col-6 text-muted">Approved At</dt>
                        <dd class="col-6"><small><?= format_date($plan['approved_at']) ?></small></dd>
                        <?php endif; ?>

                        <dt class="col-6 text-muted">Created</dt>
                        <dd class="col-6"><small><?= format_date($plan['created_at']) ?></small></dd>
                    </dl>
                    <?php if ($plan['notes']): ?>
                    <hr>
                    <p class="text-muted small mb-0"><strong>Notes:</strong> <?= e($plan['notes']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Budget Utilization Card -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="bi bi-pie-chart-fill me-1"></i>Budget Utilization</h5>
                </div>
                <div class="card-body">
                    <!-- Big numbers -->
                    <div class="row text-center g-2 mb-3">
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <div class="fw-bold text-success fs-6">₱<?= number_format($plan['total_budget'], 2) ?></div>
                                <small class="text-muted">Budget</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <div class="fw-bold text-danger fs-6">₱<?= number_format($utilization['spent'], 2) ?></div>
                                <small class="text-muted">Spent</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light">
                                <div class="fw-bold <?= $utilization['over_budget'] ? 'text-danger' : 'text-primary' ?> fs-6">
                                    <?= $utilization['over_budget'] ? '-' : '' ?>₱<?= number_format(abs($utilization['remaining']), 2) ?>
                                </div>
                                <small class="text-muted"><?= $utilization['over_budget'] ? 'Over' : 'Left' ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <?php
                    $barColor = $utilization['over_budget'] ? 'bg-danger'
                        : ($utilization['raw_pct'] >= 90 ? 'bg-warning'
                        : ($utilization['raw_pct'] >= 70 ? 'bg-info'
                        : 'bg-success'));
                    ?>
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">
                            <?= number_format($utilization['raw_pct'], 1) ?>% used
                        </small>
                        <small class="text-muted">
                            <?= $utilization['over_budget']
                                ? '<span class="text-danger fw-semibold">Over budget</span>'
                                : number_format(100 - $utilization['raw_pct'], 1) . '% free' ?>
                        </small>
                    </div>
                    <div class="progress mb-0" style="height: 12px;" title="<?= number_format($utilization['raw_pct'], 1) ?>% utilized">
                        <div class="progress-bar <?= $barColor ?>"
                             role="progressbar"
                             style="width: <?= $utilization['pct'] ?>%"
                             aria-valuenow="<?= $utilization['pct'] ?>"
                             aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle me-1"></i>Only <strong>approved</strong> expenses are counted.
                    </small>
                </div>
            </div>

            <!-- Pending expenses notice -->
            <?php
            $pendingExpenses = array_filter($expenses, fn($e) => $e['status'] === 'pending');
            $pendingTotal    = array_sum(array_column($pendingExpenses, 'amount'));
            if ($pendingTotal > 0):
                $projectedSpent     = $utilization['spent'] + $pendingTotal;
                $projectedRemaining = $plan['total_budget'] - $projectedSpent;
            ?>
            <div class="card shadow-sm border-warning mb-3">
                <div class="card-body py-2 px-3">
                    <small class="text-warning fw-semibold"><i class="bi bi-clock-history me-1"></i>Pending Expenses</small>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted"><?= count($pendingExpenses) ?> pending</small>
                        <small class="fw-semibold">₱<?= number_format($pendingTotal, 2) ?></small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">If approved, remaining:</small>
                        <small class="fw-semibold <?= $projectedRemaining < 0 ? 'text-danger' : 'text-muted' ?>">
                            <?= $projectedRemaining < 0 ? '-' : '' ?>₱<?= number_format(abs($projectedRemaining), 2) ?>
                        </small>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Right Column: Line Items + Expenses -->
        <div class="col-lg-8">

            <!-- Budget Line Items with per-item spending -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-check me-1"></i>Budget Line Items</h5>
                    <span class="badge bg-primary"><?= count($plan['items']) ?> items</span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($plan['items'])): ?>
                        <div class="text-center text-muted py-4">No line items defined.</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th class="text-end">Allocated</th>
                                    <th class="text-end">Spent</th>
                                    <th style="min-width:100px">Usage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalAllocated = 0;
                                $totalItemSpent = 0;
                                foreach ($plan['items'] as $item):
                                    $cat          = $item['category'];
                                    $itemSpent    = $spentByCategory[$cat] ?? 0;
                                    $itemPct      = $item['allocated'] > 0
                                                    ? min(100, ($itemSpent / $item['allocated']) * 100)
                                                    : 0;
                                    $itemOver     = $item['allocated'] > 0 && $itemSpent > $item['allocated'];
                                    $itemBarColor = $itemOver ? 'bg-danger'
                                                  : ($itemPct >= 90 ? 'bg-warning'
                                                  : ($itemPct >= 70 ? 'bg-info' : 'bg-success'));
                                    $totalAllocated += $item['allocated'];
                                    $totalItemSpent += $itemSpent;
                                ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?= e($cat) ?></span></td>
                                    <td><?= e($item['description']) ?></td>
                                    <td class="text-end fw-semibold">₱<?= number_format($item['allocated'], 2) ?></td>
                                    <td class="text-end <?= $itemOver ? 'text-danger fw-bold' : 'text-muted' ?>">
                                        ₱<?= number_format($itemSpent, 2) ?>
                                    </td>
                                    <td>
                                        <div class="progress" style="height:8px;" title="<?= number_format($itemPct, 1) ?>%">
                                            <div class="progress-bar <?= $itemBarColor ?>"
                                                 style="width:<?= $itemPct ?>%"></div>
                                        </div>
                                        <small class="text-muted"><?= number_format($itemPct, 0) ?>%</small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="fw-bold text-end">Total</td>
                                    <td class="text-end fw-bold text-success">₱<?= number_format($totalAllocated, 2) ?></td>
                                    <td class="text-end fw-bold text-danger">₱<?= number_format($totalItemSpent, 2) ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Linked Expenses -->
            <div class="card shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-receipt me-1"></i>Linked Expenses</h5>
                    <a href="<?= base_url('/owner/expenses/create') ?>" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-plus-lg me-1"></i>Record Expense
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($expenses)): ?>
                        <div class="text-center text-muted py-4">No expenses linked to this plan yet.</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th class="text-end">Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expenses as $exp): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('/owner/expenses/' . $exp['id']) ?>" class="text-decoration-none">
                                            <?= e($exp['description']) ?>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= e(ucwords(str_replace('_', ' ', $exp['category']))) ?></span></td>
                                    <td><small><?= format_date($exp['expense_date']) ?></small></td>
                                    <td class="text-end fw-semibold <?= $exp['status'] === 'rejected' ? 'text-decoration-line-through text-muted' : '' ?>">
                                        ₱<?= number_format($exp['amount'], 2) ?>
                                    </td>
                                    <td>
                                        <?php
                                        $expBadge = match($exp['status']) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default    => 'warning text-dark',
                                        };
                                        ?>
                                        <span class="badge bg-<?= $expBadge ?>"><?= ucfirst($exp['status']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="fw-bold text-end text-muted small">Approved total</td>
                                    <td class="text-end fw-bold text-danger">₱<?= number_format($utilization['spent'], 2) ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
