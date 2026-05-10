<?php
require_once 'includes/auth.php';
require_once 'includes/excel_import.php';

requireAdminLogin();

$pageTitle = 'Admin Management';
$activePage = 'admin';

$db = getDbConnection();
$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);

function adminRedirect(string $url, string $message, string $type = 'success'): void
{
    $_SESSION['admin_flash'] = ['type' => $type, 'message' => $message];
    header('Location: ' . $url);
    exit;
}

function scoreValue(string $key): int
{
    $value = isset($_POST[$key]) ? (int)$_POST[$key] : 1;
    return max(1, min(5, $value));
}

function importScoreValue(array $row, string $key): int
{
    $value = isset($row[$key]) ? (int)$row[$key] : 1;
    return max(1, min(5, $value));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$db) {
        adminRedirect('admin.php', 'Koneksi database gagal. Cek konfigurasi DB di includes/config.php.', 'error');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'kriteria_import') {
        $rows = readSpreadsheetAssociativeRows($_FILES['import_file'] ?? [], $error);
        if (!empty($error)) {
            adminRedirect('admin.php?section=kriteria', 'Import gagal: ' . $error, 'error');
        }

        $stmt = $db->prepare(
            'INSERT INTO kriteria (kode, nama, icon, warna, bobot, definisi, ukur)
             VALUES (?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                nama = VALUES(nama),
                icon = VALUES(icon),
                warna = VALUES(warna),
                bobot = VALUES(bobot),
                definisi = VALUES(definisi),
                ukur = VALUES(ukur)'
        );
        if (!$stmt) {
            adminRedirect('admin.php?section=kriteria', 'Import gagal: query tidak valid.', 'error');
        }

        $success = 0;
        $skipped = 0;
        foreach ($rows as $row) {
            $kode = strtoupper(trim($row['kode'] ?? ''));
            $nama = trim($row['nama'] ?? '');
            $icon = trim($row['icon'] ?? 'circle');
            $warna = trim($row['warna'] ?? '#F59E0B');
            $bobot = isset($row['bobot']) ? (float)$row['bobot'] : 0;
            $definisi = trim($row['definisi'] ?? '');
            $ukur = trim($row['ukur'] ?? '');

            if ($kode === '' || $nama === '' || $bobot <= 0) {
                $skipped++;
                continue;
            }

            $stmt->bind_param('ssssdss', $kode, $nama, $icon, $warna, $bobot, $definisi, $ukur);
            if ($stmt->execute()) {
                $success++;
            } else {
                $skipped++;
            }
        }

        $stmt->close();
        adminRedirect('admin.php?section=kriteria', "Import kriteria selesai. Berhasil: {$success}, dilewati: {$skipped}.");
    }

    if ($action === 'destinasi_import') {
        $rows = readSpreadsheetAssociativeRows($_FILES['import_file'] ?? [], $error);
        if (!empty($error)) {
            adminRedirect('admin.php?section=destinasi', 'Import gagal: ' . $error, 'error');
        }

        $stmt = $db->prepare(
            'INSERT INTO destinasi (kode, nama, jenis, deskripsi, lokasi, lat, lng, jam, K1, K2, K3, K4, K5, K6)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                nama = VALUES(nama),
                jenis = VALUES(jenis),
                deskripsi = VALUES(deskripsi),
                lokasi = VALUES(lokasi),
                lat = VALUES(lat),
                lng = VALUES(lng),
                jam = VALUES(jam),
                K1 = VALUES(K1),
                K2 = VALUES(K2),
                K3 = VALUES(K3),
                K4 = VALUES(K4),
                K5 = VALUES(K5),
                K6 = VALUES(K6)'
        );
        if (!$stmt) {
            adminRedirect('admin.php?section=destinasi', 'Import gagal: query tidak valid.', 'error');
        }

        $success = 0;
        $skipped = 0;
        foreach ($rows as $row) {
            $kode = strtoupper(trim($row['kode'] ?? ''));
            $nama = trim($row['nama'] ?? '');
            $jenis = trim($row['jenis'] ?? '');
            $deskripsi = trim($row['deskripsi'] ?? '');
            $lokasi = trim($row['lokasi'] ?? '');
            $lat = isset($row['lat']) ? (float)$row['lat'] : 0;
            $lng = isset($row['lng']) ? (float)$row['lng'] : 0;
            $jam = trim($row['jam'] ?? '');
            $k1 = importScoreValue($row, 'k1');
            $k2 = importScoreValue($row, 'k2');
            $k3 = importScoreValue($row, 'k3');
            $k4 = importScoreValue($row, 'k4');
            $k5 = importScoreValue($row, 'k5');
            $k6 = importScoreValue($row, 'k6');

            if ($kode === '' || $nama === '' || $jenis === '' || $lokasi === '') {
                $skipped++;
                continue;
            }

            $stmt->bind_param('sssssddsiiiiii', $kode, $nama, $jenis, $deskripsi, $lokasi, $lat, $lng, $jam, $k1, $k2, $k3, $k4, $k5, $k6);
            if ($stmt->execute()) {
                $success++;
            } else {
                $skipped++;
            }
        }

        $stmt->close();
        adminRedirect('admin.php?section=destinasi', "Import destinasi selesai. Berhasil: {$success}, dilewati: {$skipped}.");
    }

    if ($action === 'kriteria_create') {
        $stmt = $db->prepare('INSERT INTO kriteria (kode, nama, icon, warna, bobot, definisi, ukur) VALUES (?, ?, ?, ?, ?, ?, ?)');
        if (!$stmt) {
            adminRedirect('admin.php?section=kriteria', 'Gagal menambahkan kriteria.', 'error');
        }

        $kode = strtoupper(trim($_POST['kode'] ?? ''));
        $nama = trim($_POST['nama'] ?? '');
        $icon = trim($_POST['icon'] ?? 'circle');
        $warna = trim($_POST['warna'] ?? '#F59E0B');
        $bobot = (float)($_POST['bobot'] ?? 0);
        $definisi = trim($_POST['definisi'] ?? '');
        $ukur = trim($_POST['ukur'] ?? '');

        $stmt->bind_param('ssssdss', $kode, $nama, $icon, $warna, $bobot, $definisi, $ukur);
        if (!$stmt->execute()) {
            $stmt->close();
            adminRedirect('admin.php?section=kriteria', 'Gagal menambahkan kriteria. Pastikan kode unik.', 'error');
        }
        $stmt->close();
        adminRedirect('admin.php?section=kriteria', 'Kriteria berhasil ditambahkan.');
    }

    if ($action === 'kriteria_update') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('UPDATE kriteria SET kode = ?, nama = ?, icon = ?, warna = ?, bobot = ?, definisi = ?, ukur = ? WHERE id = ?');
        if (!$stmt) {
            adminRedirect('admin.php?section=kriteria', 'Gagal memperbarui kriteria.', 'error');
        }

        $kode = strtoupper(trim($_POST['kode'] ?? ''));
        $nama = trim($_POST['nama'] ?? '');
        $icon = trim($_POST['icon'] ?? 'circle');
        $warna = trim($_POST['warna'] ?? '#F59E0B');
        $bobot = (float)($_POST['bobot'] ?? 0);
        $definisi = trim($_POST['definisi'] ?? '');
        $ukur = trim($_POST['ukur'] ?? '');

        $stmt->bind_param('ssssdssi', $kode, $nama, $icon, $warna, $bobot, $definisi, $ukur, $id);
        if (!$stmt->execute()) {
            $stmt->close();
            adminRedirect('admin.php?section=kriteria', 'Gagal memperbarui kriteria.', 'error');
        }
        $stmt->close();
        adminRedirect('admin.php?section=kriteria', 'Kriteria berhasil diperbarui.');
    }

    if ($action === 'kriteria_delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM kriteria WHERE id = ?');
        if (!$stmt) {
            adminRedirect('admin.php?section=kriteria', 'Gagal menghapus kriteria.', 'error');
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        adminRedirect('admin.php?section=kriteria', 'Kriteria berhasil dihapus.');
    }

    if ($action === 'destinasi_create') {
        $stmt = $db->prepare('INSERT INTO destinasi (kode, nama, jenis, deskripsi, lokasi, lat, lng, jam, K1, K2, K3, K4, K5, K6) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        if (!$stmt) {
            adminRedirect('admin.php?section=destinasi', 'Gagal menambahkan destinasi.', 'error');
        }

        $kode = strtoupper(trim($_POST['kode'] ?? ''));
        $nama = trim($_POST['nama'] ?? '');
        $jenis = trim($_POST['jenis'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $lokasi = trim($_POST['lokasi'] ?? '');
        $lat = (float)($_POST['lat'] ?? 0);
        $lng = (float)($_POST['lng'] ?? 0);
        $jam = trim($_POST['jam'] ?? '');
        $k1 = scoreValue('K1');
        $k2 = scoreValue('K2');
        $k3 = scoreValue('K3');
        $k4 = scoreValue('K4');
        $k5 = scoreValue('K5');
        $k6 = scoreValue('K6');

        $stmt->bind_param('sssssddsiiiiii', $kode, $nama, $jenis, $deskripsi, $lokasi, $lat, $lng, $jam, $k1, $k2, $k3, $k4, $k5, $k6);
        if (!$stmt->execute()) {
            $stmt->close();
            adminRedirect('admin.php?section=destinasi', 'Gagal menambahkan destinasi. Pastikan kode unik.', 'error');
        }
        $stmt->close();
        adminRedirect('admin.php?section=destinasi', 'Destinasi berhasil ditambahkan.');
    }

    if ($action === 'destinasi_update') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('UPDATE destinasi SET kode = ?, nama = ?, jenis = ?, deskripsi = ?, lokasi = ?, lat = ?, lng = ?, jam = ?, K1 = ?, K2 = ?, K3 = ?, K4 = ?, K5 = ?, K6 = ? WHERE id = ?');
        if (!$stmt) {
            adminRedirect('admin.php?section=destinasi', 'Gagal memperbarui destinasi.', 'error');
        }

        $kode = strtoupper(trim($_POST['kode'] ?? ''));
        $nama = trim($_POST['nama'] ?? '');
        $jenis = trim($_POST['jenis'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $lokasi = trim($_POST['lokasi'] ?? '');
        $lat = (float)($_POST['lat'] ?? 0);
        $lng = (float)($_POST['lng'] ?? 0);
        $jam = trim($_POST['jam'] ?? '');
        $k1 = scoreValue('K1');
        $k2 = scoreValue('K2');
        $k3 = scoreValue('K3');
        $k4 = scoreValue('K4');
        $k5 = scoreValue('K5');
        $k6 = scoreValue('K6');

        $stmt->bind_param('sssssddsiiiiiii', $kode, $nama, $jenis, $deskripsi, $lokasi, $lat, $lng, $jam, $k1, $k2, $k3, $k4, $k5, $k6, $id);
        if (!$stmt->execute()) {
            $stmt->close();
            adminRedirect('admin.php?section=destinasi', 'Gagal memperbarui destinasi.', 'error');
        }
        $stmt->close();
        adminRedirect('admin.php?section=destinasi', 'Destinasi berhasil diperbarui.');
    }

    if ($action === 'destinasi_delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM destinasi WHERE id = ?');
        if (!$stmt) {
            adminRedirect('admin.php?section=destinasi', 'Gagal menghapus destinasi.', 'error');
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        adminRedirect('admin.php?section=destinasi', 'Destinasi berhasil dihapus.');
    }
}

$section = $_GET['section'] ?? 'destinasi';

$kriteriaRows = [];
$destRows = [];
$editKriteria = null;
$editDest = null;

if ($db && dbTableExists($db, 'kriteria')) {
    $kResult = $db->query('SELECT * FROM kriteria ORDER BY kode ASC');
    if ($kResult) {
        while ($row = $kResult->fetch_assoc()) {
            $kriteriaRows[] = $row;
        }
        $kResult->free();
    }
}

if ($db && dbTableExists($db, 'destinasi')) {
    $dResult = $db->query('SELECT * FROM destinasi ORDER BY kode ASC');
    if ($dResult) {
        while ($row = $dResult->fetch_assoc()) {
            $destRows[] = $row;
        }
        $dResult->free();
    }
}

$editKriteriaId = isset($_GET['edit_kriteria']) ? (int)$_GET['edit_kriteria'] : 0;
if ($editKriteriaId > 0) {
    foreach ($kriteriaRows as $row) {
        if ((int)$row['id'] === $editKriteriaId) {
            $editKriteria = $row;
            break;
        }
    }
}

$editDestId = isset($_GET['edit_dest']) ? (int)$_GET['edit_dest'] : 0;
if ($editDestId > 0) {
    foreach ($destRows as $row) {
        if ((int)$row['id'] === $editDestId) {
            $editDest = $row;
            break;
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div style="margin-bottom:24px;" class="animate-in">
    <div class="hero-eyebrow">Admin Panel</div>
    <h1 class="section-title" style="font-family:var(--font-display);font-size:1.6rem;">Manajemen Data MySQL</h1>
    <p class="section-subtitle">Kelola data kriteria, bobot, dan destinasi secara dinamis dari database.</p>
</div>

<?php if (!$db): ?>
    <div class="card">
        <div class="alert-error">Koneksi MySQL gagal. Periksa DB_HOST, DB_PORT, DB_NAME, DB_USER, dan DB_PASS pada konfigurasi.</div>
    </div>
<?php else: ?>

    <?php if ($flash): ?>
        <div class="<?= $flash['type'] === 'error' ? 'alert-error' : 'alert-success' ?>" style="margin-bottom:14px;">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="admin-tabs">
        <a href="admin.php?section=destinasi" class="btn btn-sm <?= $section === 'destinasi' ? 'btn-primary' : 'btn-outline' ?>">Data Destinasi</a>
        <a href="admin.php?section=kriteria" class="btn btn-sm <?= $section === 'kriteria' ? 'btn-primary' : 'btn-outline' ?>">Data Kriteria</a>
    </div>

    <?php if ($section === 'kriteria'): ?>
        <div class="card" style="margin-top:16px;margin-bottom:16px;">
            <div class="card-header">
                <span class="card-title">Import Kriteria (Excel/CSV)</span>
            </div>
            <form method="post" enctype="multipart/form-data" class="admin-form-grid">
                <input type="hidden" name="action" value="kriteria_import">
                <label>File Import (.xlsx / .csv)</label>
                <input type="file" name="import_file" accept=".xlsx,.csv" required>
                <div class="import-hint">
                    Header wajib: kode, nama, icon, warna, bobot, definisi, ukur<br>
                    Template: <a href="manual/template_import_kriteria.csv" target="_blank">template_import_kriteria.csv</a>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Import Kriteria</button>
                </div>
            </form>
        </div>

        <div class="card" style="margin-top:16px;margin-bottom:16px;">
            <div class="card-header">
                <span class="card-title"><?= $editKriteria ? 'Edit Kriteria' : 'Tambah Kriteria' ?></span>
            </div>
            <form method="post" class="admin-form-grid">
                <input type="hidden" name="action" value="<?= $editKriteria ? 'kriteria_update' : 'kriteria_create' ?>">
                <?php if ($editKriteria): ?>
                    <input type="hidden" name="id" value="<?= (int)$editKriteria['id'] ?>">
                <?php endif; ?>

                <label>Kode</label>
                <input name="kode" required value="<?= htmlspecialchars($editKriteria['kode'] ?? '') ?>" placeholder="K1">

                <label>Nama</label>
                <input name="nama" required value="<?= htmlspecialchars($editKriteria['nama'] ?? '') ?>" placeholder="Keaslian Kuliner">

                <label>Icon Lucide</label>
                <input name="icon" required value="<?= htmlspecialchars($editKriteria['icon'] ?? 'circle') ?>" placeholder="utensils">

                <label>Warna</label>
                <input name="warna" required value="<?= htmlspecialchars($editKriteria['warna'] ?? '#F59E0B') ?>" placeholder="#F59E0B">

                <label>Bobot</label>
                <input type="number" step="0.01" min="0" max="1" name="bobot" required value="<?= htmlspecialchars($editKriteria['bobot'] ?? '0.10') ?>">

                <label>Definisi</label>
                <textarea name="definisi" rows="3" required><?= htmlspecialchars($editKriteria['definisi'] ?? '') ?></textarea>

                <label>Cara Ukur</label>
                <textarea name="ukur" rows="3" required><?= htmlspecialchars($editKriteria['ukur'] ?? '') ?></textarea>

                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary"><?= $editKriteria ? 'Update' : 'Simpan' ?></button>
                    <?php if ($editKriteria): ?>
                        <a href="admin.php?section=kriteria" class="btn btn-outline">Batal Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">Daftar Kriteria</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Bobot</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kriteriaRows as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['kode']) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= number_format((float)$row['bobot'], 2) ?></td>
                                <td style="display:flex;gap:8px;">
                                    <a class="btn btn-outline btn-sm" href="admin.php?section=kriteria&edit_kriteria=<?= (int)$row['id'] ?>">Edit</a>
                                    <form method="post" onsubmit="return confirm('Hapus data ini?')">
                                        <input type="hidden" name="action" value="kriteria_delete">
                                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                        <button type="submit" class="btn btn-outline btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="card" style="margin-top:16px;margin-bottom:16px;">
            <div class="card-header">
                <span class="card-title">Import Destinasi (Excel/CSV)</span>
            </div>
            <form method="post" enctype="multipart/form-data" class="admin-form-grid">
                <input type="hidden" name="action" value="destinasi_import">
                <label>File Import (.xlsx / .csv)</label>
                <input type="file" name="import_file" accept=".xlsx,.csv" required>
                <div class="import-hint">
                    Header wajib: kode, nama, jenis, deskripsi, lokasi, lat, lng, jam, K1, K2, K3, K4, K5, K6<br>
                    Template: <a href="manual/template_import_destinasi.csv" target="_blank">template_import_destinasi.csv</a>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Import Destinasi</button>
                </div>
            </form>
        </div>

        <div class="card" style="margin-top:16px;margin-bottom:16px;">
            <div class="card-header">
                <span class="card-title"><?= $editDest ? 'Edit Destinasi' : 'Tambah Destinasi' ?></span>
            </div>
            <form method="post" class="admin-form-grid">
                <input type="hidden" name="action" value="<?= $editDest ? 'destinasi_update' : 'destinasi_create' ?>">
                <?php if ($editDest): ?>
                    <input type="hidden" name="id" value="<?= (int)$editDest['id'] ?>">
                <?php endif; ?>

                <label>Kode</label>
                <input name="kode" required value="<?= htmlspecialchars($editDest['kode'] ?? '') ?>" placeholder="D21">

                <label>Nama</label>
                <input name="nama" required value="<?= htmlspecialchars($editDest['nama'] ?? '') ?>" placeholder="Nama destinasi">

                <label>Jenis</label>
                <input name="jenis" required value="<?= htmlspecialchars($editDest['jenis'] ?? '') ?>" placeholder="Jenis kuliner">

                <label>Lokasi</label>
                <input name="lokasi" required value="<?= htmlspecialchars($editDest['lokasi'] ?? '') ?>" placeholder="Alamat">

                <label>Latitude</label>
                <input type="number" step="0.000001" name="lat" required value="<?= htmlspecialchars($editDest['lat'] ?? '-7.560000') ?>">

                <label>Longitude</label>
                <input type="number" step="0.000001" name="lng" required value="<?= htmlspecialchars($editDest['lng'] ?? '110.820000') ?>">

                <label>Jam Operasional</label>
                <input name="jam" required value="<?= htmlspecialchars($editDest['jam'] ?? '') ?>" placeholder="08.00 - 17.00 WIB">

                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="3" required><?= htmlspecialchars($editDest['deskripsi'] ?? '') ?></textarea>

                <label>K1</label>
                <input type="number" min="1" max="5" name="K1" required value="<?= htmlspecialchars($editDest['K1'] ?? '4') ?>">

                <label>K2</label>
                <input type="number" min="1" max="5" name="K2" required value="<?= htmlspecialchars($editDest['K2'] ?? '4') ?>">

                <label>K3</label>
                <input type="number" min="1" max="5" name="K3" required value="<?= htmlspecialchars($editDest['K3'] ?? '4') ?>">

                <label>K4</label>
                <input type="number" min="1" max="5" name="K4" required value="<?= htmlspecialchars($editDest['K4'] ?? '4') ?>">

                <label>K5</label>
                <input type="number" min="1" max="5" name="K5" required value="<?= htmlspecialchars($editDest['K5'] ?? '4') ?>">

                <label>K6</label>
                <input type="number" min="1" max="5" name="K6" required value="<?= htmlspecialchars($editDest['K6'] ?? '4') ?>">

                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary"><?= $editDest ? 'Update' : 'Simpan' ?></button>
                    <?php if ($editDest): ?>
                        <a href="admin.php?section=destinasi" class="btn btn-outline">Batal Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">Daftar Destinasi</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>K1</th>
                            <th>K2</th>
                            <th>K3</th>
                            <th>K4</th>
                            <th>K5</th>
                            <th>K6</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($destRows as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['kode']) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['jenis']) ?></td>
                                <td><?= (int)$row['K1'] ?></td>
                                <td><?= (int)$row['K2'] ?></td>
                                <td><?= (int)$row['K3'] ?></td>
                                <td><?= (int)$row['K4'] ?></td>
                                <td><?= (int)$row['K5'] ?></td>
                                <td><?= (int)$row['K6'] ?></td>
                                <td style="display:flex;gap:8px;">
                                    <a class="btn btn-outline btn-sm" href="admin.php?section=destinasi&edit_dest=<?= (int)$row['id'] ?>">Edit</a>
                                    <form method="post" onsubmit="return confirm('Hapus data ini?')">
                                        <input type="hidden" name="action" value="destinasi_delete">
                                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                        <button type="submit" class="btn btn-outline btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>