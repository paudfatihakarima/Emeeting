<?php
session_start();
require '../config/db.php';
include 'layout/header.php';

// Autentikasi
if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_name'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['user_name'] = $_COOKIE['user_name'];
    } else {
        header("Location: login.php");
        exit();
    }
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Ambil info pengguna lengkap dari DB
$stmt = $conn->prepare("SELECT name, email, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<h2>👤 Profil Pengguna</h2>

<ul>
    <li><strong>Nama:</strong> <?= htmlspecialchars($user['name']) ?></li>
    <li><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></li>
    <li><strong>Peran:</strong> <?= ucfirst($user['role']) ?></li>
    <li><strong>Bergabung Sejak:</strong> <?= date('d M Y', strtotime($user['created_at'])) ?></li>
</ul>

<p><a href="dashboard.php">← Kembali ke Dashboard</a></p>

<?php include 'layout/footer.php'; ?>
