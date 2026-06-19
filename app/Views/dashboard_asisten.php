<?php
// ==============================================================================
// EduLab UHO - Dashboard Asisten View
// ==============================================================================

$fullName = $_SESSION['name'] ?? 'Guest';
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
    <title>Dashboard Asisten - EduLab UHO</title>
    <link class="style-link" rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* CSS Extension specifically for Lecturer/Assistant Layout */
        .submissions-card {
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .submissions-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-color-dark);
            margin-bottom: 20px;
        }

        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

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

        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-graded { background-color: rgba(22, 163, 74, 0.1); color: #16A34A; }
        .badge-pending { background-color: rgba(202, 138, 4, 0.1); color: #CA8A04; }
        .badge-revision { background-color: rgba(234, 88, 12, 0.1); color: #EA580C; }

        .btn-action-table {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 32px;
            padding: 0 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-grade {
            background-color: var(--btn-primary);
            color: white;
            box-shadow: 0 2px 6px rgba(54, 64, 135, 0.2);
        }

        .btn-grade:hover {
            background-color: #2b336b;
            transform: translateY(-1px);
        }

        .btn-download-sub {
            background-color: rgba(71, 85, 105, 0.08);
            color: #475569;
            margin-right: 6px;
        }

        .btn-download-sub:hover {
            background-color: rgba(71, 85, 105, 0.15);
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: modalFadeIn 0.3s ease;
            overflow: hidden;
        }

        @keyframes modalFadeIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            padding: 20px 24px;
            background-color: var(--btn-primary);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title-text {
            font-size: 18px;
            font-weight: 600;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            line-height: 1;
            padding: 0;
        }

        .modal-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-color-dark);
        }

        .form-control {
            height: 44px;
            padding: 0 12px;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--btn-primary);
        }

        textarea.form-control {
            height: 100px;
            padding: 12px;
            resize: none;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #E2E8F0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background-color: #F8FAFC;
        }

        .btn-cancel {
            height: 42px;
            padding: 0 20px;
            background-color: #E2E8F0;
            color: #475569;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>
<body>

<!-- Settings toggle checkbox (pure-CSS modal control) -->


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
                <div class="sidebar-user-role"><?= htmlspecialchars($_SESSION['active_role'] ?? 'Asisten') ?></div>
            </div>

            <!-- Menu Navigation -->
            <nav>
                <div class="sidebar-menu-title">Menu Utama</div>
                <ul class="sidebar-menu-list">
                    <li class="sidebar-menu-item active">
                        <a href="/rpl/public/index.php?action=dashboard_asisten">
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
                        <a href="/rpl/public/index.php?action=data_kelompok">
                            <span>Data Kelompok</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
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
                    <li class="sidebar-menu-item">
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
                </ul>
            </nav>
        </div>

        <div>
            <div class="sidebar-divider"></div>
            <ul class="sidebar-menu-list">
                <li class="sidebar-menu-item">
                    <a href="/rpl/public/index.php?action=pengaturan" class="settings-open-label" style="display: flex; justify-content: flex-start; gap: 10px; padding: 10px 16px; color: rgba(255, 255, 255, 0.8) !important; text-decoration: none; font-size: 15px; font-weight: 600; cursor: pointer; border-radius: 8px; transition: all 0.2s;">
                        <span>Pengaturan</span>
                        <span class="sidebar-menu-item-icon" style="order: -1; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        </span>
                    </a>
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
            <h2 class="navbar-title">Dashboard Asisten</h2>
            <div class="navbar-profile">
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">
            <!-- Welcome Banner -->
            <section class="welcome-banner" style="margin-bottom: 24px;">
                <h1 class="welcome-text" style="font-size: 36px; font-weight: 600; color: #000000; margin: 0 0 8px 0;">Halo, <?= htmlspecialchars(explode(" ", $fullName)[0]) ?>!</h1>
                <?php
                $todoCount = 0;
                if ($pendingAttendanceModule) $todoCount++;
                if ($pendingGradesInfo) $todoCount++;
                if ($disputesCount > 0) $todoCount++;
                ?>
                <p class="welcome-subtext" style="font-size: 24px; font-weight: 500; color: #8A8A8A; margin: 0;">
                    Ada <?= $todoCount ?> hal yang perlu diselesaikan hari ini.
                </p>
            </section>

            <!-- Success/Error Alert -->
            <?php if (isset($_SESSION['grade_success'])): ?>
                <div style="background-color: #DEF7EC; color: #03543F; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 600; border: 1px solid #BCF0DA; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(3, 84, 63, 0.05);">
                    <?= htmlspecialchars($_SESSION['grade_success']); unset($_SESSION['grade_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['grade_error'])): ?>
                <div style="background-color: #FEE2E2; color: #DC2626; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 600; border: 1px solid #FCA5A5; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.05);">
                    <?= htmlspecialchars($_SESSION['grade_error']); unset($_SESSION['grade_error']); ?>
                </div>
            <?php endif; ?>

            <!-- Open Pencil Layout: To-Do & Progress Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px; margin-bottom: 24px;">
                
                <!-- Card: Yang Perlu Dilakukan Sekarang -->
                <div style="background-color: #FFFFFF; border-radius: 12px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 16px;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #000000; margin: 0;">Yang Perlu Dilakukan Sekarang</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <!-- Todo 1: Presensi -->
                        <?php if ($pendingAttendanceModule): ?>
                            <div style="background-color: #FCEBEB; border-radius: 8px; padding: 16px; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 39px; height: 39px; background-color: #F7C1C1; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #790000; flex-shrink: 0;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                    </div>
                                    <div>
                                        <h4 style="font-size: 15px; font-weight: 500; color: #000000; margin: 0 0 2px 0;">Isi presensi <?= htmlspecialchars($pendingAttendanceModule['Judul_Modul']) ?></h4>
                                        <span style="font-size: 12px; color: #8C8C8C;">Belum diisi · Batas waktu segera</span>
                                    </div>
                                </div>
                                <a href="/rpl/public/index.php?action=presensi" style="background-color: #790000; color: #FFFFFF; font-size: 12px; font-weight: 600; text-decoration: none; padding: 8px 16px; border-radius: 8px; flex-shrink: 0; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1;">Isi Sekarang</a>
                            </div>
                        <?php endif; ?>

                        <!-- Todo 2: Penilaian Tugas -->
                        <?php if ($pendingGradesInfo): ?>
                            <div style="background-color: #FEF3C7; border-radius: 8px; padding: 16px; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 39px; height: 39px; background-color: #FDE68A; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #D97706; flex-shrink: 0;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                    </div>
                                    <div>
                                        <h4 style="font-size: 15px; font-weight: 500; color: #000000; margin: 0 0 2px 0;"><?= htmlspecialchars($pendingGradesInfo['count']) ?> tugas belum dinilai</h4>
                                        <span style="font-size: 12px; color: #8C8C8C;"><?= htmlspecialchars($pendingGradesInfo['Judul_Modul']) ?></span>
                                    </div>
                                </div>
                                <a href="#submissions-section" style="background-color: #D97706; color: #FFFFFF; font-size: 12px; font-weight: 600; text-decoration: none; padding: 8px 16px; border-radius: 8px; flex-shrink: 0; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1;">Nilai</a>
                            </div>
                        <?php endif; ?>

                        <!-- Todo 3: Disputes/Sanggahan -->
                        <?php if ($disputesCount > 0): ?>
                            <div style="background-color: #FCEBEB; border-radius: 8px; padding: 16px; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 39px; height: 39px; background-color: #F7C1C1; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #790000; flex-shrink: 0;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                    </div>
                                    <div>
                                        <h4 style="font-size: 15px; font-weight: 500; color: #000000; margin: 0 0 2px 0;"><?= htmlspecialchars($disputesCount) ?> sanggah menunggu responmu</h4>
                                        <span style="font-size: 12px; color: #8C8C8C;">Segera tanggapi keluhan mahasiswa</span>
                                    </div>
                                </div>
                                <a href="/rpl/public/index.php?action=sanggah_nilai" style="background-color: #790000; color: #FFFFFF; font-size: 12px; font-weight: 600; text-decoration: none; padding: 8px 16px; border-radius: 8px; flex-shrink: 0; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1;">Balas</a>
                            </div>
                        <?php endif; ?>

                        <!-- Fallback if nothing to do -->
                        <?php if ($todoCount === 0): ?>
                            <div style="text-align: center; color: #8C8C8C; padding: 24px; font-size: 14px; font-weight: 500;">
                                🎉 Kerja bagus! Semua tugas dan presensi telah diselesaikan hari ini.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card: Progres Penilaian -->
                <div style="background-color: #FFFFFF; border-radius: 15px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; gap: 16px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 600; color: #000000; margin: 0 0 4px 0;">Progres Penilaian <?= htmlspecialchars($activeModulTitle ?: 'Modul') ?></h3>
                        <span style="font-size: 14px; color: #9F9F9F; font-weight: 500;">Aktif memantau kelas Anda</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <?php if (empty($groupProgress)): ?>
                            <div style="font-size: 14px; color: #9F9F9F; text-align: center; padding: 20px;">Belum ada data progres kelompok.</div>
                        <?php else: ?>
                            <?php foreach ($groupProgress as $gp): 
                                $percent = $gp['total_students'] > 0 ? ($gp['graded_count'] / $gp['total_students']) * 100 : 0;
                            ?>
                                <div>
                                    <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 600; margin-bottom: 6px;">
                                        <span style="color: #9F9F9F;"><?= htmlspecialchars($gp['group_name']) ?> (<?= htmlspecialchars($gp['total_students']) ?> mahasiswa)</span>
                                        <span style="color: #000000;"><?= htmlspecialchars($gp['graded_count']) ?>/<?= htmlspecialchars($gp['total_students']) ?> dinilai</span>
                                    </div>
                                    <div style="width: 100%; height: 6px; background-color: #ECECEC; border-radius: 3px; overflow: hidden; position: relative;">
                                        <div style="width: <?= $percent ?>%; height: 100%; background-color: #364087; border-radius: 3px; transition: width 0.3s ease;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div style="border-top: 1px solid #ECECEC; padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; font-weight: 600; color: #9F9F9F;">Rata-rata nilai sejauh ini</span>
                        <span style="font-size: 24px; font-weight: 700; color: #364087;"><?= htmlspecialchars($averageScore) ?></span>
                    </div>
                </div>

            </div>

            <!-- Submissions Table Card -->
            <section class="submissions-card" id="submissions-section">
                <h3 class="submissions-title">Daftar Pengumpulan Tugas Mahasiswa</h3>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Mahasiswa</th>
                                <th>Kelas</th>
                                <th>Modul</th>
                                <th>Waktu Pengumpulan</th>
                                <th>Status</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($submissions)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #9B9B9B; padding: 24px;">Belum ada pengumpulan tugas mahasiswa di kelas Anda.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($submissions as $sub): 
                                    $statusClass = 'badge-pending';
                                    $statusText = 'Belum Dinilai';
                                    if (!empty($sub['Nilai_Angka']) || $sub['Status_Tugas'] !== NULL) {
                                        if ($sub['Status_Tugas'] === 'Selesai') {
                                            $statusClass = 'badge-graded';
                                            $statusText = 'Sudah Dinilai';
                                        } elseif ($sub['Status_Tugas'] === 'Revisi') {
                                            $statusClass = 'badge-revision';
                                            $statusText = 'Revisi';
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: #1E293B;"><?= htmlspecialchars($sub['Nama_Mahasiswa']) ?></div>
                                            <div style="font-size: 12px; color: #64748B;"><?= htmlspecialchars($sub['NIM_Mahasiswa']) ?></div>
                                        </td>
                                        <td><?= htmlspecialchars($sub['Nama_Kelas']) ?></td>
                                        <td><?= htmlspecialchars($sub['Judul_Modul']) ?></td>
                                        <td><?= date('d M Y, H:i', strtotime($sub['Waktu_Submit'])) ?> WITA</td>
                                        <td>
                                            <span class="badge-status <?= $statusClass ?>"><?= $statusText ?></span>
                                        </td>
                                        <td>
                                            <span style="font-weight: 700; color: #1E293B;"><?= !empty($sub['Nilai_Angka']) ? htmlspecialchars($sub['Nilai_Angka']) : '-' ?></span>
                                        </td>
                                        <td>
                                            <!-- Download student file -->
                                            <a href="/rpl/public/index.php?action=view_tugas&file=<?= urlencode($sub['File_Tugas']) ?>" class="btn-action-table btn-download-sub" download>
                                                Unduh
                                            </a>
                                            <!-- Open Grade Modal -->
                                            <button type="button" class="btn-action-table btn-grade" onclick="openGradeModal(<?= $sub['ID_Pengumpulan'] ?>, '<?= htmlspecialchars($sub['Nama_Mahasiswa']) ?>', '<?= htmlspecialchars($sub['Judul_Modul']) ?>', '<?= $sub['Nilai_Angka'] ?? '' ?>', '<?= htmlspecialchars($sub['Feedback'] ?? '') ?>', '<?= $sub['Status_Tugas'] ?? 'Selesai' ?>')">
                                                Nilai
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>

<!-- Grading Modal -->
<div class="modal" id="gradeModal" aria-hidden="true" role="dialog">
    <div class="modal-content">
        <header class="modal-header">
            <h3 class="modal-title-text" id="modalTitle">Penilaian Tugas</h3>
            <button type="button" class="close-btn" onclick="closeGradeModal()">&times;</button>
        </header>
        <form id="gradeForm" action="/rpl/public/index.php?action=submit_grade" method="POST">
            <input type="hidden" name="id_pengumpulan" id="submitPengumpulanId" value="">
            <div class="modal-body">
                <div style="background-color: #F8FAFC; padding: 12px; border-radius: 8px; border: 1px solid #E2E8F0; margin-bottom: 12px;">
                    <div style="font-size: 13px; color: #64748B; font-weight: 500;">Mahasiswa:</div>
                    <div id="mhsName" style="font-size: 15px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">-</div>
                    <div style="font-size: 13px; color: #64748B; font-weight: 500;">Modul:</div>
                    <div id="modulTitle" style="font-size: 14px; font-weight: 600; color: #475569;">-</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="nilaiAngka">Nilai Angka (0 - 100):</label>
                    <input type="number" step="0.01" min="0" max="100" name="nilai_angka" id="nilaiAngka" class="form-control" placeholder="Contoh: 85.50" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="statusTugas">Status Tugas:</label>
                    <select name="status_tugas" id="statusTugas" class="form-control" style="padding: 0 8px;" required>
                        <option value="Selesai">Selesai (Lulus)</option>
                        <option value="Revisi">Revisi (Harus Diperbaiki)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="feedback">Umpan Balik (Feedback):</label>
                    <textarea name="feedback" id="feedback" class="form-control" placeholder="Tulis catatan atau saran perbaikan di sini..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeGradeModal()">Batal</button>
                <button type="submit" class="btn-submit" style="background-color: var(--btn-primary); color: white; border: none; padding: 0 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Simpan Nilai</button>
            </div>
        </form>
    </div>
</div>

<script>
    const gradeModal = document.getElementById('gradeModal');
    const submitPengumpulanId = document.getElementById('submitPengumpulanId');
    const mhsName = document.getElementById('mhsName');
    const modulTitle = document.getElementById('modulTitle');
    const nilaiAngka = document.getElementById('nilaiAngka');
    const statusTugas = document.getElementById('statusTugas');
    const feedback = document.getElementById('feedback');

    function openGradeModal(id, student, modul, currentNilai, currentFeedback, currentStatus) {
        submitPengumpulanId.value = id;
        mhsName.textContent = student;
        modulTitle.textContent = modul;
        nilaiAngka.value = currentNilai;
        feedback.value = currentFeedback;
        statusTugas.value = currentStatus || 'Selesai';

        gradeModal.style.display = 'flex';
        gradeModal.setAttribute('aria-hidden', 'false');
    }

    function closeGradeModal() {
        gradeModal.style.display = 'none';
        gradeModal.setAttribute('aria-hidden', 'true');
    }
</script>


    </div>
</div>

</body>
</html>
