<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - E-Meeting</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">🔐 Login ke E-Meeting</h4>
                </div>
                <div class="card-body">
                    <p class="mb-4">Masukkan email dan password Anda untuk mengakses sistem.</p>

                    <form action="../public/login_process.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">🔓 Login</button>
                    </form>

                    <div class="mt-3 text-center">
                        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                        <p>Atau <a href="login_google.php">Login dengan Google</a></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS (opsional, jika butuh interaksi) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
