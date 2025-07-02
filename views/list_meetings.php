<?php
require '../config/db.php';
include 'layout/header.php';

// Cek apakah user menggunakan filter tanggal
if (isset($_GET['filter_date']) && !empty($_GET['filter_date'])) {
    $filter_date = $_GET['filter_date'];
    $stmt = $conn->prepare("SELECT * FROM meeting_schedule WHERE DATE(date_time) = ? ORDER BY date_time ASC");
    $stmt->bind_param("s", $filter_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $filterMessage = "Menampilkan jadwal untuk tanggal <strong>" . htmlspecialchars($filter_date) . "</strong>";
} else {
    $result = $conn->query("SELECT * FROM meeting_schedule ORDER BY date_time ASC");
    $filterMessage = null;
}
?>

<div class="container mt-4">
    <h2 class="mb-4">📅 Daftar Jadwal Meeting</h2>

    <div class="card mb-4">
        <div class="card-header bg-info text-white">🔍 Filter Tanggal Meeting</div>
        <div class="card-body">
            <form method="GET" class="form-inline">
                <div class="form-group me-2">
                    <input type="date" class="form-control" name="filter_date" value="<?= isset($_GET['filter_date']) ? $_GET['filter_date'] : '' ?>">
                </div>
                <button type="submit" class="btn btn-primary me-2">Cari</button>
                <a href="list_meetings.php" class="btn btn-secondary">Reset</a>
            </form>
        </div>
    </div>

    <?php if ($filterMessage): ?>
        <div class="alert alert-warning"><?= $filterMessage ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="add_meeting.php" class="btn btn-success">➕ Tambah Jadwal Baru</a>
        <a href="logs.php" class="btn btn-outline-secondary float-end">📜 Lihat Log</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Judul</th>
                    <th>Waktu</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['date_time']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td>
                        <div class="btn-group btn-group-sm flex-wrap" role="group">
                            <a href="edit_meeting.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                            <a href="../public/delete_meeting.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">Hapus</a>
                            <a href="../public/sync_google_calendar.php?id=<?= $row['id'] ?>" class="btn btn-info">Sync Google</a>
                            <a href="attendance_report.php?id=<?= $row['id'] ?>" class="btn btn-secondary">Laporan</a>

                            <?php if (!empty($row['google_event_id'])): ?>
                                <a href="../public/delete_google_event.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Yakin hapus dari Google Calendar?')">Hapus Google</a>
                            <?php endif; ?>

                            <?php if ($row['status'] !== 'cancelled'): ?>
                                <a href="../public/cancel_meeting.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning" onclick="return confirm('Yakin ingin membatalkan meeting ini?')">❌ Batal</a>
                            <?php else: ?>
                                <span class="text-danger fw-bold ms-2">Dibatalkan</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
