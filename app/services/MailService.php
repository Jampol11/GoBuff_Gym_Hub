<?php
/**
 * MailService — wraps PHPMailer for sending transactional emails
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailException;

class MailService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host       = MAIL_HOST;
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = MAIL_USERNAME;
        $this->mailer->Password   = MAIL_PASSWORD;
        $this->mailer->SMTPSecure = MAIL_ENCRYPTION === 'ssl'
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = MAIL_PORT;
        $this->mailer->CharSet    = 'UTF-8';
        $this->mailer->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
    }

    /**
     * Send an OTP verification email.
     */
    public function sendOtp(string $toEmail, string $toName, string $otp, string $purpose = 'login'): bool
    {
        $purposeLabel = match($purpose) {
            'register'       => 'account registration',
            'password_reset' => 'password reset',
            default          => 'sign-in',
        };

        $subject = APP_NAME . ' — Your ' . ucfirst($purpose) . ' Verification Code';

        $body = $this->otpEmailTemplate($toName, $otp, $purposeLabel);

        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = "Your {$purposeLabel} OTP is: {$otp}\n"
                                   . "It expires in " . OTP_EXPIRY_MINS . " minutes.\n"
                                   . "Do not share this code with anyone.";
            $this->mailer->send();
            return true;
        } catch (MailException $e) {
            error_log('MailService error: ' . $e->getMessage());
            return false;
        }
    }

    private function otpEmailTemplate(string $name, string $otp, string $purposeLabel): string
    {
        $appName   = APP_NAME;
        $expiry    = OTP_EXPIRY_MINS;
        $year      = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 0;">
    <tr><td align="center">
      <table width="480" cellpadding="0" cellspacing="0"
             style="background:#ffffff;border-radius:16px;overflow:hidden;
                    box-shadow:0 4px 24px rgba(0,0,0,.08);">
        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#0d6efd,#0ea5e9);
                     padding:32px 40px;text-align:center;">
            <div style="width:56px;height:56px;background:rgba(255,255,255,.2);
                        border-radius:14px;display:inline-flex;align-items:center;
                        justify-content:center;margin-bottom:12px;">
              ⚡
            </div>
            <h1 style="color:#fff;margin:0;font-size:22px;font-weight:800;">{$appName}</h1>
            <p style="color:rgba(255,255,255,.8);margin:4px 0 0;font-size:13px;">
              Email Verification
            </p>
          </td>
        </tr>
        <!-- Body -->
        <tr>
          <td style="padding:36px 40px;">
            <p style="color:#1e293b;font-size:15px;margin:0 0 8px;">
              Hi <strong>{$name}</strong>,
            </p>
            <p style="color:#475569;font-size:14px;margin:0 0 28px;line-height:1.6;">
              Use the code below to complete your <strong>{$purposeLabel}</strong>.
              This code expires in <strong>{$expiry} minutes</strong>.
            </p>
            <!-- OTP Box -->
            <div style="background:#f8fafc;border:2px dashed #0d6efd;border-radius:12px;
                        padding:24px;text-align:center;margin-bottom:28px;">
              <div style="font-size:42px;font-weight:900;letter-spacing:12px;
                          color:#0d6efd;font-family:'Courier New',monospace;">
                {$otp}
              </div>
            </div>
            <p style="color:#94a3b8;font-size:12px;margin:0;line-height:1.6;">
              🔒 Never share this code with anyone.<br>
              If you didn't request this, you can safely ignore this email.
            </p>
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#f8fafc;padding:16px 40px;text-align:center;
                     border-top:1px solid #e2e8f0;">
            <p style="color:#94a3b8;font-size:11px;margin:0;">
              © {$year} {$appName}. All rights reserved.
            </p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }
}
