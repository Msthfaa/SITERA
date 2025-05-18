<?php
session_start();
require 'config.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? 0;
$error = '';

$mhs_id = $matkul_id = '';
$n_uts = $n_uas = $n_tugas = $n_akhir = 0;

// Load mahasiswa dan matkul untuk dropdown
$mahasiswa = $pdo->query("SELECT * FROM mahasiswa ORDER BY nama")->fetchAll();
$matkul = $pdo->query("SELECT * FROM matkul ORDER BY nm_matkul")->fetchAll();

// Edit data
if ($action === 'edit') {
    $stmt = $pdo->prepare("SELECT * FROM penilaian WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        $mhs_id = $data['mhs_id'];
        $matkul_id = $data['matkul_id'];
        $n_uts = $data['n_uts'];
        $n_uas = $data['n_uas'];
        $n_tugas = $data['n_tugas'];
        $n_akhir = $data['n_akhir'];
    }
} elseif ($action === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM penilaian WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: penilaian.php?success=Data+berhasil+dihapus');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mhs_id = $_POST['mhs_id'];
    $matkul_id = $_POST['matkul_id'];
    $n_uts = $_POST['n_uts'] ?? 0;
    $n_uas = $_POST['n_uas'] ?? 0;
    $n_tugas = $_POST['n_tugas'] ?? 0;
    $n_akhir = ($n_uts * 0.3) + ($n_uas * 0.4) + ($n_tugas * 0.3);

    if (empty($mhs_id) || empty($matkul_id)) {
        $error = 'Mahasiswa dan Mata Kuliah harus dipilih.';
    } else {
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE penilaian SET mhs_id=?, matkul_id=?, n_uts=?, n_uas=?, n_tugas=?, n_akhir=? WHERE id=?");
            $stmt->execute([$mhs_id, $matkul_id, $n_uts, $n_uas, $n_tugas, $n_akhir, $id]);
            header('Location: penilaian.php?success=Data+berhasil+diperbarui');
            exit();
        } else {
            $stmt = $pdo->prepare("INSERT INTO penilaian (mhs_id, matkul_id, n_uts, n_uas, n_tugas, n_akhir) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$mhs_id, $matkul_id, $n_uts, $n_uas, $n_tugas, $n_akhir]);
            header('Location: penilaian.php?success=Data+berhasil+ditambahkan');
            exit();
        }
    }
}

