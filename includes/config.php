<?php
// ============================================================
// MFEP WISATA GASTRONOMI SURAKARTA - CONFIG & DATA
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'GastroSmart Surakarta');
define('APP_VERSION', '1.0.0');
define('APP_SUBTITLE', 'Smart Model Rekomendasi Wisata Gastronomi Berbasis MFEP');

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));
define('DB_NAME', getenv('DB_NAME') ?: 'gastro_smart');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

function getDbConnection(): ?mysqli
{
    static $conn = null;

    if ($conn instanceof mysqli) {
        return $conn;
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    $db = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($db->connect_errno) {
        return null;
    }

    $db->set_charset('utf8mb4');
    $conn = $db;

    return $conn;
}

function dbTableExists(mysqli $db, string $table): bool
{
    $table = $db->real_escape_string($table);
    $result = $db->query("SHOW TABLES LIKE '{$table}'");
    if (!$result) {
        return false;
    }

    $exists = $result->num_rows > 0;
    $result->free();

    return $exists;
}

function loadKriteriaFromDb(mysqli $db): array
{
    $sql = "SELECT kode, nama, icon, warna, bobot, definisi, ukur FROM kriteria ORDER BY kode ASC";
    $result = $db->query($sql);
    if (!$result) {
        return [];
    }

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[$row['kode']] = [
            'nama' => $row['nama'],
            'icon' => $row['icon'],
            'warna' => $row['warna'],
            'bobot' => (float)$row['bobot'],
            'definisi' => $row['definisi'],
            'ukur' => $row['ukur'],
        ];
    }
    $result->free();

    return $items;
}

function loadDestinasiFromDb(mysqli $db): array
{
    $sql = "SELECT kode, nama, jenis, deskripsi, lokasi, lat, lng, jam, K1, K2, K3, K4, K5, K6 FROM destinasi ORDER BY kode ASC";
    $result = $db->query($sql);
    if (!$result) {
        return [];
    }

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'kode' => $row['kode'],
            'nama' => $row['nama'],
            'jenis' => $row['jenis'],
            'deskripsi' => $row['deskripsi'],
            'lokasi' => $row['lokasi'],
            'koordinat' => [
                'lat' => (float)$row['lat'],
                'lng' => (float)$row['lng'],
            ],
            'jam' => $row['jam'],
            'K1' => (int)$row['K1'],
            'K2' => (int)$row['K2'],
            'K3' => (int)$row['K3'],
            'K4' => (int)$row['K4'],
            'K5' => (int)$row['K5'],
            'K6' => (int)$row['K6'],
        ];
    }
    $result->free();

    return $items;
}

// ── KRITERIA ─────────────────────────────────────────────────
$KRITERIA = [
    'K1' => [
        'nama'    => 'Keaslian Kuliner',
        'icon'    => 'utensils',
        'warna'   => '#F59E0B',
        'bobot'   => 0.25,
        'definisi' => 'Tingkat orisinalitas resep, bahan baku, dan cara penyajian kuliner tradisional',
        'ukur'    => 'Observasi + penilaian pakar kuliner (1=modern, 5=sangat autentik)',
    ],
    'K2' => [
        'nama'    => 'Nilai Budaya & Sejarah',
        'icon'    => 'landmark',
        'warna'   => '#8B5CF6',
        'bobot'   => 0.20,
        'definisi' => 'Kekuatan narasi sejarah, identitas budaya Jawa, dan warisan leluhur pada kuliner',
        'ukur'    => 'Studi dokumen + wawancara komunitas (1=rendah, 5=sangat tinggi)',
    ],
    'K3' => [
        'nama'    => 'Aksesibilitas Destinasi',
        'icon'    => 'map-pin',
        'warna'   => '#10B981',
        'bobot'   => 0.15,
        'definisi' => 'Kemudahan jangkauan lokasi: transportasi, jarak, parkir, jam operasional',
        'ukur'    => 'Data lapangan + rating aksesibilitas (1=sangat sulit, 5=sangat mudah)',
    ],
    'K4' => [
        'nama'    => 'Daya Tarik Wisata',
        'icon'    => 'star',
        'warna'   => '#EF4444',
        'bobot'   => 0.20,
        'definisi' => 'Pengalaman wisata: ambiance, variasi menu, presentasi, dan ulasan pengunjung',
        'ukur'    => 'Review platform digital + observasi (1=rendah, 5=sangat tinggi)',
    ],
    'K5' => [
        'nama'    => 'Dukungan Komunitas',
        'icon'    => 'users',
        'warna'   => '#3B82F6',
        'bobot'   => 0.10,
        'definisi' => 'Keterlibatan komunitas lokal, pelaku UMKM, dan upaya kolektif pelestarian',
        'ukur'    => 'FGD komunitas + kuesioner stakeholder (1=minim, 5=sangat aktif)',
    ],
    'K6' => [
        'nama'    => 'Potensi Ekonomi Lokal',
        'icon'    => 'trending-up',
        'warna'   => '#06B6D4',
        'bobot'   => 0.10,
        'definisi' => 'Kontribusi destinasi pada pendapatan masyarakat lokal dan ekosistem pariwisata',
        'ukur'    => 'Data ekonomi dinas + estimasi transaksi (1=rendah, 5=sangat besar)',
    ],
];

