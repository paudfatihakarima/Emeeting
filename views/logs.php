<?php
session_start();
require '../config/db.php';
include 'layout/header.php';

// Cek hak akses admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='alert alert-danger'>❌ Akses ditolak. Hanya admin yang bisa melihat log.</div>";
    include 'layout/footer.php';
    exit();
}

// Ambil data log
$logs = $conn->query("SELECT * FROM meeting_logs ORDER BY performed_at DESC");
?>

<div class="container mt-4">
    <h2>📜 Log Perubahan Meeting</h2>
    <a href="list_meetings.php" class="btn btn-secondary btn-sm mb-3">← Kembali ke Daftar Meeting</a>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>ID Log</th>
                <th>ID Meeting</th>
                <th>Aksi</th>
                <th>Oleh</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($logs->num_rows === 0): ?>
            <tr>
                <td colspan="5" class="text-center text-muted">Belum ada aktivitas yang dicatat.</td>
            </tr>
        <?php else: ?>
            <?php while ($log = $logs->fetch_assoc()): ?>
                <tr>
                    <td><?= $log['id'] ?></td>
                    <td><?= $log['meeting_id'] ?></td>
                    <td><?= ucfirst($log['action']) ?></td>
                    <td><?= $log['performed_by'] ?></td>
                    <td><?= $log['performed_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layout/footer.php'; ?>
