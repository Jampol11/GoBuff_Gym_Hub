<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">Edit Member</h2>
            <p class="page-subtitle"><?= e($member['first_name'] . ' ' . $member['last_name']) ?></p>
        </div>
        <a href="<?= base_url('/members/' . $member['id']) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-pencil-fill me-2"></i>Edit Member Information</h6>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/members/' . $member['id'] . '/update') ?>"
                          method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="first_name" required
                                       value="<?= e($member['first_name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="last_name" required
                                       value="<?= e($member['last_name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="tel" class="form-control" name="phone"
                                       value="<?= e($member['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gender</label>
                                <select class="form-select" name="gender">
                                    <option value="male" <?= $member['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                                    <option value="female" <?= $member['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                                    <option value="other" <?= $member['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date of Birth</label>
                                <input type="date" class="form-control" name="date_of_birth"
                                       value="<?= e($member['date_of_birth'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?= $member['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $member['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Address</label>
                                <textarea class="form-control" name="address" rows="2"><?= e($member['address'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Emergency Contact</label>
                                <input type="text" class="form-control" name="emergency_contact"
                                       value="<?= e($member['emergency_contact'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Update Photo</label>
                                <input type="file" class="form-control" name="photo" accept="image/*">
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/members/' . $member['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
