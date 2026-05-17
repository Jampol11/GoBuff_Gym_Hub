<?php
/**
 * Member: My Membership — view status & pay via PayMongo
 */
$planMeta = [
    'monthly'     => ['icon' => 'bi-calendar-month',  'color' => 'primary',  'duration' => '1 Month'],
    'quarterly'   => ['icon' => 'bi-calendar3',        'color' => 'success',  'duration' => '3 Months'],
    'semi_annual' => ['icon' => 'bi-calendar-range',   'color' => 'warning',  'duration' => '6 Months'],
    'annual'      => ['icon' => 'bi-calendar-check',   'color' => 'danger',   'duration' => '12 Months'],
];
?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <!-- Header -->
            <div class="d-flex align-items-center mb-4 gap-3">
                <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                    <i class="bi bi-card-checklist text-primary fs-4"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold">My Membership</h4>
                    <p class="text-muted mb-0 small">Manage your GoBuff membership plan.</p>
                </div>
            </div>

            <?php if ($activeMembership): ?>
            <!-- ── ACTIVE ──────────────────────────────────────────────── -->
            <div class="card border-0 shadow-sm border-start border-success border-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>Active Membership
                        </h6>
                        <span class="badge bg-success fs-6 px-3">Active</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Membership ID</div>
                            <div class="fw-bold fs-5 font-monospace text-primary"><?= e($member['membership_id']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Plan</div>
                            <div class="fw-semibold"><?= e($activeMembership['plan_name']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Start Date</div>
                            <div><?= date('F d, Y', strtotime($activeMembership['start_date'])) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Expiry Date</div>
                            <?php
                            $daysLeft = (int)ceil((strtotime($activeMembership['expiry_date']) - time()) / 86400);
                            $expClass = $daysLeft <= 7 ? 'danger' : ($daysLeft <= 30 ? 'warning' : 'success');
                            ?>
                            <div>
                                <?= date('F d, Y', strtotime($activeMembership['expiry_date'])) ?>
                                <span class="badge bg-<?= $expClass ?> ms-1">
                                    <?= $daysLeft ?> day<?= $daysLeft !== 1 ? 's' : '' ?> left
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Amount Paid</div>
                            <div class="fw-semibold text-success">₱<?= number_format($activeMembership['amount'], 2) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php elseif ($pendingMembership): ?>
            <!-- ── PENDING (payment processing) ───────────────────────── -->
            <div class="card border-0 shadow-sm border-start border-warning border-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-hourglass-split text-warning me-2"></i>Payment Processing
                        </h6>
                        <span class="badge bg-warning text-dark fs-6 px-3">Pending</span>
                    </div>
                    <p class="text-muted mb-3">
                        Your payment is being processed. Your membership will be activated automatically
                        once the payment is confirmed by PayMongo.
                    </p>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Plan</div>
                            <div class="fw-semibold"><?= e($pendingMembership['plan_name']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Amount</div>
                            <div class="fw-semibold">₱<?= number_format($pendingMembership['amount'], 2) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Initiated</div>
                            <div><?= date('F d, Y g:i A', strtotime($pendingMembership['created_at'])) ?></div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0 py-2 small">
                        <i class="bi bi-info-circle me-1"></i>
                        If you completed payment but this still shows pending, please wait a few minutes
                        or contact the office.
                    </div>
                </div>
            </div>

            <?php else: ?>
            <!-- ── NO MEMBERSHIP: Choose plan & pay ───────────────────── -->
            <div class="alert alert-info d-flex align-items-start gap-3 mb-4 py-3">
                <i class="bi bi-lightning-charge-fill fs-5 mt-1 text-info"></i>
                <div>
                    <strong>Your application was approved!</strong>
                    Choose a plan below and complete payment via PayMongo to activate your membership instantly.
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-credit-card-2-front-fill me-2 text-primary"></i>Choose a Plan & Pay
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('/my-membership/checkout') ?>" id="checkoutForm">
                        <?= csrf_field() ?>

                        <!-- Plan Cards -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-3">Select Membership Plan <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <?php foreach ($plans as $key => [$planName, $planType, $months, $centavos]): ?>
                                <?php
                                    $amount = $centavos / 100;
                                    $meta   = $planMeta[$key];
                                    $isPref = ($member['plan_preference'] ?? '') === $key;
                                ?>
                                <div class="col-sm-6">
                                    <label class="d-block h-100">
                                        <input type="radio" name="plan_key" value="<?= $key ?>"
                                               class="d-none plan-radio"
                                               <?= $isPref ? 'checked' : '' ?> required>
                                        <div class="card border-2 h-100 plan-card <?= $isPref ? 'border-primary selected' : 'border-light' ?>
                                                    position-relative overflow-hidden">
                                            <?php if ($isPref): ?>
                                            <span class="position-absolute top-0 end-0 badge bg-primary rounded-0 rounded-bottom-start px-2 py-1" style="font-size:.7rem">
                                                <i class="bi bi-star-fill me-1"></i>Preferred
                                            </span>
                                            <?php endif; ?>
                                            <div class="card-body text-center p-3">
                                                <i class="bi <?= $meta['icon'] ?> fs-2 text-<?= $meta['color'] ?> mb-2 d-block"></i>
                                                <div class="fw-bold"><?= $planName ?></div>
                                                <div class="text-muted small mb-2"><?= $meta['duration'] ?></div>
                                                <div class="fw-bold fs-5 text-<?= $meta['color'] ?>">
                                                    ₱<?= number_format($amount, 0) ?>
                                                </div>
                                                <div class="text-muted" style="font-size:.72rem">
                                                    ₱<?= number_format($amount / $months, 0) ?>/mo
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- PayMongo badge -->
                        <div class="d-flex align-items-center gap-2 mb-4 p-3 bg-light rounded-3">
                            <i class="bi bi-shield-lock-fill text-success fs-5"></i>
                            <div class="small">
                                <strong>Secure payment powered by PayMongo.</strong><br>
                                Accepts GCash, Maya, credit/debit cards, and online banking.
                                You will be redirected to the PayMongo checkout page.
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="payBtn">
                                <i class="bi bi-credit-card-2-front me-2"></i>Proceed to Payment
                            </button>
                            <a href="<?= base_url('/dashboard') ?>" class="btn btn-outline-secondary">
                                Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Membership History -->
            <?php if (!empty($allMemberships)): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2 text-secondary"></i>Membership History
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Plan</th><th>Start</th><th>Expiry</th>
                                    <th>Amount</th><th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allMemberships as $ms): ?>
                                <tr>
                                    <td class="fw-semibold"><?= e($ms['plan_name']) ?></td>
                                    <td class="text-muted small">
                                        <?= $ms['start_date'] ? date('M d, Y', strtotime($ms['start_date'])) : '—' ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= $ms['expiry_date'] ? date('M d, Y', strtotime($ms['expiry_date'])) : '—' ?>
                                    </td>
                                    <td>₱<?= number_format($ms['amount'], 2) ?></td>
                                    <td><?= status_badge($ms['status']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
.plan-card {
    cursor: pointer;
    transition: border-color .15s, box-shadow .15s, transform .1s;
}
.plan-card:hover {
    border-color: #86b7fe !important;
    transform: translateY(-2px);
}
.plan-card.selected,
.plan-radio:checked + .plan-card {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13,110,253,.18);
    background: rgba(13,110,253,.03);
}
</style>

<script>
document.querySelectorAll('.plan-radio').forEach(function (radio) {
    radio.addEventListener('change', function () {
        document.querySelectorAll('.plan-card').forEach(function (c) {
            c.classList.remove('selected');
        });
        if (this.checked) {
            this.nextElementSibling.classList.add('selected');
        }
    });
});

document.getElementById('checkoutForm')?.addEventListener('submit', function () {
    var btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Redirecting to PayMongo…';
});
</script>
