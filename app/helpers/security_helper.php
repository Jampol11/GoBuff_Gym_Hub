<?php
/**
 * Security Helpers
 */

/**
 * Sanitize output to prevent XSS
 */
function e(string|null $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Sanitize input string
 */
function sanitize(string $value): string
{
    return trim(strip_tags($value));
}

/**
 * Hash a password
 */
function hash_password(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
}

/**
 * Verify a password
 */
function verify_password(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * Generate a random token
 */
function generate_token(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

/**
 * Generate a unique 4-digit membership ID
 * Checks the DB to guarantee no collision.
 */
function generate_membership_id(): string
{
    try {
        $db = Database::getInstance();
        do {
            $code = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
            $stmt = $db->prepare("SELECT COUNT(*) FROM members WHERE membership_id = ?");
            $stmt->execute([$code]);
        } while ((int)$stmt->fetchColumn() > 0);
        return $code;
    } catch (Exception $e) {
        // Fallback: timestamp-based 4-digit code
        return str_pad((string)(time() % 10000), 4, '0', STR_PAD_LEFT);
    }
}

/**
 * Validate file upload
 */
function validate_upload(array $file, array $allowedTypes = [], int $maxSize = 0): array
{
    $errors = [];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload failed with error code: ' . $file['error'];
        return $errors;
    }
    if ($maxSize && $file['size'] > $maxSize) {
        $errors[] = 'File size exceeds the maximum allowed size.';
    }
    if ($allowedTypes) {
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = 'File type not allowed.';
        }
    }
    return $errors;
}

/**
 * Move uploaded file safely
 */
function move_upload(array $file, string $destination): string|false
{
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = generate_token(16) . '.' . strtolower($ext);
    $path     = rtrim($destination, '/') . '/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $path)) {
        return $filename;
    }
    return false;
}

/**
 * Log activity
 */
function log_activity(string $action, string $description, int $userId = 0): void
{
    try {
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "INSERT INTO activity_logs (user_id, action, description, ip_address, created_at)
             VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->execute([
            $userId ?: (Auth::id() ?? 0),
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        ]);
    } catch (Exception $e) {
        // Silently fail - don't break app for logging errors
    }
}

/**
 * Format currency
 */
function format_currency(float $amount, string $symbol = '₱'): string
{
    return $symbol . number_format($amount, 2);
}

/**
 * Format date
 */
function format_date(string|null $date, string $format = 'M d, Y'): string
{
    if (!$date) return 'N/A';
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function format_datetime(string|null $datetime, string $format = 'M d, Y h:i A'): string
{
    if (!$datetime) return 'N/A';
    return date($format, strtotime($datetime));
}

/**
 * Status badge helper
 */
function status_badge(string $status): string
{
    $map = [
        'active'           => 'success',
        'inactive'         => 'secondary',
        'pending'          => 'warning',
        'approved'         => 'success',
        'rejected'         => 'danger',
        'expired'          => 'danger',
        'good'             => 'success',
        'needs_repair'     => 'warning',
        'under_maintenance'=> 'info',
        'completed'        => 'success',
        'cancelled'        => 'danger',
        'scheduled'        => 'primary',
    ];
    $color = $map[strtolower($status)] ?? 'secondary';
    $label = ucwords(str_replace('_', ' ', $status));
    return "<span class=\"badge bg-{$color}\">{$label}</span>";
}
