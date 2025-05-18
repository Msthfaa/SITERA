<div class="sidebar bg-white p-0 min-vh-100 border-end" style="width: 280px;">
    <!-- Logo Section -->
    <div class="d-flex flex-column align-items-center py-4 px-3 border-bottom">
        <img src="logo_sitera.png" class="img-fluid mb-2" style="max-width: 120px; height: auto;" alt="Logo SITERA">
        <p class="text-center text-muted small mt-1">Sistem Terpadu Rekapitulasi Akademik</p>
    </div>
    
    <!-- Navigation Menu -->
    <ul class="nav flex-column px-3 py-3">
        <li class="nav-item mb-2">
            <a class="nav-link text-dark d-flex align-items-center gap-3 py-2 px-3 rounded hover-bg" href="index.php">
                <div class="icon-container bg-light-red p-2 rounded">
                    <i class="bi bi-speedometer2 text-red"></i>
                </div>
                <span>Dashboard</span>
            </a>
        </li>
        
        <!-- Master Mahasiswa Section with Toggle -->
        <li class="nav-item mb-2">
            <div class="accordion">
                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-transparent shadow-none text-dark d-flex align-items-center gap-3 py-2 px-3 rounded hover-bg" type="button" data-bs-toggle="collapse" data-bs-target="#masterMahasiswaCollapse">
                            <div class="icon-container bg-light-blue p-2 rounded">
                                <i class="bi bi-people-fill text-blue"></i>
                            </div>
                            <span>Master Mahasiswa</span>
                            <i class="bi bi-chevron-down ms-auto collapse-icon"></i>
                        </button>
                    </h2>
                    <div id="masterMahasiswaCollapse" class="accordion-collapse collapse">
                        <div class="accordion-body p-0">
                            <ul class="nav flex-column ps-5 py-2">
                                <li class="nav-item mb-1">
                                    <a class="nav-link text-dark d-flex align-items-center gap-2 py-2 ps-3 rounded hover-bg-sub" href="kelas.php">
                                        <i class="bi bi-house-door text-blue"></i>
                                        <span>Kelas</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-dark d-flex align-items-center gap-2 py-2 ps-3 rounded hover-bg-sub" href="matkul.php">
                                        <i class="bi bi-book text-blue"></i>
                                        <span>Mata Kuliah</span>
                                    </a>
                                </li>
                                <li class="nav-item mb-1">
                                    <a class="nav-link text-dark d-flex align-items-center gap-2 py-2 ps-3 rounded hover-bg-sub" href="mahasiswa.php">
                                        <i class="bi bi-person-circle text-blue"></i>
                                        <span>Data Mahasiswa</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        
        <li class="nav-item mb-2">
            <a class="nav-link text-dark d-flex align-items-center gap-3 py-2 px-3 rounded hover-bg" href="penilaian.php">
                <div class="icon-container bg-light-orange p-2 rounded">
                    <i class="bi bi-journal-bookmark text-orange"></i>
                </div>
                <span>Penilaian</span>
            </a>
        </li>
        
        <li class="nav-item mb-2">
            <a class="nav-link text-dark d-flex align-items-center gap-3 py-2 px-3 rounded hover-bg" href="nilai.php">
                <div class="icon-container bg-light-teal p-2 rounded">
                    <i class="bi bi-clipboard-data text-teal"></i>
                </div>
                <span>Rekap Nilai</span>
            </a>
        </li>
    </ul>
</div>

<style>
    :root {
        --red: #dc3545;
        --light-red: #ffe6e8;
        --blue: #0d6efd;
        --light-blue: #e7f1ff;
        --orange: #fd7e14;
        --light-orange: #fff3e8;
        --teal: #20c997;
        --light-teal: #e6fcf5;
    }
    
    .sidebar {
        display: flex;
        flex-direction: column;
        box-shadow: 2px 0 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .hover-bg:hover {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .hover-bg-sub:hover {
        background-color: #f1f1f1;
        transform: translateX(3px);
        transition: all 0.2s ease;
    }
    
    .nav-link.active {
        background-color: #f1f1f1 !important;
        border-left: 4px solid var(--blue);
        font-weight: 600;
    }
    
    .nav-link.active .icon-container {
        background-color: var(--light-blue) !important;
    }
    
    .nav-link.active i {
        color: var(--blue) !important;
    }
    
    .icon-container {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .accordion-button:not(.collapsed) .collapse-icon {
        transform: rotate(0deg);
    }
    
    .accordion-button.collapsed .collapse-icon {
        transform: rotate(-90deg);
    }
    
    .collapse-icon {
        transition: transform 0.3s ease;
    }
    
    /* Custom accordion styling */
    .accordion-button {
        padding: 0;
    }
    
    .accordion-button::after {
        display: none;
    }
    
    .accordion-body {
        padding: 0;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-expand Master Mahasiswa if on one of its child pages
        const currentPath = window.location.pathname.split('/').pop();
        const masterPages = ['mahasiswa.php', 'kelas.php', 'matkul.php'];
        
        if (masterPages.includes(currentPath)) {
            const collapseElement = document.getElementById('masterMahasiswaCollapse');
            const bsCollapse = new bootstrap.Collapse(collapseElement, {
                toggle: false
            });
            bsCollapse.show();
        }
    });
</script>