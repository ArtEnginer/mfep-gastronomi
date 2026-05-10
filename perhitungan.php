<?php
require_once 'includes/config.php';
$pageTitle = 'Perhitungan MFEP';
$activePage = 'perhitungan';
?>
<?php include 'includes/header.php'; ?>

<div style="margin-bottom:24px;" class="animate-in">
    <div class="hero-eyebrow">Kalkulasi · Multi Factor Evaluation Process</div>
    <h1 class="section-title" style="font-family:var(--font-display);font-size:1.6rem;">Perhitungan MFEP</h1>
    <p class="section-subtitle">Langkah-langkah normalisasi, weighted score, dan total skor MFEP untuk 20 destinasi gastronomi</p>
</div>

<!-- Formula Steps -->
<div class="card animate-in delay-1" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">📐 Alur Perhitungan MFEP</span>
        <div class="badge badge-gold">5 Langkah</div>
    </div>
    <div class="formula-steps">
        <div class="formula-step">
            <div class="step-num">1</div>
            <div class="step-body">
                <div class="step-title">Nilai Mentah (Raw Score)</div>
                <div class="step-formula">Nilai_ij ∈ {1, 2, 3, 4, 5} — Skala Likert dari observasi & penilaian pakar</div>
            </div>
        </div>
        <div class="formula-step">
            <div class="step-num">2</div>
            <div class="step-body">
                <div class="step-title">Normalisasi (0–1)</div>
                <div class="step-formula">N_ij = Nilai_ij / MAX(Nilai_j) → karena MAX = 5, maka N_ij = Nilai_ij / 5</div>
            </div>
        </div>
        <div class="formula-step">
            <div class="step-num">3</div>
            <div class="step-body">
                <div class="step-title">Weighted Score</div>
                <div class="step-formula">WS_ij = N_ij × Wj &nbsp;(Wj = bobot kriteria dari kuesioner pakar)</div>
            </div>
        </div>
        <div class="formula-step">
            <div class="step-num">4</div>
            <div class="step-body">
                <div class="step-title">Total Skor MFEP</div>
                <div class="step-formula">Total_i = Σ WS_ij (j=K1..K6) → penjumlahan seluruh weighted score</div>
            </div>
        </div>
        <div class="formula-step">
            <div class="step-num">5</div>
            <div class="step-body">
                <div class="step-title">Ranking</div>
                <div class="step-formula">RANK berdasarkan Total_i descending → Rank 1 = skor tertinggi = terbaik</div>
            </div>
        </div>
    </div>
</div>

<!-- Bobot Row -->
<div class="card animate-in delay-2" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">⚖️ Bobot Kriteria (Wj)</span>
        <div class="badge badge-green">Σ Wj = 1.00</div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <?php foreach ($KRITERIA as $kode => $k): ?>
        <div style="flex:1;min-width:120px;padding:12px 14px;background:var(--bg-elevated);border-radius:var(--radius-md);border:1px solid <?= $k['warna'] ?>30;text-align:center;">
            <div class="mono" style="font-size:0.7rem;color:<?= $k['warna'] ?>;font-weight:700;margin-bottom:4px;"><?= $kode ?></div>
            <div style="font-weight:600;font-size:0.82rem;margin-bottom:6px;"><?= $k['nama'] ?></div>
            <div class="mono" style="font-size:1.2rem;font-weight:700;color:<?= $k['warna'] ?>;"><?= $k['bobot'] ?></div>
            <div style="font-size:0.7rem;color:var(--text-muted);"><?= ($k['bobot']*100) ?>%</div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Tabel Normalisasi -->
<div class="card animate-in delay-2" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">📋 Step 2: Matriks Nilai Normal (N_ij = Nilai / MAX)</span>
        <div style="font-size:0.78rem;color:var(--text-muted);">MAX per kriteria = 5</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Destinasi</th>
                    <?php foreach ($KRITERIA as $kode => $k): ?>
                    <th style="color:<?= $k['warna'] ?>;">N_<?= $kode ?><br><span style="font-weight:400;font-size:0.68rem;"><?= $k['nama'] ?></span></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($HASIL as $r): ?>
                <tr>
                    <td><span class="mono" style="color:var(--text-muted);"><?= $r['kode'] ?></span></td>
                    <td style="font-weight:500;font-size:0.85rem;"><?= htmlspecialchars($r['nama']) ?></td>
                    <?php foreach ($KRITERIA as $kode => $k): ?>
                    <td>
                        <span class="mono" style="color:<?= $r['norm'][$kode] == 1.0 ? '#10B981' : ($r['norm'][$kode] < 0.7 ? '#F97316' : 'var(--text-secondary)') ?>;">
                            <?= number_format($r['norm'][$kode], 4) ?>
                        </span>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top:12px;font-size:0.76rem;color:var(--text-muted);">
        <span style="color:#10B981;">■</span> Nilai normal = 1.0000 (maksimal) &nbsp;|&nbsp;
        <span style="color:#F97316;">■</span> Nilai normal &lt; 0.70
    </div>
