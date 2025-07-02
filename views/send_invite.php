<?php
require '../config/db.php';
include 'layout/header.php';

if (isset($_GET['success'])) {
    $sent = (int) $_GET['success'];
    echo "<div class='alert alert-success'>✅ Undangan berhasil dikirim ke <strong>$sent</strong> peserta.</div>";
}
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>📧 Kirim Undangan Meeting</h4>
        </div>
        <div class="card-body">
            <form action="../public/process_invite.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Pilih Jadwal Meeting</label>
                    <select name="meeting_id" class="form-select" required>
                        <option value="">-- Pilih Jadwal --</option>
                        <?php
                        $result = $conn->query("SELECT id, title FROM meeting_schedule ORDER BY date_time DESC");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['title']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Peserta</label>
                    <textarea name="emails" class="form-control" rows="4" placeholder="contoh1@email.com, contoh2@email.com" required></textarea>
                    <div class="form-text">Pisahkan dengan koma ( , ) jika lebih dari satu.</div>
                </div>

                <button type="submit" class="btn btn-primary">📨 Kirim Undangan</button>
            </form>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
