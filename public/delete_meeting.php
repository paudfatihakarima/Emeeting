<?php
session_start(); // Pastikan session dimulai jika diperlukan di bagian lain, walau di sini tidak secara langsung
require '../config/db.php';
require '../config/mailer.php'; // Pastikan file mailer.php dimuat
require '../config/google-config.php'; // Diperlukan untuk integrasi Google Calendar

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // --- Ambil data meeting sebelum dihapus untuk notifikasi dan sinkronisasi Google ---
    $stmt = $conn->prepare("SELECT title, date_time, participant_emails, google_event_id FROM meeting_schedule WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $meeting = $result->fetch_assoc();

    if ($meeting) { // Pastikan meeting ditemukan sebelum melanjutkan
        // Kirim notifikasi pembatalan
        $emails = explode(',', $meeting['participant_emails']);
        $subject = "❌ Meeting Dibatalkan: {$meeting['title']}";
        $message = "Hai, meeting berikut telah dibatalkan:\n\n"
                 . "Judul: {$meeting['title']}\n"
                 . "Waktu: {$meeting['date_time']}\n\n"
                 . "Silakan abaikan undangan sebelumnya.";

        foreach ($emails as $email) {
            $email = trim($email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                sendEmail($email, $subject, $message);
            }
        }

        // --- Hapus event dari Google Calendar jika sebelumnya sudah disinkron ---
        if (!empty($meeting['google_event_id']) && isset($_SESSION['access_token'])) {
            $client->setAccessToken($_SESSION['access_token']);

            if ($client->isAccessTokenExpired()) {
                unset($_SESSION['access_token']);
                header("Location: google_auth.php");
                exit();
            }

            try {
                $calendarService = new Google_Service_Calendar($client);
                $calendarService->events->delete('primary', $meeting['google_event_id']);
                echo "✅ Event Google Calendar berhasil dihapus.<br>";
            } catch (Exception $e) {
                echo "⚠️ Gagal menghapus event Google Calendar: " . $e->getMessage() . "<br>";
            }
        }

        // --- Hapus data dari database ---
        $stmt = $conn->prepare("DELETE FROM meeting_schedule WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "✅ Jadwal meeting berhasil dihapus.<br><a href='../views/list_meetings.php'>Kembali ke daftar jadwal</a>";

            // Simpan log perubahan setelah penghapusan berhasil
            $performed_by = 'Admin'; // atau $_SESSION['user_email'] jika ada login
            $action = 'delete';
            $stmt_log = $conn->prepare("INSERT INTO meeting_logs (meeting_id, action, performed_by) VALUES (?, ?, ?)");
            $stmt_log->bind_param("iss", $id, $action, $performed_by);
            $stmt_log->execute();

        } else {
            echo "❌ Gagal menghapus jadwal dari database.";
            // Anda bisa tambahkan log di sini juga jika penghapusan database gagal (opsional)
        }
    } else {
        echo "❌ Meeting dengan ID tersebut tidak ditemukan.";
    }
} else {
    echo "❌ ID meeting tidak ditemukan.";
}
?>