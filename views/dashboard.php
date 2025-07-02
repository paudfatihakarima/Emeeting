<?php
session_start();
require '../config/db.php';

// Autentikasi
if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_name'])) {
        $stmt = $conn->prepare("SELECT id, name FROM users WHERE id = ?");
        $stmt->bind_param("i", $_COOKIE['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && $user['name'] === $_COOKIE['user_name']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
        } else {
            header("Location: login.php");
            exit();
        }
    } else {
        header("Location: login.php");
        exit();
    }
}

$user = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

include 'layout/header.php';

// Fungsi ringkasan
function getMeetingCountThisWeek($conn) {
    $start = date('Y-m-d 00:00:00', strtotime('monday this week'));
    $end   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM meeting_schedule WHERE date_time BETWEEN ? AND ?");
    $stmt->bind_param("ss", $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total'] ?? 0;
}

function getNextMeeting($conn) {
    $now = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("SELECT title, date_time FROM meeting_schedule WHERE date_time > ? ORDER BY date_time ASC LIMIT 1");
    $stmt->bind_param("s", $now);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Ambil data
$totalThisWeek = getMeetingCountThisWeek($conn);
$nextMeeting = getNextMeeting($conn);
?>

<div class="container mt-4">
    <div class="alert alert-success">
        <h4 class="alert-heading">👋 Halo, <?= htmlspecialchars($user) ?>!</h4>
        <p>Selamat datang kembali di <strong>E-Meeting</strong>. Gunakan menu di atas untuk mengelola aktivitas meeting Anda.</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-primary text-white">📊 Ringkasan Minggu Ini</div>
                <div class="card-body">
                    <p>📅 <strong>Total Meeting:</strong> <?= $totalThisWeek ?></p>
                    <p>⏰ <strong>Meeting Berikutnya:</strong><br>
                        <?php if ($nextMeeting): ?>
                            <strong><?= htmlspecialchars($nextMeeting['title']) ?></strong><br>
                            <small><?= date("d M Y H:i", strtotime($nextMeeting['date_time'])) ?></small>
                        <?php else: ?>
                            <em>Tidak ada meeting terjadwal.</em>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-secondary text-white">🚀 Akses Cepat</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><a href="add_meeting.php">➕ Tambah Jadwal Meeting</a></li>
                        <li class="list-group-item"><a href="list_meetings.php">📅 Lihat Semua Jadwal</a></li>
                        <li class="list-group-item"><a href="send_invite.php">📨 Kirim Undangan</a></li>
                        <li class="list-group-item"><a href="logs.php">📜 Lihat Log Aktivitas</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
