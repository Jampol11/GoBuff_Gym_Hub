<?php
/**
 * OtpService — generate, store, verify, and send OTP codes
 */
class OtpService
{
    private Database|PDO $db;
    private MailService $mailer;

    public function __construct()
    {
        $this->db     = Database::getInstance();
        $this->mailer = new MailService();
    }

    /**
     * Generate a new OTP, persist it, and email it to the user.
     * Returns the OTP string on success, empty string if mail failed.
     * The OTP is always saved to DB regardless of mail result.
     */
    public function sendOtp(int $userId, string $email, string $name, string $purpose = 'login'): string
    {
        // Invalidate any existing unused OTPs for this user+purpose
        $this->db->prepare(
            "UPDATE otp_tokens SET used_at = NOW()
             WHERE user_id = ? AND purpose = ? AND used_at IS NULL"
        )->execute([$userId, $purpose]);

        $otp       = $this->generateCode();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINS . ' minutes'));

        $this->db->prepare(
            "INSERT INTO otp_tokens (user_id, token, purpose, attempts, expires_at)
             VALUES (?, ?, ?, 0, ?)"
        )->execute([$userId, $otp, $purpose, $expiresAt]);

        $sent = $this->mailer->sendOtp($email, $name, $otp, $purpose);

        // Always return the OTP so callers can show it in dev mode
        // even if mail fails. In production, log the failure.
        if (!$sent) {
            error_log("OtpService: mail failed for user {$userId} ({$email})");
        }

        return $otp;
    }

    /**
     * Verify a submitted OTP.
     * Returns 'ok' | 'invalid' | 'expired' | 'locked'
     */
    public function verify(int $userId, string $submittedToken, string $purpose = 'login'): string
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM otp_tokens
             WHERE user_id = ? AND purpose = ? AND used_at IS NULL
             ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->execute([$userId, $purpose]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return 'invalid';
        }

        // Locked out?
        if ((int)$row['attempts'] >= OTP_MAX_ATTEMPTS) {
            return 'locked';
        }

        // Expired?
        if (strtotime($row['expires_at']) < time()) {
            return 'expired';
        }

        // Wrong code — increment attempts
        if (!hash_equals($row['token'], $submittedToken)) {
            $this->db->prepare(
                "UPDATE otp_tokens SET attempts = attempts + 1 WHERE id = ?"
            )->execute([$row['id']]);
            return 'invalid';
        }

        // Mark as used
        $this->db->prepare(
            "UPDATE otp_tokens SET used_at = NOW() WHERE id = ?"
        )->execute([$row['id']]);

        return 'ok';
    }

    private function generateCode(): string
    {
        return str_pad((string)random_int(0, (int)str_repeat('9', OTP_LENGTH)), OTP_LENGTH, '0', STR_PAD_LEFT);
    }
}
