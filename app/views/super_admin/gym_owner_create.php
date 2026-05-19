<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?= base_url('/super-admin/gym-owners') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h1 class="h3 fw-bold mb-0">
            <i class="bi bi-person-plus-fill me-2 text-danger"></i>Create Gym Owner Account
        </h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('/super-admin/gym-owners') ?>">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= e($_POST['name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= e($_POST['email'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control"
                                   value="<?= e($_POST['username'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Min. 8 chars, 1 uppercase, 1 special char" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-person-plus-fill me-1"></i>Create Gym Owner
                            </button>
                            <a href="<?= base_url('/super-admin/gym-owners') ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
