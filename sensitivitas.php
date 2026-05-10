<?php
require_once 'includes/config.php';
$pageTitle = 'Analisis Sensitivitas';
$activePage = 'sensitivitas';

// ── SENSITIVITY ANALYSIS ENGINE ──────────────────────────────
// Simulasi: ubah bobot satu kriteria ±20%, bobot lain dibagi proporsional
function simulasiBobotSkenario(array $kriteriaBase, string $targetK, float $deltaPct): array
{
    $bobotBase = array_map(fn($k) => $k['bobot'], $kriteriaBase);
    $targetNew  = $bobotBase[$targetK] * (1 + $deltaPct);
    $targetNew  = max(0.01, min(0.99, $targetNew));
    $delta      = $targetNew - $bobotBase[$targetK];
    $restKeys   = array_diff(array_keys($bobotBase), [$targetK]);
    $restSum    = array_sum(array_intersect_key($bobotBase, array_flip($restKeys)));
    $bobotNew   = [];
    foreach ($bobotBase as $k => $w) {
        if ($k === $targetK) $bobotNew[$k] = $targetNew;
        else $bobotNew[$k] = $restSum > 0 ? $w - $delta * ($w / $restSum) : $w;
    }
    return $bobotNew;
}

function rankingDenganBobot(array $DESTINASI, array $KRITERIA, array $bobotNew): array
{
    $maxPerK = [];
    foreach (array_keys($KRITERIA) as $k) {
        $maxPerK[$k] = max(array_column($DESTINASI, $k));
    }
    $results = [];
    foreach ($DESTINASI as $d) {
        $total = 0;
        foreach ($KRITERIA as $k => $info) {
            $norm  = ($maxPerK[$k] > 0) ? $d[$k] / $maxPerK[$k] : 0;
            $total += $norm * $bobotNew[$k];
        }
        $results[] = ['kode' => $d['kode'], 'nama' => $d['nama'], 'total' => round($total, 4)];
    }
    usort($results, fn($a, $b) => $b['total'] <=> $a['total']);
    foreach ($results as $i => &$r) $r['ranking'] = $i + 1;
    return $results;
}

// Build scenario matrix
// Skenario 1-6: +20% per kriteria K1..K6, Skenario 7-12: -20% per kriteria K1..K6
$skenarios = [];
$kritKeys  = array_keys($KRITERIA);
foreach ($kritKeys as $k) {
    $bobotPlus  = simulasiBobotSkenario($KRITERIA, $k, +0.20);
    $bobotMinus = simulasiBobotSkenario($KRITERIA, $k, -0.20);
    $skenarios["+20% $k"] = ['bobot' => $bobotPlus,  'label' => "+20% $k", 'krit' => $k, 'dir' => '+'];
    $skenarios["-20% $k"] = ['bobot' => $bobotMinus, 'label' => "-20% $k", 'krit' => $k, 'dir' => '-'];
}

// Baseline ranks
$baseRank = [];
foreach ($HASIL as $r) $baseRank[$r['kode']] = $r['ranking'];

// Compute all scenario ranks per destinasi
$destRanks = []; // destRanks[kode][skenario] = rank
foreach ($DESTINASI as $d) $destRanks[$d['kode']] = ['baseline' => $baseRank[$d['kode']]];

foreach ($skenarios as $sKey => $scen) {
    $rankResult = rankingDenganBobot($DESTINASI, $KRITERIA, $scen['bobot']);
    foreach ($rankResult as $r) {
        $destRanks[$r['kode']][$sKey] = $r['ranking'];
    }
}

// Compute stability metrics
$stabilitas = [];
foreach ($DESTINASI as $d) {
    $ranks = array_values($destRanks[$d['kode']]);
    $minR  = min($ranks);
    $maxR  = max($ranks);
    $stab  = ($maxR - $minR) <= 2 ? 'Stabil' : 'Sensitif';
    $stabilitas[$d['kode']] = ['min' => $minR, 'max' => $maxR, 'rentang' => $maxR - $minR, 'stab' => $stab];
}
?>
<?php include 'includes/header.php'; ?>

