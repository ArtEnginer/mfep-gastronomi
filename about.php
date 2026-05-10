<?php
require_once 'includes/config.php';
$pageTitle = 'Tentang Model';
$activePage = 'about';
?>
<?php include 'includes/header.php'; ?>

<div style="margin-bottom:24px;" class="animate-in">
    <div class="hero-eyebrow">Dokumentasi · TKT Level 2</div>
    <h1 class="section-title" style="font-family:var(--font-display);font-size:1.6rem;">Tentang Model GastroSmart</h1>
    <p class="section-subtitle">Dokumentasi sistem pendukung keputusan berbasis MFEP untuk wisata gastronomi Surakarta</p>
</div>

<div class="grid-2" style="gap:20px;margin-bottom:20px;">
    <!-- Info Model -->
    <div class="card animate-in delay-1">
        <div class="card-header">
            <span class="card-title">🎯 Identitas Model</span>
        </div>
        <?php
        $infos = [
            ['Nama Sistem','GastroSmart Surakarta','layout-dashboard'],
            ['Metode DSS','Multi Factor Evaluation Process (MFEP)','sliders'],
            ['TKT (Tingkat Kesiapan Teknologi)','Level 2 – Proof of Concept','shield'],
            ['Program Penelitian','Penelitian Dosen Pemula (PDP) 2026','book-open'],
            ['Objek Penelitian','20 Destinasi Wisata Gastronomi Tradisional Kota Surakarta','map-pin'],
            ['Jumlah Kriteria','6 Kriteria Terbobot (Σ Bobot = 1.00)','check-square'],
            ['Skala Penilaian','Likert 1–5 (Sangat Rendah – Sangat Tinggi)','bar-chart'],
            ['Validasi Data','Observasi lapangan + penilaian pakar + FGD stakeholder','users'],
        ];
        foreach ($infos as [$label,$val,$icon]): ?>
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border-subtle);">
            <i data-lucide="<?= $icon ?>" style="width:16px;height:16px;color:var(--gold);flex-shrink:0;"></i>
            <div>
                <div style="font-size:0.75rem;color:var(--text-muted);"><?= $label ?></div>
                <div style="font-size:0.88rem;font-weight:600;margin-top:2px;"><?= $val ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- TKT Progress -->
    <div class="card animate-in delay-2">
        <div class="card-header">
            <span class="card-title">🚀 Roadmap Tingkat Kesiapan Teknologi</span>
        </div>
        <?php
        $tkts = [
            [1,'Observasi Prinsip Dasar','Identifikasi masalah rekomendasi wisata gastronomi','done'],
            [2,'Formulasi Konsep Teknologi','Pengembangan model MFEP & proof of concept (SAAT INI)','current'],
            [3,'Pembuktian Konsep Analitik','Validasi dengan data lapangan & uji pakar','next'],
            [4,'Validasi Lab','Pengujian sistem dengan stakeholder terbatas','future'],
            [5,'Validasi Lingkungan Relevan','Uji coba di Dinas Pariwisata Surakarta','future'],
            [6,'Demo Prototipe','Demo sistem untuk kebijakan pelestarian kuliner','future'],
            [7,'Demo Sistem Penuh','Integrasi database destinasi & peta digital','future'],
            [8,'Sistem Tervalidasi','Penggunaan oleh pemerintah kota','future'],
            [9,'Implementasi Penuh','Platform wisata gastronomi Surakarta aktif','future'],
        ];
        foreach ($tkts as [$num,$title,$desc,$status]):
            $col = match($status){'done'=>'#10B981','current'=>'#F59E0B','next'=>'#3B82F6',default=>'#3F3F46'};
            $bg  = match($status){'done'=>'#065F46','current'=>'#713F12','next'=>'#1E3A5F',default=>'var(--bg-overlay)'};
        ?>
        <div style="display:flex;gap:12px;align-items:flex-start;padding:8px 0;border-bottom:1px solid var(--border-subtle);">
            <div style="width:28px;height:28px;border-radius:50%;background:<?= $bg ?>;border:2px solid <?= $col ?>;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:<?= $col ?>;flex-shrink:0;"><?= $num ?></div>
            <div style="flex:1;">
                <div style="font-weight:600;font-size:0.82rem;color:<?= $col ?>;">TKT <?= $num ?>: <?= $title ?></div>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;"><?= $desc ?></div>
            </div>
            <?php if ($status === 'current'): ?>
            <span style="font-size:0.65rem;padding:2px 8px;background:rgba(245,158,11,0.2);border:1px solid #F59E0B;border-radius:999px;color:#F59E0B;flex-shrink:0;">AKTIF</span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- MFEP Explanation -->
