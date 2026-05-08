<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-receipt me-2"></i>Expense Details</h2>
            <p class="page-subtitle"><?= e($expense['description']) ?></p>
        </div>
        <div class="page-actions">
            <?php if ($expense['status'] === 'pending'): ?>
            <form method="POST" action="<?= base_url('/owner/expenses/' . $expense['id'] . '/approve') ?>" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success"
                        onclick="return confirm('Approve this expense?')">
                    <i class="bi bi-check-circle me-1"></i>Approve
                </button>
            </form>
            <form method="POST" action="<?= base_url('/owner/expenses/' . $expense['id'] . '/reject') ?>" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Reject this expense?')">
                    <i class="bi bi-x-circle me-1"></i>Reject
                </button>
            </form>
            <?php endif; ?>
            <a href="<?= base_url('/owner/expenses/' . $expense['id'] . '/edit') ?>" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <a href="<?= base_url('/owner/expenses') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Expense Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Expense Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Description</dt>
                        <dd class="col-sm-8 fw-semibold"><?= e($expense['description']) ?></dd>

                        <dt class="col-sm-4 text-muted">Category</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-secondary fs-6">
                                <?= e(ucwords(str_replace('_', ' ', $expense['category']))) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-4 text-muted">Amount</dt>
                        <dd class="col-sm-8 fw-bold fs-5 text-success">
                            ₱<?= number_format($expense['amount'], 2) ?>
                        </dd>

                        <dt class="col-sm-4 text-muted">Expense Date</dt>
                        <dd class="col-sm-8"><?= date('F d, Y', strtotime($expense['expense_date'])) ?></dd>

                        <dt class="col-sm-4 text-muted">Payment Method</dt>
                        <dd class="col-sm-8"><?= e(ucwords(str_replace('_', ' ', $expense['payment_method']))) ?></dd>

                        <dt class="col-sm-4 text-muted">Reference No.</dt>
                        <dd class="col-sm-8">
                            <?= $expense['reference_no'] ? e($expense['reference_no']) : '<span class="text-muted">—</span>' ?>
                        </dd>

                        <dt class="col-sm-4 text-muted">Budget Plan</dt>
                        <dd class="col-sm-8">
                            <?php if (!empty($expense['budget_plan_id'])): ?>
                                <a href="<?= base_url('/owner/budgets/' . $expense['budget_plan_id']) ?>" class="text-decoration-none">
                                    <?= e($expense['budget_plan_title'] ?? 'View Plan') ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Not linked</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-4 text-muted">Status</dt>
                        <dd class="col-sm-8">
                            <?php
                            $badge = match($expense['status']) {
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default    => 'warning text-dark',
                            };
                            ?>
                            <span class="badge bg-<?= $badge ?> fs-6"><?= ucfirst($expense['status']) ?></span>
                        </dd>

                        <?php if ($expense['approved_by']): ?>
                        <dt class="col-sm-4 text-muted">
                            <?= $expense['status'] === 'rejected' ? 'Rejected By' : 'Approved By' ?>
                        </dt>
                        <dd class="col-sm-8"><?= e($expense['approver_name'] ?? '—') ?></dd>

                        <dt class="col-sm-4 text-muted">
                            <?= $expense['status'] === 'rejected' ? 'Rejected At' : 'Approved At' ?>
                        </dt>
                        <dd class="col-sm-8"><small><?= format_date($expense['approved_at']) ?></small></dd>
                        <?php endif; ?>

                        <?php if ($expense['notes']): ?>
                        <dt class="col-sm-4 text-muted">Notes</dt>
                        <dd class="col-sm-8"><?= e($expense['notes']) ?></dd>
                        <?php endif; ?>

                        <dt class="col-sm-4 text-muted">Recorded</dt>
                        <dd class="col-sm-8"><small><?= format_date($expense['created_at']) ?></small></dd>

                        <dt class="col-sm-4 text-muted">Last Updated</dt>
                        <dd class="col-sm-8"><small><?= format_date($expense['updated_at']) ?></small></dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Sidebar: Receipt & Actions -->
        <div class="col-lg-4">
            <!-- Receipt -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Receipt</h5>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($expense['receipt_file'])): ?>
                        <?php
                        $ext  = strtolower(pathinfo($expense['receipt_file'], PATHINFO_EXTENSION));
                        $icon = match($ext) {
                            'pdf'  => 'bi-file-earmark-pdf-fill text-danger',
                            'jpg', 'jpeg', 'png', 'webp' => 'bi-file-earmark-image-fill text-success',
                            default => 'bi-file-earmark-fill text-secondary',
                        };
                        ?>
                        <i class="bi <?= $icon ?> display-3 mb-3"></i>
                        <p class="text-muted small mb-3"><?= e($expense['receipt_file']) ?></p>
                        <a href="<?= base_url('/assets/uploads/documents/' . $expense['receipt_file']) ?>"
                           target="_blank" class="btn btn-outline-primary w-100">
                            <i class="bi bi-eye me-1"></i>View Receipt
                        </a>
                    <?php else: ?>
                        <i class="bi bi-file-earmark-x display-3 text-muted mb-3"></i>
                        <p class="text-muted small">No receipt uploaded.</p>
                        <a href="<?= base_url('/owner/expenses/' . $expense['id'] . '/edit') ?>"
                           class="btn btn-outline-secondary w-100 btn-sm">
                            <i class="bi bi-upload me-1"></i>Upload Receipt
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <?php if ($expense['status'] === 'pending'): ?>
                    <form method="POST" action="<?= base_url('/owner/expenses/' . $expense['id'] . '/approve') ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Approve this expense?')">
                            <i class="bi bi-check-circle me-1"></i>Approve Expense
                        </button>
                    </form>
                    <form method="POST" action="<?= base_url('/owner/expenses/' . $expense['id'] . '/reject') ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger w-100"
                                onclick="return confirm('Reject this expense?')">
                            <i class="bi bi-x-circle me-1"></i>Reject Expense
                        </button>
                    </form>
                    <?php endif; ?>
                    <a href="<?= base_url('/owner/expenses/' . $expense['id'] . '/edit') ?>" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Edit Expense
                    </a>
                    <form method="POST" action="<?= base_url('/owner/expenses/' . $expense['id'] . '/delete') ?>"
                          onsubmit="return confirm('Permanently delete this expense?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-1"></i>Delete Expense
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
