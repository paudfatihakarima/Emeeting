<!DOCTYPE html>
<html>
<head>
    <title>Tambah Jadwal Meeting</title>
</head>
<body>
    <h2>Form Tambah Jadwal Meeting</h2>
    <form action="../public/process_meeting.php" method="POST">
        <label>Judul Meeting:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Deskripsi:</label><br>
        <textarea name="description"></textarea><br><br>

        <label>Waktu Meeting:</label><br>
        <input type="datetime-local" name="date_time" required><br><br>

        <label>Lokasi:</label><br>
        <input type="text" name="location"><br><br>

        <label>Email Peserta (pisahkan dengan koma):</label><br>
        <textarea name="participant_emails" placeholder="user1@email.com, user2@email.com" required></textarea><br><br>


        <button type="submit">Simpan Jadwal</button>
    </form>
</body>
</html>
