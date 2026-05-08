<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-file-earmark-text me-2"></i>Document Details</h2>
            <p class="page-subtitle"><?= e($doc['title']) ?></p>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('/owner/documents/' . $doc['id'] . '/download') ?>" class="btn btn-success">
                <i class="bi bi-download me-1"></i>Download
            </a>
            <a href="<?= base_url('/owner/documents/' . $doc['id'] . '/edit') ?>" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <a href="<?= base_url('/owner/documents') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <?php
    $isExpired  = $doc['expiry_date'] && strtotime($doc['expiry_date']) < time();
    $isExpiring = $doc['expiry_date'] && !$isExpired && strtotime($doc['expiry_date']) <= strtotime('+30 days');
    ?>

    <?php if ($isExpired): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-x-circle-fill fs-5"></i>
        <div>This document has <strong>expired</strong> on <?= date('F d, Y', strtotime($doc['expiry_date'])) ?>. Please renew it.</div>
    </div>
    <?php elseif ($isExpiring): ?>
    <?php $daysLeft = (int)ceil((strtotime($doc['expiry_date']) - time()) / 86400); ?>
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>This document expires in <strong><?= $daysLeft ?> day<?= $daysLeft !== 1 ? 's' : '' ?></strong> on <?= date('F d, Y', strtotime($doc['expiry_date'])) ?>.</div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Document Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Title</dt>
                        <dd class="col-sm-8 fw-semibold"><?= e($doc['title']) ?></dd>

                        <dt class="col-sm-4 text-muted">Category</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-secondary fs-6">
                                <?= e(ucwords(str_replace('_', ' ', $doc['category']))) ?>
                            </span>
                        </dd>

                        <dt class="col-sm-4 text-muted">Description</dt>
                        <dd class="col-sm-8"><?= $doc['description'] ? e($doc['description']) : '<span class="text-muted">None</span>' ?></dd>

                        <dt class="col-sm-4 text-muted">Status</dt>
                        <dd class="col-sm-8">
                            <?php
                            $statusBadge = match($doc['status']) {
                                'active'   => 'success',
                                'archived' => 'secondary',
                                'expired'  => 'danger',
                                default    => 'secondary',
                            };
                            ?>
                            <span class="badge bg-<?= $statusBadge ?>"><?= ucfirst($doc['status']) ?></span>
                        </dd>

                        <dt class="col-sm-4 text-muted">Expiry Date</dt>
                        <dd class="col-sm-8">
                            <?php if ($doc['expiry_date']): ?>
                                <span class="<?= $isExpired ? 'text-danger fw-bold' : ($isExpiring ? 'text-warning fw-bold' : '') ?>">
                                    <?= date('F d, Y', strtotime($doc['expiry_date'])) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">No expiry date</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-4 text-muted">Confidential</dt>
                        <dd class="col-sm-8">
                            <?php if ($doc['is_confidential']): ?>
                                <span class="badge bg-warning text-dark"><i class="bi bi-lock-fill me-1"></i>Yes</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border">No</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-4 text-muted">Uploaded</dt>
                        <dd class="col-sm-8"><?= format_date($doc['created_at']) ?></dd>

                        <dt class="col-sm-4 text-muted">Last Updated</dt>
                        <dd class="col-sm-8"><?= format_date($doc['updated_at']) ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">File Details</h5>
                </div>
                <div class="card-body text-center">
                    <?php
                    $ext = strtolower(pathinfo($doc['file_original'], PATHINFO_EXTENSION));
                    $icon = match($ext) {
                        'pdf'  => 'bi-file-earmark-pdf-fill text-danger',
                        'doc', 'docx' => 'bi-file-earmark-word-fill text-primary',
                        'jpg', 'jpeg', 'png' => 'bi-file-earmark-image-fill text-success',
                        default => 'bi-file-earmark-fill text-secondary',
                    };
                    ?>
                    <i class="bi <?= $icon ?> display-1 mb-3"></i>
                    <p class="fw-semibold mb-1"><?= e($doc['file_original']) ?></p>
                    <p class="text-muted small mb-3"><?= number_format($doc['file_size'] / 1024, 1) ?> KB &bull; <?= strtoupper($ext) ?></p>
                    <a href="<?= base_url('/owner/documents/' . $doc['id'] . '/download') ?>"
                       class="btn btn-success w-100">
                        <i class="bi bi-download me-1"></i>Download File
                    </a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="<?= base_url('/owner/documents/' . $doc['id'] . '/edit') ?>" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Edit Document
                    </a>
                    <form method="POST" action="<?= base_url('/owner/documents/' . $doc['id'] . '/delete') ?>"
                          onsubmit="return confirm('Permanently delete this document and its file?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-1"></i>Delete Document
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