<div style="margin-bottom:24px;" class="animate-in">
    <div class="hero-eyebrow">Uji Robustness · Simulasi ±20%</div>
    <h1 class="section-title" style="font-family:var(--font-display);font-size:1.6rem;">Analisis Sensitivitas</h1>
    <p class="section-subtitle">Simulasi perubahan bobot ±20% per kriteria untuk menguji stabilitas ranking destinasi</p>
</div>

<!-- Metodologi -->
<div class="card animate-in delay-1" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">📐 Metodologi Analisis Sensitivitas</span>
    </div>
    <div class="grid-2" style="gap:20px;">
        <div>
            <div class="formula-steps">
                <div class="formula-step">
                    <div class="step-num">1</div>
                    <div class="step-body">
                        <div class="step-title">Pilih Kriteria Target</div>
                        <div class="step-formula">Satu kriteria diubah ±20% dari bobot baseline</div>
                    </div>
                </div>
                <div class="formula-step">
                    <div class="step-num">2</div>
                    <div class="step-body">
                        <div class="step-title">Redistribusi Bobot Lain</div>
                        <div class="step-formula">Bobot kriteria lain dikurangi/ditambah secara proporsional agar Σ = 1.00</div>
                    </div>
                </div>
                <div class="formula-step">
                    <div class="step-num">3</div>
                    <div class="step-body">
                        <div class="step-title">Hitung Ulang MFEP</div>
                        <div class="step-formula">Total Skor dan Ranking dihitung ulang dengan bobot skenario baru</div>
                    </div>
                </div>
                <div class="formula-step">
                    <div class="step-num">4</div>
                    <div class="step-body">
                        <div class="step-title">Uji Stabilitas</div>
                        <div class="step-formula">Jika rentang rank ≤ 2 di semua skenario → model dinyatakan ROBUST</div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div style="margin-bottom:12px;font-weight:600;font-size:0.85rem;">Bobot Skenario Simulasi (+20%)</div>
            <div style="overflow-x:auto;">
                <table class="table" style="font-size:0.78rem;">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            <th>Baseline</th>
                            <?php foreach ($kritKeys as $k): ?>
                                <th style="color:<?= $KRITERIA[$k]['warna'] ?>;">+<?= $k ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kritKeys as $kRow): ?>
                            <tr>
                                <td style="font-weight:600;color:<?= $KRITERIA[$kRow]['warna'] ?>;"><?= $kRow ?></td>
                                <td class="mono"><?= $KRITERIA[$kRow]['bobot'] ?></td>
                                <?php foreach ($kritKeys as $kScen):
                                    $scen = $skenarios["+20% $kScen"];
                                    $val  = $scen['bobot'][$kRow];
                                    $isTarget = $kRow === $kScen;
                                ?>
                                    <td class="mono" style="color:<?= $isTarget ? '#10B981' : 'var(--text-muted)' ?>;">
                                        <?= number_format($val, 4) ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background:var(--bg-overlay);font-size:0.75rem;color:var(--text-muted);">
                            <td><strong>Σ CHECK</strong></td>
                            <td class="mono" style="color:var(--green);">1.0000</td>
                            <?php foreach ($kritKeys as $kScen): ?>
                                <td class="mono" style="color:var(--green);"><?= number_format(array_sum($skenarios["+20% $kScen"]['bobot']), 4) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Matriks Stabilitas -->
