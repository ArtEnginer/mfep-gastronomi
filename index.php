<?php
require_once 'includes/config.php';
$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// Stats
$totalDest = count($DESTINASI);
$unggulan = count(array_filter($HASIL, fn($r) => $r['kategori'] === 'Unggulan Heritage'));
$totalBobot = array_sum(array_column($KRITERIA, 'bobot'));
$top3 = array_slice($HASIL, 0, 3);
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Banner -->
<div class="hero-banner animate-in">
    <div class="orb orb-gold"></div>
    <div class="orb orb-purple"></div>
    <div class="hero-pattern"></div>
    <div class="hero-banner-content">
        <div class="hero-eyebrow">Penelitian Dosen Pemula · MFEP · TKT Level 2</div>
        <h1 class="hero-title">Smart Model Rekomendasi<br><em>Wisata Gastronomi</em></h1>
        <p class="hero-desc">
            Sistem pendukung keputusan berbasis <strong style="color:var(--text-primary)">Multi Factor Evaluation Process (MFEP)</strong>
            untuk merekomendasikan destinasi wisata gastronomi tradisional terbaik
            di Kota Surakarta, mendukung pelestarian kuliner warisan budaya Jawa.
        </p>
        <div style="margin-top:20px;display:flex;gap:10px;flex-wrap:wrap;">
            <a href="pages/ranking.php" class="btn btn-primary">
                <i data-lucide="trophy"></i> Lihat Ranking
            </a>
            <a href="pages/perhitungan.php" class="btn btn-outline">
                <i data-lucide="calculator"></i> Perhitungan MFEP
            </a>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card animate-in delay-1" style="--accent:var(--gold)">
        <div class="stat-icon" style="color:var(--gold)"><i data-lucide="map-pin"></i></div>
        <div class="stat-label">Total Destinasi</div>
        <div class="stat-value" data-countup="<?= $totalDest ?>"><?= $totalDest ?></div>
        <div class="stat-sub">Destinasi gastronomi terpetakan</div>
    </div>
    <div class="stat-card animate-in delay-2" style="--accent:var(--green)">
        <div class="stat-icon" style="color:var(--green)"><i data-lucide="award"></i></div>
        <div class="stat-label">Unggulan Heritage</div>
        <div class="stat-value" data-countup="<?= $unggulan ?>"><?= $unggulan ?></div>
        <div class="stat-sub">Destinasi kategori terbaik</div>
    </div>
    <div class="stat-card animate-in delay-3" style="--accent:var(--purple)">
        <div class="stat-icon" style="color:var(--purple)"><i data-lucide="sliders"></i></div>
        <div class="stat-label">Kriteria Evaluasi</div>
        <div class="stat-value" data-countup="<?= count($KRITERIA) ?>"><?= count($KRITERIA) ?></div>
        <div class="stat-sub">Faktor penilaian multi-kriteria</div>
    </div>
    <div class="stat-card animate-in delay-4" style="--accent:var(--blue)">
        <div class="stat-icon" style="color:var(--blue)"><i data-lucide="bar-chart-2"></i></div>
        <div class="stat-label">Skor Rata-Rata</div>
        <div class="stat-value" data-countup="<?= $AVG_SCORE ?>" data-decimals="4"><?= $AVG_SCORE ?></div>
        <div class="stat-sub">Rerata skor MFEP seluruh destinasi</div>
    </div>
    <div class="stat-card animate-in delay-5" style="--accent:var(--red)">
        <div class="stat-icon" style="color:var(--red)"><i data-lucide="trending-up"></i></div>
        <div class="stat-label">Skor Tertinggi</div>
        <div class="stat-value" data-countup="<?= $MAX_SCORE ?>" data-decimals="4"><?= $MAX_SCORE ?></div>
        <div class="stat-sub"><?= $HASIL[0]['nama'] ?></div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid-2" style="gap:20px;margin-bottom:20px;">

    <!-- Top 3 Destinasi -->
    <div class="card animate-in delay-2">
        <div class="card-header">
            <span class="card-title">🏆 Top 3 Destinasi Terbaik</span>
            <a href="pages/ranking.php" class="btn btn-outline btn-sm">Semua <i data-lucide="arrow-right"></i></a>
        </div>
        <?php foreach ($top3 as $r): ?>
            <?php
            $rankClass = match ($r['ranking']) {
                1 => 'rank-1',
                2 => 'rank-2',
                3 => 'rank-3',
                default => 'rank-other'
            };
            $pct = round(($r['total'] / $MAX_SCORE) * 100, 1);
            $kat = getKategoriStyle($r['kategori']);
            ?>
            <div style="display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid var(--border-subtle);">
                <div class="rank-num <?= $rankClass ?>"><?= $r['ranking'] ?></div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:0.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($r['nama']) ?></div>
                    <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;"><?= htmlspecialchars($r['jenis']) ?></div>
                </div>
                <div style="text-align:right;">
                    <div class="mono" style="color:var(--gold);font-size:0.95rem;font-weight:600;"><?= number_format($r['total'], 4) ?></div>
                    <div style="font-size:0.7rem;color:var(--text-muted);"><?= $r['kategori'] ?></div>
                </div>
            </div>
        <?php endforeach; ?>
        <div style="padding-top:14px;">
            <div class="info-box">
                <i data-lucide="info"></i>
                <span>Ranking berdasarkan Total Skor MFEP dengan 6 kriteria terbobot dari kuesioner pakar.</span>
            </div>
        </div>
    </div>

    <!-- Distribusi Kategori -->
    <div class="card animate-in delay-3">
        <div class="card-header">
            <span class="card-title">📊 Distribusi Kategori</span>
        </div>
        <div class="chart-container" style="height:220px;">
            <canvas id="chartKategori"></canvas>
        </div>
        <?php
        $katCount = [];
        foreach ($HASIL as $r) {
            $katCount[$r['kategori']] = ($katCount[$r['kategori']] ?? 0) + 1;
        }
        $katLabels = array_keys($katCount);
        $katValues = array_values($katCount);
        $katColors = ['#10B981', '#3B82F6', '#F59E0B', '#F97316', '#71717A'];
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                new Chart(document.getElementById('chartKategori'), {
                    type: 'doughnut',
                    data: {
                        labels: <?= json_encode($katLabels) ?>,
                        datasets: [{
                            data: <?= json_encode($katValues) ?>,
                            backgroundColor: <?= json_encode(array_slice($katColors, 0, count($katLabels))) ?>,
                            borderWidth: 2,
                            borderColor: '#131416',
                            hoverOffset: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    color: '#9CA3AF',
                                    font: {
                                        family: 'DM Sans',
                                        size: 11
                                    },
                                    padding: 12
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ` ${ctx.label}: ${ctx.raw} destinasi`
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            });
        </script>
    </div>
</div>

<!-- Skor semua destinasi - bar chart -->
<div class="card animate-in delay-4" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">📈 Total Skor MFEP — Seluruh Destinasi</span>
        <div class="badge badge-gold">Terurut: Peringkat 1→20</div>
    </div>
    <div class="chart-container" style="height:260px;">
        <canvas id="chartScores"></canvas>
    </div>
    <?php
    $chartLabels = array_map(fn($r) => $r['kode'], $HASIL);
    $chartData   = array_map(fn($r) => $r['total'], $HASIL);
    $chartColors = array_map(fn($r) => match ($r['kategori']) {
        'Unggulan Heritage' => 'rgba(16,185,129,0.8)',
        'Prioritas A' => 'rgba(59,130,246,0.8)',
        'Prioritas B' => 'rgba(245,158,11,0.8)',
        'Prioritas C' => 'rgba(249,115,22,0.8)',
        default => 'rgba(113,113,122,0.7)',
    }, $HASIL);
    $chartNames = array_map(fn($r) => $r['nama'], $HASIL);
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new Chart(document.getElementById('chartScores'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($chartLabels) ?>,
                    datasets: [{
                        label: 'Total Skor MFEP',
                        data: <?= json_encode($chartData) ?>,
                        backgroundColor: <?= json_encode($chartColors) ?>,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: ctx => <?= json_encode($chartNames) ?>[ctx[0].dataIndex],
                                label: ctx => ` Skor MFEP: ${ctx.raw.toFixed(4)}`
                            },
                            backgroundColor: '#1A1B1E',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            titleColor: '#F0F0F0',
                            bodyColor: '#9CA3AF',
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#6B7280',
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.04)'
                            }
                        },
                        y: {
                            min: 0.5,
                            max: 1.0,
                            ticks: {
                                color: '#6B7280',
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                color: 'rgba(255,255,255,0.06)'
                            }
                        }
                    }
                }
            });
        });
    </script>