// ── DESTINASI ────────────────────────────────────────────────
$DESTINASI = [
    [
        'kode' => 'D01',
        'nama' => 'Timlo Sastro',
        'jenis' => 'Timlo / Soto Tradisional',
        'deskripsi' => 'Warung ikonik di Pasar Gede dengan resep timlo yang tidak berubah sejak 1952. Kuah bening khas dengan babat dan telur pindang.',
        'lokasi' => 'Pasar Gede, Jl. Urip Sumoharjo, Surakarta',
        'koordinat' => ['lat' => -7.5686, 'lng' => 110.8322],
        'jam' => '06.00 – 13.00 WIB',
        'K1' => 5,
        'K2' => 5,
        'K3' => 5,
        'K4' => 5,
        'K5' => 4,
        'K6' => 4
    ],

    [
        'kode' => 'D02',
        'nama' => 'Nasi Liwet Bu Wongso Lemu',
        'jenis' => 'Nasi Liwet Tradisional',
        'deskripsi' => 'Legenda nasi liwet Solo yang berdiri sejak tahun 1950-an. Nasi gurih dengan lauk ayam, areh, dan sambal goreng yang autentik.',
        'lokasi' => 'Jl. Teuku Umar, Keprabon, Surakarta',
        'koordinat' => ['lat' => -7.5621, 'lng' => 110.8271],
        'jam' => '06.00 – 14.00 WIB',
        'K1' => 5,
        'K2' => 5,
        'K3' => 4,
        'K4' => 5,
        'K5' => 4,
        'K6' => 4
    ],

    [
        'kode' => 'D03',
        'nama' => 'Selat Solo Mbak Lies',
        'jenis' => 'Selat Solo (Bistik Jawa)',
        'deskripsi' => 'Hidangan fusion Belanda-Jawa warisan zaman kolonial. Daging sapi dengan mayones dan acar sayuran yang menjadi kekhasan Solo.',
        'lokasi' => 'Jl. Brigjen Katamso, Surakarta',
        'koordinat' => ['lat' => -7.5662, 'lng' => 110.8301],
        'jam' => '09.00 – 17.00 WIB',
        'K1' => 5,
        'K2' => 5,
        'K3' => 4,
        'K4' => 4,
        'K5' => 4,
        'K6' => 4
    ],

    [
        'kode' => 'D04',
        'nama' => 'Soto Gading',
        'jenis' => 'Soto Ayam Tradisional',
        'deskripsi' => 'Soto ayam bening khas Solo dengan bihun dan taburan seledri. Telah melayani pelanggan setia selama lebih dari empat dekade.',
        'lokasi' => 'Jl. Brigjen Sudiarto, Surakarta',
        'koordinat' => ['lat' => -7.5701, 'lng' => 110.8289],
        'jam' => '07.00 – 14.00 WIB',
        'K1' => 4,
        'K2' => 4,
        'K3' => 5,
        'K4' => 4,
        'K5' => 3,
        'K6' => 4
    ],

    [
        'kode' => 'D05',
        'nama' => 'Wedangan Jadul Pak Gareng',
        'jenis' => 'Wedangan / Angkringan',
        'deskripsi' => 'Angkringan bergaya retro dengan menu wedang jahe, kopi joss, dan berbagai lauk murah. Pusat interaksi sosial masyarakat Solo.',
        'lokasi' => 'Jl. Slamet Riyadi, Surakarta',
        'koordinat' => ['lat' => -7.5695, 'lng' => 110.8180],
        'jam' => '16.00 – 24.00 WIB',
        'K1' => 4,
        'K2' => 4,
        'K3' => 4,
        'K4' => 4,
        'K5' => 5,
        'K6' => 3
    ],

    [
        'kode' => 'D06',
        'nama' => 'Pasar Gede – Zona Kuliner',
        'jenis' => 'Kuliner Pasar Tradisional',
        'deskripsi' => 'Jantung kuliner Surakarta di pasar bersejarah era kolonial. Ratusan lapak kuliner tradisional dari timlo, intip, hingga dawet.',
        'lokasi' => 'Jl. Jendral Urip Sumoharjo, Surakarta',
        'koordinat' => ['lat' => -7.5683, 'lng' => 110.8326],
        'jam' => '05.00 – 17.00 WIB',
        'K1' => 4,
        'K2' => 5,
        'K3' => 5,
        'K4' => 4,
        'K5' => 5,
        'K6' => 5
    ],

    [
        'kode' => 'D07',
        'nama' => 'Nasi Gudeg Bu Semi',
        'jenis' => 'Gudeg Surakarta',
        'deskripsi' => 'Gudeg Solo versi kering dengan cita rasa lebih gurih dan manis dibanding gudeg Yogya. Sajian sarapan pagi yang ikonik.',
        'lokasi' => 'Jl. Monginsidi, Surakarta',
        'koordinat' => ['lat' => -7.5720, 'lng' => 110.8255],
        'jam' => '05.30 – 10.00 WIB',
        'K1' => 4,
        'K2' => 4,
        'K3' => 4,
        'K4' => 4,
        'K5' => 3,
        'K6' => 3
    ],

    [
        'kode' => 'D08',
        'nama' => 'Dawet Telasih Bu Dermi',
        'jenis' => 'Minuman Tradisional Dawet',
        'deskripsi' => 'Dawet legendaris di Pasar Gede dengan cendol dari tepung beras asli dan santan segar. Resep turun-temurun tiga generasi.',
        'lokasi' => 'Pasar Gede, Surakarta',
        'koordinat' => ['lat' => -7.5685, 'lng' => 110.8328],
        'jam' => '06.00 – 13.00 WIB',
        'K1' => 5,
        'K2' => 5,
        'K3' => 4,
        'K4' => 4,
        'K5' => 5,
        'K6' => 3
    ],

    [
        'kode' => 'D09',
        'nama' => 'Intip Goreng Pak Slamet',
        'jenis' => 'Camilan Tradisional Intip',
        'deskripsi' => 'Kerak nasi yang digoreng garing, camilan khas Solo yang semakin langka. Dibuat secara tradisional tanpa pengawet.',
        'lokasi' => 'Pasar Klewer, Surakarta',
        'koordinat' => ['lat' => -7.5745, 'lng' => 110.8262],
        'jam' => '08.00 – 16.00 WIB',
        'K1' => 5,
        'K2' => 4,
        'K3' => 3,
        'K4' => 3,
        'K5' => 4,
        'K6' => 3
    ],

    [
        'kode' => 'D10',
        'nama' => 'Sate Buntel Mbok Galak',
        'jenis' => 'Sate Kambing Khas Solo',
        'deskripsi' => 'Sate daging kambing cincang dibungkus lemak dengan bumbu kacang khas Solo. Salah satu kuliner heritage yang harus dicoba.',
        'lokasi' => 'Jl. Sutan Syahrir, Surakarta',
        'koordinat' => ['lat' => -7.5680, 'lng' => 110.8350],
        'jam' => '10.00 – 21.00 WIB',
        'K1' => 4,
        'K2' => 4,
        'K3' => 5,
        'K4' => 4,
        'K5' => 3,
        'K6' => 4
    ],

    [
        'kode' => 'D11',
        'nama' => 'Tengkleng Bu Edi',
        'jenis' => 'Tengkleng Kambing Tradisional',
        'deskripsi' => 'Sup tulang kambing dengan kuah bening kaya rempah. Resep 1960-an yang masih dipertahankan keasliannya hingga kini.',
        'lokasi' => 'Pasar Klewer, Surakarta',
        'koordinat' => ['lat' => -7.5743, 'lng' => 110.8258],
        'jam' => '11.00 – 16.00 WIB (habis dalam 2-3 jam)',
        'K1' => 5,
        'K2' => 5,
        'K3' => 4,
        'K4' => 5,
        'K5' => 4,
        'K6' => 4
    ],

    [
        'kode' => 'D12',
        'nama' => 'Cabuk Rambak Pak Birin',
        'jenis' => 'Cabuk Rambak Tradisional',
        'deskripsi' => 'Ketupat siraman bumbu wijen dengan kerupuk karak. Kuliner sarapan langka yang hampir punah di generasi muda.',
        'lokasi' => 'Jl. Kyai Mojo, Surakarta',
        'koordinat' => ['lat' => -7.5630, 'lng' => 110.8300],
        'jam' => '06.00 – 10.00 WIB',
        'K1' => 5,
        'K2' => 5,
        'K3' => 3,
        'K4' => 3,
        'K5' => 4,
        'K6' => 3
    ],

    [
        'kode' => 'D13',
        'nama' => 'Roti Mandarin Orion',
        'jenis' => 'Kue/Roti Tradisional Peranakan',
        'deskripsi' => 'Toko kue legendaris sejak 1945 dengan produk berbasis resep Tionghoa-Jawa. Menjadi ikon kuliner peranakan Solo.',
        'lokasi' => 'Jl. Urip Sumoharjo, Surakarta',
        'koordinat' => ['lat' => -7.5672, 'lng' => 110.8290],
        'jam' => '08.00 – 20.00 WIB',
        'K1' => 3,
        'K2' => 4,
        'K3' => 5,
        'K4' => 4,
        'K5' => 3,
        'K6' => 4
    ],

    [
        'kode' => 'D14',
        'nama' => 'Wedang Ronde Pak Min',
        'jenis' => 'Minuman Hangat Tradisional',
        'deskripsi' => 'Minuman hangat dengan bola-bola ketan berisi kacang dan jahe segar. Cocok dinikmati di malam hari.',
        'lokasi' => 'Jl. Diponegoro, Surakarta',
        'koordinat' => ['lat' => -7.5650, 'lng' => 110.8240],
        'jam' => '17.00 – 22.00 WIB',
        'K1' => 4,
        'K2' => 4,
        'K3' => 4,
        'K4' => 4,
        'K5' => 4,
        'K6' => 3
    ],

    [
        'kode' => 'D15',
        'nama' => 'Jenang Mirah',
        'jenis' => 'Jenang / Bubur Tradisional',
        'deskripsi' => 'Produsen jenang tradisional dengan berbagai varian seperti jenang sumsum, beras, dan ketan hitam yang dibuat tanpa pengawet.',
        'lokasi' => 'Jl. Honggowongso, Surakarta',
        'koordinat' => ['lat' => -7.5780, 'lng' => 110.8200],
        'jam' => '07.00 – 18.00 WIB',
        'K1' => 5,
        'K2' => 5,
        'K3' => 3,
        'K4' => 3,
        'K5' => 5,
        'K6' => 3
    ],

    [
        'kode' => 'D16',
        'nama' => 'Gempol Pleret Bu Sari',
        'jenis' => 'Minuman Tradisional Khas Solo',
        'deskripsi' => 'Minuman bola-bola tepung beras dalam santan manis, salah satu kuliner tradisional Solo yang paling autentik.',
        'lokasi' => 'Pasar Gede, Surakarta',
        'koordinat' => ['lat' => -7.5684, 'lng' => 110.8327],
        'jam' => '06.00 – 12.00 WIB',
        'K1' => 5,
        'K2' => 5,
        'K3' => 3,
        'K4' => 4,
        'K5' => 4,
        'K6' => 3
    ],

    [
        'kode' => 'D17',
        'nama' => 'Sego Pecel Bu Tun',
        'jenis' => 'Nasi Pecel Tradisional',
        'deskripsi' => 'Nasi pecel dengan sambal kacang halus dan beragam sayuran rebus. Sarapan rakyat Solo yang penuh nilai tradisi.',
        'lokasi' => 'Jl. Kalilarangan, Surakarta',
        'koordinat' => ['lat' => -7.5760, 'lng' => 110.8220],
        'jam' => '06.00 – 11.00 WIB',
        'K1' => 4,
        'K2' => 4,
        'K3' => 4,
        'K4' => 4,
        'K5' => 3,
        'K6' => 3
    ],

    [
        'kode' => 'D18',
        'nama' => 'Garang Asem Bu Sulastri',
        'jenis' => 'Garang Asem Ayam Tradisional',
        'deskripsi' => 'Ayam masak kuah asam-pedas dengan belimbing wuluh, dimasak dalam daun pisang. Kuliner khas Solo yang semakin langka.',
        'lokasi' => 'Jl. Sutan Syahrir, Surakarta',
        'koordinat' => ['lat' => -7.5682, 'lng' => 110.8345],
        'jam' => '10.00 – 16.00 WIB',
        'K1' => 5,
        'K2' => 5,
        'K3' => 3,
        'K4' => 4,
        'K5' => 4,
        'K6' => 3
    ],

    [
        'kode' => 'D19',
        'nama' => 'Bestik Jawa Pak Maryanto',
        'jenis' => 'Bestik Jawa (Bistik Tradisional)',
        'deskripsi' => 'Versi lokal bistik Belanda dengan daging sapi dan saus coklat khas Jawa. Warisan kuliner zaman kolonial yang tetap autentik.',
        'lokasi' => 'Jl. Pasar Kliwon, Surakarta',
        'koordinat' => ['lat' => -7.5780, 'lng' => 110.8330],
        'jam' => '10.00 – 20.00 WIB',
        'K1' => 5,
        'K2' => 5,
        'K3' => 4,
        'K4' => 4,
        'K5' => 4,
        'K6' => 4
    ],

    [
        'kode' => 'D20',
        'nama' => 'Pasar Triwindu – Kuliner Heritage',
        'jenis' => 'Kuliner Heritage & Antik',
        'deskripsi' => 'Kawasan pasar antik dengan spot kuliner tradisional yang terintegrasi dengan wisata budaya. Pengalaman gastronomi unik dan autentik.',
        'lokasi' => 'Jl. Diponegoro, Surakarta',
        'koordinat' => ['lat' => -7.5642, 'lng' => 110.8253],
        'jam' => '08.00 – 17.00 WIB',
        'K1' => 4,
        'K2' => 5,
        'K3' => 4,
        'K4' => 5,
        'K5' => 5,
        'K6' => 5
    ],
];