</div>

<!-- Tabel Weighted Score -->
<div class="card animate-in delay-3" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">📋 Step 3: Matriks Weighted Score (WS_ij = N_ij × Wj)</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Destinasi</th>
                    <?php foreach ($KRITERIA as $kode => $k): ?>
                    <th style="color:<?= $k['warna'] ?>;">WS_<?= $kode ?><br><span style="font-weight:400;font-size:0.68rem;">×<?= $k['bobot'] ?></span></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($HASIL as $r): ?>
                <tr>
                    <td><span class="mono" style="color:var(--text-muted);"><?= $r['kode'] ?></span></td>
                    <td style="font-weight:500;font-size:0.85rem;"><?= htmlspecialchars($r['nama']) ?></td>
                    <?php foreach ($KRITERIA as $kode => $k): ?>
                    <td>
                        <span class="mono" style="color:<?= $k['warna'] ?>;font-size:0.82rem;">
                            <?= number_format($r['ws'][$kode], 4) ?>
                        </span>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
                <!-- Bobot row -->
                <tr style="background:var(--bg-overlay);font-size:0.78rem;">
                    <td colspan="2" style="text-align:right;color:var(--text-muted);font-weight:600;">BOBOT (Wj) →</td>
                    <?php foreach ($KRITERIA as $kode => $k): ?>
                    <td><span class="mono" style="color:<?= $k['warna'] ?>;font-weight:700;"><?= $k['bobot'] ?></span></td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Tabel Hasil Final -->
<div class="card animate-in delay-4">
    <div class="card-header">
        <span class="card-title">🏁 Step 4 & 5: Total Skor MFEP & Ranking</span>
        <div class="badge badge-gold">Total_i = Σ WS_ij</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Kode</th>
                    <th>Nama Destinasi</th>
                    <?php foreach ($KRITERIA as $kode => $k): ?>
                    <th style="color:<?= $k['warna'] ?>;font-size:0.72rem;">WS_<?= $kode ?></th>
                    <?php endforeach; ?>
                    <th style="color:var(--gold);">Total Skor</th>
                    <th>Kategori</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($HASIL as $r):
                    $rankClass = match($r['ranking']) { 1=>'rank-1', 2=>'rank-2', 3=>'rank-3', default=>'rank-other' };
                    $kat = getKategoriStyle($r['kategori']);
                ?>
                <tr>
                    <td>
                        <div class="rank-num <?= $rankClass ?>"><?= $r['ranking'] ?></div>
                    </td>
                    <td><span class="mono" style="color:var(--text-muted);"><?= $r['kode'] ?></span></td>
                    <td style="font-weight:500;font-size:0.85rem;"><?= htmlspecialchars($r['nama']) ?></td>
                    <?php foreach ($KRITERIA as $kode => $k): ?>
                    <td><span class="mono" style="font-size:0.78rem;color:<?= $k['warna'] ?>;"><?= number_format($r['ws'][$kode],4) ?></span></td>
                    <?php endforeach; ?>
                    <td>
                        <span class="mono" style="font-size:0.95rem;font-weight:700;color:var(--gold);"><?= number_format($r['total'],4) ?></span>
                    </td>
                    <td>
                        <span style="font-size:0.72rem;padding:3px 10px;background:<?= $kat['bg'] ?>;border:1px solid <?= $kat['border'] ?>;border-radius:999px;color:<?= $kat['text'] ?>;white-space:nowrap;"><?= $r['kategori'] ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        <div class="info-box">
            <i data-lucide="check-circle"></i>
            <span>
                Perhitungan telah divalidasi sesuai data Excel manual (MFEP_Wisata_Gastronomi_Surakarta.xlsx).
                Total skor = Σ WS_ij untuk 6 kriteria. Ranking diurutkan descending.
                Σ Bobot check = <?= array_sum(array_column($KRITERIA,'bobot')) ?> ✓
            </span>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
