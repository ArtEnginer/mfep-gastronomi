<?php
require_once 'includes/config.php';
$pageTitle = 'Kriteria & Bobot';
$activePage = 'kriteria';
$totalBobot = array_sum(array_column($KRITERIA, 'bobot'));
?>
<?php include 'includes/header.php'; ?>

<div style="margin-bottom:24px;" class="animate-in">
    <div class="hero-eyebrow">Model MFEP · Konfigurasi</div>
    <h1 class="section-title" style="font-family:var(--font-display);font-size:1.6rem;">Kriteria & Bobot Evaluasi</h1>
    <p class="section-subtitle">6 kriteria penilaian dengan bobot terbobot dari kuesioner pakar (Σ = 1.00)</p>
</div>

<!-- Kriteria Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px;margin-bottom:24px;">
    <?php foreach ($KRITERIA as $kode => $k):
        $pct = $k['bobot'] * 100;
    ?>
        <div class="card animate-in" style="border-left:3px solid <?= $k['warna'] ?>;">
            <div style="display:flex;align-items:flex-start;gap:14px;">
                <div style="width:46px;height:46px;border-radius:var(--radius-md);background:<?= $k['warna'] ?>18;border:1px solid <?= $k['warna'] ?>35;display:flex;align-items:center;justify-content:center;color:<?= $k['warna'] ?>;flex-shrink:0;">
                    <i data-lucide="<?= $k['icon'] ?>" style="width:20px;height:20px;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <span class="mono" style="font-size:0.75rem;color:<?= $k['warna'] ?>;font-weight:700;"><?= $kode ?></span>
                        <span style="font-weight:600;font-size:0.95rem;"><?= $k['nama'] ?></span>
                    </div>
                    <p style="font-size:0.82rem;color:var(--text-muted);line-height:1.6;margin-bottom:12px;"><?= $k['definisi'] ?></p>
                    <div style="background:var(--bg-elevated);border-radius:var(--radius-sm);padding:10px 12px;font-size:0.79rem;color:var(--text-secondary);border:1px solid var(--border-subtle);margin-bottom:14px;">
                        <span style="color:var(--text-muted);font-weight:600;">Cara Ukur:</span> <?= $k['ukur'] ?>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="flex:1;height:6px;background:var(--bg-overlay);border-radius:3px;overflow:hidden;">
                            <div class="score-bar-fill" data-width="<?= $pct ?>" style="height:100%;width:0;background:<?= $k['warna'] ?>;border-radius:3px;transition:width 1s ease;"></div>
                        </div>
                        <span class="mono" style="font-size:0.9rem;font-weight:700;color:<?= $k['warna'] ?>;min-width:42px;"><?= $pct ?>%</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Skala Likert + Bobot Summary -->