</div>

<!-- Bobot Kriteria Summary -->
<div class="card animate-in delay-5">
    <div class="card-header">
        <span class="card-title">⚖️ Ringkasan Bobot Kriteria MFEP</span>
        <div class="badge badge-green">Σ Bobot = <?= number_format($totalBobot, 2) ?></div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:12px;">
        <?php foreach ($KRITERIA as $kode => $k): ?>
            <div style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--bg-elevated);border-radius:var(--radius-md);border:1px solid var(--border-subtle);">
                <div style="width:38px;height:38px;border-radius:var(--radius-sm);background:<?= $k['warna'] ?>20;border:1px solid <?= $k['warna'] ?>40;display:flex;align-items:center;justify-content:center;color:<?= $k['warna'] ?>;flex-shrink:0;">
                    <i data-lucide="<?= $k['icon'] ?>" style="width:16px;height:16px;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.82rem;font-weight:600;"><?= $kode ?>: <?= $k['nama'] ?></div>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:5px;">
                        <div class="score-bar" style="flex:1;height:4px;">
                            <div class="score-bar-fill" data-width="<?= $k['bobot'] * 100 ?>" style="width:0;background:<?= $k['warna'] ?>;"></div>
                        </div>
                        <span class="mono" style="font-size:0.8rem;color:<?= $k['warna'] ?>;min-width:38px;"><?= ($k['bobot'] * 100) ?>%</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>