<?php
/**
 * Gym Owner Application Form
 */
?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- Page Header -->
            <div class="d-flex align-items-center mb-4">
                <div class="me-3">
                    <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-building-fill-gear text-danger fs-4"></i>
                    </div>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold">Apply as Gym Owner</h4>
                    <p class="text-muted mb-0 small">Submit your ownership credentials for review.</p>
                </div>
            </div>

            <?php if ($hasPending): ?>
            <!-- Pending Notice -->
            <div class="alert alert-warning d-flex align-items-start gap-3 mb-4">
                <i class="bi bi-hourglass-split fs-5 mt-1 flex-shrink-0"></i>
                <div>
                    <strong>Application Under Review</strong><br>
                    Your application for <strong><?= e($hasPending['business_name']) ?></strong> is currently pending.
                    You will be notified once it has been reviewed.
                </div>
            </div>
            <?php else: ?>

            <!-- Info Banner -->
            <div class="alert alert-info d-flex align-items-start gap-3 mb-4">
                <i class="bi bi-info-circle-fill fs-5 mt-1 flex-shrink-0"></i>
                <div>
                    <strong>How it works</strong><br>
                    Fill in your gym's business details and upload supporting legal documents (business permit,
                    government ID, proof of ownership, etc.). The Super Admin will review your credentials
                    before granting access.
                </div>
            </div>

            <!-- Application Form -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-send-fill me-2 text-danger"></i>Ownership Application
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('/gym-owner-application/apply') ?>"
                          enctype="multipart/form-data" id="ownerAppForm">
                        <?= csrf_field() ?>

                        <!-- Business Details -->
                        <h6 class="fw-semibold text-muted text-uppercase small mb-3 mt-1">
                            <i class="bi bi-building me-1"></i>Business Details
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Gym / Business Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="business_name" class="form-control"
                                       placeholder="e.g. GoBuff Fitness Center" required minlength="2" maxlength="255">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Contact Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="contact_number" class="form-control"
                                       placeholder="e.g. 09XX-XXX-XXXX" required minlength="7" maxlength="50">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Gym Address <span class="text-danger">*</span>
                                </label>
                                <textarea name="address" class="form-control" rows="2" required minlength="10"
                                    placeholder="Complete address of the gym..."></textarea>
                            </div>
                        </div>

                        <!-- Reason -->
                        <h6 class="fw-semibold text-muted text-uppercase small mb-3">
                            <i class="bi bi-chat-text me-1"></i>Statement of Ownership
                        </h6>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Why are you applying as Gym Owner? <span class="text-danger">*</span>
                            </label>
                            <textarea name="reason" class="form-control" rows="4" required minlength="20"
                                placeholder="Briefly describe your ownership claim, experience, and intent for using this system..."></textarea>
                            <div class="form-text">Minimum 20 characters.</div>
                        </div>

                        <!-- Document Uploads -->
                        <h6 class="fw-semibold text-muted text-uppercase small mb-3">
                            <i class="bi bi-paperclip me-1"></i>Supporting Documents
                            <span class="text-danger">*</span>
                        </h6>
                        <p class="text-muted small mb-3">
                            Upload at least one document proving your ownership (PDF, DOC, DOCX, JPG, PNG, WEBP — max 10 MB each).
                        </p>

                        <div id="documentRows">
                            <!-- Initial document row -->
                            <div class="document-row card border bg-light mb-3 p-3">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Document Type</label>
                                        <select name="document_type[]" class="form-select form-select-sm">
                                            <?php foreach ($documentTypes as $val => $label): ?>
                                            <option value="<?= e($val) ?>"><?= e($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label small fw-semibold">File</label>
                                        <input type="file" name="documents[]" class="form-control form-control-sm"
                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" required>
                                    </div>
                                    <div class="col-md-1 d-flex justify-content-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-doc-row d-none">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="addDocRow" class="btn btn-sm btn-outline-secondary mb-4">
                            <i class="bi bi-plus-circle me-1"></i>Add Another Document
                        </button>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="bi bi-send me-2"></i>Submit Application
                            </button>
                            <a href="<?= base_url('/dashboard') ?>" class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Application History -->
            <?php if (!empty($myApplications)): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2 text-secondary"></i>My Application History
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Business Name</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Review Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myApplications as $app): ?>
                                <tr>
                                    <td class="fw-semibold"><?= e($app['business_name']) ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = match($app['status']) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default    => 'warning',
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($app['status']) ?></span>
                                    </td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($app['created_at'])) ?></td>
                                    <td class="text-muted small"><?= e($app['review_notes'] ?? '—') ?></td>
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

<script>
(function () {
    const container  = document.getElementById('documentRows');
    const addBtn     = document.getElementById('addDocRow');
    const firstRow   = container.querySelector('.document-row');

    // Document type options HTML (reused when cloning)
    const typeOptions = firstRow.querySelector('select').innerHTML;

    function updateRemoveButtons() {
        const rows = container.querySelectorAll('.document-row');
        rows.forEach(row => {
            const btn = row.querySelector('.remove-doc-row');
            btn.classList.toggle('d-none', rows.length === 1);
        });
    }

    addBtn.addEventListener('click', function () {
        const newRow = document.createElement('div');
        newRow.className = 'document-row card border bg-light mb-3 p-3';
        newRow.innerHTML = `
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Document Type</label>
                    <select name="document_type[]" class="form-select form-select-sm">${typeOptions}</select>
                </div>
                <div class="col-md-7">
                    <label class="form-label small fw-semibold">File</label>
                    <input type="file" name="documents[]" class="form-control form-control-sm"
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
                </div>
                <div class="col-md-1 d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-doc-row">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>`;
        container.appendChild(newRow);
        newRow.querySelector('.remove-doc-row').addEventListener('click', removeRow);
        updateRemoveButtons();
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-doc-row')) {
            removeRow.call(e.target.closest('.remove-doc-row'));
        }
    });

    function removeRow() {
        this.closest('.document-row').remove();
        updateRemoveButtons();
    }

    updateRemoveButtons();
})();
</script>
