<?php
session_start();
require '../config/google-config.php';

if (!isset($_SESSION['access_token'])) {
    die("❌ Silakan login dengan Google terlebih dahulu.");
}

$client->setAccessToken($_SESSION['access_token']);
// Set token dari session
$client->setAccessToken($_SESSION['access_token']);

// Jika token sudah kedaluwarsa, arahkan ulang untuk login
if ($client->isAccessTokenExpired()) {
    unset($_SESSION['access_token']);
    header("Location: google_auth.php");
    exit();
}

$calendarService = new Google_Service_Calendar($client);

$calendarId = 'primary';
$events = $calendarService->events->listEvents($calendarId);

// Tampilkan hasil
echo "<h2>📅 Daftar Event di Google Calendar</h2>";

if (count($events->getItems()) === 0) {
    echo "Tidak ada event ditemukan.";
} else {
    foreach ($events->getItems() as $event) {
        $title = $event->getSummary() ?? '(Tanpa Judul)';
        $start = $event->getStart()->getDateTime() ?? $event->getStart()->getDate();
        $link  = $event->getHtmlLink();
        echo "<p><strong>$title</strong><br>";
        echo "⏰ $start<br>";
        echo "<a href='$link' target='_blank'>Lihat di Google Calendar</a></p><hr>";
    }
}
?>
