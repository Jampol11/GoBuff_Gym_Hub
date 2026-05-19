<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= base_url('/super-admin/owner-applications') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <div>
            <h1 class="h3 fw-bold mb-0">
                <i class="bi bi-building-fill-gear me-2 text-warning"></i>Review Application
            </h1>
        </div>
    </div>

    <div class="row g-4">

        <!-- Application Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <strong>Application Details</strong>
                    <?php
                    $badge = match($application['status']) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'warning text-dark',
                    };
                    ?>
                    <span class="badge bg-<?= $badge ?> fs-6"><?= ucfirst($application['status']) ?></span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Applicant</dt>
                        <dd class="col-sm-8"><?= e($application['user_name']) ?> <span class="text-muted small">(<?= e($application['user_email']) ?>)</span></dd>

                        <dt class="col-sm-4">Business Name</dt>
                        <dd class="col-sm-8"><?= e($application['business_name']) ?></dd>

                        <dt class="col-sm-4">Contact Number</dt>
                        <dd class="col-sm-8"><?= e($application['contact_number']) ?></dd>

                        <dt class="col-sm-4">Address</dt>
                        <dd class="col-sm-8"><?= e($application['address']) ?></dd>

                        <dt class="col-sm-4">Reason / Motivation</dt>
                        <dd class="col-sm-8"><?= nl2br(e($application['reason'])) ?></dd>

                        <dt class="col-sm-4">Submitted</dt>
                        <dd class="col-sm-8"><?= date('F d, Y g:i A', strtotime($application['created_at'])) ?></dd>

                        <?php if ($application['reviewed_by']): ?>
                        <dt class="col-sm-4">Reviewed By</dt>
                        <dd class="col-sm-8"><?= e($application['reviewer_name'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-4">Reviewed At</dt>
                        <dd class="col-sm-8"><?= date('F d, Y g:i A', strtotime($application['reviewed_at'])) ?></dd>

                        <dt class="col-sm-4">Review Notes</dt>
                        <dd class="col-sm-8"><?= e($application['review_notes'] ?? '—') ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <!-- Supporting Documents -->
            <?php if (!empty($application['documents'])): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <strong><i class="bi bi-paperclip me-1"></i>Supporting Documents</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Document Type</th>
                                    <th>File Name</th>
                                    <th>Size</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($application['documents'] as $doc): ?>
                                <tr>
                                    <td><?= e($documentTypes[$doc['document_type']] ?? ucfirst($doc['document_type'])) ?></td>
                                    <td class="text-muted small"><?= e($doc['file_original']) ?></td>
                                    <td class="text-muted small"><?= number_format($doc['file_size'] / 1024, 1) ?> KB</td>
                                    <td>
                                        <a href="<?= base_url('/super-admin/owner-applications/documents/' . $doc['id'] . '/download') ?>"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Action Panel -->
        <div class="col-lg-4">
            <?php if ($application['status'] === 'pending'): ?>
            <div class="card border-0 shadow-sm border-top border-warning border-4 mb-3">
                <div class="card-header bg-transparent">
                    <strong><i class="bi bi-check-circle-fill text-success me-1"></i>Approve Application</strong>
                </div>
                <div class="card-body">
                    <form method="POST"
                          action="<?= base_url('/super-admin/owner-applications/' . $application['id'] . '/approve') ?>"
                          onsubmit="return confirm('Approve this Gym Owner application?')">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Review Notes (optional)</label>
                            <textarea name="review_notes" class="form-control form-control-sm" rows="3"
                                      placeholder="Add a note for the applicant..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-lg me-1"></i>Approve &amp; Grant Access
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm border-top border-danger border-4">
                <div class="card-header bg-transparent">
                    <strong><i class="bi bi-x-circle-fill text-danger me-1"></i>Reject Application</strong>
                </div>
                <div class="card-body">
                    <form method="POST"
                          action="<?= base_url('/super-admin/owner-applications/' . $application['id'] . '/reject') ?>"
                          onsubmit="return confirm('Reject this application? This cannot be undone.')">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="review_notes" class="form-control form-control-sm" rows="3"
                                      placeholder="Explain why the application is rejected..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-x-lg me-1"></i>Reject Application
                        </button>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-<?= $application['status'] === 'approved' ? 'success' : 'danger' ?> d-flex align-items-center gap-2">
                <i class="bi bi-<?= $application['status'] === 'approved' ? 'check-circle-fill' : 'x-circle-fill' ?> fs-5"></i>
                <div>
                    This application has been <strong><?= $application['status'] ?></strong>.
                    <?php if ($application['review_notes']): ?>
                        <br><small><?= e($application['review_notes']) ?></small>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>
