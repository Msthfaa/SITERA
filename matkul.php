<?php
session_start();
require 'config.php';

// Ambil action dan ID dari query string
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Inisialisasi variabel
$nm_matkul = '';
$error = '';

// Proses aksi hapus
if ($action === 'delete' && $id > 0) {
    $stmt = $pdo->prepare("DELETE FROM matkul WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: matkul.php?success=Mata kuliah berhasil dihapus');
    exit();
}

// Proses aksi edit - mengambil data yang akan diedit
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM matkul WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        $nm_matkul = $data['nm_matkul'];
    }
}

// Proses simpan (insert/update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nm_matkul = trim($_POST['nm_matkul']);

    // Validasi
    if (empty($nm_matkul)) {
        $error = 'Nama Mata Kuliah harus diisi!';
    } else {
        if ($id > 0) {
            // Update
            $stmt = $pdo->prepare("UPDATE matkul SET nm_matkul = ? WHERE id = ?");
            $stmt->execute([$nm_matkul, $id]);
            header('Location: matkul.php?success=Mata kuliah berhasil diperbarui');
            exit();
        } else {
            // Insert baru
            $stmt = $pdo->prepare("INSERT INTO matkul (nm_matkul) VALUES (?)");
            $stmt->execute([$nm_matkul]);
            header('Location: matkul.php?success=Mata kuliah berhasil ditambahkan');
            exit();
        }
    }
}

// Ambil semua data matkul
$stmt = $pdo->query("SELECT * FROM matkul ORDER BY nm_matkul");
$matkul = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITERA - Mata Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Data Mata Kuliah</h1>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <!-- Form Tambah/Edit -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><?= $id > 0 ? 'Edit' : 'Tambah' ?> Mata Kuliah</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nm_matkul" class="form-label">Nama Mata Kuliah</label>
                                    <input type="text" class="form-control" id="nm_matkul" name="nm_matkul" value="<?= htmlspecialchars($nm_matkul) ?>" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <?php if ($id > 0): ?>
                                        <a href="matkul.php" class="btn btn-secondary">Batal</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabel Mata Kuliah -->
                <div class="card">
                    <div class="card-header">
                        <h5>Daftar Mata Kuliah</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Mata Kuliah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($matkul as $i => $m): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= htmlspecialchars($m['nm_matkul']) ?></td>
                                            <td>
                                                <a href="matkul.php?action=edit&id=<?= $m['id'] ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="matkul.php?action=delete&id=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (count($matkul) === 0): ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Belum ada data mata kuliah.</td>
                                        </tr>
                                    <?php endif; ?>
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
