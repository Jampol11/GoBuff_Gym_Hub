<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title"><i class="bi bi-pencil me-2"></i>Edit Document</h2>
            <p class="page-subtitle"><?= e($doc['title']) ?></p>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('/owner/documents/' . $doc['id']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('/owner/documents/' . $doc['id'] . '/update') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Document Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required maxlength="255"
                                   value="<?= e($_POST['title'] ?? $doc['title']) ?>">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
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
                                    $current = $_POST['category'] ?? $doc['category'];
                                    foreach ($categories as $val => $label):
                                    ?>
                                    <option value="<?= $val ?>" <?= $current === $val ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <?php foreach (['active' => 'Active', 'archived' => 'Archived', 'expired' => 'Expired'] as $val => $label): ?>
                                    <option value="<?= $val ?>" <?= ($doc['status'] === $val) ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control"
                                   value="<?= e($_POST['expiry_date'] ?? $doc['expiry_date'] ?? '') ?>">
                            <div class="form-text">Leave blank if the document has no expiry.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= e($_POST['description'] ?? $doc['description'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Replace File</label>
                            <div class="alert alert-light border d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-file-earmark-fill text-secondary"></i>
                                <div>
                                    Current file: <strong><?= e($doc['file_original']) ?></strong>
                                    (<?= number_format($doc['file_size'] / 1024, 1) ?> KB)
                                </div>
                            </div>
                            <input type="file" name="document" class="form-control"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">Upload a new file to replace the current one. Leave empty to keep the existing file.</div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_confidential"
                                       id="is_confidential" value="1"
                                       <?= ($doc['is_confidential'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_confidential">
                                    <i class="bi bi-lock-fill text-warning me-1"></i>Mark as Confidential
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Save Changes
                            </button>
                            <a href="<?= base_url('/owner/documents/' . $doc['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
