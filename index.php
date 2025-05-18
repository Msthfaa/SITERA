<?php
session_start();
require 'config.php';

// Get data for nilai chart (average scores by assessment type)
$nilaiData = [
    'UTS' => 0,
    'UAS' => 0,
    'Tugas' => 0,
    'Akhir' => 0
];

$query = "SELECT 
            AVG(n_uts) as avg_uts,
            AVG(n_uas) as avg_uas,
            AVG(n_tugas) as avg_tugas,
            AVG(n_akhir) as avg_akhir
          FROM penilaian";
$stmt = $pdo->query($query);
$averages = $stmt->fetch(PDO::FETCH_ASSOC);

if ($averages) {
    $nilaiData['UTS'] = round($averages['avg_uts'], 1);
    $nilaiData['UAS'] = round($averages['avg_uas'], 1);
    $nilaiData['Tugas'] = round($averages['avg_tugas'], 1);
    $nilaiData['Akhir'] = round($averages['avg_akhir'], 1);
}

// Get data for distribution chart (grade distribution)
$gradeDistribution = [
    'A (90-100)' => 0,
    'B (80-89)' => 0,
    'C (70-79)' => 0,
    'D (60-69)' => 0,
    'E (<60)' => 0
];

$query = "SELECT 
            SUM(CASE WHEN n_akhir >= 90 THEN 1 ELSE 0 END) as grade_a,
            SUM(CASE WHEN n_akhir >= 80 AND n_akhir < 90 THEN 1 ELSE 0 END) as grade_b,
            SUM(CASE WHEN n_akhir >= 70 AND n_akhir < 80 THEN 1 ELSE 0 END) as grade_c,
            SUM(CASE WHEN n_akhir >= 60 AND n_akhir < 70 THEN 1 ELSE 0 END) as grade_d,
            SUM(CASE WHEN n_akhir < 60 THEN 1 ELSE 0 END) as grade_e,
            COUNT(*) as total
          FROM penilaian";
$stmt = $pdo->query($query);
$grades = $stmt->fetch(PDO::FETCH_ASSOC);

