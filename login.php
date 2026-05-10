<?php
require_once 'includes/auth.php';

if (isAdminLoggedIn()) {
    header('Location: admin.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (adminLogin($username, $password, $error)) {
        header('Location: admin.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body class="auth-page">
    <div class="auth-shell">
        <div class="auth-card">
            <div class="hero-eyebrow">Admin Access</div>
            <h1 class="auth-title">Login Admin</h1>
            <p class="auth-subtitle">Masuk untuk mengelola data destinasi, kriteria, dan bobot MFEP.</p>

            <?php if ($error !== ''): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" class="auth-form">
                <label>Username</label>
                <input type="text" name="username" required autocomplete="username" placeholder="Masukkan username">

                <label>Password</label>
                <input type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">

                <button type="submit" class="btn btn-primary auth-submit">Masuk</button>
            </form>

            <div class="auth-help">
                <div>Default akun: <strong>admin</strong></div>
                <div>Default password: <strong>admin123</strong></div>
                <div class="muted">Segera ganti password hash admin di database setelah login pertama.</div>
            </div>

            <a href="index.php" class="btn btn-outline btn-sm" style="margin-top:14px;">Kembali ke Dashboard</a>
        </div>
    </div>
</body>

</html>