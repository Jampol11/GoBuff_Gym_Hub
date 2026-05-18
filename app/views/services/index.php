<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-tags-fill me-2 text-primary"></i>Gym Services &amp; Rates</h2>
            <p class="page-subtitle">Manage your gym's service offerings and membership rates, then submit them to the Marketing Officer.</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="<?= base_url('/owner/services/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-1"></i>Add Service
            </a>
        </div>
    </div>

    <!-- Submit to Marketing Panel -->
    <?php if (!empty($pending)): ?>
    <div class="card border-0 shadow-sm border-start border-warning border-4 mb-4">
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-send-fill text-warning fs-5"></i>
                <strong>Submit Services to Marketing Officer</strong>
                <span class="badge bg-warning text-dark"><?= count($pending) ?> pending</span>
            </div>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                Select the services and rates you want the Marketing Officer to feature in advertising campaigns.
            </p>
            <form method="POST" action="<?= base_url('/owner/services/submit-to-marketing') ?>">
                <?= csrf_field() ?>
                <div class="row g-3 mb-3">
                    <?php foreach ($pending as $svc): ?>
                    <div class="col-md-6 col-lg-4">
                        <label class="d-flex align-items-start gap-2 p-3 border rounded-3 cursor-pointer service-check-label">
                            <input type="checkbox" name="service_ids[]" value="<?= $svc['id'] ?>"
                                   class="form-check-input mt-1 flex-shrink-0">
                            <div>
                                <div class="fw-semibold"><?= e($svc['name']) ?></div>
                                <div class="text-muted small"><?= ucfirst($svc['category']) ?></div>
                                <div class="fw-bold text-success">₱<?= number_format($svc['price'], 2) ?><?= $svc['duration'] ? ' / ' . e($svc['duration']) : '' ?></div>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-send-fill me-1"></i>Submit Selected to Marketing Officer
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- All Services Table -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-list-ul me-2"></i>All Services &amp; Rates</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Service / Rate</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($services)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-tags fs-2 d-block mb-2"></i>
                                    No services added yet. Click "Add Service" to get started.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($services as $i => $svc): ?>
                            <tr>
                                <td><?= $pagination['offset'] + $i + 1 ?></td>
                                <td>
                                    <div class="fw-semibold"><?= e($svc['name']) ?></div>
                                    <?php if ($svc['description']): ?>
                                    <small class="text-muted"><?= e(mb_strimwidth($svc['description'], 0, 60, '…')) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $catColors = [
                                        'membership'       => 'primary',
                                        'class'            => 'success',
                                        'personal_training'=> 'info',
                                        'amenity'          => 'warning',
                                        'other'            => 'secondary',
                                    ];
                                    $catColor = $catColors[$svc['category']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $catColor ?>">
                                        <?= ucwords(str_replace('_', ' ', $svc['category'])) ?>
                                    </span>
                                </td>
                                <td class="fw-semibold text-success">₱<?= number_format($svc['price'], 2) ?></td>
                                <td class="text-muted small"><?= e($svc['duration'] ?: '—') ?></td>
                                <td>
                                    <?php if ($svc['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($svc['submitted_to_marketing']): ?>
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-check-circle me-1"></i>Submitted
                                        </span>
                                        <div class="text-muted" style="font-size:.7rem"><?= format_date($svc['submitted_at']) ?></div>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('/owner/services/' . $svc['id'] . '/edit') ?>"
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="<?= base_url('/owner/services/' . $svc['id'] . '/delete') ?>"
                                              class="d-inline" onsubmit="return confirm('Delete this service?')">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-outline-danger" title="Delete">
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
                of <?= $pagination['total'] ?>
            </small>
            <?= pagination_links($pagination, base_url('/owner/services')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.service-check-label { cursor: pointer; transition: background .15s, border-color .15s; }
.service-check-label:hover { background: rgba(13,110,253,.04); border-color: #86b7fe !important; }
.service-check-label:has(input:checked) { background: rgba(13,110,253,.07); border-color: #0d6efd !important; }
</style>
