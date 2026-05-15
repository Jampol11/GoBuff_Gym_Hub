<?php
/**
 * Gym Owner Application Detail — Reviewer view
 */
$isPending = $application['status'] === 'pending';
$badgeClass = match($application['status']) {
    'approved' => 'success',
    'rejected' => 'danger',
    default    => 'warning',
};
?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- Back -->
            <a href="<?= base_url('/gym-owner-applications') ?>" class="btn btn-sm btn-outline-secondary mb-4">
                <i class="bi bi-arrow-left me-1"></i>Back to Applications
            </a>

            <!-- Application Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-building-fill-gear me-2 text-danger"></i>
                        Application #<?= $application['id'] ?>
                    </h6>
                    <span class="badge bg-<?= $badgeClass ?> fs-6 px-3"><?= ucfirst($application['status']) ?></span>
                </div>
                <div class="card-body p-4">

                    <!-- Applicant Info -->
                    <h6 class="fw-semibold text-muted text-uppercase small mb-3">
                        <i class="bi bi-person me-1"></i>Applicant
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <div class="text-muted small mb-1">Name</div>
                            <div class="fw-semibold"><?= e($application['user_name']) ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small mb-1">Email</div>
                            <div><?= e($application['user_email']) ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small mb-1">Username</div>
                            <div><?= e($application['username']) ?></div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Business Details -->
                    <h6 class="fw-semibold text-muted text-uppercase small mb-3">
                        <i class="bi bi-building me-1"></i>Business Details
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Gym / Business Name</div>
                            <div class="fw-semibold"><?= e($application['business_name']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Contact Number</div>
                            <div><?= e($application['contact_number']) ?></div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small mb-1">Address</div>
                            <div><?= nl2br(e($application['address'])) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Submitted</div>
                            <div><?= date('F d, Y g:i A', strtotime($application['created_at'])) ?></div>
                        </div>
                    </div>

                    <!-- Statement -->
                    <div class="mb-4">
                        <div class="text-muted small mb-1">Statement of Ownership</div>
                        <div class="bg-light rounded p-3"><?= nl2br(e($application['reason'])) ?></div>
                    </div>

                    <!-- Supporting Documents -->
                    <?php if (!empty($application['documents'])): ?>
                    <hr class="my-3">
                    <h6 class="fw-semibold text-muted text-uppercase small mb-3">
                        <i class="bi bi-paperclip me-1"></i>Supporting Documents
                    </h6>
                    <div class="list-group list-group-flush mb-2">
                        <?php foreach ($application['documents'] as $doc): ?>
                        <div class="list-group-item px-0 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <?php
                                $icon = str_contains($doc['file_type'], 'pdf') ? 'bi-file-earmark-pdf text-danger'
                                    : (str_contains($doc['file_type'], 'image') ? 'bi-file-earmark-image text-info'
                                    : 'bi-file-earmark-word text-primary');
                                ?>
                                <i class="bi <?= $icon ?> fs-4"></i>
                                <div>
                                    <div class="fw-semibold small"><?= e($doc['file_original']) ?></div>
                                    <div class="text-muted small">
                                        <?= e($documentTypes[$doc['document_type']] ?? ucfirst(str_replace('_', ' ', $doc['document_type']))) ?>
                                        &middot; <?= number_format($doc['file_size'] / 1024, 1) ?> KB
                                    </div>
                                </div>
                            </div>
                            <a href="<?= base_url('/gym-owner-applications/documents/' . $doc['id'] . '/download') ?>"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-download me-1"></i>Download
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning py-2">
                        <i class="bi bi-exclamation-triangle me-2"></i>No documents were attached to this application.
                    </div>
                    <?php endif; ?>

                    <!-- Review Notes (if already reviewed) -->
                    <?php if (!$isPending && $application['review_notes']): ?>
                    <hr class="my-3">
                    <div>
                        <div class="text-muted small mb-1">Review Notes</div>
                        <div class="bg-light rounded p-3"><?= nl2br(e($application['review_notes'])) ?></div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <?php if ($isPending): ?>
            <!-- Approve / Reject -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm border-start border-success border-3">
                        <div class="card-body p-4">
                            <h6 class="fw-semibold text-success mb-3">
                                <i class="bi bi-check-circle-fill me-2"></i>Approve Application
                            </h6>
                            <form method="POST"
                                  action="<?= base_url('/gym-owner-applications/' . $application['id'] . '/approve') ?>">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label class="form-label small">Notes (optional)</label>
                                    <textarea name="review_notes" class="form-control form-control-sm" rows="3"
                                        placeholder="Welcome message or instructions..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success w-100"
                                    onclick="return confirm('Grant Gym Owner access to this applicant?')">
                                    <i class="bi bi-check-lg me-1"></i>Approve & Grant Access
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm border-start border-danger border-3">
                        <div class="card-body p-4">
                            <h6 class="fw-semibold text-danger mb-3">
                                <i class="bi bi-x-circle-fill me-2"></i>Reject Application
                            </h6>
                            <form method="POST"
                                  action="<?= base_url('/gym-owner-applications/' . $application['id'] . '/reject') ?>">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label class="form-label small">
                                        Reason for rejection <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="review_notes" class="form-control form-control-sm" rows="3"
                                        required placeholder="Explain why the application is rejected..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger w-100"
                                    onclick="return confirm('Reject this application?')">
                                    <i class="bi bi-x-lg me-1"></i>Reject Application
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <div class="alert alert-secondary">
                <i class="bi bi-info-circle me-2"></i>
                This application was <strong><?= $application['status'] ?></strong>
                <?php if ($application['reviewed_at']): ?>
                on <?= date('F d, Y', strtotime($application['reviewed_at'])) ?>
                <?php endif; ?>
                <?php if ($application['reviewer_name']): ?>
                by <strong><?= e($application['reviewer_name']) ?></strong>
                <?php endif; ?>.
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
