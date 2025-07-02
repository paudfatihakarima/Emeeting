<?php
session_start();
require '../config/google-config.php';

if (!isset($_SESSION['access_token'])) {
    die('Harap login dengan Google!');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client->setAccessToken($_SESSION['access_token']);
    $calendarService = new Google_Service_Calendar($client);

    // Format ulang waktu agar sesuai dengan Google API
    function toGoogleDateTime($input) {
        $dt = new DateTime($input);
        return $dt->format(DateTime::RFC3339); // 2025-06-28T10:00:00+07:00
    }

    $event = new Google_Service_Calendar_Event([
        'summary' => $_POST['summary'],
        'location' => $_POST['location'],
        'start' => ['dateTime' => toGoogleDateTime($_POST['start'])],
        'end' => ['dateTime' => toGoogleDateTime($_POST['end'])]
    ]);

    $calendarId = 'primary';
    $event = $calendarService->events->insert($calendarId, $event);

    echo "Acara berhasil ditambahkan: <a href='{$event->htmlLink}'>Lihat di Google Calendar</a>";
} else {
?>
<form method="POST">
    <label>Judul:</label><input type="text" name="summary" required><br>
    <label>Mulai:</label><input type="datetime-local" name="start" required><br>
    <label>Selesai:</label><input type="datetime-local" name="end" required><br>
    <label>Lokasi:</label><input type="text" name="location"><br>
    <button type="submit">Tambah Acara</button>
</form>
<?php } ?>
