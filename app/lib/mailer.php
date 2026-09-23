<?php
// app/lib/mailer.php - very simple mailer stub (logs emails in dev)

declare(strict_types=1);

function send_mail(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody = ''): bool {
    // In dev, we simply log the email. Integrate PHPMailer/SMTP later as needed.
    $log = sprintf("[%s] To: %s <%s> | Subject: %s\n", date('c'), $toName, $toEmail, $subject);
    $log .= "HTML:\n" . $htmlBody . "\n\n";
    if (!is_dir(__DIR__ . '/../../logs')) { mkdir(__DIR__ . '/../../logs', 0775, true); }
    file_put_contents(__DIR__ . '/../../logs/mail.log', $log, FILE_APPEND);
    return true;
}
