<?php
require_once 'includes/config.php';
$pageTitle = 'Ranking & Rekomendasi';
$activePage = 'ranking';

$top5 = array_slice($HASIL, 0, 5);

// Rekomendasi kebijakan per destinasi
$rekomendasi = [
    'D01' => ['kebijakan'=>'Program Sertifikasi Kuliner Warisan Budaya','dana'=>'APBD Kota + Kemenpar'],
    'D02' => ['kebijakan'=>'Festival Gastronomi Tahunan & promosi digital','dana'=>'APBD + CSR'],
    'D03' => ['kebijakan'=>'Dokumentasi resep & sertifikasi autentisitas','dana'=>'Hibah kebudayaan'],
    'D04' => ['kebijakan'=>'Penetapan kawasan kuliner tradisional','dana'=>'APBD Kota'],
    'D05' => ['kebijakan'=>'Pemberdayaan UMKM & inkubasi kuliner','dana'=>'Kemenparekraf'],
    'D06' => ['kebijakan'=>'Peta wisata gastronomi digital','dana'=>'Dinas Pariwisata'],
    'D07' => ['kebijakan'=>'Coaching kuliner tradisional','dana'=>'Dana Desa/Kelurahan'],
    'D08' => ['kebijakan'=>'Pengembangan kemasan & branding','dana'=>'UMKM Center'],
    'D09' => ['kebijakan'=>'Fasilitasi akses pasar modern','dana'=>'Dinas Perdagangan'],
    'D10' => ['kebijakan'=>'Standardisasi resep & pelatihan','dana'=>'Kemenpar'],
    'D11' => ['kebijakan'=>'Pendampingan pengembangan bisnis','dana'=>'BPD/KUR'],
    'D12' => ['kebijakan'=>'Integrasi ke paket wisata kuliner','dana'=>'Biro perjalanan'],
    'D13' => ['kebijakan'=>'Peningkatan branding & media sosial','dana'=>'Digital marketing'],
    'D14' => ['kebijakan'=>'Program magang kuliner tradisional','dana'=>'Dinas Pendidikan'],
    'D15' => ['kebijakan'=>'Dokumentasi digital resep','dana'=>'Perpusnas'],
    'D16' => ['kebijakan'=>'Pendampingan intensif & capacity building','dana'=>'LPPM PT'],
    'D17' => ['kebijakan'=>'Inovasi produk berbasis tradisi','dana'=>'Hibah inovasi'],
    'D18' => ['kebijakan'=>'Pelatihan hygiene & food safety','dana'=>'BPOM'],
    'D19' => ['kebijakan'=>'Promosi melalui food influencer','dana'=>'Kolaborasi media'],
    'D20' => ['kebijakan'=>'Revitalisasi & dokumentasi sejarah','dana'=>'Dana kebudayaan'],
];

$kekuatan = [
    'D01'=>'Keaslian & budaya kuat','D02'=>'Nilai budaya & komunitas','D03'=>'Keaslian tinggi',
    'D04'=>'Aksesibilitas & lokasi strategis','D05'=>'Komunitas aktif','D06'=>'Kekayaan varian kuliner',
    'D07'=>'Keaslian terjaga','D08'=>'Tradisi kuat','D09'=>'Produk unik',
    'D10'=>'Tradisi kuliner','D11'=>'Autentisitas terjaga','D12'=>'Produk heritage',
    'D13'=>'Lokasi strategis','D14'=>'Nilai tradisi','D15'=>'Identitas lokal',
    'D16'=>'Keaslian tinggi','D17'=>'Potensi lokal','D18'=>'Kekhasan resep',
    'D19'=>'Teknik memasak unik','D20'=>'Peluang heritage',
];
?>
<?php include 'includes/header.php'; ?>

<div style="margin-bottom:24px;" class="animate-in">
    <div class="hero-eyebrow">Hasil MFEP · Rekomendasi Kebijakan</div>
    <h1 class="section-title" style="font-family:var(--font-display);font-size:1.6rem;">Ranking & Rekomendasi</h1>
    <p class="section-subtitle">Peringkat final 20 destinasi wisata gastronomi berdasarkan Total Skor MFEP</p>
</div>