<div class="grid-2" style="gap:20px;margin-bottom:20px;">

    <!-- Bobot Pie Summary -->
    <div class="card animate-in delay-2">
        <div class="card-header">
            <span class="card-title">⚖️ Distribusi Bobot Kriteria</span>
            <div class="badge badge-green">Σ = <?= number_format($totalBobot, 2) ?></div>
        </div>
        <div class="chart-container" style="height:240px;">
            <canvas id="chartBobot"></canvas>
        </div>
        <?php
        // Prepare chart arrays to avoid PHP interpolation issues and ensure valid JSON
        $chart_labels = [];
        $chart_data = [];
        $chart_colors = [];
        foreach ($KRITERIA as $kode => $k) {
            $chart_labels[] = $kode . ': ' . $k['nama'];
            $chart_data[] = $k['bobot'] * 100;
            $chart_colors[] = $k['warna'];
        }
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const ctx = document.getElementById('chartBobot').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: <?= json_encode($chart_labels) ?>,
                        datasets: [{
                            data: <?= json_encode($chart_data) ?>,
                            backgroundColor: <?= json_encode($chart_colors) ?>,
                            borderWidth: 2,
                            borderColor: '#131416',
                            hoverOffset: 8
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
                                        size: 10
                                    },
                                    padding: 10
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        return ` ${ctx.label}: ${ctx.raw.toFixed(0)}%`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </div>

    <!-- Skala Likert Table -->
    <div class="card animate-in delay-3">
        <div class="card-header">
            <span class="card-title">📏 Skala Penilaian Likert (1–5)</span>
        </div>
        <?php
        $likert = [
            [5, 'Sangat Tinggi', '#10B981', 'Resep, bahan, penyajian 100% tradisional & terdokumentasi'],
            [4, 'Tinggi', '#3B82F6', 'Resep dan bahan masih autentik dengan sedikit variasi'],
            [3, 'Cukup', '#F59E0B', 'Resep asli dengan beberapa adaptasi modern'],
            [2, 'Rendah', '#F97316', 'Resep dimodifikasi signifikan, hanya nama yang sama'],
            [1, 'Sangat Rendah', '#EF4444', 'Resep sudah berubah total / menggunakan bahan modern'],
        ];
        foreach ($likert as [$val, $label, $color, $desc]):
        ?>
            <div style="display:flex;gap:12px;align-items:flex-start;padding:10px 0;border-bottom:1px solid var(--border-subtle);">
                <div style="width:32px;height:32px;border-radius:50%;background:<?= $color ?>22;border:1px solid <?= $color ?>55;display:flex;align-items:center;justify-content:center;color:<?= $color ?>;font-weight:700;font-size:0.85rem;flex-shrink:0;"><?= $val ?></div>
                <div>
                    <div style="font-weight:600;font-size:0.85rem;color:<?= $color ?>;"><?= $label ?></div>
                    <div style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;"><?= $desc ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Tabel Ringkasan -->
<div class="card animate-in delay-4">
    <div class="card-header">
        <span class="card-title">📋 Tabel Ringkasan Kriteria MFEP</span>
        <div class="badge badge-gold">6 Kriteria Aktif</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Kriteria</th>
                    <th>Bobot (Wj)</th>
                    <th>% Bobot</th>
                    <th>Definisi Operasional</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($KRITERIA as $kode => $k): ?>
                    <tr>
                        <td><span class="mono" style="color:<?= $k['warna'] ?>;font-weight:700;"><?= $kode ?></span></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <i data-lucide="<?= $k['icon'] ?>" style="width:14px;height:14px;color:<?= $k['warna'] ?>;"></i>
                                <span style="font-weight:500;"><?= $k['nama'] ?></span>
                            </div>
                        </td>
                        <td><span class="mono" style="color:var(--gold);"><?= $k['bobot'] ?></span></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="flex:1;min-width:60px;height:4px;background:var(--bg-overlay);border-radius:2px;">
                                    <div style="width:<?= $k['bobot'] * 100 ?>%;height:100%;background:<?= $k['warna'] ?>;border-radius:2px;"></div>
                                </div>
                                <span class="mono" style="font-size:0.8rem;"><?= ($k['bobot'] * 100) ?>%</span>
                            </div>
                        </td>
                        <td style="font-size:0.8rem;color:var(--text-muted);"><?= $k['definisi'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background:var(--bg-overlay);">
                    <td colspan="2" style="font-weight:700;text-align:right;">TOTAL BOBOT</td>
                    <td><span class="mono" style="color:var(--green);font-weight:700;"><?= $totalBobot ?></span></td>
                    <td><span class="mono" style="color:var(--green);font-weight:700;">100%</span></td>
                    <td style="font-size:0.8rem;color:var(--green);">✓ Valid – Total bobot = 1.00</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">
        <div class="info-box">
            <i data-lucide="info"></i>
            <span>Bobot kriteria ditetapkan berdasarkan kuesioner pakar kuliner, pariwisata, dan pemangku kepentingan di Kota Surakarta. Metode pembobotan menggunakan Analytic Hierarchy Process (AHP) sederhana. Total bobot = 1.00 (100%).</span>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>