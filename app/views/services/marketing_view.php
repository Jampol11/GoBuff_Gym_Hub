<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-tags-fill me-2 text-info"></i>Services &amp; Rates from Owner</h2>
            <p class="page-subtitle">These services and membership rates were submitted by the Gym Owner for your campaigns.</p>
        </div>
        <a href="<?= base_url('/campaigns/create') ?>" class="btn btn-primary">
            <i class="bi bi-megaphone-fill me-1"></i>Create Campaign
        </a>
    </div>

    <?php if (empty($services)): ?>
    <div class="card shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
            <h5>No services submitted yet</h5>
            <p class="mb-0">The Gym Owner hasn't submitted any services or rates for campaigns yet.</p>
        </div>
    </div>
    <?php else: ?>

    <!-- Group by category -->
    <?php
    $grouped = [];
    foreach ($services as $svc) {
        $grouped[$svc['category']][] = $svc;
    }
    $catLabels = [
        'membership'       => ['label' => 'Membership Plans', 'icon' => 'bi-card-checklist', 'color' => 'primary'],
        'class'            => ['label' => 'Group Classes',    'icon' => 'bi-people-fill',    'color' => 'success'],
        'personal_training'=> ['label' => 'Personal Training','icon' => 'bi-person-badge-fill','color' => 'info'],
        'amenity'          => ['label' => 'Amenities & Add-ons','icon' => 'bi-stars',         'color' => 'warning'],
        'other'            => ['label' => 'Other Services',   'icon' => 'bi-grid-fill',       'color' => 'secondary'],
    ];
    ?>

    <?php foreach ($grouped as $cat => $items): ?>
    <?php $meta = $catLabels[$cat] ?? ['label' => ucfirst($cat), 'icon' => 'bi-tag', 'color' => 'secondary']; ?>
    <div class="mb-4">
        <h6 class="fw-bold text-<?= $meta['color'] ?> mb-3">
            <i class="bi <?= $meta['icon'] ?> me-2"></i><?= $meta['label'] ?>
        </h6>
        <div class="row g-3">
            <?php foreach ($items as $svc): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-<?= $meta['color'] ?> border-opacity-25 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0"><?= e($svc['name']) ?></h6>
                            <span class="badge bg-<?= $meta['color'] ?> ms-2">
                                <?= ucwords(str_replace('_', ' ', $svc['category'])) ?>
                            </span>
                        </div>
                        <?php if ($svc['description']): ?>
                        <p class="text-muted small mb-2"><?= e($svc['description']) ?></p>
                        <?php endif; ?>
                        <div class="d-flex align-items-center gap-2 mt-auto">
                            <span class="fw-bold fs-5 text-success">₱<?= number_format($svc['price'], 2) ?></span>
                            <?php if ($svc['duration']): ?>
                            <span class="text-muted small">/ <?= e($svc['duration']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="text-muted mt-2" style="font-size:.72rem">
                            <i class="bi bi-clock me-1"></i>Submitted <?= format_date($svc['submitted_at']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="alert alert-info d-flex align-items-start gap-3 mt-2">
        <i class="bi bi-lightbulb-fill fs-5 mt-1 text-info"></i>
        <div>
            <strong>Ready to create a campaign?</strong>
            Use these services and rates as the basis for your advertising campaign.
            When creating a campaign, you can select which services to feature and choose your publishing platforms.
        </div>
        <a href="<?= base_url('/campaigns/create') ?>" class="btn btn-info btn-sm text-white text-nowrap ms-auto">
            <i class="bi bi-megaphone me-1"></i>Create Campaign
        </a>
    </div>
    <?php endif; ?>
</div>
