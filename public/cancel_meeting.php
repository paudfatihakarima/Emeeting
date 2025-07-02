<?php
session_start();
require '../config/db.php';
require '../config/google-config.php';

if (!isset($_GET['id'])) {
    die("❌ ID meeting tidak ditemukan.");
}

$id = $_GET['id'];

// Ambil google_event_id dari DB
$stmt = $conn->prepare("SELECT google_event_id FROM meeting_schedule WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$meeting = $result->fetch_assoc();

if (!$meeting || empty($meeting['google_event_id'])) {
    die("❌ Event Google tidak ditemukan.");
}

// Update status di DB
$update = $conn->prepare("UPDATE meeting_schedule SET status = 'cancelled' WHERE id = ?");
$update->bind_param("i", $id);
$update->execute();

// Batalkan event di Google Calendar
$client->setAccessToken($_SESSION['access_token']);
if ($client->isAccessTokenExpired()) {
    unset($_SESSION['access_token']);
    header("Location: google_auth.php");
    exit();
}

try {
    $calendarService = new Google_Service_Calendar($client);
    $event = $calendarService->events->get('primary', $meeting['google_event_id']);
    $event->setStatus('cancelled');
    $calendarService->events->update('primary', $meeting['google_event_id'], $event);

    echo "✅ Meeting berhasil dibatalkan dan status di Google Calendar diubah menjadi 'Cancelled'.<br>";
} catch (Exception $e) {
    echo "❌ Gagal membatalkan meeting di Google: " . $e->getMessage() . "<br>";
}

echo "<a href='../views/list_meetings.php'>← Kembali ke daftar meeting</a>";
?>
