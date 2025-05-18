<?php
session_start();
require 'config.php';

// Filter by kelas
$kelas_id = $_GET['kelas_id'] ?? 0;

// Get all kelas for filter
$kelas = $pdo->query("SELECT * FROM kelas ORDER BY nm_kelas")->fetchAll();

// Get nilai data
$query = "
    SELECT m.id, m.nama, m.nrp, k.nm_kelas, mk.nm_matkul, 
           p.n_uts, p.n_uas, p.n_tugas, p.n_akhir
    FROM mahasiswa m
    JOIN kelas k ON m.kelas_id = k.id
    JOIN penilaian p ON m.id = p.mhs_id
    JOIN matkul mk ON p.matkul_id = mk.id
";

if ($kelas_id > 0) {
    $query .= " WHERE m.kelas_id = :kelas_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['kelas_id' => $kelas_id]);
} else {
    $stmt = $pdo->query($query);
}

$nilai = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SITERA - Rekap Nilai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .page-header {
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 1rem;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--secondary-color);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            background-color: #f8f9fc;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .grade-A { color: var(--success-color); font-weight: bold; }
        .grade-B { color: #28a745; font-weight: bold; }
        .grade-C { color: var(--warning-color); font-weight: bold; }
        .grade-D { color: #fd7e14; font-weight: bold; }
        .grade-E { color: var(--danger-color); font-weight: bold; }
        
        .filter-card {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1.25rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .form-select {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            border: 1px solid #e0e0e0;
        }
        
        .btn-filter {
            border-radius: 0.5rem;
            padding: 0.5rem 1.5rem;
        }
        
        .empty-state {
            padding: 3rem;
            text-align: center;
            color: var(--secondary-color);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d1d3e2;
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
                <h2 class="mb-0">
                    <i class="bi bi-clipboard-data me-2"></i>Rekapitulasi Nilai
                </h2>
            </div>

            <!-- Filter Section -->
            <div class="filter-card mb-4">
                <form method="get" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="kelas_id" class="form-label small text-muted">Filter Kelas</label>
                        <select class="form-select" id="kelas_id" name="kelas_id">
                            <option value="0">Semua Kelas</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= $kelas_id == $k['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nm_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-filter w-100">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                    <?php if ($kelas_id > 0): ?>
                    <div class="col-md-2">
                        <a href="nilai.php" class="btn btn-outline-secondary btn-filter w-100">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                        </a>
                    </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Nilai Mahasiswa</h5>
                </div>
                
                <?php if (!empty($nilai)): ?>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>NRP</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Mata Kuliah</th>
                                    <th>UTS</th>
                                    <th>UAS</th>
                                    <th>Tugas</th>
                                    <th>Akhir</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($nilai as $i => $n): 
                                    $grade = '';
                                    if ($n['n_akhir'] >= 85) $grade = 'A';
                                    elseif ($n['n_akhir'] >= 75) $grade = 'B';
                                    elseif ($n['n_akhir'] >= 65) $grade = 'C';
                                    elseif ($n['n_akhir'] >= 50) $grade = 'D';
                                    else $grade = 'E';
                                ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($n['nrp']) ?></td>
                                    <td><?= htmlspecialchars($n['nama']) ?></td>
                                    <td><?= htmlspecialchars($n['nm_kelas']) ?></td>
                                    <td><?= htmlspecialchars($n['nm_matkul']) ?></td>
                                    <td><?= htmlspecialchars($n['n_uts']) ?></td>
                                    <td><?= htmlspecialchars($n['n_uas']) ?></td>
                                    <td><?= htmlspecialchars($n['n_tugas']) ?></td>
                                    <td><?= htmlspecialchars($n['n_akhir']) ?></td>
                                    <td class="grade-<?= $grade ?>"><?= $grade ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-clipboard-x"></i>
                    <h5>Tidak ada data nilai</h5>
                    <p class="text-muted">Data nilai akan muncul setelah diinputkan</p>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>