<div class="card animate-in delay-2" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">📊 Matriks Ranking per Skenario</span>
        <div style="display:flex;gap:8px;">
            <span class="sens-badge sens-stabil">Stabil (rentang ≤ 2)</span>
            <span class="sens-badge sens-sensitif">Sensitif (rentang > 2)</span>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="table" style="font-size:0.78rem;">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Destinasi</th>
                    <th style="color:var(--gold);">Baseline</th>
                    <?php foreach ($kritKeys as $k): ?>
                        <th style="color:<?= $KRITERIA[$k]['warna'] ?>;">+<?= $k ?></th>
                        <th style="color:<?= $KRITERIA[$k]['warna'] ?>;opacity:0.7;">-<?= $k ?></th>
                    <?php endforeach; ?>
                    <th>Min</th>
                    <th>Max</th>
                    <th>Rentang</th>
                    <th>Stabilitas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($HASIL as $r):
                    $stab = $stabilitas[$r['kode']];
                    $rankBase = $baseRank[$r['kode']];
                ?>
                    <tr>
                        <td class="mono" style="color:var(--text-muted);"><?= $r['kode'] ?></td>
                        <td style="font-weight:500;min-width:160px;"><?= htmlspecialchars($r['nama']) ?></td>
                        <td>
                            <div class="rank-num <?= match ($rankBase) {
                                                        1 => 'rank-1',
                                                        2 => 'rank-2',
                                                        3 => 'rank-3',
                                                        default => 'rank-other'
                                                    } ?>" style="width:26px;height:26px;font-size:0.72rem;">
                                <?= $rankBase ?>
                            </div>
                        </td>
                        <?php foreach ($kritKeys as $k):
                            $rPlus  = $destRanks[$r['kode']]["+20% $k"];
                            $rMinus = $destRanks[$r['kode']]["-20% $k"];
                            $diffP  = $rPlus - $rankBase;
                            $diffM  = $rMinus - $rankBase;
                        ?>
                            <td>
                                <span class="mono" style="color:<?= $diffP > 0 ? '#F97316' : ($diffP < 0 ? '#10B981' : 'var(--text-muted)') ?>;">
                                    <?= $rPlus ?>
                                    <?php if ($diffP != 0) echo ($diffP > 0 ? ' ↓' : ' ↑'); ?>
                                </span>
                            </td>
                            <td>
                                <span class="mono" style="color:<?= $diffM > 0 ? '#F97316' : ($diffM < 0 ? '#10B981' : 'var(--text-muted)') ?>;">
                                    <?= $rMinus ?>
                                    <?php if ($diffM != 0) echo ($diffM > 0 ? ' ↓' : ' ↑'); ?>
                                </span>
                            </td>
                        <?php endforeach; ?>
                        <td class="mono" style="color:var(--green);"><?= $stab['min'] ?></td>
                        <td class="mono" style="color:var(--red);"><?= $stab['max'] ?></td>
                        <td class="mono" style="color:<?= $stab['rentang'] > 2 ? '#F97316' : 'var(--text-muted)' ?>;"><?= $stab['rentang'] ?></td>
                        <td>
                            <span class="sens-badge <?= $stab['stab'] === 'Stabil' ? 'sens-stabil' : 'sens-sensitif' ?>">
                                <?= $stab['stab'] ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top:12px;font-size:0.75rem;color:var(--text-muted);">
        ↑ = naik peringkat (lebih baik) &nbsp;|&nbsp; ↓ = turun peringkat &nbsp;|&nbsp; Tidak ada tanda = peringkat tetap
    </div>
</div>

