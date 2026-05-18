<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">My Campaigns</h2>
            <p class="page-subtitle">Campaigns you've joined and your referral codes</p>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('/dashboard') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <?php if (empty($my_campaigns)): ?>
    <div class="card shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-megaphone fs-1 d-block mb-3 text-secondary"></i>
            <h5>No campaigns joined yet</h5>
            <p class="mb-4">Check the dashboard for active promotions you can join.</p>
            <a href="<?= base_url('/dashboard') ?>" class="btn btn-primary">
                <i class="bi bi-house me-1"></i>Go to Dashboard
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach ($my_campaigns as $c): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0"><?= e($c['title']) ?></h6>
                        <?= status_badge($c['campaign_status']) ?>
                    </div>
                    <?php if (!empty($c['description'])): ?>
                    <p class="text-muted small mb-3"><?= e($c['description']) ?></p>
                    <?php endif; ?>

                    <div class="text-muted small mb-3">
                        <i class="bi bi-calendar-range me-1"></i>
                        <?= format_date($c['start_date']) ?> &ndash; <?= format_date($c['end_date']) ?>
                    </div>

                    <?php if ($c['discount_pct'] > 0): ?>
                    <div class="mb-3">
                        <span class="badge bg-danger"><?= number_format($c['discount_pct'], 2) ?>% OFF</span>
                    </div>
                    <?php endif; ?>

                    <!-- Referral Code Box -->
                    <?php if (!empty($c['referral_code'])): ?>
                    <div class="bg-light rounded p-3 mb-3 text-center">
                        <div class="text-muted small mb-1">Your Referral Code</div>
                        <div class="fw-bold fs-5 font-monospace" id="code-<?= $c['id'] ?>"><?= e($c['referral_code']) ?></div>
                        <button class="btn btn-sm btn-outline-primary mt-2"
                                onclick="copyCode('code-<?= $c['id'] ?>', this)">
                            <i class="bi bi-clipboard me-1"></i>Copy
                        </button>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($c['referred_by_name'])): ?>
                    <div class="text-muted small mb-2">
                        <i class="bi bi-person-fill me-1"></i>Referred by <strong><?= e($c['referred_by_name']) ?></strong>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted">Joined <?= format_date($c['joined_at']) ?></small>
                        <?php
                        $rewardMap = ['pending' => 'warning text-dark', 'applied' => 'success', 'expired' => 'secondary'];
                        $rewardColor = $rewardMap[$c['reward_status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $rewardColor ?>">
                            <i class="bi bi-gift-fill me-1"></i><?= ucfirst($c['reward_status']) ?>
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="<?= base_url('/campaigns/' . $c['campaign_id']) ?>" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-eye me-1"></i>View Campaign
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function copyCode(elemId, btn) {
    const code = document.getElementById(elemId)?.innerText;
    if (!code) return;
    navigator.clipboard.writeText(code).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copied!';
        btn.classList.replace('btn-outline-primary', 'btn-primary');
        setTimeout(() => { btn.innerHTML = orig; btn.classList.replace('btn-primary', 'btn-outline-primary'); }, 2000);
    });
}
</script>
