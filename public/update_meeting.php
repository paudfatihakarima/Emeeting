<?php
session_start();
require '../config/db.php';
require '../config/google-config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date_time = $_POST['date_time'];
    $location = $_POST['location'];

    // Ambil google_event_id dari database
    $stmt = $conn->prepare("SELECT google_event_id FROM meeting_schedule WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $google_event_id = $data['google_event_id'];

    // Update data di database
    $stmt = $conn->prepare("UPDATE meeting_schedule SET title = ?, description = ?, date_time = ?, location = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $description, $date_time, $location, $id);
    $stmt->execute();

    // Kirim notifikasi ke peserta
    // Pastikan file mailer.php dimuat sebelum fungsi sendEmail dipanggil
    require '../config/mailer.php';

    // Ambil kembali data lengkap meeting termasuk email peserta
    $stmt = $conn->prepare("SELECT title, date_time, location, participant_emails FROM meeting_schedule WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $meeting = $result->fetch_assoc();

    $emails = explode(',', $meeting['participant_emails']);
    $subject = "📢 Perubahan Jadwal Meeting: {$meeting['title']}";
    $message = "Hai, jadwal meeting yang kamu ikuti telah diperbarui:\n\n"
             . "Judul     : {$meeting['title']}\n"
             . "Waktu     : {$meeting['date_time']}\n"
             . "Lokasi    : {$meeting['location']}\n\n"
             . "Silakan cek aplikasi untuk informasi lengkap.";

    foreach ($emails as $email) {
        $email = trim($email);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendEmail($email, $subject, $message);
        }
    }

    // --- Sinkron ke Google jika event sebelumnya sudah disinkron ---
    if (!empty($google_event_id) && isset($_SESSION['access_token'])) {
        $client->setAccessToken($_SESSION['access_token']);

        if ($client->isAccessTokenExpired()) {
            unset($_SESSION['access_token']);
            header("Location: google_auth.php");
            exit();
        }

        try {
            $calendarService = new Google_Service_Calendar($client);
            $event = $calendarService->events->get('primary', $google_event_id);

            $event->setSummary($title);
            $event->setDescription($description);
            $event->setLocation($location);

            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime(date('c', strtotime($date_time)));
            $start->setTimeZone('Asia/Jakarta');
            $event->setStart($start);

            $event->setReminders(new Google_Service_Calendar_EventReminders([
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 30],
                    ['method' => 'popup', 'minutes' => 10],
                ],
            ]));

            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime(date('c', strtotime($date_time . ' +1 hour')));
            $end->setTimeZone('Asia/Jakarta');
            $event->setEnd($end);

            $calendarService->events->update('primary', $google_event_id, $event);

            echo "✅ Meeting berhasil diperbarui dan event Google juga di-*sync* ulang.<br>";

            // Simpan log perubahan setelah sinkronisasi Google berhasil
            $performed_by = 'Admin'; // atau $_SESSION['user_email'] jika ada login
            $action = 'edit';
            $stmt = $conn->prepare("INSERT INTO meeting_logs (meeting_id, action, performed_by) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $id, $action, $performed_by);
            $stmt->execute();

        } catch (Exception $e) {
            echo "⚠️ Gagal update event Google: " . $e->getMessage() . "<br>";
            // Simpan log perubahan meskipun sinkronisasi Google gagal (opsional, tergantung kebutuhan)
            $performed_by = 'Admin'; // atau $_SESSION['user_email']
            $action = 'edit_failed_google_sync'; // Aksi khusus jika Google Sync gagal
            $stmt = $conn->prepare("INSERT INTO meeting_logs (meeting_id, action, performed_by) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $id, $action, $performed_by);
            $stmt->execute();
        }
    } else {
        echo "✅ Meeting berhasil diperbarui.<br>";
        // Simpan log perubahan jika tidak ada sinkronisasi Google
        $performed_by = 'Admin'; // atau $_SESSION['user_email'] jika ada login
        $action = 'edit';
        $stmt = $conn->prepare("INSERT INTO meeting_logs (meeting_id, action, performed_by) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id, $action, $performed_by);
        $stmt->execute();
    }

    echo "<a href='../views/list_meetings.php'>Kembali ke daftar</a>";
}
?>