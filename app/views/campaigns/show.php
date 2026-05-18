<?php
$isGuest  = !Auth::check();
$userRole = Auth::role();
$isStaff  = in_array($userRole, ['gym_owner', 'admin', 'marketing']);
$isMember = $userRole === 'member';
$isUser   = $userRole === 'user';
?>
<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title"><?= e($campaign['title']) ?></h2></div>
        <div class="page-actions d-flex gap-2">
            <?php if ($isStaff): ?>
            <a href="<?= base_url('/campaigns/' . $campaign['id'] . '/edit') ?>" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <?php endif; ?>
            <?php if ($isGuest || $isUser): ?>
            <a href="<?= base_url('/dashboard') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
            <?php else: ?>
            <a href="<?= base_url($isMember ? '/dashboard' : '/campaigns') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Campaign Detail Card -->
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <?php if (!empty($campaign['banner_image'])): ?>
                    <img src="<?= asset('uploads/campaigns/' . $campaign['banner_image']) ?>"
                         class="card-img-top" style="max-height:320px;object-fit:cover"
                         alt="<?= e($campaign['title']) ?>">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center bg-gradient"
                         style="height:180px;background:linear-gradient(135deg,#0d6efd22,#19875422)">
                        <i class="bi bi-megaphone-fill text-primary" style="font-size:4rem;opacity:.4"></i>
                    </div>
                <?php endif; ?>

                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h4 class="fw-bold mb-0"><?= e($campaign['title']) ?></h4>
                        <?= status_badge($campaign['status']) ?>
                    </div>

                    <?php if (!empty($campaign['description'])): ?>
                    <p class="text-muted mb-3"><?= nl2br(e($campaign['description'])) ?></p>
                    <?php endif; ?>

                    <!-- Campaign Meta -->
                    <dl class="row mb-3">
                        <dt class="col-5 text-muted small">Target Audience</dt>
                        <dd class="col-7 small"><?= e($campaign['target_audience'] ?? 'General Public') ?></dd>

                        <dt class="col-5 text-muted small">Campaign Period</dt>
                        <dd class="col-7 small">
                            <?= format_date($campaign['start_date']) ?> &ndash; <?= format_date($campaign['end_date']) ?>
                        </dd>

                        <?php if ($campaign['discount_pct'] > 0): ?>
                        <dt class="col-5 text-muted small">Discount</dt>
                        <dd class="col-7">
                            <span class="badge bg-danger fs-6"><?= number_format($campaign['discount_pct'], 2) ?>% OFF</span>
                        </dd>
                        <?php endif; ?>

                        <?php if ($isStaff && $campaign['budget'] > 0): ?>
                        <dt class="col-5 text-muted small">Budget</dt>
                        <dd class="col-7 small"><?= format_currency($campaign['budget']) ?></dd>
                        <?php endif; ?>

                        <?php if (!empty($campaign['size']) || !empty($campaign['theme'])): ?>
                        <?php if (!empty($campaign['size'])): ?>
                        <dt class="col-5 text-muted small">Size / Format</dt>
                        <dd class="col-7 small"><?= e(ucwords($campaign['size'])) ?></dd>
                        <?php endif; ?>
                        <?php if (!empty($campaign['theme'])): ?>
                        <dt class="col-5 text-muted small">Theme</dt>
                        <dd class="col-7 small"><?= e(ucwords(str_replace('_', ' ', $campaign['theme']))) ?></dd>
                        <?php endif; ?>
                        <?php endif; ?>

                        <dt class="col-5 text-muted small">Participants</dt>
                        <dd class="col-7">
                            <span class="badge bg-primary"><?= number_format($participant_count) ?> joined</span>
                        </dd>
                    </dl>

                    <!-- Publishing Platforms -->
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="text-muted small fw-semibold">Published on:</span>
                        <?php if ($campaign['platform_website']): ?>
                        <span title="GoBuff Website" class="d-flex align-items-center gap-1 text-primary small">
                            <i class="bi bi-globe2 fs-5"></i> Website
                        </span>
                        <?php endif; ?>
                        <?php if ($campaign['platform_facebook']): ?>
                        <span title="Facebook" class="d-flex align-items-center gap-1 small" style="color:#1877F2">
                            <i class="bi bi-facebook fs-5"></i> Facebook
                        </span>
                        <?php endif; ?>
                        <?php if ($campaign['platform_instagram']): ?>
                        <span title="Instagram" class="d-flex align-items-center gap-1 small" style="color:#E1306C">
                            <i class="bi bi-instagram fs-5"></i> Instagram
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Featured Services & Rates -->
            <?php if (!empty($featured_services)): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-tags-fill me-2"></i>Featured Services &amp; Rates</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        <?php
                        $catColors = [
                            'membership'       => 'primary',
                            'class'            => 'success',
                            'personal_training'=> 'info',
                            'amenity'          => 'warning',
                            'other'            => 'secondary',
                        ];
                        foreach ($featured_services as $svc):
                            $cc = $catColors[$svc['category']] ?? 'secondary';
                        ?>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-3 p-3 border rounded-3 h-100">
                                <div class="bg-<?= $cc ?> bg-opacity-10 rounded-circle p-2 flex-shrink-0">
                                    <i class="bi bi-tag-fill text-<?= $cc ?>"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold"><?= e($svc['name']) ?></div>
                                    <span class="badge bg-<?= $cc ?> mb-1" style="font-size:.65rem">
                                        <?= ucwords(str_replace('_', ' ', $svc['category'])) ?>
                                    </span>
                                    <?php if ($svc['description']): ?>
                                    <div class="text-muted small"><?= e($svc['description']) ?></div>
                                    <?php endif; ?>
                                    <div class="fw-bold text-success mt-1">
                                        ₱<?= number_format($svc['price'], 2) ?>
                                        <?php if ($svc['duration']): ?>
                                        <span class="text-muted fw-normal small">/ <?= e($svc['duration']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($campaign['discount_pct'] > 0): ?>
                                        <span class="badge bg-danger ms-1"><?= number_format($campaign['discount_pct'], 0) ?>% OFF</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- ── Guest / New User: CTA to Join ──────────────────────── -->
            <?php if ($isGuest || $isUser): ?>
            <div class="card shadow-sm mt-4 border-primary border-2">
                <div class="card-body p-4 text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:72px;height:72px">
                        <i class="bi bi-lightning-charge-fill text-primary fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Ready to Join GoBuff?</h5>
                    <p class="text-muted mb-4">
                        Take advantage of this offer! Apply for a gym membership today and start your fitness journey.
                        <?php if ($campaign['discount_pct'] > 0): ?>
                        <strong>Get <?= number_format($campaign['discount_pct'], 0) ?>% off</strong> when you sign up now.
                        <?php endif; ?>
                    </p>
                    <?php if ($isGuest): ?>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="<?= base_url('/register') ?>" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-person-plus-fill me-2"></i>Create Account &amp; Apply
                        </a>
                        <a href="<?= base_url('/login') ?>" class="btn btn-outline-primary btn-lg px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Already have an account? Log In
                        </a>
                    </div>
                    <?php else: ?>
                    <a href="<?= base_url('/role-application/apply') ?>" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-send-fill me-2"></i>Apply for Membership
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- ── Member: Join / Already Joined Panel ─────────────────── -->
            <?php if ($isMember): ?>
            <div class="card mt-4 shadow-sm">
                <div class="card-body">
                    <?php if ($participation): ?>
                    <div class="text-center py-2">
                        <i class="bi bi-patch-check-fill text-success fs-1 d-block mb-2"></i>
                        <h5 class="fw-bold text-success">You're In!</h5>
                        <p class="text-muted mb-3">You joined this campaign on <?= format_date($participation['joined_at']) ?>.</p>

                        <?php if (!empty($participation['referral_code'])): ?>
                        <div class="alert alert-success d-inline-block px-4">
                            <div class="small text-muted mb-1">Your Referral Code</div>
                            <div class="fw-bold fs-4 letter-spacing-1" id="refCode"><?= e($participation['referral_code']) ?></div>
                            <button class="btn btn-sm btn-outline-success mt-2" onclick="copyCode()">
                                <i class="bi bi-clipboard me-1"></i>Copy Code
                            </button>
                        </div>
                        <p class="text-muted small mt-3">Share this code with friends. When they join using your code, you both earn rewards!</p>
                        <?php endif; ?>

                        <?php if ($participation['reward_status'] === 'applied'): ?>
                        <div class="badge bg-success fs-6 mt-2"><i class="bi bi-gift-fill me-1"></i>Reward Applied</div>
                        <?php elseif ($participation['reward_status'] === 'pending'): ?>
                        <div class="badge bg-warning text-dark fs-6 mt-2"><i class="bi bi-hourglass-split me-1"></i>Reward Pending</div>
                        <?php endif; ?>

                        <div class="mt-3">
                            <a href="<?= base_url('/my-campaigns') ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-collection me-1"></i>View All My Campaigns
                            </a>
                        </div>
                    </div>

                    <?php elseif ($campaign['status'] === 'active'): ?>
                    <h6 class="fw-bold mb-1"><i class="bi bi-megaphone-fill me-2 text-success"></i>Join This Campaign</h6>
                    <p class="text-muted small mb-3">
                        <?php if ($campaign['discount_pct'] > 0): ?>
                        Get <strong><?= number_format($campaign['discount_pct'], 2) ?>% off</strong> and earn rewards by referring friends!
                        <?php else: ?>
                        Participate and earn rewards by referring friends!
                        <?php endif; ?>
                    </p>
                    <form method="POST" action="<?= base_url('/campaigns/' . $campaign['id'] . '/join') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Have a referral code? <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="referral_code" class="form-control form-control-sm text-uppercase"
                                   placeholder="Enter referral code" maxlength="8"
                                   style="letter-spacing:.15em"
                                   value="<?= e($_GET['ref'] ?? '') ?>">
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-person-check-fill me-2"></i>Join Campaign
                        </button>
                    </form>

                    <?php else: ?>
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                        This campaign is not currently accepting participants.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- ── Admin/Marketing: Participants List ──────────────────────── -->
        <?php if ($isStaff): ?>
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-people-fill me-2 text-primary"></i>Participants</h6>
                    <span class="badge bg-primary"><?= count($participants) ?></span>
                </div>
                <?php if (empty($participants)): ?>
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-person-x fs-2 d-block mb-2"></i>No participants yet
                </div>
                <?php else: ?>
                <div class="card-body p-0" style="max-height:480px;overflow-y:auto">
                    <div class="list-group list-group-flush">
                        <?php foreach ($participants as $p): ?>
                        <div class="list-group-item py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold small"><?= e($p['member_name']) ?></div>
                                    <div class="text-muted" style="font-size:.75rem">
                                        <code><?= e($p['member_code']) ?></code>
                                        &bull; Joined <?= format_date($p['joined_at']) ?>
                                    </div>
                                    <?php if (!empty($p['referred_by_name'])): ?>
                                    <div class="text-muted" style="font-size:.75rem">
                                        <i class="bi bi-person-fill me-1"></i>Referred by <?= e($p['referred_by_name']) ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($p['referral_code'])): ?>
                                    <div class="text-muted" style="font-size:.75rem">
                                        Code: <code><?= e($p['referral_code']) ?></code>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?= status_badge($p['reward_status']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function copyCode() {
    const code = document.getElementById('refCode')?.innerText;
    if (!code) return;
    navigator.clipboard.writeText(code).then(() => {
        const btn = event.target.closest('button');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copied!';
        btn.classList.replace('btn-outline-success', 'btn-success');
        setTimeout(() => { btn.innerHTML = orig; btn.classList.replace('btn-success', 'btn-outline-success'); }, 2000);
    });
}
</script>
