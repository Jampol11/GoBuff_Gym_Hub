<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Booking Details</h2></div>
        <a href="<?= base_url('/bookings') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <h6 class="mb-0">Booking #<?= $booking['id'] ?></h6>
                    <?= status_badge($booking['status']) ?>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-5">Date</dt><dd class="col-7"><?= format_date($booking['booking_date']) ?></dd>
                        <dt class="col-5">Time</dt><dd class="col-7"><?= e($booking['booking_time']) ?></dd>
                        <dt class="col-5">Duration</dt><dd class="col-7"><?= e($booking['duration']) ?> minutes</dd>
                        <dt class="col-5">Notes</dt><dd class="col-7"><?= e($booking['notes'] ?? 'None') ?></dd>
                        <dt class="col-5">Created</dt><dd class="col-7"><?= format_datetime($booking['created_at']) ?></dd>
                    </dl>
                    <?php if ($booking['status'] === 'scheduled'): ?>
                    <div class="d-flex gap-2 mt-3">
                        <form method="POST" action="<?= base_url('/bookings/' . $booking['id'] . '/complete') ?>">
                            <?= csrf_field() ?>
                            <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Mark Complete</button>
                        </form>
                        <form method="POST" action="<?= base_url('/bookings/' . $booking['id'] . '/cancel') ?>"
                              onsubmit="return confirm('Cancel this booking?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-danger"><i class="bi bi-x-lg me-1"></i>Cancel</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
