<?php
session_start();
require 'config.php';

// CRUD Operations
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;

// Form data
$nm_kelas = '';
$error = '';

// Handle actions
if ($action === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM kelas WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: kelas.php?success=Kelas berhasil dihapus');
    exit();
} elseif ($action === 'edit') {
    $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        $nm_kelas = $data['nm_kelas'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nm_kelas = $_POST['nm_kelas'];

    // Validation
    if (empty($nm_kelas)) {
        $error = 'Nama kelas harus diisi!';
    } else {
        if ($id > 0) {
            // Update
            $stmt = $pdo->prepare("UPDATE kelas SET nm_kelas = ? WHERE id = ?");
            $stmt->execute([$nm_kelas, $id]);
            header('Location: kelas.php?success=Kelas berhasil diperbarui');
            exit();
        } else {
            // Create
            $stmt = $pdo->prepare("INSERT INTO kelas (nm_kelas) VALUES (?)");
            $stmt->execute([$nm_kelas]);
            header('Location: kelas.php?success=Kelas berhasil ditambahkan');
            exit();
        }
    }
}

// Ambil data kelas tanpa join jurusan
$stmt = $pdo->query("SELECT * FROM kelas ORDER BY id");
$kelas = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SITERA - Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <main class="col-md-9 col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Data Kelas</h1>
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
                        <h5><?= $id > 0 ? 'Edit' : 'Tambah' ?> Kelas</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <div class="mb-3">
                                <label for="nm_kelas" class="form-label">Nama Kelas</label>
                                <input type="text" class="form-control" id="nm_kelas" name="nm_kelas" value="<?= htmlspecialchars($nm_kelas) ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <?php if ($id > 0): ?>
                                <a href="kelas.php" class="btn btn-secondary">Batal</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="card">
                    <div class="card-header">
                        <h5>Daftar Kelas</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Kelas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kelas as $i => $k): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= htmlspecialchars($k['nm_kelas']) ?></td>
                                            <td>
                                                <a href="kelas.php?action=edit&id=<?= $k['id'] ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="kelas.php?action=delete&id=<?= $k['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
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