if ($grades && $grades['total'] > 0) {
    $gradeDistribution['A (90-100)'] = round(($grades['grade_a'] / $grades['total']) * 100, 1);
    $gradeDistribution['B (80-89)'] = round(($grades['grade_b'] / $grades['total']) * 100, 1);
    $gradeDistribution['C (70-79)'] = round(($grades['grade_c'] / $grades['total']) * 100, 1);
    $gradeDistribution['D (60-69)'] = round(($grades['grade_d'] / $grades['total']) * 100, 1);
    $gradeDistribution['E (<60)'] = round(($grades['grade_e'] / $grades['total']) * 100, 1);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITERA - Sistem Terpadu Rekapitulasi Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 80px;
            --top-nav-height: 60px;
            --card-radius: 12px;
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .main-content {
            padding: 20px;
            transition: all 0.3s ease;
            min-height: 100vh;
            background-color: #f8f9fc;
        }
        
        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        .top-nav {
            height: var(--top-nav-height);
            background: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: var(--card-radius);
            padding: 0 20px;
        }
        
        .stat-card {
            border-radius: var(--card-radius);
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .card-icon {
            font-size: 2rem;
            opacity: 0.3;
        }
        
        .stat-card.primary {
            background-color: var(--primary-color);
            border-left: 5px solid #3a5bcd;
        }
        
        .stat-card.success {
            background-color: var(--success-color);
            border-left: 5px solid #17a673;
        }
        
        .stat-card.info {
            background-color: var(--info-color);
            border-left: 5px solid #2a9bb3;
        }
        
        .chart-card {
            border-radius: var(--card-radius);
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .chart-card .card-header {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            color: #4e73df;
            border-top-left-radius: var(--card-radius) !important;
            border-top-right-radius: var(--card-radius) !important;
        }
        
        @media (max-width: 767.98px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Toggle Button -->
    <button class="btn btn-dark position-fixed start-0 mt-3 ms-3 z-3 d-md-none" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>
    
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content flex-grow-1" id="mainContent">
            <!-- Top Navigation -->
            <div class="d-flex justify-content-between align-items-center top-nav mb-4">
                <h4 class="mb-0 text-gray-800">Dashboard</h4>
                <div class="d-flex align-items-center">
                    <span class="me-2 text-muted">Selamat datang</span>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card primary text-white p-4">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-uppercase mb-1">Mahasiswa</h6>
                                <?php $stmt = $pdo->query("SELECT COUNT(*) FROM mahasiswa"); ?>
                                <h2 class="mb-0"><?= $stmt->fetchColumn() ?></h2>
                            </div>
                            <div class="card-icon">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stat-card success text-white p-4">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-uppercase mb-1">Mata Kuliah</h6>
                                <?php $stmt = $pdo->query("SELECT COUNT(*) FROM matkul"); ?>
                                <h2 class="mb-0"><?= $stmt->fetchColumn() ?></h2>
                            </div>
                            <div class="card-icon">
                                <i class="bi bi-journal-bookmark"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stat-card info text-white p-4">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-uppercase mb-1">Kelas</h6>
                                <?php $stmt = $pdo->query("SELECT COUNT(*) FROM kelas"); ?>
                                <h2 class="mb-0"><?= $stmt->fetchColumn() ?></h2>
                            </div>
                            <div class="card-icon">
                                <i class="bi bi-house-door"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="chart-card card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Rata-rata Nilai</h5>
                            <small class="text-muted">Berdasarkan data penilaian</small>
                        </div>
                        <div class="card-body">
                            <canvas id="nilaiChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="chart-card card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Distribusi Nilai Akhir</h5>
                            <small class="text-muted">Dalam persentase</small>
                        </div>
                        <div class="card-body">
                            <canvas id="distributionChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Convert PHP arrays to JavaScript
        const nilaiData = {
            labels: ['UTS', 'UAS', 'Tugas', 'Akhir'],
            values: [
                <?= $nilaiData['UTS'] ?>,
                <?= $nilaiData['UAS'] ?>,
                <?= $nilaiData['Tugas'] ?>,
                <?= $nilaiData['Akhir'] ?>
            ]
        };

        const gradeDistribution = {
            labels: ['A (90-100)', 'B (80-89)', 'C (70-79)', 'D (60-69)', 'E (<60)'],
            values: [
                <?= $gradeDistribution['A (90-100)'] ?>,
                <?= $gradeDistribution['B (80-89)'] ?>,
                <?= $gradeDistribution['C (70-79)'] ?>,
                <?= $gradeDistribution['D (60-69)'] ?>,
                <?= $gradeDistribution['E (<60)'] ?>
            ]
        };

        // Main chart
        const ctx = document.getElementById('nilaiChart').getContext('2d');
        const nilaiChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: nilaiData.labels,
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: nilaiData.values,
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.5)',
                        'rgba(28, 200, 138, 0.5)',
                        'rgba(246, 194, 62, 0.5)',
                        'rgba(54, 185, 204, 0.5)'
                    ],
                    borderColor: [
                        'rgba(78, 115, 223, 1)',
                        'rgba(28, 200, 138, 1)',
                        'rgba(246, 194, 62, 1)',
                        'rgba(54, 185, 204, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw.toFixed(1);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(0);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        
        // Distribution chart
        const distCtx = document.getElementById('distributionChart').getContext('2d');
        const distributionChart = new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: gradeDistribution.labels,
                datasets: [{
                    data: gradeDistribution.values,
                    backgroundColor: [
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(231, 74, 59, 0.8)',
                        'rgba(126, 142, 159, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });
        
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mainContent = document.getElementById('mainContent');
            
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    sidebar.classList.toggle('show');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                }
            });
        });
    </script>
</body>
</html>