<div class="card animate-in delay-2" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">📖 Tentang Metode MFEP</span>
    </div>
    <div class="grid-2" style="gap:24px;">
        <div>
            <h3 style="font-family:var(--font-display);font-size:1rem;margin-bottom:10px;color:var(--gold);">Multi Factor Evaluation Process</h3>
            <p style="font-size:0.85rem;color:var(--text-muted);line-height:1.8;margin-bottom:14px;">
                MFEP adalah metode pengambilan keputusan multi-kriteria yang mengevaluasi alternatif
                berdasarkan beberapa faktor terbobot. Metode ini cocok untuk pengambilan keputusan
                yang melibatkan banyak kriteria kualitatif dengan bobot berbeda dari penilaian pakar.
            </p>
            <p style="font-size:0.85rem;color:var(--text-muted);line-height:1.8;">
                Dalam penelitian ini, MFEP digunakan untuk meranking 20 destinasi gastronomi
                berdasarkan 6 kriteria yang mencerminkan keaslian, nilai budaya, aksesibilitas,
                daya tarik, dukungan komunitas, dan potensi ekonomi.
            </p>
        </div>
        <div>
            <h3 style="font-family:var(--font-display);font-size:1rem;margin-bottom:10px;color:var(--gold);">Keunggulan Metode MFEP</h3>
            <?php
            $advantages = [
                ['✅','Transparan','Semua langkah perhitungan dapat diaudit dan diverifikasi'],
                ['✅','Fleksibel','Bobot dapat disesuaikan dengan hasil kuesioner pakar yang berbeda'],
                ['✅','Terukur','Menggunakan skala Likert yang terdefinisi dengan jelas'],
                ['✅','Robust','Analisis sensitivitas menunjukkan model stabil terhadap perubahan bobot ±20%'],
                ['✅','Implementatif','Mudah diterapkan oleh dinas tanpa keahlian teknis tinggi'],
            ];
            foreach ($advantages as [$icon,$title,$desc]): ?>
            <div style="display:flex;gap:10px;padding:8px 0;">
                <span style="font-size:0.9rem;flex-shrink:0;"><?= $icon ?></span>
                <div>
                    <span style="font-weight:600;font-size:0.85rem;"><?= $title ?></span>
                    <span style="font-size:0.82rem;color:var(--text-muted);margin-left:6px;"><?= $desc ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Alur Kerja Penelitian -->
