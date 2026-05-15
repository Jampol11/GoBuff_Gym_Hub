<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-plus-lg me-2"></i>Record Expense</h2>
            <p class="page-subtitle">Log a new operational expense</p>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('/owner/expenses') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('/owner/expenses') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                            <input type="text" name="description" class="form-control" required maxlength="255"
                                   placeholder="e.g. Monthly electricity bill"
                                   value="<?= e($_POST['description'] ?? '') ?>">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category" id="categorySelect" class="form-select" required>
                                    <?php
                                    $defaultCategories = [
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
                                    $current = $_POST['category'] ?? 'miscellaneous';
                                    foreach ($defaultCategories as $val => $label):
                                    ?>
                                    <option value="<?= $val ?>" <?= $current === $val ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text" id="categoryHint"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" name="amount" class="form-control" required
                                           step="0.01" min="0" placeholder="0.00"
                                           value="<?= e($_POST['amount'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" class="form-control" required
                                       value="<?= e($_POST['expense_date'] ?? date('Y-m-d')) ?>">
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
                                    $currentMethod = $_POST['payment_method'] ?? 'cash';
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
                                       placeholder="e.g. OR-2026-001"
                                       value="<?= e($_POST['reference_no'] ?? '') ?>">
                                <div class="form-text">Official receipt or transaction reference number.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Link to Budget Plan</label>
                                <select name="budget_plan_id" id="budgetPlanSelect" class="form-select">
                                    <option value="">— None —</option>
                                    <?php foreach ($budgetPlans as $plan): ?>
                                    <option value="<?= $plan['id'] ?>"
                                        <?= (($_POST['budget_plan_id'] ?? '') == $plan['id']) ? 'selected' : '' ?>>
                                        <?= e($plan['title']) ?> (<?= e($plan['fiscal_year']) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Optional additional notes..."><?= e($_POST['notes'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Receipt / Proof of Payment</label>
                            <input type="file" name="receipt" class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png,.webp">
                            <div class="form-text">
                                Optional. Accepted formats: PDF, JPG, PNG, WEBP. Maximum size: 5 MB.
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Record Expense
                            </button>
                            <a href="<?= base_url('/owner/expenses') ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const planCategories = <?= json_encode($planCategories) ?>;
    const defaultCategories = {
        'rent':               'Rent',
        'utilities':          'Utilities',
        'salaries':           'Salaries',
        'equipment_purchase': 'Equipment Purchase',
        'equipment_repair':   'Equipment Repair',
        'supplies':           'Supplies',
        'marketing':          'Marketing',
        'insurance':          'Insurance',
        'taxes':              'Taxes',
        'miscellaneous':      'Miscellaneous',
    };

    const planSelect     = document.getElementById('budgetPlanSelect');
    const categorySelect = document.getElementById('categorySelect');
    const categoryHint   = document.getElementById('categoryHint');

    function rebuildCategories(planId, selectedValue) {
        categorySelect.innerHTML = '';

        if (planId && planCategories[planId] && planCategories[planId].length > 0) {
            // Use budget plan line item categories
            planCategories[planId].forEach(function (cat) {
                const opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                if (cat === selectedValue) opt.selected = true;
                categorySelect.appendChild(opt);
            });
            // Also append default categories as fallback options
            Object.entries(defaultCategories).forEach(function ([val, label]) {
                const opt = document.createElement('option');
                opt.value = val;
                opt.textContent = label + ' (general)';
                if (val === selectedValue) opt.selected = true;
                categorySelect.appendChild(opt);
            });
            categoryHint.textContent = 'Plan categories shown first — select one to track against the budget line item.';
            categoryHint.className = 'form-text text-primary';
        } else {
            // Use default categories
            Object.entries(defaultCategories).forEach(function ([val, label]) {
                const opt = document.createElement('option');
                opt.value = val;
                opt.textContent = label;
                if (val === selectedValue) opt.selected = true;
                categorySelect.appendChild(opt);
            });
            categoryHint.textContent = '';
        }
    }

    // Init on page load
    rebuildCategories(planSelect.value, <?= json_encode($_POST['category'] ?? 'miscellaneous') ?>);

    // Update on plan change
    planSelect.addEventListener('change', function () {
        rebuildCategories(this.value, null);
    });
})();
</script>
