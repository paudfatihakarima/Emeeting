<?php
session_start();
require '../config/db.php';
require '../config/google-config.php';

if (!isset($_SESSION['access_token'])) {
    die("❌ Silakan login dengan Google terlebih dahulu.");
}

$client->setAccessToken($_SESSION['access_token']);
$calendarService = new Google_Service_Calendar($client);

// Ambil data meeting dari database berdasarkan ID
if (!isset($_GET['id'])) {
    die("❌ ID meeting tidak ditemukan.");
}

$meeting_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM meeting_schedule WHERE id = ?");
$stmt->bind_param("i", $meeting_id);
$stmt->execute();
$result = $stmt->get_result();
$meeting = $result->fetch_assoc();

if (!$meeting) {
    die("❌ Data meeting tidak ditemukan.");
}

// Buat objek event Google Calendar
$event = new Google_Service_Calendar_Event([
    'summary' => $meeting['title'],
    'description' => $meeting['description'],
    'start' => [
        'dateTime' => date('c', strtotime($meeting['date_time'])),
        'timeZone' => 'Asia/Jakarta',
    ],
    'end' => [
        'dateTime' => date('c', strtotime($meeting['date_time'] . ' +1 hour')),
        'timeZone' => 'Asia/Jakarta',
    ],
    'location' => $meeting['location'],
    'reminders' => [
        'useDefault' => false,
        'overrides' => [
            ['method' => 'email', 'minutes' => 30],
            ['method' => 'popup', 'minutes' => 10],
        ],
    ],
]);


// Masukkan ke Google Calendar
$calendarId = 'primary';
$event = $calendarService->events->insert($calendarId, $event);
$google_event_id = $event->getId();
$google_event_link = $event->getHtmlLink();


$updateStmt = $conn->prepare("UPDATE meeting_schedule SET google_event_id = ?, google_event_link = ? WHERE id = ?");
$updateStmt->bind_param("ssi", $google_event_id, $google_event_link, $meeting_id);
$updateStmt->execute();


// Tampilkan hasil
echo "✅ Jadwal berhasil dikirim ke Google Calendar.<br>";
echo "<a href='{$event->htmlLink}' target='_blank'>Lihat di Google Calendar</a><br>";
echo "<a href='../views/list_meetings.php'>Kembali ke daftar meeting</a>";
?>
