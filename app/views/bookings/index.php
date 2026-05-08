<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div><h2 class="page-title">Trainer Bookings</h2><p class="page-subtitle">Schedule and manage trainer sessions</p></div>
        <a href="<?= base_url('/bookings/create') ?>" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-1"></i>New Booking</a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr><th>#</th><th>Member</th><th>Trainer</th><th>Date</th><th>Time</th><th>Duration</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                            <tr><td colspan="8" class="text-center py-5 text-muted">No bookings found</td></tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $i => $b): ?>
                                <tr>
                                    <td><?= $pagination['offset'] + $i + 1 ?></td>
                                    <td><?= e($b['member_name']) ?></td>
                                    <td><?= e($b['trainer_name']) ?></td>
                                    <td><?= format_date($b['booking_date']) ?></td>
                                    <td><?= e($b['booking_time']) ?></td>
                                    <td><?= e($b['duration']) ?> min</td>
                                    <td><?= status_badge($b['status']) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('/bookings/' . $b['id']) ?>" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                            <?php if ($b['status'] === 'scheduled'): ?>
                                                <form method="POST" action="<?= base_url('/bookings/' . $b['id'] . '/complete') ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button class="btn btn-outline-success" title="Complete"><i class="bi bi-check-lg"></i></button>
                                                </form>
                                                <form method="POST" action="<?= base_url('/bookings/' . $b['id'] . '/cancel') ?>"
                                                      class="d-inline" onsubmit="return confirm('Cancel this booking?')">
                                                    <?= csrf_field() ?>
                                                    <button class="btn btn-outline-danger" title="Cancel"><i class="bi bi-x-lg"></i></button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?></small>
            <?= pagination_links($pagination, base_url('/bookings')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
