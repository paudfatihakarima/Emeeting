<?php
require '../config/db.php';
require '../config/mailer.php'; // PHPMailer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $meeting_id = $_POST['meeting_id'];
    $emails_raw = $_POST['emails'];
    $emails = explode(',', $emails_raw);

    // Ambil detail meeting
    $stmt = $conn->prepare("SELECT title, date_time, location FROM meeting_schedule WHERE id = ?");
    $stmt->bind_param("i", $meeting_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $meeting = $result->fetch_assoc();

    if (!$meeting) {
        die("❌ Meeting tidak ditemukan.");
    }

    $sent = 0;

    foreach ($emails as $email) {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "❌ Email tidak valid: $email<br>";
            continue;
        }

        $token = bin2hex(random_bytes(16));

        // Simpan ke DB
        $stmt = $conn->prepare("INSERT INTO meeting_invitations (meeting_id, email, token) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $meeting_id, $email, $token);
        $stmt->execute();

        // Siapkan isi email
        $subject = "Undangan Meeting: {$meeting['title']}";
        $rsvp_link = "http://localhost/e_meeting/public/rsvp.php?token=$token";
        $message = "Anda diundang untuk menghadiri meeting berikut:\n\n"
                 . "Judul: {$meeting['title']}\n"
                 . "Waktu: {$meeting['date_time']}\n"
                 . "Lokasi: {$meeting['location']}\n\n"
                 . "Silakan konfirmasi kehadiran:\n$rsvp_link";

        // Kirim email
        $success = sendEmail($email, $subject, $message);
        if ($success) {
            $sent++;
        } else {
            echo "❌ Gagal mengirim ke: $email<br>";
        }
    }

    header("Location: ../views/send_invite.php?success=$sent");
exit();

}
?>
