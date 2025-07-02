<?php include 'layout/header.php'; ?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <h1 class="mb-4">📘 Tentang Aplikasi <span class="text-primary">E-Meeting</span></h1>

            <p>
                <strong>E-Meeting</strong> adalah aplikasi berbasis web yang dirancang untuk memudahkan pengguna dalam:
            </p>

            <ul class="list-group mb-4">
                <li class="list-group-item">📅 Menjadwalkan meeting secara online</li>
                <li class="list-group-item">🔗 Sinkronisasi otomatis dengan Google Calendar</li>
                <li class="list-group-item">📨 Mengirim undangan via email ke peserta</li>
                <li class="list-group-item">✅ Mengelola kehadiran dan RSVP peserta</li>
                <li class="list-group-item">🗂️ Mencatat log perubahan jadwal untuk transparansi</li>
            </ul>

            <h4 class="mb-3">⚙️ Teknologi yang Digunakan</h4>
            <ul class="list-group mb-4">
                <li class="list-group-item">🌐 PHP (Plain, Procedural)</li>
                <li class="list-group-item">🛢️ MySQL (Database Jadwal & Pengguna)</li>
                <li class="list-group-item">🔐 Google OAuth 2.0 (Login dengan Google)</li>
                <li class="list-group-item">📧 PHPMailer (Untuk pengiriman email undangan)</li>
                <li class="list-group-item">🗓️ Google Calendar API</li>
            </ul>

            <p class="mb-0">Proyek ini dikembangkan sebagai latihan praktikum integrasi layanan API dalam aplikasi web sederhana.</p>
            <p>Terima kasih telah menggunakan <strong>E-Meeting</strong> 🙏</p>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
