<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-wallet2 me-2"></i><?= e($plan['title']) ?></h2>
            <p class="page-subtitle">Fiscal Year <?= e($plan['fiscal_year']) ?> &bull; <?= ucfirst(str_replace('_', '-', $plan['period'])) ?>
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

    <div class="row g-4">
        <!-- Plan Details -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Plan Summary</h5>
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

            <!-- Expense Summary -->
            <?php
            $totalExpenses = array_sum(array_column($expenses, 'amount'));
            $remaining = $plan['total_budget'] - $totalExpenses;
            $pct = $plan['total_budget'] > 0 ? min(100, ($totalExpenses / $plan['total_budget']) * 100) : 0;
            ?>
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Budget Utilization</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Spent</small>
                        <small class="fw-semibold">₱<?= number_format($totalExpenses, 2) ?></small>
                    </div>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar <?= $pct >= 90 ? 'bg-danger' : ($pct >= 70 ? 'bg-warning' : 'bg-success') ?>"
                             style="width: <?= $pct ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Remaining</span>
                        <span class="fw-bold <?= $remaining < 0 ? 'text-danger' : 'text-success' ?>">
                            ₱<?= number_format($remaining, 2) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Items -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Budget Line Items</h5>
                    <span class="badge bg-primary"><?= count($plan['items']) ?> items</span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($plan['items'])): ?>
                        <div class="text-center text-muted py-4">No line items defined.</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th class="text-end">Allocated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($plan['items'] as $item): ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?= e($item['category']) ?></span></td>
                                    <td><?= e($item['description']) ?></td>
                                    <td class="text-end fw-semibold">₱<?= number_format($item['allocated'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="fw-bold text-end">Total Allocated</td>
                                    <td class="text-end fw-bold text-success">
                                        ₱<?= number_format(array_sum(array_column($plan['items'], 'allocated')), 2) ?>
                                    </td>
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
                    <h5 class="mb-0">Linked Expenses</h5>
                    <a href="<?= base_url('/owner/expenses/create') ?>" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-plus-lg me-1"></i>Record Expense
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($expenses)): ?>
                        <div class="text-center text-muted py-4">No expenses linked to this plan.</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
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
                                    <td><small><?= date('M d, Y', strtotime($exp['expense_date'])) ?></small></td>
                                    <td class="text-end fw-semibold">₱<?= number_format($exp['amount'], 2) ?></td>
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
            </div>
        </div>
    </div>
</div>
