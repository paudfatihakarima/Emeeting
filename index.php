<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']) || isset($_COOKIE['user_id']);

if ($isLoggedIn) {
    header("Location: views/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang di E-Meeting</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 40px auto;
            max-width: 700px;
            padding: 20px;
        }

        h1 {
            color: #2c3e50;
        }

        .actions a {
            display: inline-block;
            margin: 10px 10px 0 0;
            padding: 10px 15px;
            text-decoration: none;
            background-color: #3498db;
            color: #fff;
            border-radius: 5px;
        }

        .actions a:hover {
            background-color: #2980b9;
        }

        .footer {
            margin-top: 50px;
            font-size: 0.9em;
            color: #888;
        }
    </style>
</head>
<body>

    <h1>📅 E-Meeting</h1>
    <p>Selamat datang di aplikasi untuk menjadwalkan dan mengelola meeting dengan integrasi Google Calendar.</p>

    <p><strong>Waktu Server Saat Ini:</strong> <?= date("d F Y, H:i:s") ?></p>

    <hr>

    <h3>🔑 Masuk atau Daftar</h3>
    <div class="actions">
        <a href="views/login.php">🔐 Login</a>
        <a href="views/register.php">🆕 Register</a>
        <a href="views/login_google.php">🔗 Login dengan Google</a>
        <a href="public/logout.php">🔁 Reset Login</a>
    </div>

    <div class="footer">
        <p>© <?= date("Y") ?> E-Meeting App. Dibangun dengan 💙 oleh [Azzah Fatinah].</p>
    </div>

</body>
</html>
