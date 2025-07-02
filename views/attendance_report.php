<?php
require '../config/db.php';
include 'layout/header.php';

$meeting_id = $_GET['id'] ?? null;

if (!$meeting_id) {
    echo "<div class='alert alert-danger'>❌ ID meeting tidak ditemukan.</div>";
    include 'layout/footer.php';
    exit;
}

// Ambil detail meeting
$stmt = $conn->prepare("SELECT title, date_time FROM meeting_schedule WHERE id = ?");
$stmt->bind_param("i", $meeting_id);
$stmt->execute();
$result = $stmt->get_result();
$meeting = $result->fetch_assoc();

if (!$meeting) {
    echo "<div class='alert alert-warning'>❌ Meeting tidak ditemukan.</div>";
    include 'layout/footer.php';
    exit;
}
?>

<div class="container mt-4">
    <h2>📊 Laporan Kehadiran</h2>
    <p><strong>Judul:</strong> <?= htmlspecialchars($meeting['title']) ?></p>
    <p><strong>Waktu:</strong> <?= date("d M Y H:i", strtotime($meeting['date_time'])) ?></p>
    <a href="list_meetings.php" class="btn btn-secondary mb-3">← Kembali ke Daftar Meeting</a>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Email</th>
                <th>Status</th>
                <th>Waktu Respon</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $stmt = $conn->prepare("SELECT email, status, responded_at FROM meeting_invitations WHERE meeting_id = ? ORDER BY email ASC");
        $stmt->bind_param("i", $meeting_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo "<tr><td colspan='3' class='text-center text-muted'>Belum ada undangan dikirim untuk meeting ini.</td></tr>";
        } else {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . ucfirst($row['status']) . "</td>";
                echo "<td>" . ($row['responded_at'] ?? '-') . "</td>";
                echo "</tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>

<?php include 'layout/footer.php'; ?>
