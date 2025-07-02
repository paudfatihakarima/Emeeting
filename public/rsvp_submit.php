<?php
require '../config/db.php';

if (!isset($_POST['token']) || !isset($_POST['response'])) {
    die("❌ Permintaan tidak valid.");
}

$token = $_POST['token'];
$response = $_POST['response'];

if (!in_array($response, ['accepted', 'declined'])) {
    die("❌ Respons tidak valid.");
}

// Update status RSVP
$stmt = $conn->prepare("UPDATE meeting_invitations SET status = ?, responded_at = NOW() WHERE token = ?");
$stmt->bind_param("ss", $response, $token);

if ($stmt->execute()) {
    echo "✅ Terima kasih! Anda telah menyatakan: <strong>" . strtoupper($response) . "</strong>";
} else {
    echo "❌ Gagal menyimpan jawaban Anda.";
}
?>
