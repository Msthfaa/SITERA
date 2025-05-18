<?php
session_start();
require 'config.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

// Form data
$nama = $nrp = $kelas_id = '';
$error = '';

// Handle actions
if ($action === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM mahasiswa WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: mahasiswa.php?success=Mahasiswa berhasil dihapus');
    exit();
} elseif ($action === 'edit') {
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        $nama = $data['nama'];
        $nrp = $data['nrp'];
        $kelas_id = $data['kelas_id'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $nrp = $_POST['nrp'];
    $kelas_id = $_POST['kelas_id'];

    if (empty($nama) || empty($nrp) || empty($kelas_id)) {
        $error = 'Semua field harus diisi!';
    } else {
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE mahasiswa SET nama = ?, nrp = ?, kelas_id = ? WHERE id = ?");
            $stmt->execute([$nama, $nrp, $kelas_id, $id]);
            header('Location: mahasiswa.php?success=Mahasiswa berhasil diperbarui');
            exit();
        } else {
            $stmt = $pdo->prepare("INSERT INTO mahasiswa (nama, nrp, kelas_id) VALUES (?, ?, ?)");
            $stmt->execute([$nama, $nrp, $kelas_id]);
            header('Location: mahasiswa.php?success=Mahasiswa berhasil ditambahkan');
            exit();
        }
    }
}

// Get all mahasiswa
$stmt = $pdo->query("
    SELECT m.*, k.nm_kelas 
    FROM mahasiswa m
    JOIN kelas k ON m.kelas_id = k.id
    ORDER BY m.nama
");
$mahasiswa = $stmt->fetchAll();

// Get kelas for dropdown
$kelas = $pdo->query("SELECT * FROM kelas ORDER BY nm_kelas")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITERA - Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Data Mahasiswa</h1>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><?= $id > 0 ? 'Edit' : 'Tambah' ?> Mahasiswa</h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nrp" class="form-label">NRP</label>
                                <input type="text" class="form-control" id="nrp" name="nrp" value="<?= htmlspecialchars($nrp) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="kelas_id" class="form-label">Kelas</label>
                                <select class="form-select" id="kelas_id" name="kelas_id" required>
                                    <option value="">Pilih Kelas</option>
                                    <?php foreach ($kelas as $k): ?>
                                        <option value="<?= $k['id'] ?>" <?= $kelas_id == $k['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($k['nm_kelas']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <?php if ($id > 0): ?>
                                    <a href="mahasiswa.php" class="btn btn-secondary">Batal</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-header">
                    <h5>Daftar Mahasiswa</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NRP</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mahasiswa as $i => $m): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($m['nrp']) ?></td>
                                        <td><?= htmlspecialchars($m['nama']) ?></td>
                                        <td><?= htmlspecialchars($m['nm_kelas']) ?></td>
                                        <td>
                                            <a href="mahasiswa.php?action=edit&id=<?= $m['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="mahasiswa.php?action=delete&id=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
