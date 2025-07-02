<?php
session_start();
require '../config/db.php';
require '../config/google-config.php';

if (!isset($_SESSION['access_token'])) {
    die("❌ Silakan login dengan Google terlebih dahulu.");
}

$client->setAccessToken($_SESSION['access_token']);

// Cek token valid atau tidak
if ($client->isAccessTokenExpired()) {
    unset($_SESSION['access_token']);
    header("Location: google_auth.php");
    exit();
}

// Ambil event ID dari DB berdasarkan meeting ID
if (!isset($_GET['id'])) {
    die("❌ ID meeting tidak ditemukan.");
}

$meeting_id = $_GET['id'];
$stmt = $conn->prepare("SELECT google_event_id FROM meeting_schedule WHERE id = ?");
$stmt->bind_param("i", $meeting_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row || empty($row['google_event_id'])) {
    // Jika tidak ada Google Event ID, tetap kosongkan di DB jika ada kemungkinan inkonsistensi
    $clearStmt = $conn->prepare("UPDATE meeting_schedule SET google_event_id = NULL WHERE id = ?");
    $clearStmt->bind_param("i", $meeting_id);
    $clearStmt->execute();
    die("❌ Tidak ditemukan Google Event ID untuk meeting ini atau sudah kosong di database. Data di sistem sudah diperbarui.<br><a href='../views/list_meetings.php'>Kembali</a>");
}

$google_event_id = $row['google_event_id'];

$calendarService = new Google_Service_Calendar($client);

try {
    // Coba hapus event dari Google Calendar
    $calendarService->events->delete('primary', $google_event_id);
} catch (Google_Service_Exception $e) {
    // Tangkap error jika event tidak ditemukan (sudah dihapus manual)
    if ($e->getCode() == 404) {
        echo "⚠️ Event tidak ditemukan di Google Calendar (mungkin sudah dihapus).<br>";
    } else {
        echo "❌ Gagal menghapus event dari Google Calendar: " . $e->getMessage() . "<br>";
        // Hentikan eksekusi jika ada error lain dari Google API
        exit();
    }
}

// Kosongkan google_event_id di DB meskipun event sudah tidak ada atau gagal dihapus dari Google Calendar karena error selain 404
$clearStmt = $conn->prepare("UPDATE meeting_schedule SET google_event_id = NULL WHERE id = ?");
$clearStmt->bind_param("i", $meeting_id);
$clearStmt->execute();

echo "✅ Event berhasil dihapus/dikosingkan dari sistem.<br><a href='../views/list_meetings.php'>Kembali</a>";
?>