// Ambil semua data penilaian
$stmt = $pdo->query("
    SELECT p.*, mhs.nama AS nama_mhs, mk.nm_matkul 
    FROM penilaian p
    JOIN mahasiswa mhs ON p.mhs_id = mhs.id
    JOIN matkul mk ON p.matkul_id = mk.id
    ORDER BY mhs.nama, mk.nm_matkul
");
$penilaian = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SITERA - Penilaian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #858796;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            transition: all 0.3s;
        }
        
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
            color: var(--primary);
            padding: 1rem 1.5rem;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
        
        .form-section {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        
        .form-control, .form-select, .select2-selection {
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            border: 1px solid #e0e0e0;
            height: calc(2.5rem + 2px);
            transition: all 0.2s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .btn {
            border-radius: 0.375rem;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #3a5bc7;
            border-color: #3a5bc7;
            transform: translateY(-1px);
        }
        
        .btn-outline-secondary {
            border: 1px solid #e0e0e0;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .table {
            margin-bottom: 0;
            font-size: 0.875rem;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--secondary);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            background-color: #f8f9fc;
            padding: 1rem;
        }
        
        .table td {
            vertical-align: middle;
            padding: 1rem;
            border-top: 1px solid #f0f0f0;
        }
        
        .grade-A { color: var(--success); font-weight: bold; }
        .grade-B { color: #28a745; font-weight: bold; }
        .grade-C { color: var(--warning); font-weight: bold; }
        .grade-D { color: #fd7e14; font-weight: bold; }
        .grade-E { color: var(--danger); font-weight: bold; }
        
        .empty-state {
            padding: 3rem;
            text-align: center;
            color: var(--secondary);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d1d3e2;
        }
        
        .select2-container--default .select2-selection--single {
            border: 1px solid #e0e0e0;
            border-radius: 0.375rem;
            height: calc(2.5rem + 2px);
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 2.5rem;
            padding-left: 1rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 2.5rem;
        }
        
        .score-input {
            max-width: 100px;
        }
        
        .final-score {
            background-color: #f8f9fc;
            font-weight: 600;
        }
        
        .action-buttons .btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
                <h2 class="h4 mb-0">
                    <i class="bi bi-journal-bookmark text-primary me-2"></i>Penilaian Akademik
                </h2>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4">
                    <?= htmlspecialchars(urldecode($_GET['success'])) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Form Section -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-<?= $id > 0 ? 'pen' : 'plus' ?>-circle-fill text-primary me-2"></i>
                    <h5 class="mb-0"><?= $id > 0 ? 'Edit Penilaian' : 'Tambah Penilaian Baru' ?></h5>
                </div>
                <div class="card-body">
                    <form method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="id" value="<?= $id ?>">
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-5">
                                <label class="form-label">Mahasiswa</label>
                                <select name="mhs_id" class="form-select select2" required>
                                    <option value="">Pilih Mahasiswa</option>
                                    <?php foreach ($mahasiswa as $m): ?>
                                        <option value="<?= $m['id'] ?>" <?= $m['id'] == $mhs_id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($m['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Harap pilih mahasiswa
                                </div>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label">Mata Kuliah</label>
                                <select name="matkul_id" class="form-select" required>
                                    <option value="">Pilih Mata Kuliah</option>
                                    <?php foreach ($matkul as $mk): ?>
                                        <option value="<?= $mk['id'] ?>" <?= $mk['id'] == $matkul_id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($mk['nm_matkul']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Harap pilih mata kuliah
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Nilai UTS (30%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control score-input" id="n_uts" name="n_uts" value="<?= htmlspecialchars($n_uts) ?>" required>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Nilai UAS (40%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control score-input" id="n_uas" name="n_uas" value="<?= htmlspecialchars($n_uas) ?>" required>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Nilai Tugas (30%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control score-input" id="n_tugas" name="n_tugas" value="<?= htmlspecialchars($n_tugas) ?>" required>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Nilai Akhir</label>
                                <input type="text" class="form-control final-score" id="n_akhir" value="<?= htmlspecialchars($n_akhir) ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <?php if ($id > 0): ?>
                                <a href="penilaian.php" class="btn btn-outline-secondary px-4">
                                    <i class="bi bi-x-lg me-1"></i> Batal
                                </a>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check me-2"></i>Daftar Penilaian
                    </h5>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control form-control-sm" placeholder="Cari penilaian...">
                        <button class="btn btn-sm btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                
                <?php if (!empty($penilaian)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Mahasiswa</th>
                                <th>Mata Kuliah</th>
                                <th>UTS</th>
                                <th>UAS</th>
                                <th>Tugas</th>
                                <th>Nilai Akhir</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($penilaian as $i => $p): 
                                $grade = '';
                                if ($p['n_akhir'] >= 85) $grade = 'A';
                                elseif ($p['n_akhir'] >= 75) $grade = 'B';
                                elseif ($p['n_akhir'] >= 65) $grade = 'C';
                                elseif ($p['n_akhir'] >= 50) $grade = 'D';
                                else $grade = 'E';
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($p['nama_mhs']) ?></td>
                                <td><?= htmlspecialchars($p['nm_matkul']) ?></td>
                                <td><?= htmlspecialchars($p['n_uts']) ?></td>
                                <td><?= htmlspecialchars($p['n_uas']) ?></td>
                                <td><?= htmlspecialchars($p['n_tugas']) ?></td>
                                <td class="grade-<?= $grade ?>"><?= number_format($p['n_akhir'], 2) ?></td>
                                <td class="action-buttons">
                                    <a href="penilaian.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-warning me-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="penilaian.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus penilaian ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state py-5">
                    <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Belum ada data penilaian</h5>
                    <p class="text-muted">Mulai dengan menambahkan penilaian baru</p>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<script>
    // Real-time final score calculation
    const n_uts = document.getElementById('n_uts');
    const n_uas = document.getElementById('n_uas');
    const n_tugas = document.getElementById('n_tugas');
    const n_akhir = document.getElementById('n_akhir');

    [n_uts, n_uas, n_tugas].forEach(el => el.addEventListener('input', updateFinal));

    function updateFinal() {
        const uts = parseFloat(n_uts.value) || 0;
        const uas = parseFloat(n_uas.value) || 0;
        const tugas = parseFloat(n_tugas.value) || 0;
        const akhir = (uts * 0.3) + (uas * 0.4) + (tugas * 0.3);
        n_akhir.value = akhir.toFixed(2);
    }
    
    // Form validation
    (function() {
        'use strict'
        
        var forms = document.querySelectorAll('.needs-validation')
        
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Pilih Mahasiswa",
            allowClear: true,
            width: '100%'
        });
    });
</script>
</body>
</html>