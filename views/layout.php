<?php
if (!isset($content)) die("No content set.");
?>
<!DOCTYPE html>
<html>
<head>
    <title>E-Meeting App</title>
</head>
<body>
    <h2>📅 E-Meeting Dashboard</h2>
    <nav>
        <a href="dashboard.php">🏠 Dashboard</a> |
        <a href="list_meetings.php">📋 Jadwal</a> |
        <a href="calendar.php">📅 Kalender</a> |
        <a href="send_invite.php">📧 Undangan</a> |
        <a href="logs.php">📜 Log</a> |
        <a href="profile.php">👤 Profil</a> |
        <a href="../public/logout.php">🚪 Logout</a>
    </nav>
    <hr>
    <main>
        <?php include $content; ?>
    </main>
</body>
</html>

// dashboard.php
<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_COOKIE['user_id'])) {
    header("Location: login.php");
    exit();
}
$content = 'dashboard_content.php';
include 'layout.php';
?>

// dashboard_content.php
<p>Selamat datang di Aplikasi E-Meeting 🎉</p>
<p>Gunakan menu di atas untuk mulai mengelola jadwal meeting Anda.</p>

// meeting_detail.php
<?php
require '../config/db.php';

if (!isset($_GET['id'])) {
    die("ID meeting tidak ditemukan");
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM meeting_schedule WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$meeting = $result->fetch_assoc();

if (!$meeting) {
    die("Meeting tidak ditemukan");
}
?>
<h2>Detail Meeting</h2>
<p><strong>Judul:</strong> <?= htmlspecialchars($meeting['title']) ?></p>
<p><strong>Waktu:</strong> <?= htmlspecialchars($meeting['date_time']) ?></p>
<p><strong>Lokasi:</strong> <?= htmlspecialchars($meeting['location']) ?></p>
<p><strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($meeting['description'])) ?></p>