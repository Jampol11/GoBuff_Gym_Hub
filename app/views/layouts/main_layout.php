<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GoBuff: Gym Hub - Gym Management System">
    <title><?= e($title ?? 'Dashboard') ?> | <?= APP_NAME ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= asset('css/app.css') ?>?v=<?= filemtime(PUBLIC_PATH . '/assets/css/app.css') ?>" rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<?php require VIEWS_PATH . '/layouts/sidebar.php'; ?>

<!-- Main Content Wrapper -->
<div class="main-content" id="mainContent">

    <!-- Top Navbar -->
    <?php require VIEWS_PATH . '/layouts/navbar.php'; ?>

    <!-- Page Content -->
    <div class="content-area">
        <!-- Flash Messages -->
        <?php foreach (['success','error','warning','info'] as $type): ?>
            <?php if ($msg = (new Session())->getFlash($type)): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show mx-3 mt-3" role="alert">
                    <i class="bi bi-<?= $type === 'success' ? 'check-circle' : ($type === 'error' ? 'x-circle' : 'info-circle') ?> me-2"></i>
                    <?= e($msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- View Content -->
        <?= $content ?>
    </div>

    <!-- Footer -->
    <?php require VIEWS_PATH . '/layouts/footer.php'; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Custom JS -->
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
