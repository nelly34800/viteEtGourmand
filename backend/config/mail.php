<?php
// lecture propre de la config
return [
    'host' => $_ENV['SMTP_HOST'] ?? 'smtp-relay.brevo.com',
    'port' => $_ENV['SMTP_PORT'] ?? 587,
    'username' => $_ENV['SMTP_USER'] ?? '',
    'password' => $_ENV['SMTP_PASS'] ?? '',
    'from_email' => $_ENV['MAIL_FROM'] ?? '',
    'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Vite et Gourmand',
];