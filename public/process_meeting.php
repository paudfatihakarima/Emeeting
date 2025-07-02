<?php
require '../config/db.php';
require '../config/mailer.php'; // pastikan ini versi hybrid (pakai .env + fallback)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date_time = $_POST['date_time'];
    $location = $_POST['location'];
    $raw_emails = $_POST['participant_emails'];

    // Simpan ke DB
    $stmt = $conn->prepare("INSERT INTO meeting_schedule (title, description, date_time, location, participant_emails) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $description, $date_time, $location, $raw_emails);

    if ($stmt->execute()) {
        // Kirim email ke setiap peserta
        $emails = explode(',', $raw_emails);
        $subject = "Undangan Meeting: $title";
        $message = "Hai! Anda diundang untuk meeting berikut:\n\n"
                 . "Judul: $title\n"
                 . "Waktu: $date_time\n"
                 . "Lokasi: $location\n"
                 . "Deskripsi: $description\n\n"
                 . "Silakan hadir tepat waktu. Terima kasih.";

        $success_count = 0;
        foreach ($emails as $email) {
            $clean_email = trim($email);
            if (filter_var($clean_email, FILTER_VALIDATE_EMAIL)) {
                if (sendEmail($clean_email, $subject, $message)) {
                    $success_count++;
                } else {
                    echo "❌ Gagal kirim ke: $clean_email<br>";
                }
            } else {
                echo "❌ Email tidak valid: $clean_email<br>";
            }
        }

        echo "<br>✅ Jadwal meeting berhasil disimpan.<br>";
        echo "📨 Email berhasil dikirim ke $success_count peserta.<br>";
        echo "<a href='../views/list_meetings.php'>Lihat daftar meeting</a>";
    } else {
        echo "❌ Gagal menyimpan jadwal.";
    }
}
?>