// ── MFEP CALCULATION ENGINE ──────────────────────────────────
function hitungMFEP(array $destinasi, array $kriteria): array
{
    // Step 1: Temukan MAX per kriteria
    $maxPerK = [];
    foreach (array_keys($kriteria) as $k) {
        $vals = array_column($destinasi, $k);
        $maxPerK[$k] = max($vals);
    }

    // Step 2: Normalisasi & Weighted Score
    $results = [];
    foreach ($destinasi as $d) {
        $norm = [];
        $ws   = [];
        $total = 0;
        foreach ($kriteria as $k => $info) {
            $norm[$k] = ($maxPerK[$k] > 0) ? $d[$k] / $maxPerK[$k] : 0;
            $ws[$k]   = $norm[$k] * $info['bobot'];
            $total   += $ws[$k];
        }
        $results[] = array_merge($d, [
            'norm'  => $norm,
            'ws'    => $ws,
            'total' => round($total, 4),
        ]);
    }

    // Step 3: Ranking (descending)
    usort($results, fn($a, $b) => $b['total'] <=> $a['total']);
    foreach ($results as $i => &$r) {
        $r['ranking'] = $i + 1;
        // Kategori
        if ($r['total'] >= 0.85)      $r['kategori'] = 'Unggulan Heritage';
        elseif ($r['total'] >= 0.75)  $r['kategori'] = 'Prioritas A';
        elseif ($r['total'] >= 0.65)  $r['kategori'] = 'Prioritas B';
        elseif ($r['total'] >= 0.55)  $r['kategori'] = 'Prioritas C';
        else                           $r['kategori'] = 'Perlu Pembinaan';
    }
    return $results;
}

