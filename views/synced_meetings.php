<?php
session_start();
require '../config/db.php';
require '../config/google-config.php';
include 'layout/header.php';

if (!isset($_SESSION['access_token'])) {
    echo "<div class='alert alert-warning'>❌ Silakan login dengan Google terlebih dahulu.</div>";
    include 'layout/footer.php';
    exit();
}

$client->setAccessToken($_SESSION['access_token']);
if ($client->isAccessTokenExpired()) {
    unset($_SESSION['access_token']);
    header("Location: ../public/google_auth.php");
    exit();
}
?>

<div class="container mt-4">
    <h2>📤 Jadwal yang Tersinkron ke Google Calendar</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Judul</th>
                <th>Waktu</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $query = "SELECT * FROM meeting_schedule WHERE google_event_id IS NOT NULL ORDER BY date_time ASC";
        $result = $conn->query($query);

        if ($result->num_rows === 0) {
            echo "<tr><td colspan='4' class='text-center text-muted'>Belum ada meeting yang disinkronkan ke Google Calendar.</td></tr>";
        } else {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td>" . date('d M Y H:i', strtotime($row['date_time'])) . "</td>";
                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                echo "<td>";
                echo "<a href='" . htmlspecialchars($row['google_event_link']) . "' target='_blank'>📎 Lihat</a> | ";
                echo "<a href='../public/delete_google_event.php?id=" . $row['id'] . "' onclick=\"return confirm('Yakin hapus dari Google Calendar?')\">❌ Hapus</a>";
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>

<?php include 'layout/footer.php'; ?>
