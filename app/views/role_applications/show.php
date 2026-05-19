<?php
/**
 * Role Application Detail — Owner review
 */
$isPending = $application['status'] === 'pending';
?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <!-- Back -->
            <a href="<?= base_url('/role-applications') ?>" class="btn btn-sm btn-outline-secondary mb-4">
                <i class="bi bi-arrow-left me-1"></i>Back to Applications
            </a>

            <!-- Application Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-person-badge-fill me-2 text-primary"></i>Application #<?= $application['id'] ?></h6>
                    <?php
                    $badgeClass = match($application['status']) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'warning',
                    };
                    ?>
                    <span class="badge bg-<?= $badgeClass ?> fs-6 px-3"><?= ucfirst($application['status']) ?></span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Applicant Name</div>
                            <div class="fw-semibold"><?= e($application['user_name']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Email</div>
                            <div><?= e($application['user_email']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Username</div>
                            <div><?= e($application['username']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Requested Role</div>
                            <div class="fw-semibold text-primary"><?= e($application['role_label'] ?? role_label($application['requested_role'])) ?></div>
                        </div>
                        <?php if (!empty($application['gym_name'])): ?>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Applying to Gym</div>
                            <div class="fw-semibold">
                                <i class="bi bi-building me-1 text-primary"></i><?= e($application['gym_name']) ?>
                            </div>
                            <?php if (!empty($application['gym_address'])): ?>
                            <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i><?= e($application['gym_address']) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">Submitted</div>
                            <div><?= date('F d, Y g:i A', strtotime($application['created_at'])) ?></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="text-muted small mb-1">Reason / Background</div>
                        <div class="bg-light rounded p-3"><?= nl2br(e($application['reason'] ?? '—')) ?></div>
                    </div>

                    <?php if (!empty($documents)): ?>
                    <!-- Supporting Documents -->
                    <div class="mb-4">
                        <div class="text-muted small mb-2 fw-semibold">
                            <i class="bi bi-paperclip me-1"></i>Supporting Documents
                            <span class="badge bg-secondary ms-1"><?= count($documents) ?></span>
                        </div>
                        <div class="list-group list-group-flush rounded border">
                            <?php foreach ($documents as $doc): ?>
                            <?php
                            $ext = strtolower(pathinfo($doc['file_original'], PATHINFO_EXTENSION));
                            $iconClass = match($ext) {
                                'pdf'  => 'bi-file-earmark-pdf-fill text-danger',
                                'doc', 'docx' => 'bi-file-earmark-word-fill text-primary',
                                'jpg', 'jpeg', 'png', 'webp' => 'bi-file-earmark-image-fill text-success',
                                default => 'bi-file-earmark-fill text-secondary',
                            };
                            $sizeKb = round($doc['file_size'] / 1024, 1);
                            ?>
                            <div class="list-group-item d-flex align-items-center gap-3 py-3">
                                <i class="bi <?= $iconClass ?> fs-4 flex-shrink-0"></i>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold small text-truncate"><?= e($doc['file_original']) ?></div>
                                    <div class="text-muted" style="font-size:.75rem">
                                        <?= e($docTypes[$doc['document_type']] ?? ucfirst($doc['document_type'])) ?>
                                        &bull; <?= $sizeKb ?> KB
                                        &bull; <?= date('M d, Y', strtotime($doc['created_at'])) ?>
                                    </div>
                                </div>
                                <a href="<?= base_url('/role-applications/documents/' . $doc['id'] . '/download') ?>"
                                   class="btn btn-sm btn-outline-primary flex-shrink-0">
                                    <i class="bi bi-download me-1"></i>Download
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="mb-4">
                        <div class="text-muted small mb-1 fw-semibold"><i class="bi bi-paperclip me-1"></i>Supporting Documents</div>
                        <div class="text-muted small fst-italic">No documents uploaded.</div>
                    </div>
                    <?php endif; ?>

                    <?php if (!$isPending && $application['review_notes']): ?>
                    <div class="mb-2">
                        <div class="text-muted small mb-1">Review Notes</div>
                        <div class="bg-light rounded p-3"><?= nl2br(e($application['review_notes'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($isPending): ?>
            <!-- Approve / Reject Forms -->
            <div class="row g-3">
                <!-- Approve -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm border-start border-success border-3">
                        <div class="card-body p-4">
                            <h6 class="fw-semibold text-success mb-3"><i class="bi bi-check-circle-fill me-2"></i>Approve Application</h6>
                            <form method="POST" action="<?= base_url('/role-applications/' . $application['id'] . '/approve') ?>">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label class="form-label small">Notes (optional)</label>
                                    <textarea name="review_notes" class="form-control form-control-sm" rows="3"
                                        placeholder="Welcome message or instructions..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success w-100"
                                    onclick="return confirm('Approve this application and assign the role?')">
                                    <i class="bi bi-check-lg me-1"></i>Approve & Assign Role
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Reject -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm border-start border-danger border-3">
                        <div class="card-body p-4">
                            <h6 class="fw-semibold text-danger mb-3"><i class="bi bi-x-circle-fill me-2"></i>Reject Application</h6>
                            <form method="POST" action="<?= base_url('/role-applications/' . $application['id'] . '/reject') ?>">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label class="form-label small">Reason for rejection <span class="text-danger">*</span></label>
                                    <textarea name="review_notes" class="form-control form-control-sm" rows="3" required
                                        placeholder="Explain why the application is rejected..."></textarea>
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
