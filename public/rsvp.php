<?php
require '../config/db.php';

if (!isset($_GET['token'])) {
    die("❌ Token tidak ditemukan.");
}

$token = $_GET['token'];

// Ambil data undangan berdasarkan token
$stmt = $conn->prepare("SELECT mi.id AS invitation_id, mi.status, ms.title, ms.date_time, ms.location 
                        FROM meeting_invitations mi 
                        JOIN meeting_schedule ms ON mi.meeting_id = ms.id 
                        WHERE mi.token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$invite = $result->fetch_assoc();

if (!$invite) {
    die("❌ Undangan tidak valid atau tidak ditemukan.");
}

// Jika peserta sudah RSVP
if ($invite['status'] !== 'pending') {
    echo "✅ Terima kasih! Anda telah menyatakan: <strong>" . strtoupper($invite['status']) . "</strong>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Kehadiran</title>
</head>
<body>
    <h2>Konfirmasi Kehadiran Meeting</h2>
    <p><strong>Judul:</strong> <?= htmlspecialchars($invite['title']) ?></p>
    <p><strong>Waktu:</strong> <?= htmlspecialchars($invite['date_time']) ?></p>
    <p><strong>Lokasi:</strong> <?= htmlspecialchars($invite['location']) ?></p>

    <form action="rsvp_submit.php" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <button type="submit" name="response" value="accepted">✅ Saya akan hadir</button>
        <button type="submit" name="response" value="declined">❌ Maaf, saya tidak bisa hadir</button>
    </form>
</body>
</html>
