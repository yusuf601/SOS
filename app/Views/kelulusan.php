<?php
// ==============================================================================
// EduLab UHO - Status Kelulusan View (Student & Staff)
// ==============================================================================

$fullName = $_SESSION['name'] ?? 'Guest';
$role = $_SESSION['active_role'] ?? 'Mahasiswa';

$words = explode(" ", $fullName);
$initials = "";
foreach ($words as $w) {
    $initials .= strtoupper($w[0] ?? '');
}
$initials = substr($initials, 0, 2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Kelulusan - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        .kelulusan-card {
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .kelulusan-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-color-dark);
            margin-bottom: 20px;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-lulus { background-color: rgba(22, 163, 74, 0.1); color: #16A34A; }
        .badge-mengulang { background-color: rgba(220, 38, 38, 0.1); color: #DC2626; }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .custom-table th {
            padding: 14px 16px;
            background-color: #F8FAFC;
            color: #475569;
            font-weight: 600;
            border-bottom: 2px solid #E2E8F0;
        }

        .custom-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #E2E8F0;
            color: #1E293B;
            font-weight: 500;
        }

        .custom-table tr:hover {
            background-color: #F8FAFC;
        }

        /* Certificate-like Seal or Alert Box for Graduation */
        .graduation-banner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 48px 24px;
            border-radius: 20px;
            border: 2px dashed #E2E8F0;
            margin-bottom: 24px;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        .graduation-banner.lulus {
            background-color: rgba(22, 163, 74, 0.03);
            border-color: #16A34A;
        }

        .graduation-banner.mengulang {
            background-color: rgba(220, 38, 38, 0.03);
            border-color: #DC2626;
        }

        .seal-icon-wrapper {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }

        .seal-icon-wrapper.lulus {
            background-color: #16A34A;
            color: white;
        }

        .seal-icon-wrapper.mengulang {
            background-color: #DC2626;
            color: white;
        }

        .graduation-status-text {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .graduation-status-text.lulus {
            color: #16A34A;
        }

        .graduation-status-text.mengulang {
            color: #DC2626;
        }

        .graduation-desc {
            font-size: 16px;
            color: #475569;
            max-width: 500px;
            line-height: 1.6;
            margin-bottom: 24px;
            font-weight: 500;
        }

        .stats-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 600px;
            margin-top: 10px;
        }

        .stat-mini-card {
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        }

        .stat-mini-val {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .stat-mini-lbl {
            font-size: 13px;
            color: #64748B;
            font-weight: 600;
        }

        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

        .search-bar-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-end;
        }

        .search-input {
            width: 250px;
            height: 40px;
            padding: 0 16px;
            border-radius: 8px;
            border: 1px solid #CBD5E1;
            font-size: 14px;
            outline: none;
        }

        .search-input:focus {
            border-color: var(--btn-primary);
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <!-- Sidebar Brand -->
            <div class="sidebar-header">
                <img src="/rpl/public/assets/images/logo_uho.png" alt="Logo UHO" class="sidebar-logo">
                <span class="sidebar-brand-name">EduLab</span>
            </div>

            <!-- User profile summary -->
            <div class="sidebar-user-card">
                <div class="sidebar-user-name"><?= htmlspecialchars($fullName) ?></div>
                <div class="sidebar-user-role"><?= htmlspecialchars($role) ?></div>
            </div>

            <!-- Menu Navigation -->
            <nav>
                <div class="sidebar-menu-title">Menu Utama</div>
                <ul class="sidebar-menu-list">
                    <?php if ($role === 'Mahasiswa'): ?>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=dashboard_student">
                                <span>Dashboard</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=my_classes">
                                <span>Kelas Saya</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=upload_tugas">
                                <span>Upload Tugas</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=sanggah_nilai">
                                <span>Lihat Nilai</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=presensi">
                                <span>Data Presensi</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=sanggah_nilai">
                                <span>Sanggah Nilai</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item active">
                            <a href="/rpl/public/index.php?action=kelulusan">
                                <span>Status Kelulusan</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                </span>
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Dosen / Asisten Menu -->
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=dashboard_<?= strtolower($role) ?>">
                                <span>Dashboard</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=my_classes">
                                <span>Kelas Saya</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=presensi">
                                <span>Input Presensi</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=sanggah_nilai">
                                <span>Tanggapan Sanggah</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item active">
                            <a href="/rpl/public/index.php?action=kelulusan">
                                <span>Status Kelulusan</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                </span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div>
            <div class="sidebar-divider"></div>
            <ul class="sidebar-menu-list">
                <li class="sidebar-menu-item">
                    <a href="/rpl/public/index.php?action=logout" style="color: #FF8A8A;">
                        <span>Keluar</span>
                        <span class="sidebar-menu-item-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Workspace -->
    <main class="main-workspace">
        <!-- Top Navbar -->
        <header class="workspace-navbar">
            <h2 class="navbar-title">Status Kelulusan</h2>
            <div class="navbar-profile">
                <button type="button" style="background:none; border:none; color:white; cursor:pointer;" aria-label="Notifikasi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </button>
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">
            
            <?php if ($role === 'Mahasiswa'): ?>
                <!-- STUDENT KELULUSAN VIEW -->
                <?php if (!$classInfo): ?>
                    <section class="kelulusan-card" style="text-align: center; padding: 48px;">
                        <div style="color: #94A3B8; margin-bottom: 16px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        </div>
                        <h3 class="kelulusan-title" style="margin-bottom: 8px;">Belum Terdaftar di Kelas</h3>
                        <p style="color: #64748B;">Anda belum didaftarkan di kelas praktikum apa pun. Hubungi dosen pembimbing atau asisten praktikum.</p>
                    </section>
                <?php else: ?>
                    <?php 
                    $isLulus = ($finalGrades && $finalGrades['Status_Kelulusan'] === 'Lulus');
                    $bannerClass = $isLulus ? 'lulus' : 'mengulang';
                    $sealClass = $isLulus ? 'lulus' : 'mengulang';
                    $statusText = $isLulus ? 'LULUS' : 'MENGULANG';
                    ?>
                    
                    <section class="kelulusan-card" style="padding: 24px;">
                        <div class="graduation-banner <?= $bannerClass ?>">
                            <div class="seal-icon-wrapper <?= $sealClass ?>">
                                <?php if ($isLulus): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                <?php endif; ?>
                            </div>
                            
                            <h2 class="graduation-status-text <?= $bannerClass ?>"><?= $statusText ?></h2>
                            <p class="graduation-desc">
                                Berdasarkan akumulasi pengerjaan tugas dan tingkat absensi di kelas <strong><?= htmlspecialchars($classInfo['Nama_Kelas']) ?></strong>, Anda dinyatakan <strong><?= strtolower($statusText) ?></strong> praktikum.
                            </p>
                            
                            <div class="stats-summary-grid">
                                <div class="stat-mini-card">
                                    <div class="stat-mini-val" style="color: var(--btn-primary);"><?= htmlspecialchars($progress['average_score']) ?></div>
                                    <div class="stat-mini-lbl">Rerata Nilai Tugas (Min: 70)</div>
                                </div>
                                <div class="stat-mini-card">
                                    <div class="stat-mini-val" style="color: var(--btn-primary);"><?= htmlspecialchars($attendanceRate) ?>%</div>
                                    <div class="stat-mini-lbl">Tingkat Absensi (Min: 75%)</div>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

            <?php else: ?>
                <!-- DOSEN / ASISTEN KELULUSAN VIEW -->
                <section class="kelulusan-card">
                    <h3 class="kelulusan-title">Rekapitulasi Kelulusan Kelas Praktikum</h3>
                    
                    <div class="search-bar-container">
                        <input type="text" id="studentSearch" class="search-input" placeholder="Cari NIM atau Nama..." onkeyup="filterStudentsTable()">
                    </div>

                    <div class="table-responsive">
                        <table class="custom-table" id="kelulusanTable">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Lengkap</th>
                                    <th>Kelas</th>
                                    <th>Nilai Akhir (Rerata)</th>
                                    <th>Status Kelulusan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($finalGrades)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: #94A3B8; padding: 24px;">Belum ada data nilai akhir mahasiswa yang dihitung.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($finalGrades as $row): ?>
                                        <tr class="student-row">
                                            <td class="search-nim"><?= htmlspecialchars($row['NIM']) ?></td>
                                            <td class="search-nama" style="font-weight: 600;"><?= htmlspecialchars($row['Nama_Mahasiswa']) ?></td>
                                            <td><?= htmlspecialchars($row['Nama_Kelas']) ?></td>
                                            <td style="font-size: 16px; font-weight: 700;"><?= htmlspecialchars($row['Nilai_Akhir']) ?></td>
                                            <td>
                                                <span class="badge-status badge-<?= strtolower($row['Status_Kelulusan']) ?>">
                                                    <?= htmlspecialchars($row['Status_Kelulusan']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <script>
                    function filterStudentsTable() {
                        var input = document.getElementById("studentSearch");
                        var filter = input.value.toLowerCase();
                        var rows = document.getElementsByClassName("student-row");

                        for (var i = 0; i < rows.length; i++) {
                            var nim = rows[i].getElementsByClassName("search-nim")[0].innerText.toLowerCase();
                            var nama = rows[i].getElementsByClassName("search-nama")[0].innerText.toLowerCase();
                            
                            if (nim.includes(filter) || nama.includes(filter)) {
                                rows[i].style.display = "";
                            } else {
                                rows[i].style.display = "none";
                            }
                        }
                    }
                </script>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>
