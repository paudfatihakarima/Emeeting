<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("❌ Akses hanya untuk admin.");
}

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}

$id = $_GET['id'];

// Tidak boleh hapus diri sendiri
if ($id == $_SESSION['user_id']) {
    die("❌ Anda tidak bisa menghapus akun sendiri.");
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "✅ User berhasil dihapus.<br><a href='../views/user_management.php'>Kembali</a>";
} else {
    echo "❌ Gagal menghapus user.";
}
