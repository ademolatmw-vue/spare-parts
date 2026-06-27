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

if (file_exists(__DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php')) {
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
    // Autoload other PHPMailer classes if needed
    if (file_exists(__DIR__ . '/../vendor/phpmailer/phpmailer/autoload.php')) {
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/autoload.php';
    }

    $GLOBALS['SPNG_MAILER_AVAILABLE'] = true;
} else {
    $GLOBALS['SPNG_MAILER_AVAILABLE'] = false;
    $GLOBALS['SPNG_EMAIL_ERROR'] = 'PHPMailer library not found. Install PHPMailer into vendor/phpmailer/phpmailer.';
}

