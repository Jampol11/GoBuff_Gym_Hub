<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Book a Trainer</h2></div>
        <a href="<?= base_url('/bookings') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white"><h6 class="mb-0">Booking Details</h6></div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/bookings') ?>" method="POST" novalidate>
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Member <span class="text-danger">*</span></label>
                                <select class="form-select" name="member_id" required>
                                    <option value="">Select member</option>
                                    <?php foreach ($members as $m): ?>
                                        <option value="<?= $m['id'] ?>"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Trainer <span class="text-danger">*</span></label>
                                <select class="form-select" name="trainer_id" required>
                                    <option value="">Select trainer</option>
                                    <?php foreach ($trainers as $t): ?>
                                        <option value="<?= $t['id'] ?>"><?= e($t['first_name'] . ' ' . $t['last_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="booking_date" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="booking_time" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Duration (minutes)</label>
                                <select class="form-select" name="duration">
                                    <option value="30">30 minutes</option>
                                    <option value="60" selected>60 minutes</option>
                                    <option value="90">90 minutes</option>
                                    <option value="120">120 minutes</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Any special requests or notes..."></textarea>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= base_url('/bookings') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-calendar-plus me-1"></i>Create Booking</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
