<?php
$notifModel = new Notification();
$unreadCount = $notifModel->getUnreadCount(auth_id() ?? 0);
$notifications = $notifModel->getForUser(auth_id() ?? 0, 5);
$user = auth_user();
?>
<nav class="top-navbar">
    <div class="navbar-left">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Home</a></li>
                <li class="breadcrumb-item active"><?= e($title ?? 'Dashboard') ?></li>
            </ol>
        </nav>
    </div>

    <div class="navbar-right">
        <!-- Dark Mode Toggle -->
        <button class="btn btn-icon" id="themeToggle" title="Toggle Dark Mode">
            <i class="bi bi-moon-fill" id="themeIcon"></i>
        </button>

        <!-- Notifications -->
        <div class="dropdown">
            <button class="btn btn-icon position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell-fill"></i>
                <?php if ($unreadCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $unreadCount > 9 ? '9+' : $unreadCount ?>
                    </span>
                <?php endif; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                <div class="dropdown-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Notifications</span>
                    <?php if ($unreadCount > 0): ?>
                        <form method="POST" action="<?= base_url('/notifications/read-all') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-link text-primary small p-0 border-0"
                                    onclick="return confirm('Mark all as read?')">Mark all read</button>
                        </form>
                    <?php endif; ?>
                </div>
                <?php if (empty($notifications)): ?>
                    <div class="dropdown-item text-center text-muted py-3">
                        <i class="bi bi-bell-slash d-block fs-4 mb-1"></i>
                        No notifications
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notif): ?>
                        <form method="POST" action="<?= base_url('/notifications/' . $notif['id'] . '/read') ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="dropdown-item notif-item <?= $notif['is_read'] ? '' : 'unread' ?>" style="text-align:left;width:100%;background:none;border:none;padding:0;">
                                <div class="notif-icon bg-<?= $notif['type'] === 'membership' ? 'primary' : 'info' ?>">
                                    <i class="bi bi-<?= $notif['type'] === 'membership' ? 'card-checklist' : 'bell' ?>"></i>
                                </div>
                                <div class="notif-content">
                                    <div class="notif-title"><?= e($notif['title']) ?></div>
                                    <div class="notif-text"><?= e(substr($notif['message'], 0, 60)) ?>...</div>
                                    <div class="notif-time"><?= format_datetime($notif['created_at']) ?></div>
                                </div>
                            </button>
                        </form>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="dropdown-footer">
                    <a href="<?= base_url('/notifications') ?>" class="text-center d-block text-primary small">
                        View all notifications
                    </a>
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <div class="dropdown">
            <button class="btn user-menu-btn" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-avatar-sm">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="user-menu-info d-none d-md-block">
                    <span class="user-menu-name"><?= e($user['name'] ?? 'User') ?></span>
                    <span class="user-menu-role"><?= role_label(auth_role() ?? '') ?></span>
                </div>
                <i class="bi bi-chevron-down ms-1"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><h6 class="dropdown-header"><?= e($user['email'] ?? '') ?></h6></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('/change-password') ?>">
                        <i class="bi bi-shield-lock me-2"></i>Change Password
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="<?= base_url('/logout') ?>">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
