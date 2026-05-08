<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-receipt-cutoff me-2"></i>Operational Expenses</h2>
            <p class="page-subtitle">Track and manage gym operational expenses</p>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('/owner/expenses/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Record Expense
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-warning bg-opacity-10 p-3">
                            <i class="bi bi-calendar-range fs-3 text-warning"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold">₱<?= number_format($totalThisYear, 2) ?></div>
                            <div class="text-muted small">Total This Year</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3">Expenses by Category (This Year)</h6>
                    <div class="row g-2">
                        <?php foreach (array_slice($byCategory, 0, 4) as $cat): ?>
                        <div class="col-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-secondary"><?= e(ucwords(str_replace('_', ' ', $cat['category']))) ?></span>
                                <span class="fw-semibold">₱<?= number_format($cat['total'], 2) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" action="<?= base_url('/owner/expenses') ?>" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Search by description, category, or reference..."
                               value="<?= e($search) ?>">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search): ?>
                        <a href="<?= base_url('/owner/expenses') ?>" class="btn btn-outline-secondary">Clear</a>
                    <?php endif; ?>
                </div>
                <div class="col-auto ms-auto">
                    <span class="text-muted small"><?= number_format($pagination['total']) ?> expense(s)</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Budget Plan</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                                    No expenses recorded yet
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenses as $i => $exp): ?>
                            <tr>
                                <td><?= $pagination['offset'] + $i + 1 ?></td>
                                <td>
                                    <div class="fw-semibold"><?= e($exp['description']) ?></div>
                                    <?php if ($exp['reference_no']): ?>
                                        <small class="text-muted">Ref: <?= e($exp['reference_no']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-secondary"><?= e(ucwords(str_replace('_', ' ', $exp['category']))) ?></span></td>
                                <td><small><?= date('M d, Y', strtotime($exp['expense_date'])) ?></small></td>
                                <td class="fw-semibold">₱<?= number_format($exp['amount'], 2) ?></td>
                                <td><small class="text-muted"><?= e(ucwords(str_replace('_', ' ', $exp['payment_method']))) ?></small></td>
                                <td>
                                    <?php if ($exp['budget_plan_title']): ?>
                                        <small><?= e($exp['budget_plan_title']) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
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
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('/owner/expenses/' . $exp['id']) ?>"
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('/owner/expenses/' . $exp['id'] . '/edit') ?>"
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="<?= base_url('/owner/expenses/' . $exp['id'] . '/delete') ?>"
                                              class="d-inline" onsubmit="return confirm('Delete this expense?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?= $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?>
                of <?= $pagination['total'] ?> expenses
            </small>
            <?= pagination_links($pagination, base_url('/owner/expenses')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
