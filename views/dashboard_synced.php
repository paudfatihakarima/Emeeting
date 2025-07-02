<?php
require '../config/db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Jadwal Tersinkron ke Google</title>
</head>
<body>
    <h2>📅 Meeting yang Sudah Tersinkron ke Google Calendar</h2>
    <a href="list_meetings.php">← Kembali ke Semua Jadwal</a><br><br>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Judul</th>
            <th>Waktu</th>
            <th>Lokasi</th>
            <th>Aksi</th>
        </tr>

        <?php
        $query = "SELECT * FROM meeting_schedule WHERE google_event_id IS NOT NULL ORDER BY date_time ASC";
        $result = $conn->query($query);

        if ($result->num_rows === 0) {
            echo "<tr><td colspan='4'>Tidak ada meeting yang tersinkron ke Google.</td></tr>";
        } else {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td>" . htmlspecialchars($row['date_time']) . "</td>";
                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                echo "<td>";
                echo "<a href='" . $row['google_event_link'] . "' target='_blank'>Lihat di Google</a> | ";
                echo "<a href='../public/delete_google_event.php?id=" . $row['id'] . "' onclick=\"return confirm('Yakin hapus dari Google?')\">Hapus Google</a>";
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
</body>
</html>
