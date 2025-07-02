<?php
session_start();
require '../config/google-config.php';
require '../config/db.php';

if (!isset($_GET['code'])) {
    die('Kode otorisasi Google tidak ditemukan.');
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$client->setAccessToken($token);
$_SESSION['access_token'] = $token;

$oauth = new Google_Service_Oauth2($client);
$google_user = $oauth->userinfo->get();

$google_id = $google_user->id;
$name      = $google_user->name;
$email     = $google_user->email;

// Cari user berdasarkan google_id
$stmt = $conn->prepare("SELECT id, name, role FROM users WHERE google_id = ?");
$stmt->bind_param("s", $google_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    // Jika belum ada, buat user baru (default role = 'user')
    $insert = $conn->prepare("INSERT INTO users (name, email, google_id) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $name, $email, $google_id);
    $insert->execute();
    $user_id = $insert->insert_id;
    $role = 'user'; // default
} else {
    // Jika sudah ada, ambil data user
    $stmt->bind_result($user_id, $name, $role);
    $stmt->fetch();
}

// Simpan ke session
$_SESSION['user_id'] = $user_id;
$_SESSION['user_name'] = $name;
$_SESSION['role'] = $role; // ✅ Fix penting

header("Location: ../views/dashboard.php");
exit();
