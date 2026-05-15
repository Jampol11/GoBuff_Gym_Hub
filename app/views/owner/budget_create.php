<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-plus-lg me-2"></i>New Budget Plan</h2>
            <p class="page-subtitle">Create a new budget plan with line items</p>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('/owner/budgets') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('/owner/budgets') ?>" id="budgetForm">
                        <?= csrf_field() ?>

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Budget Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required maxlength="255"
                                       placeholder="e.g. Q1 2026 Operational Budget"
                                       value="<?= e($_POST['title'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Fiscal Year <span class="text-danger">*</span></label>
                                <input type="number" name="fiscal_year" class="form-control" required
                                       min="2020" max="2099" value="<?= e($_POST['fiscal_year'] ?? date('Y')) ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Period</label>
                                <select name="period" class="form-select">
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="semi_annual">Semi-Annual</option>
                                    <option value="annual" selected>Annual</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Period Label</label>
                                <input type="text" name="period_label" class="form-control"
                                       placeholder="e.g. Q1 2026, January 2026"
                                       value="<?= e($_POST['period_label'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Total Budget</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" name="total_budget" class="form-control" step="0.01" min="0"
                                       value="<?= e($_POST['total_budget'] ?? '0') ?>" id="totalBudget" readonly>
                            </div>
                            <div class="form-text">This will be calculated from line items below.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"><?= e($_POST['notes'] ?? '') ?></textarea>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Budget Line Items</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBudgetItem()">
                                <i class="bi bi-plus-lg me-1"></i>Add Item
                            </button>
                        </div>

                        <div id="budgetItems">
                            <div class="row g-2 mb-2 budget-item">
                                <div class="col-md-3">
                                    <?= budgetCategorySelect() ?>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="item_description[]" class="form-control" placeholder="Description" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="item_amount[]" class="form-control item-amount" placeholder="Amount" step="0.01" min="0" value="0" onchange="calculateTotal()">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger w-100" onclick="removeItem(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Create Budget Plan
                            </button>
                            <a href="<?= base_url('/owner/budgets') ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function budgetCategorySelect(string $selected = ''): string {
    $cats = [
        'Rent'               => 'Rent',
        'Utilities'          => 'Utilities',
        'Salaries'           => 'Salaries',
        'Equipment Purchase' => 'Equipment Purchase',
        'Equipment Repair'   => 'Equipment Repair',
        'Supplies'           => 'Supplies',
        'Essentials'         => 'Essentials',
        'Membership'         => 'Membership',
        'Marketing'          => 'Marketing',
        'Insurance'          => 'Insurance',
        'Taxes'              => 'Taxes',
        'Miscellaneous'      => 'Miscellaneous',
    ];
    $isCustom = $selected !== '' && !array_key_exists($selected, $cats);
    $html  = '<select name="item_category[]" class="form-select category-select" onchange="handleCategoryChange(this)" required>';
    $html .= '<option value="">— Select —</option>';
    foreach ($cats as $val => $label) {
        $sel   = ($selected === $val) ? ' selected' : '';
        $html .= "<option value=\"{$val}\"{$sel}>{$label}</option>";
    }
    $html .= '<option value="__custom__"' . ($isCustom ? ' selected' : '') . '>Other (custom)...</option>';
    $html .= '</select>';
    $customVal = $isCustom ? htmlspecialchars($selected, ENT_QUOTES) : '';
    $display   = $isCustom ? '' : 'display:none;';
    $html .= "<input type=\"text\" class=\"form-control mt-1 custom-category\" placeholder=\"Enter custom category\" style=\"{$display}\" value=\"{$customVal}\">";
    return $html;
}
?>

<script>
const BUDGET_CATEGORIES = [
    'Rent','Utilities','Salaries','Equipment Purchase','Equipment Repair',
    'Supplies','Essentials','Membership','Marketing','Insurance','Taxes','Miscellaneous'
];

function buildCategorySelect(selectedValue = '') {
    const isCustom = selectedValue !== '' && !BUDGET_CATEGORIES.includes(selectedValue);
    let html = '<select name="item_category[]" class="form-select category-select" onchange="handleCategoryChange(this)" required>';
    html += '<option value="">— Select —</option>';
    BUDGET_CATEGORIES.forEach(cat => {
        const sel = (cat === selectedValue) ? ' selected' : '';
        html += `<option value="${cat}"${sel}>${cat}</option>`;
    });
    html += `<option value="__custom__"${isCustom ? ' selected' : ''}>Other (custom)...</option>`;
    html += '</select>';
    const customDisplay = isCustom ? '' : 'display:none;';
    const customVal     = isCustom ? selectedValue : '';
    html += `<input type="text" class="form-control mt-1 custom-category" placeholder="Enter custom category" style="${customDisplay}" value="${customVal}">`;
    return html;
}

function handleCategoryChange(select) {
    const customInput = select.nextElementSibling;
    if (select.value === '__custom__') {
        customInput.style.display = '';
        customInput.required = true;
        customInput.focus();
    } else {
        customInput.style.display = 'none';
        customInput.required = false;
        customInput.value = '';
    }
}

function addBudgetItem() {
    const container = document.getElementById('budgetItems');
    const item = document.createElement('div');
    item.className = 'row g-2 mb-2 budget-item';
    item.innerHTML = `
        <div class="col-md-3">
            ${buildCategorySelect()}
        </div>
        <div class="col-md-6">
            <input type="text" name="item_description[]" class="form-control" placeholder="Description" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="item_amount[]" class="form-control item-amount" placeholder="Amount" step="0.01" min="0" value="0" onchange="calculateTotal()">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger w-100" onclick="removeItem(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(item);
}

function removeItem(btn) {
    const items = document.querySelectorAll('.budget-item');
    if (items.length > 1) {
        btn.closest('.budget-item').remove();
        calculateTotal();
    }
}

function calculateTotal() {
    const amounts = document.querySelectorAll('.item-amount');
    let total = 0;
    amounts.forEach(input => { total += parseFloat(input.value) || 0; });
    document.getElementById('totalBudget').value = total.toFixed(2);
}

// Before submit: copy custom input value into the select's value via a hidden input
document.getElementById('budgetForm').addEventListener('submit', function () {
    document.querySelectorAll('.budget-item').forEach(row => {
        const select      = row.querySelector('.category-select');
        const customInput = row.querySelector('.custom-category');
        if (select && select.value === '__custom__' && customInput && customInput.value.trim()) {
            // Replace select name so it won't submit, inject hidden input with custom value
            select.removeAttribute('name');
            const hidden = document.createElement('input');
            hidden.type  = 'hidden';
            hidden.name  = 'item_category[]';
            hidden.value = customInput.value.trim();
            row.appendChild(hidden);
        }
    });
});
</script>
