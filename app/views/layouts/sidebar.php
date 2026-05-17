<?php $user = auth_user(); $role = auth_role(); ?>
<nav class="sidebar" id="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-logo">
            <i class="bi bi-lightning-charge-fill"></i>
        </div>
        <div class="brand-text">
            <span class="brand-name">GoBuff</span>
            <span class="brand-sub">Gym Hub</span>
        </div>
        <button class="sidebar-toggle-btn d-lg-none" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- User Info -->
    <div class="sidebar-user">
        <div class="user-avatar">
            <i class="bi bi-person-circle"></i>
        </div>
        <div class="user-info">
            <span class="user-name"><?= e($user['name'] ?? 'User') ?></span>
            <span class="user-role"><?= role_label($role ?? '') ?></span>
        </div>
    </div>

    <!-- Navigation -->
    <ul class="sidebar-nav">
        <li class="nav-label">Main</li>

        <li class="nav-item">
            <a href="<?= base_url('/dashboard') ?>" class="nav-link <?= is_active('/dashboard') ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <?php if (has_role(['user'])): ?>
        <!-- User: Apply for Role -->
        <li class="nav-item">
            <a href="<?= base_url('/role-application/apply') ?>" class="nav-link <?= is_active('/role-application/apply') ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>Apply for Role</span>
            </a>
        </li>
        <?php
        // Show "My Membership" link if user has an approved member application
        $raCheck = new RoleApplication();
        $approvedMemberApp = $raCheck->query(
            "SELECT id FROM role_applications WHERE user_id = ? AND requested_role = 'member' AND status = 'approved' LIMIT 1",
            [auth_id()]
        )->fetch();
        if ($approvedMemberApp):
        ?>
        <li class="nav-item">
            <a href="<?= base_url('/my-membership') ?>" class="nav-link <?= is_active('/my-membership') ?>">
                <i class="bi bi-credit-card-2-front-fill text-primary"></i>
                <span class="fw-semibold text-primary">Pay Membership</span>
            </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (has_role(['user'])): ?>
        <!-- Only users without a role can apply as Gym Owner -->
        <li class="nav-item">
            <a href="<?= base_url('/gym-owner-application/apply') ?>" class="nav-link <?= is_active('/gym-owner-application') ?>">
                <i class="bi bi-building-fill-gear"></i>
                <span>Apply as Gym Owner</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (has_role(['admin'])): ?>
        <!-- Admin Officer Features -->
        <li class="nav-item">
            <a href="<?= base_url('/my-profile') ?>" class="nav-link <?= is_active('/my-profile') ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>My Profile</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/attendance') ?>" class="nav-link <?= is_active('/attendance') ?>">
                <i class="bi bi-clock-history"></i>
                <span>Attendance (Time In/Out)</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/member-applications') ?>" class="nav-link <?= is_active('/member-applications') ?>">
                <i class="bi bi-card-checklist"></i>
                <span>Membership Applications</span>
                <?php
                $maModel = new RoleApplication();
                $pendingMa = $maModel->countByStatusAndRole('pending', ['member']);
                if ($pendingMa > 0): ?>
                    <span class="badge bg-danger ms-auto"><?= $pendingMa ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/memberships') ?>" class="nav-link <?= is_active('/memberships') ?>">
                <i class="bi bi-card-checklist"></i>
                <span>Verify Memberships</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/members') ?>" class="nav-link <?= is_active('/members') ?>">
                <i class="bi bi-people-fill"></i>
                <span>Members</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/notifications') ?>" class="nav-link <?= is_active('/notifications') ?>">
                <i class="bi bi-bell-fill"></i>
                <span>Membership Expiry Alerts</span>
                <?php
                $notifModel = new Notification();
                $unread = $notifModel->getUnreadCount(auth_id() ?? 0);
                if ($unread > 0): ?>
                    <span class="badge bg-danger ms-auto"><?= $unread ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/admin/users') ?>" class="nav-link <?= is_active('/admin/users') ?>">
                <i class="bi bi-person-gear"></i>
                <span>User Management</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (has_role(['trainer'])): ?>
        <!-- Trainer Features -->
        <li class="nav-label">Fitness</li>
        <li class="nav-item">
            <a href="<?= base_url('/my-profile') ?>" class="nav-link <?= is_active('/my-profile') ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>My Profile</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/attendance') ?>" class="nav-link <?= is_active('/attendance') ?>">
                <i class="bi bi-clock-history"></i>
                <span>Attendance (Time In/Out)</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/members') ?>" class="nav-link <?= is_active('/members') ?>">
                <i class="bi bi-people-fill"></i>
                <span>Members</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/trainers/fitness-plans') ?>" class="nav-link <?= is_active('/trainers/fitness-plans') ?>">
                <i class="bi bi-clipboard2-pulse-fill"></i>
                <span>Fitness Plans</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/trainers/nutrition-plans') ?>" class="nav-link <?= is_active('/trainers/nutrition-plans') ?>">
                <i class="bi bi-egg-fried"></i>
                <span>Nutrition Plans</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/trainers/progress') ?>" class="nav-link <?= is_active('/trainers/progress') ?>">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Progress Tracking</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/bookings') ?>" class="nav-link <?= is_active('/bookings') ?>">
                <i class="bi bi-calendar-check-fill"></i>
                <span>Bookings</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (has_role(['maintenance'])): ?>
        <!-- Maintenance Supervisor Features -->
        <li class="nav-label">Operations</li>
        <li class="nav-item">
            <a href="<?= base_url('/my-profile') ?>" class="nav-link <?= is_active('/my-profile') ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>My Profile</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/attendance') ?>" class="nav-link <?= is_active('/attendance') ?>">
                <i class="bi bi-clock-history"></i>
                <span>Attendance (Time In/Out)</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/equipment') ?>" class="nav-link <?= is_active('/equipment') ?>">
                <i class="bi bi-tools"></i>
                <span>Equipment</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/maintenance') ?>" class="nav-link <?= is_active('/maintenance') ?>">
                <i class="bi bi-wrench-adjustable-circle-fill"></i>
                <span>Maintenance</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (has_role(['marketing'])): ?>
        <!-- Marketing Officer Features -->
        <li class="nav-label">Marketing</li>
        <li class="nav-item">
            <a href="<?= base_url('/my-profile') ?>" class="nav-link <?= is_active('/my-profile') ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>My Profile</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/attendance') ?>" class="nav-link <?= is_active('/attendance') ?>">
                <i class="bi bi-clock-history"></i>
                <span>Attendance (Time In/Out)</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/campaigns') ?>" class="nav-link <?= is_active('/campaigns') ?>">
                <i class="bi bi-megaphone-fill"></i>
                <span>Campaigns</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (has_role(['member'])): ?>
        <!-- Member Features -->
        <li class="nav-item">
            <a href="<?= base_url('/my-membership') ?>" class="nav-link <?= is_active('/my-membership') ?>">
                <i class="bi bi-card-checklist"></i>
                <span>My Membership</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/checkins') ?>" class="nav-link <?= is_active('/checkins') ?>">
                <i class="bi bi-door-open-fill"></i>
                <span>Check-Ins</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/bookings') ?>" class="nav-link <?= is_active('/bookings') ?>">
                <i class="bi bi-calendar-check-fill"></i>
                <span>Bookings</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/trainers') ?>" class="nav-link <?= is_active('/trainers') ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>Trainers</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/diet') ?>" class="nav-link <?= is_active('/diet') ?>">
                <i class="bi bi-journal-medical"></i>
                <span>Dietary Log</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/equipment') ?>" class="nav-link <?= is_active('/equipment') ?>">
                <i class="bi bi-tools"></i>
                <span>Equipment</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (has_role(['gym_owner'])): ?>
        <!-- Gym Owner: Full Access -->
        <li class="nav-item">
            <a href="<?= base_url('/members') ?>" class="nav-link <?= is_active('/members') ?>">
                <i class="bi bi-people-fill"></i>
                <span>Members</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/memberships') ?>" class="nav-link <?= is_active('/memberships') ?>">
                <i class="bi bi-card-checklist"></i>
                <span>Memberships</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/checkins') ?>" class="nav-link <?= is_active('/checkins') ?>">
                <i class="bi bi-door-open-fill"></i>
                <span>Check-Ins</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/bookings') ?>" class="nav-link <?= is_active('/bookings') ?>">
                <i class="bi bi-calendar-check-fill"></i>
                <span>Bookings</span>
            </a>
        </li>

        <li class="nav-label">Fitness</li>
        <li class="nav-item">
            <a href="<?= base_url('/trainers') ?>" class="nav-link <?= is_active('/trainers') ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>Trainers</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/trainers/fitness-plans') ?>" class="nav-link <?= is_active('/trainers/fitness-plans') ?>">
                <i class="bi bi-clipboard2-pulse-fill"></i>
                <span>Fitness Plans</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/trainers/nutrition-plans') ?>" class="nav-link <?= is_active('/trainers/nutrition-plans') ?>">
                <i class="bi bi-egg-fried"></i>
                <span>Nutrition Plans</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/trainers/progress') ?>" class="nav-link <?= is_active('/trainers/progress') ?>">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Progress Tracking</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/diet') ?>" class="nav-link <?= is_active('/diet') ?>">
                <i class="bi bi-journal-medical"></i>
                <span>Dietary Log</span>
            </a>
        </li>

        <li class="nav-label">Operations</li>
        <li class="nav-item">
            <a href="<?= base_url('/equipment') ?>" class="nav-link <?= is_active('/equipment') ?>">
                <i class="bi bi-tools"></i>
                <span>Equipment</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/maintenance') ?>" class="nav-link <?= is_active('/maintenance') ?>">
                <i class="bi bi-wrench-adjustable-circle-fill"></i>
                <span>Maintenance</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/attendance') ?>" class="nav-link <?= is_active('/attendance') ?>">
                <i class="bi bi-clock-history"></i>
                <span>Attendance</span>
            </a>
        </li>

        <li class="nav-label">Marketing</li>
        <li class="nav-item">
            <a href="<?= base_url('/campaigns') ?>" class="nav-link <?= is_active('/campaigns') ?>">
                <i class="bi bi-megaphone-fill"></i>
                <span>Campaigns</span>
            </a>
        </li>

        <li class="nav-label">Owner Hub</li>
        <li class="nav-item">
            <a href="<?= base_url('/owner') ?>" class="nav-link <?= is_active('/owner') ?>">
                <i class="bi bi-building-fill-gear"></i>
                <span>Owner Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/owner/documents') ?>" class="nav-link <?= is_active('/owner/documents') ?>">
                <i class="bi bi-file-earmark-lock2-fill"></i>
                <span>Legal Documents</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/owner/budgets') ?>" class="nav-link <?= is_active('/owner/budgets') ?>">
                <i class="bi bi-wallet2"></i>
                <span>Budget Plans</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/owner/expenses') ?>" class="nav-link <?= is_active('/owner/expenses') ?>">
                <i class="bi bi-receipt-cutoff"></i>
                <span>Expenses</span>
            </a>
        </li>

        <li class="nav-label">Staff Management</li>
        <li class="nav-item">
            <a href="<?= base_url('/employees') ?>" class="nav-link <?= is_active('/employees') ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>Employee Profiles</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/employees/schedule') ?>" class="nav-link <?= is_active('/employees/schedule') ?>">
                <i class="bi bi-calendar2-week-fill"></i>
                <span>Work Schedule Sheet</span>
            </a>
        </li>

        <li class="nav-label">System</li>
        <li class="nav-item">
            <a href="<?= base_url('/role-applications') ?>" class="nav-link <?= is_active('/role-applications') ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>Staff Role Applications</span>
                <?php
                $raModel = new RoleApplication();
                $pendingRa = $raModel->countByStatusAndRole('pending', ['trainer','marketing','maintenance','admin']);
                if ($pendingRa > 0): ?>
                    <span class="badge bg-warning text-dark ms-auto"><?= $pendingRa ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/gym-owner-applications') ?>" class="nav-link <?= is_active('/gym-owner-applications') ?>">
                <i class="bi bi-building-fill-gear"></i>
                <span>Owner Applications</span>
                <?php
                $goaModel = new GymOwnerApplication();
                $pendingGoa = $goaModel->countByStatus('pending');
                if ($pendingGoa > 0): ?>
                    <span class="badge bg-danger ms-auto"><?= $pendingGoa ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('/admin/users') ?>" class="nav-link <?= is_active('/admin/users') ?>">
                <i class="bi bi-person-gear"></i>
                <span>User Management</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- Common for all roles -->
        <li class="nav-label">Account</li>
        <li class="nav-item">
            <a href="<?= base_url('/notifications') ?>" class="nav-link <?= is_active('/notifications') ?>">
                <i class="bi bi-bell-fill"></i>
                <span>Notifications</span>
                <?php
                $notifModel = new Notification();
                $unread = $notifModel->getUnreadCount(auth_id() ?? 0);
                if ($unread > 0): ?>
                    <span class="badge bg-danger ms-auto"><?= $unread ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= base_url('/change-password') ?>" class="nav-link <?= is_active('/change-password') ?>">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Change Password</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= base_url('/logout') ?>" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</nav>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
