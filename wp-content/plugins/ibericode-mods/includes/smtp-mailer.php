<?php

use PHPMailer\PHPMailer\PHPMailer;

// Prevent direct file access
defined('ABSPATH') or exit;

add_action('phpmailer_init', function (PHPMailer $phpmailer) {
    // make sure all configuration constants are given
    if (! defined('SMTP_HOST') || ! defined('SMTP_USER')) {
        return;
    }

    $phpmailer->Mailer = 'smtp';
    $phpmailer->Host = constant('SMTP_HOST');
    if (defined('SMTP_PORT')) {
        $phpmailer->Port = (int) constant('SMTP_PORT');
    }
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = constant('SMTP_USER');
    if (defined('SMTP_PASSWORD')) {
        $phpmailer->Password = constant('SMTP_PASSWORD');
    }
    $phpmailer->SMTPSecure = defined('SMTP_ENCRYPTION') ? constant('SMTP_ENCRYPTION') : PHPMailer::ENCRYPTION_STARTTLS;
});

add_filter('wp_mail_from', function ($from) {
    return defined('SMTP_USER') ? constant('SMTP_USER') : $from;
});
