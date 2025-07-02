<?php
require '../config/db.php';

// Cek jadwal dalam 1 jam ke depan
$now = new DateTime();
$future = (clone $now)->modify('+1 hour');

$stmt = $conn->prepare("SELECT title, date_time, email FROM meetings WHERE date_time BETWEEN ? AND ?");
$nowStr = $now->format('Y-m-d H:i:s');
$futureStr = $future->format('Y-m-d H:i:s');

$stmt->bind_param("ss", $nowStr, $futureStr);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $to = $row['email'];
    $subject = "Pengingat Meeting: " . $row['title'];
    $message = "Hai, ini pengingat bahwa meeting \"" . $row['title'] . "\" akan dimulai pada " . $row['date_time'] . ".\n\nSalam,\nAplikasi E-Meeting";
    $headers = "From: noreply@emeeting.local";

    // Fungsi mail sederhana
    if (mail($to, $subject, $message, $headers)) {
        echo "Pengingat terkirim ke $to<br>";
    } else {
        echo "Gagal mengirim ke $to<br>";
    }
}
?>