<div class="card animate-in delay-3" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title">🔬 Alur Kerja Penelitian</span>
    </div>
    <div style="display:flex;gap:0;align-items:center;flex-wrap:wrap;justify-content:center;padding:10px 0;">
        <?php
        $steps = [
            ['Identifikasi Masalah','Pemetaan kebutuhan rekomendasi gastronomi','map'],
            ['Studi Pustaka','Review metode MFEP & wisata gastronomi','book-open'],
            ['Penetapan Kriteria','6 kriteria + bobot dari kuesioner pakar','sliders'],
            ['Pengumpulan Data','Observasi + wawancara + penilaian lapangan','database'],
            ['Perhitungan MFEP','Normalisasi → WS → Total Skor → Ranking','calculator'],
            ['Analisis Sensitivitas','Uji robustness ±20% bobot','activity'],
            ['Rekomendasi Kebijakan','Top-5 prioritas pelestarian kuliner','award'],
        ];
        foreach ($steps as $idx => [$title,$sub,$icon]): ?>
        <div style="text-align:center;flex:0 0 auto;width:110px;">
            <div style="width:50px;height:50px;border-radius:50%;background:<?= $idx===4?'var(--gold-subtle)':'var(--bg-elevated)' ?>;border:2px solid <?= $idx===4?'var(--gold)':'var(--border-medium)' ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;color:<?= $idx===4?'var(--gold)':'var(--text-muted)' ?>;">
                <i data-lucide="<?= $icon ?>" style="width:20px;height:20px;"></i>
            </div>
            <div style="font-size:0.75rem;font-weight:600;line-height:1.4;"><?= $title ?></div>
            <div style="font-size:0.65rem;color:var(--text-muted);margin-top:3px;line-height:1.4;"><?= $sub ?></div>
        </div>
        <?php if ($idx < count($steps)-1): ?>
        <div style="flex:1;min-width:12px;border-top:2px dashed var(--border-subtle);margin-bottom:40px;"></div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<!-- Referensi -->
<div class="card animate-in delay-4">
    <div class="card-header">
        <span class="card-title">📚 Referensi & Sumber Data</span>
    </div>
    <div class="grid-2" style="gap:20px;">
        <div>
            <div style="font-weight:600;font-size:0.85rem;margin-bottom:12px;color:var(--gold);">Metode MFEP</div>
            <?php
            $refs = [
                'Koo, T. K., & Li, M. Y. (2016). Systematic Review of Multi-Criteria Decision Analysis Methods. Journal of Chiropractic Medicine.',
                'Malczewski, J. (1999). GIS and Multicriteria Decision Analysis. John Wiley & Sons.',
                'Triantaphyllou, E. (2000). Multi-Criteria Decision Making Methods: A Comparative Study. Springer.',
                'Turban, E., et al. (2011). Decision Support and Business Intelligence Systems. Prentice Hall.',
            ];
            foreach ($refs as $ref): ?>
            <div style="font-size:0.78rem;color:var(--text-muted);padding:6px 0;border-bottom:1px solid var(--border-subtle);line-height:1.6;"><?= $ref ?></div>
            <?php endforeach; ?>
        </div>
        <div>
            <div style="font-weight:600;font-size:0.85rem;margin-bottom:12px;color:var(--gold);">Sumber Data Penelitian</div>
            <?php
            $sources = [
                'Observasi lapangan langsung ke 20 destinasi gastronomi Kota Surakarta (2025-2026)',
                'Kuesioner bobot pakar: pakar kuliner tradisional Jawa, akademisi pariwisata, praktisi UMKM kuliner',
                'Data Dinas Pariwisata Kota Surakarta – Peta Destinasi Wisata Kuliner 2025',
                'Wawancara mendalam dengan pemilik destinasi dan komunitas kuliner lokal',
                'Review platform digital (Google Maps, TripAdvisor, media sosial) untuk penilaian K4',
            ];
            foreach ($sources as $src): ?>
            <div style="display:flex;gap:8px;font-size:0.78rem;color:var(--text-muted);padding:6px 0;border-bottom:1px solid var(--border-subtle);line-height:1.6;">
                <i data-lucide="circle-dot" style="width:12px;height:12px;flex-shrink:0;margin-top:3px;color:var(--gold);"></i>
                <?= $src ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div style="margin-top:16px;">
        <div class="info-box">
            <i data-lucide="alert-circle"></i>
            <span>
                Sistem ini merupakan <strong>Proof of Concept (TKT Level 2)</strong> untuk keperluan penelitian akademis.
                Hasil rekomendasi bersifat indikatif dan perlu divalidasi lebih lanjut sebelum digunakan sebagai dasar kebijakan resmi.
                Kontak: Tim Peneliti PDP 2026 – Universitas/Politeknik Surakarta.
            </span>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
