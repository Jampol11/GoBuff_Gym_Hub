<?php
/**
 * GoBuff: Mail & OAuth Configuration — EXAMPLE FILE
 * ─────────────────────────────────────────────────────────────
 * Copy this file to config/mail.php and fill in your real values.
 * config/mail.php is in .gitignore and must NEVER be committed.
 */

// ── SMTP (PHPMailer) ──────────────────────────────────────────
// Gmail: enable 2FA on your Google account, then generate an
// App Password at https://myaccount.google.com/apppasswords
// Use that 16-character app password below (not your real password).
define('MAIL_HOST',       'smtp.gmail.com');
define('MAIL_PORT',       587);
define('MAIL_USERNAME',   'your_email@gmail.com');
define('MAIL_PASSWORD',   'xxxx xxxx xxxx xxxx');  // Gmail App Password
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_EMAIL', 'your_email@gmail.com');
define('MAIL_FROM_NAME',  APP_NAME);

// ── Google OAuth 2.0 ─────────────────────────────────────────
// 1. Go to https://console.cloud.google.com/
// 2. APIs & Services → Credentials → Create OAuth 2.0 Client ID
// 3. Application type: Web application
// 4. Authorized redirect URI: http://localhost/GoBuff/public/auth/google/callback
define('GOOGLE_CLIENT_ID',     'YOUR_CLIENT_ID.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI',  APP_URL . '/auth/google/callback');

// ── OTP Settings ─────────────────────────────────────────────
define('OTP_LENGTH',       6);
define('OTP_EXPIRY_MINS',  10);
define('OTP_MAX_ATTEMPTS', 5);
