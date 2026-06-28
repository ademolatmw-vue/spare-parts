<?php
/*
 * PHPMailer loader / fallback.
 *
 * This file expects PHPMailer to exist under:
 *   spare-parts-platform/vendor/phpmailer/phpmailer/src/PHPMailer.php
 *   spare-parts-platform/vendor/phpmailer/phpmailer/src/SMTP.php
 *
 * If PHPMailer is not installed, the helper will set $GLOBALS['SPNG_EMAIL_ERROR']
 * so calling code can safely ignore email failures.
 */

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $GLOBALS['SPNG_MAILER_AVAILABLE'] = true;
} elseif (file_exists(__DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php')) {
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
    $GLOBALS['SPNG_MAILER_AVAILABLE'] = true;
} else {
    $GLOBALS['SPNG_MAILER_AVAILABLE'] = false;
    $GLOBALS['SPNG_EMAIL_ERROR'] = 'PHPMailer library not found. Install PHPMailer into vendor/phpmailer/phpmailer.';
}

