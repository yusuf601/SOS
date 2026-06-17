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

<!-- Settings toggle checkbox (pure-CSS modal control) -->
<input type="checkbox" id="settings-toggle" class="settings-toggle">

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
                            <ul class="sidebar-submenu-list">
                                <li class="sidebar-submenu-item">
                                    <a href="/rpl/public/index.php?action=bank_modul">
                                        <span>Bank Modul</span>
                                        <span class="sidebar-menu-item-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        </span>
                                    </a>
                                </li>
                            </ul>
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
                            <a href="/rpl/public/index.php?action=sanggah_form">
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
                        <?php if ($role === 'Dosen'): ?>
                            <!-- Dosen Menu -->
                            <li class="sidebar-menu-item">
                                <a href="/rpl/public/index.php?action=dashboard_dosen">
                                    <span>Dashboard</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                                    </span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="/rpl/public/index.php?action=my_classes">
                                    <span>Buat Kelas</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                    </span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="/rpl/public/index.php?action=data_kelompok">
                                    <span>Buat Kelompok</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                    </span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="/rpl/public/index.php?action=upload_modul">
                                    <span>Upload Modul</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                    </span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="/rpl/public/index.php?action=presensi">
                                    <span>Monitoring Kelas</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    </span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item active">
                                <a href="/rpl/public/index.php?action=kelulusan">
                                    <span>Export Rekapitulasi</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                    </span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="/rpl/public/index.php?action=sanggah_nilai">
                                    <span>Tinjau Sanggahan</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                    </span>
                                </a>
                            </li>
                        <?php else: ?>
                            <!-- Asisten Menu -->
                            <li class="sidebar-menu-item">
                                <a href="/rpl/public/index.php?action=dashboard_asisten">
                                    <span>Dashboard</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
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
                                <a href="/rpl/public/index.php?action=verifikasi_tugas">
                                    <span>Verifikasi Tugas</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                                    </span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item active">
                                <a href="/rpl/public/index.php?action=kelulusan">
                                    <span>Input Nilai</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                    </span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="/rpl/public/index.php?action=sanggah_nilai">
                                    <span>Tinjau Sanggahan</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                    </span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div>
            <div class="sidebar-divider"></div>
            <ul class="sidebar-menu-list">
                <li class="sidebar-menu-item">
                    <label for="settings-toggle" class="settings-open-label">
                        <span>Pengaturan</span>
                        <span class="sidebar-menu-item-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        </span>
                    </label>
                </li>
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
                <?php if (empty($studentClassesData)): ?>
                    <section class="kelulusan-card" style="text-align: center; padding: 48px; border-radius: 20px; border: 1px solid #E2E8F0; max-width: 1078px;">
                        <div style="color: #94A3B8; margin-bottom: 16px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        </div>
                        <h3 class="kelulusan-title" style="margin-bottom: 8px;">Belum Terdaftar di Kelas</h3>
                        <p style="color: #64748B;">Anda belum didaftarkan di kelas praktikum apa pun. Hubungi dosen pembimbing atau asisten praktikum.</p>
                    </section>
                <?php else: ?>
                    
                    <!-- Course status cards -->
                    <?php foreach ($studentClassesData as $cData): ?>
                        <?php 
                        $isLulus = ($cData['status'] === 'Lulus');
                        $cardBg = $isLulus ? '#EAF3DE' : '#FCEBEB';
                        $cardStroke = $isLulus ? '#C0DD97' : '#FF9595';
                        $badgeColor = $isLulus ? '#27500A' : '#791F1F';
                        $classColor = $isLulus ? '#3B6D11' : '#B72D2D';
                        $statusText = $isLulus ? 'LULUS' : 'MENGULANG';
                        ?>
                        <div class="graduation-status-card" style="background-color: <?= $cardBg ?>; border: 1px solid <?= $cardStroke ?>; box-shadow: 0 4px 4px rgba(0, 0, 0, 0.05); padding: 20px 24px; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; max-width: 1078px; height: 96px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <?php if ($isLulus): ?>
                                    <svg width="33" height="33" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" fill="#27500E" />
                                        <path d="M8 12L11 15L16 9" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php else: ?>
                                    <svg width="33" height="33" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" fill="#791F1F" />
                                        <path d="M8 8L16 16M16 8L8 16" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php endif; ?>
                                
                                <div style="display: flex; flex-direction: column; gap: 4px; text-align: left;">
                                    <span style="font-size: 20px; font-weight: bold; color: <?= $badgeColor ?>; line-height: 1.2;"><?= $statusText ?></span>
                                    <span style="font-size: 16px; font-weight: 500; color: <?= $classColor ?>;"><?= htmlspecialchars($cData['name']) ?></span>
                                </div>
                            </div>
                            
                            <div style="text-align: right; display: flex; align-items: center; gap: 16px;">
                                <span style="font-size: 13px; font-weight: 500; color: #7E7E7E;">Nilai Akhir</span>
                                <span style="font-size: 40px; font-weight: bold; color: #000000; line-height: 1; min-width: 59px; text-align: center;"><?= $cData['nilai_akhir'] ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Detail Komponen Nilai Table -->
                    <section class="kelulusan-card" style="padding: 24px; border-radius: 20px; max-width: 1078px; margin-top: 32px; border: 0.5px solid rgba(0, 0, 0, 0.1);">
                        <h3 class="kelulusan-title" style="font-size: 20px; font-weight: 600; color: #000000; margin-bottom: 24px;">Detail Komponen Nilai</h3>
                        <div class="table-responsive">
                            <table class="custom-table" style="width: 100%;">
                                <thead>
                                    <tr style="border-bottom: 1.5px solid #00000033;">
                                        <th style="font-weight: 600; color: rgba(0,0,0,0.5); padding: 16px 24px; border: none; font-size: 14px; background: none;">Kelas</th>
                                        <th style="font-weight: 600; color: rgba(0,0,0,0.5); padding: 16px 24px; border: none; text-align: center; font-size: 14px; background: none;">Presensi (30%)</th>
                                        <th style="font-weight: 600; color: rgba(0,0,0,0.5); padding: 16px 24px; border: none; text-align: center; font-size: 14px; background: none;">Tugas (70%)</th>
                                        <th style="font-weight: 600; color: rgba(0,0,0,0.5); padding: 16px 24px; border: none; text-align: center; font-size: 14px; background: none;">Nilai Akhir</th>
                                        <th style="font-weight: 600; color: rgba(0,0,0,0.5); padding: 16px 24px; border: none; text-align: center; font-size: 14px; background: none;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($studentClassesData as $cData): ?>
                                        <tr style="border-bottom: 0.5px solid #00000022;">
                                            <td style="padding: 16px 24px; font-weight: 500; font-size: 14px; color: #000000;"><?= htmlspecialchars($cData['name']) ?></td>
                                            <td style="padding: 16px 24px; font-weight: 500; font-size: 14px; text-align: center; color: #000000;"><?= $cData['presensi'] ?>%</td>
                                            <td style="padding: 16px 24px; font-weight: 500; font-size: 14px; text-align: center; color: #000000;"><?= $cData['tugas'] ?></td>
                                            <td style="padding: 16px 24px; font-weight: 600; font-size: 14px; text-align: center; color: #000000;"><?= $cData['nilai_akhir'] ?></td>
                                            <td style="padding: 16px 24px; font-weight: 600; text-align: center;">
                                                <?php if ($cData['status'] === 'Lulus'): ?>
                                                    <span style="color: #27500A; background-color: #EAF3DE; padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700;">Lulus</span>
                                                <?php else: ?>
                                                    <span style="color: #791F1F; background-color: #FCEBEB; padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700;">Mengulang</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

            <?php else: ?>
                <!-- DOSEN / ASISTEN VIEW -->
                
                <?php 
                // Toggle view based on view_recap parameter
                $viewRecap = isset($_GET['view_recap']) && $_GET['view_recap'] == 1;
                ?>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; max-width: 1078px; width: 100%;">
                    <h2 class="kelulusan-title" style="margin: 0; font-size: 28px; font-weight: 700; color: #1E293B;">
                        <?= $viewRecap ? 'Rekapitulasi Kelulusan' : 'Input Nilai' ?>
                    </h2>
                    <a href="/rpl/public/index.php?action=kelulusan<?= $viewRecap ? '' : '&view_recap=1' ?>" 
                       style="background-color: var(--sidebar-bg); color: #FFFFFF; font-size: 14px; font-weight: 600; text-decoration: none; padding: 10px 20px; border-radius: 8px; transition: opacity 0.2s;" 
                       onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1;">
                        <?= $viewRecap ? '← Kembali ke Input Nilai' : 'Lihat Rekapitulasi Kelulusan' ?>
                    </a>
                </div>

                <?php if ($viewRecap): ?>
                    <!-- REKAPITULASI KELULUSAN VIEW -->
                    <section class="kelulusan-card" style="background-color: #FFFFFF; border-radius: 20px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); padding: 24px 32px; width: 100%; max-width: 1078px;">
                        <div class="search-bar-container" style="margin-bottom: 24px;">
                            <input type="text" id="studentSearch" class="search-input" placeholder="Cari NIM atau Nama..." onkeyup="filterStudentsTable()" style="width: 100%; max-width: 320px; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 14px;">
                        </div>

                        <div class="table-responsive">
                            <table class="custom-table" id="kelulusanTable" style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="border-bottom: 1.5px solid rgba(0, 0, 0, 0.1);">
                                        <th style="font-weight: 600; color: rgba(0,0,0,0.5); padding: 16px; border: none; font-size: 14px;">NIM</th>
                                        <th style="font-weight: 600; color: rgba(0,0,0,0.5); padding: 16px; border: none; font-size: 14px;">Nama Lengkap</th>
                                        <th style="font-weight: 600; color: rgba(0,0,0,0.5); padding: 16px; border: none; font-size: 14px;">Kelas</th>
                                        <th style="font-weight: 600; color: rgba(0,0,0,0.5); padding: 16px; border: none; font-size: 14px;">Nilai Akhir (Rerata)</th>
                                        <th style="font-weight: 600; color: rgba(0,0,0,0.5); padding: 16px; border: none; font-size: 14px;">Status Kelulusan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($finalGrades)): ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; color: #94A3B8; padding: 24px;">Belum ada data nilai akhir mahasiswa yang dihitung.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($finalGrades as $row): ?>
                                            <tr class="student-row" style="border-bottom: 0.5px solid rgba(0, 0, 0, 0.08);">
                                                <td class="search-nim" style="padding: 16px; font-size: 13px; font-weight: 500;"><?= htmlspecialchars($row['NIM']) ?></td>
                                                <td class="search-nama" style="padding: 16px; font-size: 13px; font-weight: 600;"><?= htmlspecialchars($row['Nama_Mahasiswa']) ?></td>
                                                <td style="padding: 16px; font-size: 13px;"><?= htmlspecialchars($row['Nama_Kelas']) ?></td>
                                                <td style="padding: 16px; font-size: 15px; font-weight: 700;"><?= htmlspecialchars($row['Nilai_Akhir']) ?></td>
                                                <td style="padding: 16px;">
                                                    <span class="badge-status badge-<?= strtolower($row['Status_Kelulusan']) ?>" style="padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; display: inline-block; text-align: center; background-color: <?= strtolower($row['Status_Kelulusan']) === 'lulus' ? '#EAF3DE' : '#FCEBEB' ?>; color: <?= strtolower($row['Status_Kelulusan']) === 'lulus' ? '#27500A' : '#791F1F' ?>;">
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

                <?php else: ?>
                    <!-- SPLIT LAYOUT INPUT NILAI -->
                    
                    <!-- Feedback Success/Error Alert -->
                    <?php if (isset($_SESSION['grade_success'])): ?>
                        <div class="alert alert-success" style="background-color: #DEF7EC; color: #03543F; border: 1px solid #BCF0DA; padding: 12px 20px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 600; max-width: 1078px;">
                            <?= htmlspecialchars($_SESSION['grade_success']) ?>
                        </div>
                        <?php unset($_SESSION['grade_success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['grade_error'])): ?>
                        <div class="alert alert-error" style="background-color: #FDE8E8; color: #9B1C1C; border: 1px solid #FBD5D5; padding: 12px 20px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 600; max-width: 1078px;">
                            <?= htmlspecialchars($_SESSION['grade_error']) ?>
                        </div>
                        <?php unset($_SESSION['grade_error']); ?>
                    <?php endif; ?>

                    <div style="display: flex; gap: 24px; width: 100%; max-width: 1078px; align-items: flex-start;">
                        
                        <!-- Left Panel: Antrian Penilaian -->
                        <div style="width: 38%; background-color: #FFFFFF; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); padding: 20px; border: 0.5px solid rgba(0,0,0,0.1);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                <h3 style="font-size: 16px; font-weight: 700; color: #000000;">Antrian Penilaian</h3>
                                <span style="font-size: 12px; font-weight: 600; color: #797979;"><?= $pendingCount ?> belum dinilai</span>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 10px; max-height: 520px; overflow-y: auto; padding-right: 4px;">
                                <?php if (empty($submissionsQueue)): ?>
                                    <div style="text-align: center; color: #797979; padding: 32px 16px; font-size: 13px;">
                                        Tidak ada tugas mahasiswa untuk dinilai.
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($submissionsQueue as $sub): 
                                        $isSelected = $selectedSubmission && (int)$sub['ID_Pengumpulan'] === (int)$selectedSubmission['ID_Pengumpulan'];
                                        $hasGrade = !empty($sub['Nilai_Angka']) && $sub['Nilai_Angka'] > 0.00;
                                        
                                        // Initials
                                        $nameParts = explode(' ', $sub['Nama_Mahasiswa']);
                                        $initials = '';
                                        foreach ($nameParts as $part) {
                                            $initials .= strtoupper(substr($part, 0, 1));
                                        }
                                        $initials = substr($initials, 0, 2);
                                    ?>
                                        <a href="/rpl/public/index.php?action=kelulusan&pengumpulan_id=<?= $sub['ID_Pengumpulan'] ?>" 
                                           style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; border: 1px solid <?= $isSelected ? '#E2E8F0' : '#F1F5F9' ?>; background-color: <?= $isSelected ? '#EAF3DE' : '#FFFFFF' ?>; transition: all 0.2s ease;">
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #7C94B8; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0;">
                                                <?= htmlspecialchars($initials) ?>
                                            </div>
                                            <div style="flex-grow: 1; min-width: 0; text-align: left;">
                                                <div style="font-size: 14px; font-weight: 700; color: #1E293B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    <?= htmlspecialchars($sub['Nama_Mahasiswa']) ?>
                                                </div>
                                                <div style="font-size: 11px; color: #64748B; margin-top: 2px;">
                                                    <?= htmlspecialchars($sub['Nama_Kelompok']) ?> • <?= date('d M y', strtotime($sub['Waktu_Submit'])) ?>
                                                </div>
                                            </div>
                                            <?php if ($hasGrade): ?>
                                                <div style="background-color: #27500A; color: #FFFFFF; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700;" title="Sudah dinilai: <?= htmlspecialchars($sub['Nilai_Angka']) ?>">
                                                    ✓
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Right Panel: Grading Form -->
                        <div style="flex-grow: 1; background-color: #FFFFFF; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); padding: 24px; border: 0.5px solid rgba(0,0,0,0.1);">
                            <?php if (!$selectedSubmission): ?>
                                <div style="text-align: center; color: #797979; padding: 48px;">
                                    Pilih mahasiswa di panel kiri untuk memulai penilaian.
                                </div>
                            <?php else: 
                                // Selected student initials
                                $selParts = explode(' ', $selectedSubmission['Nama_Mahasiswa']);
                                $selInitials = '';
                                foreach ($selParts as $part) {
                                    $selInitials .= strtoupper(substr($part, 0, 1));
                                }
                                $selInitials = substr($selInitials, 0, 2);
                            ?>
                                <!-- Student Header -->
                                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #E2E8F0;">
                                    <div style="width: 50px; height: 50px; border-radius: 50%; background-color: #7C94B8; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700;">
                                        <?= htmlspecialchars($selInitials) ?>
                                    </div>
                                    <div style="flex-grow: 1; text-align: left;">
                                        <div style="display: flex; align-items: center; justify-content: space-between;">
                                            <h4 style="font-size: 16px; font-weight: 700; color: #1E293B;"><?= htmlspecialchars($selectedSubmission['Nama_Mahasiswa']) ?></h4>
                                            <span style="font-size: 12px; font-weight: 600; color: #64748B;"><?= htmlspecialchars($selectedSubmission['Judul_Modul']) ?></span>
                                        </div>
                                        <div style="font-size: 12px; color: #64748B; margin-top: 4px;">
                                            <?= htmlspecialchars($selectedSubmission['NIM_Mahasiswa']) ?> • <?= htmlspecialchars($selectedSubmission['Nama_Kelompok']) ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- File bar -->
                                <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                                    <div style="display: flex; align-items: center; gap: 12px; text-align: left; min-width: 0;">
                                        <svg style="color: #791F1F; width: 24px; height: 24px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <div style="min-width: 0;">
                                            <div style="font-size: 13px; font-weight: 600; color: #1E293B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <?= htmlspecialchars($selectedSubmission['File_Tugas']) ?>
                                            </div>
                                            <div style="font-size: 11px; color: #64748B; margin-top: 2px;">
                                                Diunggah <?= date('d M, H:i', strtotime($selectedSubmission['Waktu_Submit'])) ?> WITA
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <a href="/rpl/public/index.php?action=view_tugas&file=<?= urlencode($selectedSubmission['File_Tugas']) ?>" 
                                       target="_blank"
                                       class="btn-view" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; background-color: #FFFFFF; border: 1px solid #CBD5E1; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; color: #1E293B;">
                                        <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        Lihat
                                    </a>
                                </div>

                                <!-- Form -->
                                <form action="/rpl/public/index.php?action=submit_grade" method="POST" style="text-align: left;">
                                    <input type="hidden" name="id_pengumpulan" value="<?= $selectedSubmission['ID_Pengumpulan'] ?>">
                                    <!-- Preserve current selection on redirect -->
                                    <input type="hidden" name="redirect_url" value="/rpl/public/index.php?action=kelulusan&pengumpulan_id=<?= $selectedSubmission['ID_Pengumpulan'] ?>">
                                    
                                    <!-- Preset status to Selesai as grading is being done -->
                                    <input type="hidden" name="status_tugas" value="Selesai">

                                    <div class="form-group" style="margin-bottom: 20px;">
                                        <label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Nilai (0-100)</label>
                                        <input type="number" step="0.01" min="0" max="100" name="nilai_angka" class="form-control" placeholder="Masukkan nilai" value="<?= $selectedSubmission['Nilai_Angka'] > 0.00 ? htmlspecialchars($selectedSubmission['Nilai_Angka']) : '' ?>" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 14px;" required>
                                    </div>

                                    <div class="form-group" style="margin-bottom: 24px;">
                                        <label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Feedback/Catatan untuk Mahasiswa</label>
                                        <textarea name="feedback" class="form-control" placeholder="Tulis catatan atau komentar untuk mahasiswa ini..." style="width: 100%; min-height: 120px; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 14px; resize: vertical;"><?= htmlspecialchars($selectedSubmission['Feedback'] ?? '') ?></textarea>
                                    </div>

                                    <button type="submit" class="btn-submit" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background-color: #27500A; border: none; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 600; color: #FFFFFF; cursor: pointer; transition: opacity 0.2s;">
                                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                            <polyline points="7 3 7 8 15 8"></polyline>
                                        </svg>
                                        Simpan Nilai
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Bottom Notice banner -->
                    <div style="background-color: #E0E7FF; border: 1px solid #C7D2FE; border-radius: 12px; padding: 14px 20px; display: flex; align-items: center; gap: 12px; text-align: left; max-width: 1078px; width: 100%; margin-top: 24px;">
                        <svg style="color: #4F46E5; width: 22px; height: 22px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span style="font-size: 13px; font-weight: 600; color: #3730A3;">Nilai tidak bisa diubah sendiri setelah 3×24 jam. Hubungi dosen jika perlu koreksi</span>
                    </div>

                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- ====== Settings Modal ====== -->
<div class="settings-backdrop">
    <div class="settings-modal">
        <div class="settings-header">
            <div class="settings-header-left">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                <span class="settings-title">Pengaturan Akun</span>
            </div>
            <label for="settings-toggle" class="settings-close-btn" title="Tutup">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </label>
        </div>
        <div class="settings-tabs">
            <span class="settings-tab">Profil</span>
            <span class="settings-tab">Keamanan</span>
            <span class="settings-tab settings-tab-active">Notifikasi</span>
        </div>
        <div class="settings-body">
            <p class="settings-tab-desc">Atur jenis notifikasi yang ingin kamu terima</p>
            <div class="notif-item">
                <div class="notif-info">
                    <span class="notif-title">Pembaruan Nilai Akademik</span>
                    <span class="notif-desc">Notifikasi ketika nilai baru dipublikasikan oleh dosen atau asisten dosen.</span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="notif-item">
                <div class="notif-info">
                    <span class="notif-title">Pengingat Batas Waktu Tugas</span>
                    <span class="notif-desc">Notifikasi satu hari sebelum batas waktu pengumpulan tugas berakhir.</span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="notif-item">
                <div class="notif-info">
                    <span class="notif-title">Tanggapan atas Sanggahan</span>
                    <span class="notif-desc">Notifikasi ketika dosen atau asisten dosen memberikan tanggapan terhadap sanggahan yang diajukan.</span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="notif-item">
                <div class="notif-info">
                    <span class="notif-title">Pemberitahuan Modul Baru</span>
                    <span class="notif-desc">Notifikasi ketika dosen mengunggah atau menerbitkan modul pembelajaran baru.</span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>
</div>

</body>
</html>
