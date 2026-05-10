<?php

require_once __DIR__ . '/config.php';

function isAdminLoggedIn(): bool
{
    return !empty($_SESSION['admin_user']);
}

function currentAdmin(): ?array
{
    return $_SESSION['admin_user'] ?? null;
}

function requireAdminLogin(): void
{
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function adminLogin(string $username, string $password, ?string &$error = null): bool
{
    $username = trim($username);
    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
        return false;
    }

    $db = getDbConnection();
    if (!$db || !dbTableExists($db, 'admin_users')) {
        $error = 'Tabel admin belum tersedia. Jalankan file SQL inisialisasi terlebih dahulu.';
        return false;
    }

    $stmt = $db->prepare('SELECT id, username, display_name, password_hash, is_active FROM admin_users WHERE username = ? LIMIT 1');
    if (!$stmt) {
        $error = 'Gagal memproses login.';
        return false;
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$user || (int)$user['is_active'] !== 1) {
        $error = 'Akun tidak ditemukan atau tidak aktif.';
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        $error = 'Password salah.';
        return false;
    }

    $_SESSION['admin_user'] = [
        'id' => (int)$user['id'],
        'username' => $user['username'],
        'display_name' => $user['display_name'],
    ];

    return true;
}

function adminLogout(): void
{
    unset($_SESSION['admin_user']);
}
