<?php
// Konfigurasi database
$host = "localhost";
$user = "root";
$pass = ""; // kosongkan jika tidak pakai password
$dbname = "e_meeting";

// Buat koneksi
$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