<!-- Podium Top 3 -->
<div class="card animate-in delay-1" style="margin-bottom:20px;background:linear-gradient(135deg,var(--bg-surface) 0%,var(--bg-elevated) 100%);">
    <div class="card-header">
        <span class="card-title">🏆 Podium Top 3 Destinasi Unggulan Heritage</span>
    </div>
    <div style="display:flex;align-items:flex-end;justify-content:center;gap:16px;padding:10px 0 20px;">
        <!-- Rank 2 -->
        <?php $r2 = $HASIL[1]; $k2 = getKategoriStyle($r2['kategori']); ?>
        <div style="text-align:center;flex:1;max-width:200px;">
            <div style="font-size:1.5rem;margin-bottom:8px;">🥈</div>
            <div style="background:var(--bg-overlay);border:1px solid var(--border-medium);border-radius:var(--radius-lg);padding:16px 12px;">
                <div class="mono" style="font-size:0.7rem;color:var(--text-muted);"><?= $r2['kode'] ?></div>
                <div style="font-weight:700;font-size:0.85rem;margin:6px 0;"><?= htmlspecialchars($r2['nama']) ?></div>
                <div class="mono" style="font-size:1.2rem;font-weight:700;color:#9CA3AF;"><?= number_format($r2['total'],4) ?></div>
            </div>
            <div style="height:60px;background:linear-gradient(to top,rgba(156,163,175,0.2),transparent);border-radius:0 0 8px 8px;margin-top:-8px;"></div>
        </div>

        <!-- Rank 1 -->
        <?php $r1 = $HASIL[0]; $k1 = getKategoriStyle($r1['kategori']); ?>
        <div style="text-align:center;flex:1;max-width:220px;transform:translateY(-20px);">
            <div style="font-size:2rem;margin-bottom:8px;">🥇</div>
            <div style="background:var(--gold-subtle);border:2px solid var(--gold);border-radius:var(--radius-lg);padding:20px 14px;position:relative;box-shadow:var(--glow-gold);">
                <div style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:var(--gold);color:#0C0D0F;font-size:0.65rem;font-weight:700;padding:2px 10px;border-radius:999px;">TERBAIK</div>
                <div class="mono" style="font-size:0.7rem;color:var(--gold-dim);"><?= $r1['kode'] ?></div>
                <div style="font-weight:700;font-size:0.95rem;margin:6px 0;color:var(--text-gold);"><?= htmlspecialchars($r1['nama']) ?></div>
                <div class="mono" style="font-size:1.5rem;font-weight:700;color:var(--gold);"><?= number_format($r1['total'],4) ?></div>
            </div>
            <div style="height:80px;background:linear-gradient(to top,rgba(245,158,11,0.1),transparent);border-radius:0 0 8px 8px;margin-top:-8px;"></div>
        </div>

        <!-- Rank 3 -->
        <?php $r3 = $HASIL[2]; $k3 = getKategoriStyle($r3['kategori']); ?>
        <div style="text-align:center;flex:1;max-width:200px;">
            <div style="font-size:1.5rem;margin-bottom:8px;">🥉</div>
            <div style="background:var(--bg-overlay);border:1px solid var(--border-medium);border-radius:var(--radius-lg);padding:16px 12px;">
                <div class="mono" style="font-size:0.7rem;color:var(--text-muted);"><?= $r3['kode'] ?></div>
                <div style="font-weight:700;font-size:0.85rem;margin:6px 0;"><?= htmlspecialchars($r3['nama']) ?></div>
                <div class="mono" style="font-size:1.2rem;font-weight:700;color:#CD7F32;"><?= number_format($r3['total'],4) ?></div>
            </div>
            <div style="height:40px;background:linear-gradient(to top,rgba(205,127,50,0.1),transparent);border-radius:0 0 8px 8px;margin-top:-8px;"></div>
        </div>
    </div>
</div>

