<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Auth') ?> | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
</head>
<body class="auth-bg-body">

    <div class="auth-bg-overlay"></div>

    <div class="auth-modal-wrap">
        <div class="auth-modal-card">

            <!-- ① Left: Branding panel -->
            <div class="auth-panel-left">
                <div class="blob blob-1"></div>
                <div class="blob blob-2"></div>
                <div class="blob blob-3"></div>

                <div class="auth-brand-content">
                    <div class="auth-brand-logo">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h1 class="auth-brand-name"><?= APP_NAME ?></h1>
                    <p class="auth-brand-tagline">Your all-in-one gym management platform. Track members, trainers, equipment and more.</p>
                    <ul class="auth-feature-list">
                        <li><i class="bi bi-check-circle-fill"></i> Member &amp; Membership Management</li>
                        <li><i class="bi bi-check-circle-fill"></i> Trainer Scheduling &amp; Plans</li>
                        <li><i class="bi bi-check-circle-fill"></i> Equipment &amp; Maintenance Tracking</li>
                        <li><i class="bi bi-check-circle-fill"></i> Real-time Check-in &amp; Attendance</li>
                    </ul>
                </div>

                <!-- ② Wavy SVG edge — overlays the right edge of the left panel -->
                <div class="auth-cloud-divider" aria-hidden="true">
                    <svg viewBox="0 0 60 520" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M60,0 C40,40 20,60 40,100 C60,140 20,160 40,200 C60,240 20,260 40,300 C60,340 20,360 40,400 C60,440 20,460 40,500 C50,520 60,520 60,520 L60,0 Z" fill="#ffffff"/>
                    </svg>
                </div>
            </div>

            <!-- ③ Right: Form panel -->
            <div class="auth-panel-right">
                <?php foreach (['success','error','warning','info'] as $type): ?>
                    <?php if ($msg = (new Session())->getFlash($type)): ?>
                        <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show mb-3" role="alert">
                            <i class="bi bi-<?= $type === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                            <?= e($msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?= $content ?>
            </div>

        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
