<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Send Notification</h2></div>
        <a href="<?= base_url('/notifications') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="bi bi-send me-2"></i>Compose Notification</h6></div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/notifications') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Send To</label>
                            <select class="form-select" name="user_id">
                                <option value="all">All Users (Broadcast)</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= e($u['name']) ?> (<?= e($u['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Type</label>
                            <select class="form-select" name="type">
                                <option value="system">System</option>
                                <option value="membership">Membership</option>
                                <option value="booking">Booking</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="message" rows="4" required></textarea>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/notifications') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