<!-- Chart Skor -->
<div class="card animate-in delay-2" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">📊 Visualisasi Total Skor MFEP — 20 Destinasi</span>
    </div>
    <div class="chart-container" style="height:280px;">
        <canvas id="chartRanking"></canvas>
    </div>
    <?php
    $labels  = array_map(fn($r)=>$r['kode'],$HASIL);
    $scores  = array_map(fn($r)=>$r['total'],$HASIL);
    $names   = array_map(fn($r)=>$r['nama'],$HASIL);
    $bgColors= array_map(fn($r)=>match($r['kategori']){
        'Unggulan Heritage'=>'rgba(16,185,129,0.85)',
        'Prioritas A'=>'rgba(59,130,246,0.85)',
        'Prioritas B'=>'rgba(245,158,11,0.85)',
        'Prioritas C'=>'rgba(249,115,22,0.85)',
        default=>'rgba(113,113,122,0.7)'
    },$HASIL);
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        new Chart(document.getElementById('chartRanking'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Total Skor MFEP',
                    data: <?= json_encode($scores) ?>,
                    backgroundColor: <?= json_encode($bgColors) ?>,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: ctx => <?= json_encode($names) ?>[ctx[0].dataIndex],
                            label: ctx => ` Skor MFEP: ${ctx.raw.toFixed(4)}`
                        },
                        backgroundColor:'#1A1B1E',borderColor:'rgba(255,255,255,0.1)',borderWidth:1,
                        titleColor:'#F0F0F0',bodyColor:'#9CA3AF'
                    }
                },
                scales: {
                    x: { ticks:{color:'#6B7280',font:{size:10}}, grid:{color:'rgba(255,255,255,0.04)'} },
                    y: { min:0.7,max:1.0, ticks:{color:'#6B7280',font:{size:10}}, grid:{color:'rgba(255,255,255,0.06)'} }
                }
            }
        });
    });
    </script>
    <!-- Legend -->
    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:12px;padding-top:12px;border-top:1px solid var(--border-subtle);">
        <?php
        $katDefs = [
            'Unggulan Heritage'=>['color'=>'rgba(16,185,129,0.85)','range'=>'≥ 0.85'],
            'Prioritas A'=>['color'=>'rgba(59,130,246,0.85)','range'=>'0.75–0.84'],
            'Prioritas B'=>['color'=>'rgba(245,158,11,0.85)','range'=>'0.65–0.74'],
            'Prioritas C'=>['color'=>'rgba(249,115,22,0.85)','range'=>'0.55–0.64'],
            'Perlu Pembinaan'=>['color'=>'rgba(113,113,122,0.7)','range'=>'< 0.55'],
        ];
        foreach ($katDefs as $nama => $def): ?>
        <div style="display:flex;align-items:center;gap:6px;font-size:0.76rem;color:var(--text-muted);">
            <div style="width:12px;height:12px;border-radius:3px;background:<?= $def['color'] ?>;flex-shrink:0;"></div>
            <span><?= $nama ?> (<?= $def['range'] ?>)</span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Tabel Ranking Lengkap dengan Rekomendasi -->