function getKategoriStyle(string $cat): array
{
    return match ($cat) {
        'Unggulan Heritage' => ['bg' => '#065F46', 'border' => '#10B981', 'badge' => '#10B981', 'text' => '#ECFDF5'],
        'Prioritas A'       => ['bg' => '#1E3A5F', 'border' => '#3B82F6', 'badge' => '#3B82F6', 'text' => '#EFF6FF'],
        'Prioritas B'       => ['bg' => '#713F12', 'border' => '#F59E0B', 'badge' => '#F59E0B', 'text' => '#FFFBEB'],
        'Prioritas C'       => ['bg' => '#7C2D12', 'border' => '#F97316', 'badge' => '#F97316', 'text' => '#FFF7ED'],
        default             => ['bg' => '#3F3F46', 'border' => '#71717A', 'badge' => '#71717A', 'text' => '#F4F4F5'],
    };
}

$DB_READY = false;
$dbConnection = getDbConnection();
if (
    $dbConnection instanceof mysqli
    && dbTableExists($dbConnection, 'kriteria')
    && dbTableExists($dbConnection, 'destinasi')
) {
    $dbKriteria = loadKriteriaFromDb($dbConnection);
    $dbDestinasi = loadDestinasiFromDb($dbConnection);
    if (!empty($dbKriteria) && !empty($dbDestinasi)) {
        $KRITERIA = $dbKriteria;
        $DESTINASI = $dbDestinasi;
        $DB_READY = true;
    }
}

// Global computed results
$HASIL = hitungMFEP($DESTINASI, $KRITERIA);
$MAX_SCORE = max(array_column($HASIL, 'total'));
$MIN_SCORE = min(array_column($HASIL, 'total'));
$AVG_SCORE = round(array_sum(array_column($HASIL, 'total')) / count($HASIL), 4);
