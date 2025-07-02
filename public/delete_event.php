<?php
session_start();
require '../config/google-config.php';

if (!isset($_SESSION['access_token'])) {
    die('Akses ditolak!');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client->setAccessToken($_SESSION['access_token']);
    $calendarService = new Google_Service_Calendar($client);

    $eventId = $_POST['event_id'];
    $calendarId = 'primary';

    try {
        $calendarService->events->delete($calendarId, $eventId);
        echo "Acara berhasil dihapus. <a href='../views/events.php'>Kembali ke daftar acara</a>";
    } catch (Exception $e) {
        echo "Gagal menghapus acara: " . $e->getMessage();
    }
} else {
    header("Location: ../views/events.php");
    exit();
}
?>
