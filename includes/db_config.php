<?php
const DB_HOST = 'localhost';
const DB_NAME = 'dog_walk';
const DB_USER = 'root';
const DB_PASS = '';

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(dirname($scriptName), '/');
if (str_ends_with($basePath, '/admin') || str_ends_with($basePath, '/ajax')) {
    $basePath = rtrim(dirname($basePath), '/');
}
if ($basePath === '.' || $basePath === '/') {
    $basePath = '';
}

define('BASE_URL', $basePath);
define('APP_URL', 'http://localhost' . BASE_URL);

// Gmail SMTP configuration used by PHPMailer.
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_PORT = 587;
const SMTP_USERNAME = 'markoprobojcevic@gmail.com';
const SMTP_SECURE = 'tls';
const MAIL_FROM_ADDRESS = 'markoprobojcevic@gmail.com';
const MAIL_FROM_NAME = 'Dog Walk';

// Keep the Google App Password outside the tracked project configuration.
$smtpPassword = getenv('DOG_WALK_SMTP_PASSWORD') ?: '';
$localSmtpConfigPath = __DIR__ . '/smtp_config.local.php';

if (file_exists($localSmtpConfigPath)) {
    $localSmtpConfig = require $localSmtpConfigPath;
    if (is_array($localSmtpConfig) && isset($localSmtpConfig['password'])) {
        $smtpPassword = (string) $localSmtpConfig['password'];
    }
}

// Google displays App Passwords with spaces; PHPMailer should receive them without spaces.
define('SMTP_PASSWORD', str_replace(' ', '', trim($smtpPassword)));
