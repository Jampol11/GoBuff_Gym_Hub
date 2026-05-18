<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">New Campaign</h2></div>
        <a href="<?= base_url('/campaigns') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <form action="<?= base_url('/campaigns') ?>" method="POST" enctype="multipart/form-data" novalidate>
                <?= csrf_field() ?>

                <!-- Basic Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-megaphone-fill me-2"></i>Campaign Details</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Campaign Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" required
                                       placeholder="e.g. Summer Fitness Promo 2026">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" name="description" rows="3"
                                          placeholder="Describe the campaign offer and benefits..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Target Audience</label>
                                <select class="form-select" name="target_audience">
                                    <option value="General Public">General Public</option>
                                    <option value="New Members">New Members</option>
                                    <option value="All Members">All Members</option>
                                    <option value="Existing Members">Existing Members</option>
                                    <option value="Expired Members">Expired Members</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" name="status">
                                    <option value="scheduled">Scheduled</option>
                                    <option value="active">Active (Publish Now)</option>
                                    <option value="inactive">Inactive (Draft)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="end_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Budget (₱)</label>
                                <input type="number" class="form-control" name="budget" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Discount (%)</label>
                                <input type="number" class="form-control" name="discount_pct" min="0" max="100" step="0.1" placeholder="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Banner Image</label>
                                <input type="file" class="form-control" name="banner" accept="image/*">
                                <div class="form-text">Recommended: 1200×628px. Max 5MB.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Services from Owner -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bi bi-tags-fill me-2"></i>Featured Services &amp; Rates</h6>
                        <a href="<?= base_url('/marketing/services') ?>" class="btn btn-sm btn-light text-success" target="_blank">
                            <i class="bi bi-eye me-1"></i>View All
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <?php if (empty($services)): ?>
                        <div class="alert alert-warning d-flex align-items-start gap-3 mb-0">
                            <i class="bi bi-exclamation-triangle-fill fs-5 mt-1 text-warning"></i>
                            <div>
                                <strong>No services submitted yet.</strong>
                                The Gym Owner hasn't submitted any services or rates for campaigns.
                                You can still create a campaign without featured services.
                            </div>
                        </div>
                        <?php else: ?>
                        <p class="text-muted small mb-3">
                            Select the services and membership rates to feature in this campaign.
                            These will be displayed to potential customers.
                        </p>
                        <div class="row g-3">
                            <?php
                            $catColors = [
                                'membership'       => 'primary',
                                'class'            => 'success',
                                'personal_training'=> 'info',
                                'amenity'          => 'warning',
                                'other'            => 'secondary',
                            ];
                            foreach ($services as $svc):
                                $cc = $catColors[$svc['category']] ?? 'secondary';
                            ?>
                            <div class="col-md-6 col-lg-4">
                                <label class="d-flex align-items-start gap-2 p-3 border rounded-3 svc-label h-100">
                                    <input type="checkbox" name="service_ids[]" value="<?= $svc['id'] ?>"
                                           class="form-check-input mt-1 flex-shrink-0">
                                    <div>
                                        <div class="fw-semibold small"><?= e($svc['name']) ?></div>
                                        <span class="badge bg-<?= $cc ?> mb-1" style="font-size:.65rem">
                                            <?= ucwords(str_replace('_', ' ', $svc['category'])) ?>
                                        </span>
                                        <?php if ($svc['description']): ?>
                                        <div class="text-muted" style="font-size:.72rem"><?= e(mb_strimwidth($svc['description'], 0, 60, '…')) ?></div>
                                        <?php endif; ?>
                                        <div class="fw-bold text-success mt-1">
                                            ₱<?= number_format($svc['price'], 2) ?>
                                            <?= $svc['duration'] ? '<span class="text-muted fw-normal">/ ' . e($svc['duration']) . '</span>' : '' ?>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Publishing Platforms -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-share-fill me-2"></i>Publishing Platforms</h6>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Choose where to publish this campaign.</p>
                        <div class="row g-3 mb-4">
                            <!-- Website -->
                            <div class="col-md-4">
                                <label class="platform-card d-flex flex-column align-items-center justify-content-center p-4 border rounded-3 text-center">
                                    <input type="checkbox" name="platform_website" class="d-none platform-check" checked>
                                    <i class="bi bi-globe2 fs-1 text-primary mb-2"></i>
                                    <div class="fw-semibold">GoBuff Website</div>
                                    <div class="text-muted small">Visible on dashboard</div>
                                    <span class="badge bg-success mt-2 platform-badge">Selected</span>
                                </label>
                            </div>
                            <!-- Facebook -->
                            <div class="col-md-4">
                                <label class="platform-card d-flex flex-column align-items-center justify-content-center p-4 border rounded-3 text-center">
                                    <input type="checkbox" name="platform_facebook" class="d-none platform-check">
                                    <i class="bi bi-facebook fs-1 text-primary mb-2" style="color:#1877F2 !important"></i>
                                    <div class="fw-semibold">Facebook</div>
                                    <div class="text-muted small">Share to Facebook page</div>
                                    <span class="badge bg-secondary mt-2 platform-badge">Not Selected</span>
                                </label>
                            </div>
                            <!-- Instagram -->
                            <div class="col-md-4">
                                <label class="platform-card d-flex flex-column align-items-center justify-content-center p-4 border rounded-3 text-center">
                                    <input type="checkbox" name="platform_instagram" class="d-none platform-check">
                                    <i class="bi bi-instagram fs-1 mb-2" style="color:#E1306C !important"></i>
                                    <div class="fw-semibold">Instagram</div>
                                    <div class="text-muted small">Share to Instagram</div>
                                    <span class="badge bg-secondary mt-2 platform-badge">Not Selected</span>
                                </label>
                            </div>
                        </div>

                        <!-- Size & Theme (from DFD) -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Campaign Size / Format</label>
                                <select class="form-select" name="size">
                                    <option value="">— Select Size —</option>
                                    <option value="banner">Banner (1200×628)</option>
                                    <option value="square">Square Post (1080×1080)</option>
                                    <option value="story">Story (1080×1920)</option>
                                    <option value="flyer">Flyer (A4)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Campaign Theme</label>
                                <select class="form-select" name="theme">
                                    <option value="">— Select Theme —</option>
                                    <option value="summer">Summer Vibes</option>
                                    <option value="fitness">Fitness & Strength</option>
                                    <option value="promo">Promo / Sale</option>
                                    <option value="wellness">Health & Wellness</option>
                                    <option value="new_year">New Year, New You</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="<?= base_url('/campaigns') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-megaphone me-1"></i>Create &amp; Post Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.svc-label { cursor: pointer; transition: background .15s, border-color .15s; }
