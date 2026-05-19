<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= base_url('/super-admin/gyms') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h1 class="h3 fw-bold mb-0">
            <i class="bi bi-building-add me-2 text-dark"></i>Add New Gym
        </h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('/super-admin/gyms') ?>">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Gym Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= e($_POST['name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="2" required><?= e($_POST['address'] ?? '') ?></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Contact Number</label>
                                <input type="text" name="contact" class="form-control"
                                       value="<?= e($_POST['contact'] ?? '') ?>">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control"
                                       value="<?= e($_POST['email'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Assign Gym Owner</label>
                            <select name="owner_id" class="form-select">
                                <option value="">— Unassigned —</option>
                                <?php foreach ($owners as $owner): ?>
                                <option value="<?= (int)$owner['id'] ?>"
                                    <?= (($_POST['owner_id'] ?? '') == $owner['id']) ? 'selected' : '' ?>>
                                    <?= e($owner['name']) ?> (<?= e($owner['email']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= e($_POST['description'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= (($_POST['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= (($_POST['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-dark">
                                <i class="bi bi-building-add me-1"></i>Create Gym
                            </button>
                            <a href="<?= base_url('/super-admin/gyms') ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
