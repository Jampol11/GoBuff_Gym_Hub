<?php
/**
 * GoBuff: Gym Hub — One-Time Password Fix Script
 *
 * Run this ONCE if you can't log in with the seed accounts.
 * Access via: http://localhost/GoBuff/public/fix_passwords.php
 *
 * DELETE THIS FILE immediately after running it.
 */

// ── Simple IP guard — only allow localhost ──────────────────
$allowedIPs = ['127.0.0.1', '::1', 'localhost'];
if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIPs)) {
    http_response_code(403);
    die('Access denied. This script can only be run from localhost.');
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$newPassword = 'password'; // the plain-text password for all seed accounts
$hash        = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

$seedEmails = [
    'owner@gobuff.com',
    'admin@gobuff.com',
    'marketing@gobuff.com',
    'trainer@gobuff.com',
    'maintenance@gobuff.com',
    'member@gobuff.com',
];

$results = [];

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    foreach ($seedEmails as $email) {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hash, $email]);
        $results[] = [
            'email'   => $email,
            'updated' => $stmt->rowCount() > 0,
        ];
    }

    // Verify one of them works
    $stmt = $pdo->prepare("SELECT password FROM users WHERE email = 'owner@gobuff.com'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $verified = $row && password_verify($newPassword, $row['password']);

} catch (PDOException $e) {
    die('<div style="color:red;font-family:monospace;padding:20px">
        <strong>Database Error:</strong><br>' . htmlspecialchars($e->getMessage()) . '
        <br><br>Check your <code>config/database.php</code> settings.
    </div>');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Fix | GoBuff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:640px">

    <div class="text-center mb-4">
        <div style="width:64px;height:64px;background:linear-gradient(135deg,#0d6efd,#0ea5e9);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:1.8rem;color:white">
            <i class="bi bi-lightning-charge-fill"></i>
        </div>
        <h3 class="fw-bold">GoBuff Password Fix</h3>
        <p class="text-muted">Resets all seed account passwords to <code>password</code></p>
    </div>

    <!-- Verification result -->
    <div class="alert <?= $verified ? 'alert-success' : 'alert-danger' ?> d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-<?= $verified ? 'check-circle-fill' : 'x-circle-fill' ?> fs-5"></i>
        <div>
            <?php if ($verified): ?>
                <strong>Success!</strong> Passwords updated and verified. You can now log in.
            <?php else: ?>
                <strong>Verification failed.</strong> Passwords were updated but the verify check failed. Try logging in anyway.
            <?php endif; ?>
        </div>
    </div>

    <!-- Results table -->
    <div class="card mb-4">
        <div class="card-header fw-semibold">Updated Accounts</div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr><th>Email</th><th>Password</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['email']) ?></td>
                            <td><code>password</code></td>
                            <td>
                                <?php if ($r['updated']): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Updated</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation me-1"></i>Not found</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Next steps -->
    <div class="card border-warning mb-4">
        <div class="card-body">
            <h6 class="fw-bold text-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>Important — Do This Now</h6>
            <ol class="mb-0 small">
                <li class="mb-1"><strong>Delete this file</strong> immediately: <code>public/fix_passwords.php</code></li>
                <li class="mb-1">Log in and change all default passwords via <strong>Change Password</strong></li>
                <li>Never leave default passwords in a production environment</li>
            </ol>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-center">
        <a href="<?= defined('APP_URL') ? APP_URL . '/login' : '/GoBuff/public/login' ?>"
           class="btn btn-primary">
            <i class="bi bi-box-arrow-in-right me-1"></i>Go to Login
        </a>
        <button onclick="if(confirm('Delete this fix script now?')) { fetch('?delete=1').then(()=>location.href='login') }"
                class="btn btn-outline-danger">
            <i class="bi bi-trash me-1"></i>Delete This File
        </button>
    </div>
</div>
</body>
</html>
<?php
// Self-delete when requested
if (isset($_GET['delete'])) {
    @unlink(__FILE__);
    echo '<script>alert("File deleted."); window.location="' . (defined('APP_URL') ? APP_URL . '/login' : '/login') . '"</script>';
    exit;
}
