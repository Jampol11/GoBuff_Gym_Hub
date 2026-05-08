<?php
/**
 * One-time migration runner — DELETE THIS FILE after running.
 */
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$db = Database::getInstance();

$statements = [
    // OTP tokens table
    "CREATE TABLE IF NOT EXISTS `otp_tokens` (
        `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id`    INT UNSIGNED NOT NULL,
        `token`      VARCHAR(10)  NOT NULL,
        `purpose`    ENUM('login','register','password_reset') NOT NULL DEFAULT 'login',
        `attempts`   TINYINT UNSIGNED NOT NULL DEFAULT 0,
        `expires_at` DATETIME     NOT NULL,
        `used_at`    DATETIME     NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        INDEX `idx_otp_user_id` (`user_id`),
        INDEX `idx_otp_expires` (`expires_at`)
    ) ENGINE=InnoDB",

    // Google OAuth columns
    "ALTER TABLE `users`
        ADD COLUMN IF NOT EXISTS `google_id`     VARCHAR(100) NULL AFTER `username`",
    "ALTER TABLE `users`
        ADD COLUMN IF NOT EXISTS `avatar_url`    VARCHAR(500) NULL AFTER `google_id`",
    "ALTER TABLE `users`
        ADD COLUMN IF NOT EXISTS `auth_provider` ENUM('local','google') NOT NULL DEFAULT 'local' AFTER `avatar_url`",
];

$results = [];
foreach ($statements as $sql) {
    try {
        $db->exec($sql);
        $results[] = ['status' => 'OK', 'sql' => substr(trim($sql), 0, 80)];
    } catch (PDOException $e) {
        $results[] = ['status' => 'ERR', 'msg' => $e->getMessage(), 'sql' => substr(trim($sql), 0, 80)];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Migration 003</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container" style="max-width:800px">
    <h3 class="mb-4">Migration 003 — OTP + Google OAuth</h3>
    <table class="table table-bordered">
        <thead class="table-dark"><tr><th>Status</th><th>Statement</th><th>Error</th></tr></thead>
        <tbody>
        <?php foreach ($results as $r): ?>
        <tr class="<?= $r['status'] === 'OK' ? 'table-success' : 'table-danger' ?>">
            <td><strong><?= $r['status'] ?></strong></td>
            <td><code><?= htmlspecialchars($r['sql']) ?>…</code></td>
            <td><?= htmlspecialchars($r['msg'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="alert alert-warning mt-3">
        <strong>⚠️ Delete this file after running:</strong>
        <code>public/run_migration.php</code>
    </div>
</div>
</body>
</html>
