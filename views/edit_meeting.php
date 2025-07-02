<?php
require '../config/db.php';
include 'layout/header.php';

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>❌ ID tidak ditemukan.</div>";
    include 'layout/footer.php';
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM meeting_schedule WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$meeting = $result->fetch_assoc();

if (!$meeting) {
    echo "<div class='alert alert-warning'>❌ Jadwal tidak ditemukan.</div>";
    include 'layout/footer.php';
    exit;
}
?>

<div class="container mt-4">
    <h2 class="mb-4">✏️ Edit Jadwal Meeting</h2>

    <form action="../public/update_meeting.php" method="POST" class="card p-4 shadow-sm">
        <input type="hidden" name="id" value="<?= $meeting['id'] ?>">

        <div class="mb-3">
            <label for="title" class="form-label">Judul Meeting</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($meeting['title']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea name="description" id="description" class="form-control" rows="3"><?= htmlspecialchars($meeting['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="date_time" class="form-label">Waktu Meeting</label>
            <input type="datetime-local" name="date_time" id="date_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($meeting['date_time'])) ?>" required>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Lokasi</label>
            <input type="text" name="location" id="location" class="form-control" value="<?= htmlspecialchars($meeting['location']) ?>">
        </div>

        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
        <a href="list_meetings.php" class="btn btn-secondary">⬅️ Kembali</a>
    </form>
</div>

<?php include 'layout/footer.php'; ?>
