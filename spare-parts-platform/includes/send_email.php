<?php
// Send email with PHPMailer using Gmail SMTP.
// Email failures must NOT break registration/login.

function spng_send_email(string $toEmail, string $toName, string $subject, string $bodyHtml, string $bodyText = ''): bool {
    $cfg = require __DIR__ . '/email_config.php';

    if (empty($cfg['enabled'])) {
        return false;
    }

    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // Load PHPMailer if present
    require_once __DIR__ . '/phpmailer_stub.php';

    if (empty($GLOBALS['SPNG_MAILER_AVAILABLE'])) {
        // Fail silently
        return false;
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);


    try {
        $mail->isSMTP();
        $mail->Host = $cfg['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $cfg['smtp_username'];
        $mail->Password = $cfg['smtp_password'];
        $mail->SMTPSecure = $cfg['smtp_secure'];
        $mail->Port = (int)$cfg['smtp_port'];

        $mail->setFrom($cfg['from_email'], $cfg['from_name']);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $bodyHtml;
        if ($bodyText !== '') {
            $mail->AltBody = $bodyText;
        }

        $mail->send();
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

