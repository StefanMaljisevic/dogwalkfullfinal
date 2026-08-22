<?php
require_once __DIR__ . '/db_config.php';

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    public static function sendActivationEmail(string $email, string $token): bool
    {
        $link = APP_URL . '/activate.php?token=' . urlencode($token);
        $body = '<p>Hello,</p><p>Activate your Dog Walk account by clicking this link:</p><p><a href="' . $link . '">' . $link . '</a></p>';
        return self::sendEmail($email, 'Dog Walk - activation link', $body, 'Activation link: ' . $link);
    }

    public static function sendResetEmail(string $email, string $token): bool
    {
        $link = APP_URL . '/reset_password.php?token=' . urlencode($token);
        $body = '<p>Hello,</p><p>Reset your password by clicking this link:</p><p><a href="' . $link . '">' . $link . '</a></p>';
        return self::sendEmail($email, 'Dog Walk - password reset', $body, 'Password reset link: ' . $link);
    }

    public static function sendContactEmail(string $email, string $subject, string $message): bool
    {
        $safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
        return self::sendEmail($email, $subject, '<p>' . $safeMessage . '</p>', $message);
    }

    private static function sendEmail(string $email, string $subject, string $htmlBody, string $plainBody): bool
    {
        if (!class_exists(PHPMailer::class)) {
            self::logMail($email, $subject, $plainBody . ' | PHPMailer is not installed. Run: composer install');
            return false;
        }

        // Refuse to connect until a real Google App Password has been configured locally.
        if (SMTP_PASSWORD === '' || str_contains(SMTP_PASSWORD, 'PASTE_YOUR')) {
            self::logMail($email, $subject, $plainBody . ' | SMTP App Password is not configured in includes/smtp_config.local.php');
            return false;
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            $mail->Timeout = 15;

            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $plainBody;
            $mail->send();
            return true;
        } catch (Exception $exception) {
            self::logMail($email, $subject, $plainBody . ' | Mailer error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    private static function logMail(string $email, string $subject, string $message): void
    {
        $line = date('Y-m-d H:i:s') . " | To: {$email} | {$subject} | {$message}\n";
        file_put_contents(__DIR__ . '/../logs/mail_log.txt', $line, FILE_APPEND);
    }
}
