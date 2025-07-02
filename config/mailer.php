<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

// Coba load .env (jika ada)
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad();
}

// Variabel fallback (digunakan jika .env gagal)
$defaultEmail = 'lodido4@gmail.com';
$defaultPassword = 'btrmreeexagxtgzr';
$defaultFrom = 'lodido4@gmail.com';
$defaultName = 'E-Meeting App admin';

function sendEmail($to, $subject, $body) {
    global $defaultEmail, $defaultPassword, $defaultFrom, $defaultName;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('MAIL_USERNAME') ?: $defaultEmail;
        $mail->Password   = getenv('MAIL_PASSWORD') ?: $defaultPassword;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $fromEmail = getenv('MAIL_FROM') ?: $defaultFrom;
        $fromName  = getenv('MAIL_NAME') ?: $defaultName;

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        echo "✅ Email berhasil dikirim ke: $to<br>";
        return true;
    } catch (Exception $e) {
        echo "❌ PHPMailer Error: " . $mail->ErrorInfo . "<br>";
        return false;
    }
}