<div class="card animate-in delay-3" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">📋 Tabel Ranking Lengkap & Rekomendasi Kebijakan</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Kode</th>
                    <th>Nama Destinasi</th>
                    <th>Total Skor</th>
                    <th>Kategori</th>
                    <th>Kekuatan Utama</th>
                    <th>Rekomendasi Kebijakan</th>
                    <th>Sumber Dana</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($HASIL as $r):
                    $kat = getKategoriStyle($r['kategori']);
                    $rankClass = match($r['ranking']){ 1=>'rank-1', 2=>'rank-2', 3=>'rank-3', default=>'rank-other' };
                    $rek = $rekomendasi[$r['kode']] ?? ['kebijakan'=>'—','dana'=>'—'];
                    $kuat = $kekuatan[$r['kode']] ?? '—';
                ?>
                <tr>
                    <td><div class="rank-num <?= $rankClass ?>"><?= $r['ranking'] ?></div></td>
                    <td><span class="mono" style="color:var(--text-muted);"><?= $r['kode'] ?></span></td>
                    <td style="font-weight:600;font-size:0.85rem;min-width:160px;"><?= htmlspecialchars($r['nama']) ?></td>
                    <td><span class="mono" style="color:var(--gold);font-weight:700;font-size:0.95rem;"><?= number_format($r['total'],4) ?></span></td>
                    <td>
                        <span style="font-size:0.7rem;padding:3px 9px;background:<?= $kat['bg'] ?>;border:1px solid <?= $kat['border'] ?>;border-radius:999px;color:<?= $kat['text'] ?>;white-space:nowrap;"><?= $r['kategori'] ?></span>
                    </td>
                    <td style="font-size:0.8rem;color:var(--text-muted);"><?= $kuat ?></td>
                    <td style="font-size:0.8rem;min-width:200px;"><?= $rek['kebijakan'] ?></td>
                    <td style="font-size:0.78rem;color:var(--text-muted);"><?= $rek['dana'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Kategori Summary -->
<div class="card animate-in delay-4" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">📌 Keterangan Kategori Prioritas Pelestarian</span>
    </div>
    <?php
    $katInfo = [
        'Unggulan Heritage' => ['range'=>'≥ 0.85','desc'=>'Destinasi dengan nilai heritage tertinggi, perlu perlindungan dan promosi intensif','color'=>'#10B981'],
        'Prioritas A'       => ['range'=>'0.75–0.84','desc'=>'Destinasi potensial, butuh dukungan infrastruktur dan regulasi','color'=>'#3B82F6'],
        'Prioritas B'       => ['range'=>'0.65–0.74','desc'=>'Destinasi berkembang, perlu pembinaan UMKM dan branding','color'=>'#F59E0B'],
        'Prioritas C'       => ['range'=>'0.55–0.64','desc'=>'Destinasi rintisan, butuh pendampingan dan akses pasar','color'=>'#F97316'],
        'Perlu Pembinaan'   => ['range'=>'< 0.55','desc'=>'Destinasi berisiko, perlu intervensi khusus dan revitalisasi','color'=>'#71717A'],
    ];
    foreach ($katInfo as $nama => $info):
        $count = count(array_filter($HASIL, fn($r)=>$r['kategori']===$nama));
    ?>
    <div style="display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid var(--border-subtle);">
        <div style="width:14px;height:14px;border-radius:4px;background:<?= $info['color'] ?>;flex-shrink:0;"></div>
        <div style="flex:1;">
            <div style="font-weight:600;font-size:0.88rem;"><?= $nama ?> <span class="mono" style="color:<?= $info['color'] ?>;font-size:0.78rem;">(Skor <?= $info['range'] ?>)</span></div>
            <div style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;"><?= $info['desc'] ?></div>
        </div>
        <div style="text-align:center;min-width:50px;">
            <div style="font-size:1.4rem;font-weight:700;color:<?= $info['color'] ?>;"><?= $count ?></div>
            <div style="font-size:0.68rem;color:var(--text-muted);">destinasi</div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Statistik Model -->
<div class="card animate-in delay-5">
    <div class="card-header">
        <span class="card-title">📈 Statistik Ringkasan Model MFEP</span>
    </div>
    <?php
    $scores = array_column($HASIL,'total');
    $mean   = array_sum($scores)/count($scores);
    $variance = array_sum(array_map(fn($s)=>pow($s-$mean,2),$scores))/count($scores);
    $stddev = sqrt($variance);
    $stats = [
        ['Total Destinasi Dievaluasi','20','Jumlah destinasi dalam basis data','var(--text-primary)'],
        ['Skor MFEP Tertinggi',number_format(max($scores),4),$HASIL[0]['nama'],'var(--gold)'],
        ['Skor MFEP Terendah',number_format(min($scores),4),end($HASIL)['nama'],'var(--red)'],
        ['Skor MFEP Rata-Rata',number_format($mean,4),'Benchmark kualitas rata-rata','var(--blue)'],
        ['Std Deviasi Skor',number_format($stddev,4),'Tingkat dispersi antar destinasi','var(--purple)'],
        ['Jumlah Unggulan Heritage',count(array_filter($HASIL,fn($r)=>$r['kategori']==='Unggulan Heritage')),'Layak perlindungan budaya prioritas','var(--green)'],
    ];
    ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px;">
        <?php foreach ($stats as [$label,$val,$sub,$color]): ?>
        <div style="display:flex;align-items:center;gap:12px;padding:14px;background:var(--bg-elevated);border-radius:var(--radius-md);border:1px solid var(--border-subtle);">
            <div style="flex:1;">
                <div style="font-size:0.78rem;color:var(--text-muted);"><?= $label ?></div>
                <div class="mono" style="font-size:1.1rem;font-weight:700;color:<?= $color ?>;margin:4px 0;"><?= $val ?></div>
                <div style="font-size:0.74rem;color:var(--text-muted);"><?= $sub ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div style="margin-top:16px;">
        <div class="info-box">
            <i data-lucide="alert-triangle"></i>
            <span>Model ini berada pada TKT Level 2 (proof of concept). Hasil ranking bersifat indikatif dan harus divalidasi melalui FGD dengan pakar pariwisata, dinas terkait, dan komunitas kuliner Kota Surakarta sebelum dijadikan dasar kebijakan resmi.</span>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
