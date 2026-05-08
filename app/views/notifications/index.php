<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <div>
            <h2 class="page-title">Notifications</h2>
            <p class="page-subtitle"><?= $unread_count ?> unread notification(s)</p>
        </div>
        <div class="page-actions">
            <?php if (has_role(['gym_owner','admin'])): ?>
            <a href="<?= base_url('/notifications/create') ?>" class="btn btn-primary"><i class="bi bi-send me-1"></i>Send Notification</a>
            <?php endif; ?>
            <?php if ($unread_count > 0): ?>
            <form method="POST" action="<?= base_url('/notifications/read-all') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-check-all me-1"></i>Mark All Read</button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if (empty($notifications)): ?>
                <div class="card">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="bi bi-bell-slash fs-1 d-block mb-3"></i>
                        <h5>No notifications</h5>
                        <p>You're all caught up!</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $n): ?>
                    <div class="card mb-3 <?= $n['is_read'] ? '' : 'border-primary border-start border-3' ?>">
                        <div class="card-body d-flex align-items-start gap-3">
                            <div class="notif-icon-lg bg-<?= $n['type'] === 'membership' ? 'primary' : ($n['type'] === 'system' ? 'secondary' : 'info') ?> text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                <i class="bi bi-<?= $n['type'] === 'membership' ? 'card-checklist' : 'bell' ?>"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="fw-semibold mb-1 <?= $n['is_read'] ? 'text-muted' : '' ?>"><?= e($n['title']) ?></h6>
                                    <small class="text-muted ms-2 flex-shrink-0"><?= format_datetime($n['created_at']) ?></small>
                                </div>
                                <p class="mb-2 <?= $n['is_read'] ? 'text-muted' : '' ?>"><?= e($n['message']) ?></p>
                                <div class="d-flex gap-2">
                                    <?php if (!$n['is_read']): ?>
                                        <a href="<?= base_url('/notifications/' . $n['id'] . '/read') ?>"
                                           class="btn btn-sm btn-outline-primary">Mark as Read</a>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Read</span>
                                    <?php endif; ?>
                                    <?php if (has_role(['gym_owner','admin'])): ?>
                                        <form method="POST" action="<?= base_url('/notifications/' . $n['id'] . '/delete') ?>"
                                              class="d-inline" onsubmit="return confirm('Delete this notification?')">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