.svc-label:hover { background: rgba(13,110,253,.04); border-color: #86b7fe !important; }
.svc-label:has(input:checked) { background: rgba(25,135,84,.07); border-color: #198754 !important; }

.platform-card { cursor: pointer; transition: background .15s, border-color .15s; min-height: 140px; }
.platform-card:hover { background: rgba(13,110,253,.04); border-color: #86b7fe !important; }
.platform-card:has(.platform-check:checked) { background: rgba(13,110,253,.07); border-color: #0d6efd !important; }
.platform-card:has(.platform-check:checked) .platform-badge { background-color: #198754 !important; }
.platform-card:has(.platform-check:checked) .platform-badge::after { content: ''; }
</style>

<script>
// Update platform badge text on toggle
document.querySelectorAll('.platform-check').forEach(function(cb) {
    cb.addEventListener('change', function() {
        const badge = this.closest('.platform-card').querySelector('.platform-badge');
        if (this.checked) {
            badge.textContent = 'Selected';
            badge.className = 'badge bg-success mt-2 platform-badge';
        } else {
            badge.textContent = 'Not Selected';
            badge.className = 'badge bg-secondary mt-2 platform-badge';
        }
    });
    // Init state
    const badge = cb.closest('.platform-card').querySelector('.platform-badge');
    if (cb.checked) {
        badge.textContent = 'Selected';
        badge.className = 'badge bg-success mt-2 platform-badge';
    }
});
</script>
