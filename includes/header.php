<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=yes">
    <meta name="theme-color" content="#111827">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GastroSmart">
    <title><?= $pageTitle ?? APP_NAME ?></title>
    <meta name="description" content="Smart Model Rekomendasi Wisata Gastronomi Tradisional Kota Surakarta berbasis Metode MFEP">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>

    <!-- ── SIDEBAR NAV ── -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="20" cy="20" r="19" stroke="#F59E0B" stroke-width="1.5" />
                    <path d="M20 8C13.37 8 8 13.37 8 20s5.37 12 12 12 12-5.37 12-12S26.63 8 20 8z" fill="#F59E0B" opacity="0.15" />
                    <path d="M14 20l4 4 8-8" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="20" cy="20" r="3" fill="#F59E0B" />
                </svg>
            </div>
            <div>
                <span class="brand-name">GastroSmart</span>
                <span class="brand-sub">Surakarta</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Menu Utama</div>
            <a href="index.php" class="nav-item <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i data-lucide="layout-dashboard"></i><span>Dashboard</span>
            </a>
            <a href="kriteria.php" class="nav-item <?= ($activePage ?? '') === 'kriteria' ? 'active' : '' ?>">
                <i data-lucide="sliders"></i><span>Kriteria & Bobot</span>
            </a>
            <a href="destinasi.php" class="nav-item <?= ($activePage ?? '') === 'destinasi' ? 'active' : '' ?>">
                <i data-lucide="map-pin"></i><span>Data Destinasi</span>
            </a>

            <div class="nav-section-label">Kalkulasi</div>
            <a href="perhitungan.php" class="nav-item <?= ($activePage ?? '') === 'perhitungan' ? 'active' : '' ?>">
                <i data-lucide="calculator"></i><span>Perhitungan MFEP</span>
            </a>
            <a href="sensitivitas.php" class="nav-item <?= ($activePage ?? '') === 'sensitivitas' ? 'active' : '' ?>">
                <i data-lucide="activity"></i><span>Analisis Sensitivitas</span>
            </a>

            <div class="nav-section-label">Hasil</div>
            <a href="ranking.php" class="nav-item <?= ($activePage ?? '') === 'ranking' ? 'active' : '' ?>">
                <i data-lucide="trophy"></i><span>Ranking & Rekomendasi</span>
            </a>
            <a href="about.php" class="nav-item <?= ($activePage ?? '') === 'about' ? 'active' : '' ?>">
                <i data-lucide="info"></i><span>Tentang Model</span>
            </a>

            <div class="nav-section-label">Admin</div>
            <a href="admin.php" class="nav-item <?= ($activePage ?? '') === 'admin' ? 'active' : '' ?>">
                <i data-lucide="settings"></i><span>Management Data</span>
            </a>
            <?php if (!empty($_SESSION['admin_user'])): ?>
                <a href="logout.php" class="nav-item">
                    <i data-lucide="log-out"></i><span>Logout Admin</span>
                </a>
            <?php else: ?>
                <a href="login.php" class="nav-item <?= ($activePage ?? '') === 'login' ? 'active' : '' ?>">
                    <i data-lucide="log-in"></i><span>Login Admin</span>
                </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="tkt-badge">
                <span class="tkt-dot"></span>
                TKT Level 2 · PDP 2026
            </div>
        </div>
    </aside>

    <!-- ── MOBILE OVERLAY ── -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- ── MAIN CONTENT ── -->
    <div class="main-content">
        <!-- Top Bar -->
        <header class="topbar">
            <button class="menu-btn" onclick="toggleSidebar()">
                <i data-lucide="menu"></i>
            </button>
            <div class="topbar-breadcrumb">
                <span class="breadcrumb-root">GastroSmart</span>
                <i data-lucide="chevron-right" style="width:14px;height:14px;opacity:0.4"></i>
                <span class="breadcrumb-current"><?= $pageTitle ?? 'Dashboard' ?></span>
            </div>
            <div class="topbar-right">
                <?php if (!empty($_SESSION['admin_user'])): ?>
                    <div class="status-pill" style="background:rgba(59,130,246,0.1);border-color:rgba(59,130,246,0.25);color:var(--blue);">
                        <span class="status-dot" style="background:var(--blue);box-shadow:0 0 4px var(--blue);"></span>
                        Admin: <?= htmlspecialchars($_SESSION['admin_user']['display_name']) ?>
                    </div>
                <?php else: ?>
                    <div class="status-pill">
                        <span class="status-dot"></span>
                        Model Aktif
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- Page Content Start -->
        <main class="page-main">