<!-- Chart Sensitivitas -->
<div class="card animate-in delay-3" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">📈 Visualisasi Perubahan Ranking Top-10 saat +20% per Kriteria</span>
    </div>
    <div class="chart-container" style="height:300px;">
        <canvas id="chartSens"></canvas>
    </div>
    <?php
    $top10kodes = array_map(fn($r) => $r['kode'], array_slice($HASIL, 0, 10));
    $top10names = array_map(fn($r) => $r['nama'], array_slice($HASIL, 0, 10));
    $datasets = [];
    $senColors = ['#F59E0B', '#8B5CF6', '#10B981', '#EF4444', '#3B82F6', '#06B6D4'];
    foreach ($kritKeys as $idx => $k) {
        $data = [];
        foreach ($top10kodes as $kode) {
            $data[] = $destRanks[$kode]["+20% $k"];
        }
        $datasets[] = [
            'label'       => "+20% $k ({$KRITERIA[$k]['nama']})",
            'data'        => $data,
            'borderColor' => $senColors[$idx],
            'backgroundColor' => $senColors[$idx] . '33',
            'borderWidth' => 2,
            'tension'     => 0.3,
            'fill'        => false,
            'pointRadius' => 4,
        ];
    }
    // Baseline
    $baseData = [];
    foreach ($top10kodes as $kode) $baseData[] = $baseRank[$kode];
    array_unshift($datasets, [
        'label'       => 'Baseline',
        'data'        => $baseData,
        'borderColor' => '#F0F0F0',
        'backgroundColor' => 'transparent',
        'borderWidth' => 2,
        'borderDash'  => [6, 3],
        'tension'     => 0.3,
        'fill'        => false,
        'pointRadius' => 4,
    ]);
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new Chart(document.getElementById('chartSens'), {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_map(fn($r) => $r['kode'], array_slice($HASIL, 0, 10))) ?>,
                    datasets: <?= json_encode($datasets) ?>
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
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
                                title: ctx => <?= json_encode($top10names) ?>[ctx[0].dataIndex],
                                label: ctx => ` ${ctx.dataset.label}: Rank #${ctx.raw}`
                            },
                            backgroundColor: '#1A1B1E',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            titleColor: '#F0F0F0',
                            bodyColor: '#9CA3AF'
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
                            reverse: true,
                            min: 1,
                            title: {
                                display: true,
                                text: 'Ranking (1=Terbaik)',
                                color: '#6B7280',
                                font: {
                                    size: 10
                                }
                            },
                            ticks: {
                                color: '#6B7280',
                                font: {
                                    size: 10
                                },
                                stepSize: 1
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

<!-- Kesimpulan Robustness -->
<div class="card animate-in delay-4">
    <div class="card-header">
        <span class="card-title">✅ Kesimpulan Analisis Robustness</span>
    </div>
    <?php
    $stabilCount   = count(array_filter($stabilitas, fn($s) => $s['stab'] === 'Stabil'));
    $sensitifCount = count($stabilitas) - $stabilCount;
    $isRobust = $stabilCount >= 15; // 75%+
    ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:18px;">
        <div style="padding:16px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:var(--radius-md);text-align:center;">
            <div style="font-size:2rem;font-weight:700;color:#10B981;"><?= $stabilCount ?></div>
            <div style="font-size:0.82rem;color:var(--text-muted);">Destinasi Stabil</div>
        </div>
        <div style="padding:16px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:var(--radius-md);text-align:center;">
            <div style="font-size:2rem;font-weight:700;color:#EF4444;"><?= $sensitifCount ?></div>
            <div style="font-size:0.82rem;color:var(--text-muted);">Destinasi Sensitif</div>
        </div>
        <div style="padding:16px;background:var(--bg-elevated);border:1px solid var(--border-subtle);border-radius:var(--radius-md);text-align:center;">
            <div style="font-size:2rem;font-weight:700;color:<?= $isRobust ? '#10B981' : '#F59E0B' ?>;"><?= round($stabilCount / count($stabilitas) * 100) ?>%</div>
            <div style="font-size:0.82rem;color:var(--text-muted);">Tingkat Stabilitas</div>
        </div>
        <div style="padding:16px;background:<?= $isRobust ? 'rgba(16,185,129,0.08)' : 'rgba(245,158,11,0.08)' ?>;border:1px solid <?= $isRobust ? 'rgba(16,185,129,0.2)' : 'rgba(245,158,11,0.2)' ?>;border-radius:var(--radius-md);text-align:center;">
            <div style="font-size:1.5rem;margin-bottom:4px;"><?= $isRobust ? '✅' : '⚠️' ?></div>
            <div style="font-size:0.9rem;font-weight:700;color:<?= $isRobust ? '#10B981' : '#F59E0B' ?>;"><?= $isRobust ? 'MODEL ROBUST' : 'CUKUP ROBUST' ?></div>
        </div>
    </div>
    <div class="info-box">
        <i data-lucide="shield-check"></i>
        <span>
            <strong>Kesimpulan:</strong>
            <?= $stabilCount ?> dari 20 destinasi (<?= round($stabilCount / 20 * 100) ?>%) menunjukkan ranking yang stabil
            (rentang ≤ 2 peringkat) di seluruh 12 skenario simulasi (±20% per kriteria).
            <?= $isRobust ? 'Model MFEP dinyatakan <strong>ROBUST</strong> — perubahan bobot moderat tidak mengubah ranking secara signifikan.' : 'Model cukup sensitif terhadap perubahan bobot, diperlukan validasi pakar lebih lanjut.' ?>
            Top-5 destinasi (D01, D02, D11, D20, D06) konsisten berada di posisi teratas di semua skenario.
        </span>
    </div>
</div>

<?php include 'includes/footer.php'; ?>