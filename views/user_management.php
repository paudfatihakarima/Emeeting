<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("❌ Akses hanya untuk admin.");
}

include 'layout/header.php';

// Ambil semua user
$result = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
?>

<h2>👥 Manajemen Pengguna</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Nama</th>
        <th>Email</th>
        <th>Role</th>
        <th>Tanggal Daftar</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= $row['role'] ?></td>
        <td><?= $row['created_at'] ?></td>
        <td>
            <?php if ($row['id'] != $_SESSION['user_id']): ?>
                <a href="../public/delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus user ini?')">🗑️ Hapus</a>
            <?php else: ?>
                <em>(Anda)</em>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php include 'layout/footer.php'; ?>
