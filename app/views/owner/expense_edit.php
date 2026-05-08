<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-pencil me-2"></i>Edit Expense</h2>
            <p class="page-subtitle"><?= e($expense['description']) ?></p>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('/owner/expenses/' . $expense['id']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('/owner/expenses/' . $expense['id'] . '/update') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                            <input type="text" name="description" class="form-control" required maxlength="255"
                                   value="<?= e($_POST['description'] ?? $expense['description']) ?>">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <?php
                                    $categories = [
                                        'rent'               => 'Rent',
                                        'utilities'          => 'Utilities',
                                        'salaries'           => 'Salaries',
                                        'equipment_purchase' => 'Equipment Purchase',
                                        'equipment_repair'   => 'Equipment Repair',
                                        'supplies'           => 'Supplies',
                                        'marketing'          => 'Marketing',
                                        'insurance'          => 'Insurance',
                                        'taxes'              => 'Taxes',
                                        'miscellaneous'      => 'Miscellaneous',
                                    ];
                                    $current = $_POST['category'] ?? $expense['category'];
                                    foreach ($categories as $val => $label):
                                    ?>
                                    <option value="<?= $val ?>" <?= $current === $val ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" name="amount" class="form-control" required
                                           step="0.01" min="0"
                                           value="<?= e($_POST['amount'] ?? $expense['amount']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" class="form-control" required
                                       value="<?= e($_POST['expense_date'] ?? $expense['expense_date']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Payment Method</label>
                                <select name="payment_method" class="form-select">
                                    <?php
                                    $methods = [
                                        'cash'          => 'Cash',
                                        'bank_transfer' => 'Bank Transfer',
                                        'check'         => 'Check',
                                        'credit_card'   => 'Credit Card',
                                        'gcash'         => 'GCash',
                                        'other'         => 'Other',
                                    ];
                                    $currentMethod = $_POST['payment_method'] ?? $expense['payment_method'];
                                    foreach ($methods as $val => $label):
                                    ?>
                                    <option value="<?= $val ?>" <?= $currentMethod === $val ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Reference No.</label>
                                <input type="text" name="reference_no" class="form-control"
                                       value="<?= e($_POST['reference_no'] ?? $expense['reference_no'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Link to Budget Plan</label>
                                <select name="budget_plan_id" class="form-select">
                                    <option value="">— None —</option>
                                    <?php foreach ($budgetPlans as $plan): ?>
                                    <option value="<?= $plan['id'] ?>"
                                        <?= (($_POST['budget_plan_id'] ?? $expense['budget_plan_id']) == $plan['id']) ? 'selected' : '' ?>>
                                        <?= e($plan['title']) ?> (<?= e($plan['fiscal_year']) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"><?= e($_POST['notes'] ?? $expense['notes'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Replace Receipt</label>
                            <?php if (!empty($expense['receipt_file'])): ?>
                            <div class="alert alert-light border d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-file-earmark-fill text-secondary"></i>
                                <div>Current receipt: <strong><?= e($expense['receipt_file']) ?></strong></div>
                            </div>
                            <?php endif; ?>
                            <input type="file" name="receipt" class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png,.webp">
                            <div class="form-text">
                                Upload a new file to replace the current receipt. Leave empty to keep the existing one.
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Save Changes
                            </button>
                            <a href="<?= base_url('/owner/expenses/' . $expense['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
