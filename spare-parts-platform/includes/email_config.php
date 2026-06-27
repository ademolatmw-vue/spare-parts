<?php
// SparePartsNG Email configuration (PHPMailer support)
// IMPORTANT:
// - Set these values to your Gmail credentials.
// - Do NOT commit real credentials in production.

// Gmail SMTP settings
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls',

    // Gmail username (the account that sends emails)
    'smtp_username' => 'sparepartsng.project@gmail.com',

    // Gmail App Password (NOT your regular password)
    'smtp_password' => 'qjgzyqchgxayvwun',

    // From details
    'from_email' => 'sparepartsng.project@gmail.com',
    'from_name' => 'SparePartsNG',

    // Optional: enable/disable emails without breaking pages
    'enabled' => true,
];

