<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Meeting App</title>
    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional JS for Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
    <a class="navbar-brand" href="dashboard.php">📅 E-Meeting</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="dashboard.php">🏠 Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="list_meetings.php">📋 Jadwal</a></li>
            <li class="nav-item"><a class="nav-link" href="send_invite.php">📨 Undangan</a></li>
            <li class="nav-item"><a class="nav-link" href="synced_meetings.php">🔄 Sinkron</a></li>
            <li class="nav-item"><a class="nav-link" href="about.php">❓ Tentang</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="user_management.php">👥 Pengguna</a></li>
                <li class="nav-item"><a class="nav-link" href="logs.php">📜 Log</a></li>
            <?php endif; ?>
        </ul>

        <span class="navbar-text me-3">
            👤 <?= $_SESSION['user_name'] ?? '' ?>
        </span>
        <a class="btn btn-outline-light btn-sm" href="../public/logout.php">Logout</a>
    </div>
</nav>

<!-- ✅ Kontainer Konten -->
<div class="container mt-4">
