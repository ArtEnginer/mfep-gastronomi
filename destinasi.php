<?php
require_once 'includes/config.php';
$pageTitle = 'Data Destinasi';
$activePage = 'destinasi';

// Get selected destinasi for modal
$selectedKode = $_GET['kode'] ?? null;
$selectedDest = null;
if ($selectedKode) {
    foreach ($DESTINASI as $d) {
        if ($d['kode'] === $selectedKode) {
            $selectedDest = $d;
            break;
        }
    }
    // Find in HASIL for MFEP scores
    foreach ($HASIL as $h) {
        if ($h['kode'] === $selectedKode) {
            $selectedHasil = $h;
            break;
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div style="margin-bottom:24px;" class="animate-in">
    <div class="hero-eyebrow">Data Primer · 20 Destinasi</div>
    <h1 class="section-title" style="font-family:var(--font-display);font-size:1.6rem;">Data Destinasi Gastronomi</h1>
    <p class="section-subtitle">20 destinasi wisata kuliner tradisional Kota Surakarta dengan nilai per kriteria (skala Likert 1–5)</p>
</div>

<!-- Filter / Search Bar -->
<div class="card animate-in delay-1" style="margin-bottom:20px;padding:16px 20px;">
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
        <div style="flex:1;min-width:200px;position:relative;">
            <i data-lucide="search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--text-muted);"></i>
            <input type="text" id="searchInput" placeholder="Cari destinasi..." style="width:100%;background:var(--bg-elevated);border:1px solid var(--border-subtle);border-radius:var(--radius-sm);padding:8px 12px 8px 34px;color:var(--text-primary);font-family:var(--font-body);font-size:0.85rem;outline:none;" oninput="filterDest()">
        </div>
        <select id="filterKat" onchange="filterDest()" style="background:var(--bg-elevated);border:1px solid var(--border-subtle);border-radius:var(--radius-sm);padding:8px 12px;color:var(--text-primary);font-family:var(--font-body);font-size:0.85rem;outline:none;">
            <option value="">Semua Kategori</option>
            <option value="Unggulan Heritage">Unggulan Heritage</option>
            <option value="Prioritas A">Prioritas A</option>
            <option value="Prioritas B">Prioritas B</option>
            <option value="Prioritas C">Prioritas C</option>
            <option value="Perlu Pembinaan">Perlu Pembinaan</option>
        </select>
        <div style="font-size:0.8rem;color:var(--text-muted);" id="countLabel">Menampilkan <strong id="countNum" style="color:var(--gold);"><?= count($DESTINASI) ?></strong> destinasi</div>
    </div>
</div>

<!-- Destinasi Grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;margin-bottom:24px;" id="destGrid">
    <?php
    // Build a lookup from HASIL for each destinasi
    $hasilMap = [];
    foreach ($HASIL as $h) $hasilMap[$h['kode']] = $h;

    foreach ($DESTINASI as $d):
        $h = $hasilMap[$d['kode']];
        $kat = getKategoriStyle($h['kategori']);
        $pct = round(($h['total'] / 1.0) * 100, 1); // max is 1.0
    ?>
        <div class="dest-card animate-in"
            data-nama="<?= strtolower(htmlspecialchars($d['nama'])) ?>"
            data-jenis="<?= strtolower(htmlspecialchars($d['jenis'])) ?>"
            data-kat="<?= htmlspecialchars($h['kategori']) ?>"
            style="--card-color:<?= $kat['badge'] ?>;"
            onclick="openDestModal('<?= $d['kode'] ?>')">

            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:12px;">
                <div>
                    <span class="mono" style="font-size:0.7rem;color:var(--text-muted);"><?= $d['kode'] ?></span>
                    <div style="font-weight:700;font-size:0.95rem;margin-top:2px;"><?= htmlspecialchars($d['nama']) ?></div>
                    <div style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;"><?= htmlspecialchars($d['jenis']) ?></div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div class="mono" style="font-size:1.1rem;font-weight:700;color:<?= $kat['badge'] ?>;"><?= number_format($h['total'], 4) ?></div>
                    <div style="font-size:0.68rem;padding:2px 8px;background:<?= $kat['bg'] ?>;border:1px solid <?= $kat['border'] ?>;border-radius:999px;color:<?= $kat['text'] ?>;margin-top:4px;display:inline-block;"><?= $h['kategori'] ?></div>
                </div>
            </div>

            <!-- Nilai per kriteria mini-bars -->
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:12px;">
                <?php foreach ($KRITERIA as $kode => $k): ?>
                    <div style="text-align:center;">
                        <div style="font-size:0.65rem;color:var(--text-muted);margin-bottom:3px;"><?= $kode ?></div>
                        <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <div style="width:6px;height:<?= 4 + $i * 2 ?>px;border-radius:2px;background:<?= $i <= $d[$kode] ? $k['warna'] : 'var(--bg-overlay)' ?>;"></div>
                            <?php endfor; ?>
                            <span class="mono" style="font-size:0.7rem;color:<?= $k['warna'] ?>;"><?= $d[$kode] ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="display:flex;align-items:center;gap:8px;">
                <div style="flex:1;height:4px;background:var(--bg-overlay);border-radius:2px;overflow:hidden;">
                    <div style="width:<?= $pct ?>%;height:100%;background:<?= $kat['badge'] ?>;border-radius:2px;transition:width 0.8s ease;"></div>
                </div>
                <span style="font-size:0.7rem;color:var(--text-muted);">Rank #<?= $h['ranking'] ?></span>
            </div>

            <div style="display:flex;align-items:center;gap:6px;margin-top:10px;font-size:0.75rem;color:var(--text-muted);">
                <i data-lucide="map-pin" style="width:12px;height:12px;"></i>
                <span><?= htmlspecialchars(explode(',', $d['lokasi'])[0]) ?></span>
                <span style="margin-left:auto;">
                    <i data-lucide="clock" style="width:12px;height:12px;display:inline;vertical-align:middle;"></i>
                    <?= $d['jam'] ?>
                </span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Tabel Data Lengkap -->
<div class="card animate-in delay-3">
    <div class="card-header">
        <span class="card-title">📊 Tabel Data Nilai per Kriteria</span>
        <div style="font-size:0.78rem;color:var(--text-muted);">MAX per kriteria = 5</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Destinasi</th>
                    <th style="color:#F59E0B;">K1<br><span style="font-weight:400;font-size:0.7rem;">Keaslian</span></th>
                    <th style="color:#8B5CF6;">K2<br><span style="font-weight:400;font-size:0.7rem;">Budaya</span></th>
                    <th style="color:#10B981;">K3<br><span style="font-weight:400;font-size:0.7rem;">Aksesib.</span></th>
                    <th style="color:#EF4444;">K4<br><span style="font-weight:400;font-size:0.7rem;">Daya Tarik</span></th>
                    <th style="color:#3B82F6;">K5<br><span style="font-weight:400;font-size:0.7rem;">Komunitas</span></th>
                    <th style="color:#06B6D4;">K6<br><span style="font-weight:400;font-size:0.7rem;">Ekonomi</span></th>
                    <th>Skor MFEP</th>
                    <th>Rank</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $colors = ['K1' => '#F59E0B', 'K2' => '#8B5CF6', 'K3' => '#10B981', 'K4' => '#EF4444', 'K5' => '#3B82F6', 'K6' => '#06B6D4'];
                foreach ($DESTINASI as $idx => $d):
                    $h = $hasilMap[$d['kode']];
                    $kat = getKategoriStyle($h['kategori']);
                ?>
                    <tr style="cursor:pointer;" onclick="openDestModal('<?= $d['kode'] ?>')">
                        <td><span class="mono" style="color:var(--text-muted);"><?= $d['kode'] ?></span></td>
                        <td style="font-weight:500;"><?= htmlspecialchars($d['nama']) ?></td>
                        <?php foreach (['K1', 'K2', 'K3', 'K4', 'K5', 'K6'] as $k): ?>
                            <td>
                                <div style="display:flex;align-items:center;justify-content:center;gap:2px;">
                                    <span class="mono" style="color:<?= $colors[$k] ?>;font-weight:700;"><?= $d[$k] ?></span>
                                </div>
                            </td>
                        <?php endforeach; ?>
                        <td><span class="mono" style="color:var(--gold);font-weight:600;"><?= number_format($h['total'], 4) ?></span></td>
                        <td>
                            <div style="width:28px;height:28px;border-radius:50%;background:<?= $kat['bg'] ?>;border:1px solid <?= $kat['border'] ?>;display:flex;align-items:center;justify-content:center;font-size:0.78rem;font-weight:700;color:<?= $kat['badge'] ?>;"><?= $h['ranking'] ?></div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <!-- MAX row -->
                <tr style="background:var(--bg-overlay);font-weight:700;">
                    <td colspan="2" style="text-align:right;color:var(--text-muted);font-size:0.8rem;">MAX per Kriteria →</td>
                    <?php foreach (['K1', 'K2', 'K3', 'K4', 'K5', 'K6'] as $k): ?>
                        <td><span class="mono" style="color:var(--green);">5</span></td>
                    <?php endforeach; ?>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail Destinasi -->
<div class="modal-backdrop" id="destModal" onclick="if(event.target===this)closeModal('destModal')">
    <div class="modal-box" style="max-width:720px;" id="destModalContent">
        <button class="modal-close" onclick="closeModal('destModal')"><i data-lucide="x"></i></button>
        <div id="destModalBody"><!-- filled by JS --></div>
    </div>
</div>

<script>
    const DESTINASI = <?= json_encode($DESTINASI) ?>;
    const HASIL = <?= json_encode($HASIL) ?>;
    const KRITERIA = <?= json_encode($KRITERIA) ?>;
    const hasilMap = {};
    HASIL.forEach(h => hasilMap[h.kode] = h);

    function openDestModal(kode) {
        const d = DESTINASI.find(x => x.kode === kode);
        const h = hasilMap[kode];
        if (!d || !h) return;

        const katColors = {
            'Unggulan Heritage': {
                bg: '#065F46',
                border: '#10B981',
                badge: '#10B981',
                text: '#ECFDF5'
            },
            'Prioritas A': {
                bg: '#1E3A5F',
                border: '#3B82F6',
                badge: '#3B82F6',
                text: '#EFF6FF'
            },
            'Prioritas B': {
                bg: '#713F12',
                border: '#F59E0B',
                badge: '#F59E0B',
                text: '#FFFBEB'
            },
            'Prioritas C': {
                bg: '#7C2D12',
                border: '#F97316',
                badge: '#F97316',
                text: '#FFF7ED'
            },
            'Perlu Pembinaan': {
                bg: '#3F3F46',
                border: '#71717A',
                badge: '#71717A',
                text: '#F4F4F5'
            },
        };
        const kat = katColors[h.kategori] || katColors['Perlu Pembinaan'];

        const kriteriaList = Object.entries(KRITERIA).map(([kode, k]) => {
            const rawVal = d[kode];
            const normVal = h.norm[kode].toFixed(4);
            const wsVal = h.ws[kode].toFixed(4);
            const bars = Array.from({
                    length: 5
                }, (_, i) =>
                `<div style="width:8px;height:${10+i*4}px;border-radius:2px;background:${i<rawVal?k.warna:'#2A2B2E'};"></div>`
            ).join('');
            return `
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
            <div style="width:36px;height:36px;border-radius:8px;background:${k.warna}18;border:1px solid ${k.warna}35;display:flex;align-items:center;justify-content:center;color:${k.warna};flex-shrink:0;font-size:0.7rem;font-weight:700;">${kode}</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:0.82rem;font-weight:600;margin-bottom:4px;">${k.nama}</div>
                <div style="display:flex;align-items:flex-end;gap:3px;">${bars}</div>
            </div>
            <div style="text-align:right;min-width:120px;">
                <div style="font-size:0.7rem;color:var(--text-muted);">Raw: <span style="color:${k.warna};font-weight:700;">${rawVal}</span></div>
                <div style="font-size:0.7rem;color:var(--text-muted);">Norm: <span class="mono">${normVal}</span></div>
                <div style="font-size:0.7rem;color:var(--text-muted);">WS: <span class="mono" style="color:var(--gold);">${wsVal}</span></div>
            </div>
        </div>`;
        }).join('');

        document.getElementById('destModalBody').innerHTML = `
        <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:20px;">
            <div>
                <div style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;background:${kat.bg};border:1px solid ${kat.border};border-radius:999px;font-size:0.72rem;color:${kat.text};margin-bottom:8px;">${h.kategori}</div>
                <h2 class="modal-title">${d.nama}</h2>
                <div class="modal-subtitle">${d.jenis} · Rank #${h.ranking} dari 20</div>
            </div>
            <div style="text-align:right;margin-left:auto;">
                <div style="font-size:0.75rem;color:var(--text-muted);">Total Skor MFEP</div>
                <div class="mono" style="font-size:2rem;font-weight:700;color:${kat.badge};">${h.total.toFixed(4)}</div>
            </div>
        </div>

        <div class="info-box" style="margin-bottom:18px;">
            <i data-lucide="map-pin"></i>
            <div>
                <div style="font-weight:600;">${d.lokasi}</div>
                <div style="margin-top:2px;font-size:0.78rem;color:var(--text-muted);">Jam Operasional: ${d.jam}</div>
            </div>
        </div>

        <p style="font-size:0.85rem;color:var(--text-muted);line-height:1.7;margin-bottom:18px;">${d.deskripsi}</p>

        <div style="font-weight:600;font-size:0.85rem;margin-bottom:4px;">Detail Nilai MFEP per Kriteria</div>
        <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:12px;">Raw Score → Normalisasi → Weighted Score (× Bobot)</div>
        ${kriteriaList}

        <div style="margin-top:16px;display:flex;align-items:center;justify-content:space-between;padding:14px;background:var(--bg-elevated);border-radius:var(--radius-md);border:1px solid var(--border-subtle);">
            <div style="font-size:0.85rem;font-weight:600;">Total Skor MFEP (Σ WS)</div>
            <div class="mono" style="font-size:1.3rem;font-weight:700;color:var(--gold);">${h.total.toFixed(4)}</div>
        </div>
    `;

        openModal('destModal');
        if (window.lucide) lucide.createIcons();
    }

    function filterDest() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        const kat = document.getElementById('filterKat').value;
        const cards = document.querySelectorAll('#destGrid .dest-card');
        let count = 0;
        cards.forEach(c => {
            const nameMatch = c.dataset.nama.includes(q) || c.dataset.jenis.includes(q);
            const katMatch = !kat || c.dataset.kat === kat;
            const show = nameMatch && katMatch;
            c.style.display = show ? '' : 'none';
            if (show) count++;
        });
        document.getElementById('countNum').textContent = count;
    }
</script>

<?php include 'includes/footer.php'; ?>