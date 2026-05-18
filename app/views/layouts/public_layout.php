<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GoBuff: Gym Hub - Gym Management System">
    <title><?= e($title ?? 'Welcome') ?> | <?= APP_NAME ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= asset('css/app.css') ?>?v=<?= filemtime(PUBLIC_PATH . '/assets/css/app.css') ?>" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8f9fa; }
        .public-navbar {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .public-brand { display: flex; align-items: center; gap: .6rem; text-decoration: none; }
        .public-brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.1rem;
        }
        .public-brand-name { font-weight: 700; font-size: 1.1rem; color: #212529; }
        .public-brand-sub  { font-size: .7rem; color: #6c757d; display: block; line-height: 1; }
        .public-content { min-height: calc(100vh - 64px); }
        .public-footer {
            background: #fff;
            border-top: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            text-align: center;
            color: #6c757d;
            font-size: .85rem;
        }
    </style>
</head>
<body>

<!-- Public Navbar -->
<nav class="public-navbar">
    <a href="<?= base_url('/') ?>" class="public-brand">
        <div class="public-brand-icon">
            <i class="bi bi-lightning-charge-fill"></i>
        </div>
        <div>
            <span class="public-brand-name"><?= APP_NAME ?></span>
            <span class="public-brand-sub">Gym Hub</span>
        </div>
    </a>
    <div class="d-flex gap-2 align-items-center">
        <a href="<?= base_url('/login') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-box-arrow-in-right me-1"></i>Log In
        </a>
        <a href="<?= base_url('/register') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus-fill me-1"></i>Sign Up
        </a>
    </div>
</nav>

<!-- Flash Messages -->
<div class="container-fluid px-4 pt-3">
    <?php foreach (['success','error','warning','info'] as $type): ?>
        <?php if ($msg = (new Session())->getFlash($type)): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-<?= $type === 'success' ? 'check-circle' : ($type === 'error' ? 'x-circle' : 'info-circle') ?> me-2"></i>
                <?= e($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Page Content -->
<div class="public-content">
    <?= $content ?>
</div>

<!-- Footer -->
<footer class="public-footer">
    &copy; <?= date('Y') ?> <strong><?= APP_NAME ?></strong>. All rights reserved.
    &nbsp;&bull;&nbsp;
    <a href="<?= base_url('/login') ?>" class="text-primary text-decoration-none">Log In</a>
    &nbsp;&bull;&nbsp;
    <a href="<?= base_url('/register') ?>" class="text-primary text-decoration-none">Sign Up</a>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
