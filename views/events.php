<?php
session_start();
require '../config/google-config.php';

if (!isset($_SESSION['access_token'])) {
    die('Harap login dengan Google!');
}

$client->setAccessToken($_SESSION['access_token']);
$calendarService = new Google_Service_Calendar($client);

$calendarId = 'primary';
$events = $calendarService->events->listEvents($calendarId);

foreach ($events->getItems() as $event) {
    $id = $event->getId();
    $title = $event->getSummary();
    $start = $event->getStart()->getDateTime() ?? $event->getStart()->getDate();
    $location = $event->getLocation() ?? 'Tidak ada lokasi';

    echo "<div style='margin-bottom:15px;'>";
    echo "<strong>$title</strong><br>";
    echo "Waktu: $start<br>";
    echo "Lokasi: $location<br>";
    echo "<form method='POST' action='../public/delete_event.php' onsubmit='return confirm(\"Yakin ingin menghapus acara ini?\");'>
            <input type='hidden' name='event_id' value='$id'>
            <button type='submit'>Hapus Acara</button>
          </form>";
    echo "</div>";
}
?>
