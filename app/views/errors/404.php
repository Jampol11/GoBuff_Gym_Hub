<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | GoBuff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="text-center">
        <div class="display-1 fw-bold text-primary mb-3">404</div>
        <i class="bi bi-search display-4 text-muted mb-3 d-block"></i>
        <h2 class="mb-3">Page Not Found</h2>
        <p class="text-muted mb-4">The page you're looking for doesn't exist or has been moved.</p>
        <a href="javascript:history.back()" class="btn btn-outline-secondary me-2">Go Back</a>
        <a href="<?= defined('APP_URL') ? APP_URL . '/dashboard' : '/dashboard' ?>" class="btn btn-primary">Dashboard</a>
    </div>
</div>
</body>
</html>
