<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-upload me-2"></i>Upload Legal Document</h2>
            <p class="page-subtitle">Add a new legal document to the Owner Hub</p>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('/owner/documents') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('/owner/documents') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Document Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control"
                                   placeholder="e.g. Business Permit 2026" required maxlength="255"
                                   value="<?= e($_POST['title'] ?? '') ?>">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="">— Select Category —</option>
                                    <?php
                                    $categories = [
                                        'business_permit'    => 'Business Permit',
                                        'bir_registration'   => 'BIR Registration',
                                        'sec_registration'   => 'SEC Registration',
                                        'dti_registration'   => 'DTI Registration',
                                        'sanitary_permit'    => 'Sanitary Permit',
                                        'fire_safety_permit' => 'Fire Safety Permit',
                                        'lease_contract'     => 'Lease Contract',
                                        'insurance_policy'   => 'Insurance Policy',
                                        'employment_contract'=> 'Employment Contract',
                                        'nda'                => 'NDA',
                                        'other'              => 'Other',
                                    ];
                                    foreach ($categories as $val => $label):
                                    ?>
                                    <option value="<?= $val ?>" <?= ($_POST['category'] ?? '') === $val ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Expiry Date</label>
                                <input type="date" name="expiry_date" class="form-control"
                                       value="<?= e($_POST['expiry_date'] ?? '') ?>"
                                       min="<?= date('Y-m-d') ?>">
                                <div class="form-text">Leave blank if the document has no expiry.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Optional notes about this document..."><?= e($_POST['description'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Document File <span class="text-danger">*</span></label>
                            <input type="file" name="document" class="form-control" required
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">
                                Accepted formats: PDF, DOC, DOCX, JPG, PNG. Maximum size: 10 MB.
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_confidential"
                                       id="is_confidential" value="1"
                                       <?= isset($_POST['is_confidential']) ? 'checked' : 'checked' ?>>
                                <label class="form-check-label" for="is_confidential">
                                    <i class="bi bi-lock-fill text-warning me-1"></i>
                                    Mark as Confidential
                                </label>
                                <div class="form-text">Confidential documents are only accessible to the Gym Owner.</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-1"></i>Upload Document
                            </button>
                            <a href="<?= base_url('/owner/documents') ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
