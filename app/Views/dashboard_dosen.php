<?php
// ==============================================================================
// EduLab UHO - Dashboard Dosen View
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
    <title>Dashboard Dosen - EduLab UHO</title>
    <link class="style-link" rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* CSS Extension specifically for Dosen Grid Layout */
        .dosen-dashboard-grid {
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-top: 16px;
        }

        .widgets-section-horizontal {
            background-color: rgba(54, 64, 135, 0.66); /* Matches #364087A8 semi-transparent navy */
            border-radius: 15px;
            padding: 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 8px;
            align-items: start; /* Align widgets at the top instead of stretching */
        }

        .widgets-section-horizontal .widget-card {
            margin-bottom: 0;
            padding: 16px 20px; /* Reduced padding to fit content naturally */
        }

        .widgets-section-horizontal .widget-title {
            margin-bottom: 12px; /* Tighter title margin */
        }

        .widgets-section-horizontal .widget-list {
            gap: 12px; /* Tighter list gap */
        }

        @media (max-width: 768px) {
            .widgets-section-horizontal {
                grid-template-columns: 1fr;
            }
        }

        .class-card {
            background-color: #FFFFFF;
            border-radius: 16px;
            padding: 24px 24px 36px 24px; /* Space for absolute progress bar */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column !important;
            align-items: stretch;
            margin-bottom: 16px;
            border: 1px solid #F1F5F9;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative; /* Relative positioning for absolute progress bar */
            max-width: none !important; /* Override global max-width constraint */
            width: 100%;
        }

        .class-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        }

        .class-card-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .class-thumb {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background-color: #F1F5F9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF; /* White icon color */
            flex-shrink: 0;
        }

        .class-info {
            display: flex;
            flex-direction: column;
            gap: 6px;
            text-align: left;
        }

        .class-name {
            font-size: 20px;
            font-weight: 600;
            color: #000000;
            margin: 0;
            line-height: 1.2;
        }

        .class-meta {
            font-size: 16px;
            color: #9F9F9F;
            font-weight: 600;
        }

        .class-card-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-tag {
            padding: 4px 10px;
            border-radius: 30px; /* Pill border radius */
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }

        .badge-tag-danger {
            background-color: #FCEBEB;
            color: #791F1F;
        }

        .badge-tag-success {
            background-color: #EAF3DE;
            color: #27500A;
        }

        .btn-create-class {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 15px;
            font-weight: 600;
            color: #364087;
            text-decoration: none;
            transition: color 0.2s;
        }

        .btn-create-class:hover {
            color: #2b336b;
        }

        .widget-card {
            background-color: #FFFFFF;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #F1F5F9;
            margin-bottom: 24px;
        }

        .widget-title {
            font-size: 16px;
            font-weight: 600;
            color: #0F172A;
            margin-top: 0;
            margin-bottom: 16px;
        }

        .widget-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .widget-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .widget-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .widget-icon-green {
            background-color: #EAF3DE;
            color: #27500A;
        }

        .widget-icon-blue {
            background-color: #ABDAE866;
            color: #29316B;
        }

        .widget-icon-red {
            background-color: #FCEBEB;
            color: #C21111;
        }

        .widget-item-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .widget-item-title {
            font-size: 12px;
            font-weight: 600;
            color: #0F172A;
        }

        .widget-item-subtitle {
            font-size: 11px;
            color: #64748B;
        }

        .widget-divider {
            height: 1px;
            background-color: #F1F5F9;
            margin: 4px 0;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
        }

        .summary-name {
            font-size: 13px;
            font-weight: 500;
            color: #64748B;
        }

        .summary-status {
            font-size: 13px;
            font-weight: 700;
            color: #94A3B8;
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
                <div class="sidebar-user-role"><?= htmlspecialchars($_SESSION['active_role'] ?? 'Dosen') ?></div>
            </div>

            <!-- Menu Navigation -->
            <nav>
                <div class="sidebar-menu-title">Menu Utama</div>
                <ul class="sidebar-menu-list">
                    <li class="sidebar-menu-item active">
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
                        <a href="/rpl/public/index.php?action=verifikasi_tugas">
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
                    <li class="sidebar-menu-item">
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
                </ul>
            </nav>
        </div>

        <div>
            <div class="sidebar-divider"></div>
            <ul class="sidebar-menu-list">
                <li class="sidebar-menu-item">
                    <label for="settings-toggle" class="settings-open-label" style="display: flex; justify-content: flex-start; gap: 10px; padding: 10px 16px; color: rgba(255, 255, 255, 0.8) !important; text-decoration: none; font-size: 15px; font-weight: 600; cursor: pointer; border-radius: 8px; transition: all 0.2s;">
                        <span>Pengaturan</span>
                        <span class="sidebar-menu-item-icon" style="order: -1; flex-shrink: 0;">
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
            <h2 class="navbar-title">Dashboard Dosen</h2>
            <div class="navbar-profile">
                <button type="button" style="background:none; border:none; color:white; cursor:pointer;" aria-label="Notifikasi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </button>
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">
            <!-- Welcome Banner -->
            <section class="welcome-banner" style="margin-bottom: 24px;">
                <h1 class="welcome-text">Selamat datang, <?= htmlspecialchars($fullName) ?>!</h1>
                <p class="welcome-subtext">Memantau <?= htmlspecialchars($stats['total_classes'] ?? 0) ?> kelas aktif dengan total <?= htmlspecialchars($stats['total_students'] ?? 0) ?> mahasiswa semester ini.</p>
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

            <!-- Dosen Dashboard Grid -->
            <div class="dosen-dashboard-grid">
                <!-- Semua Kelas Aktif (Full Width) -->
                <div class="classes-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0;">Semua Kelas Aktif</h3>
                        <a href="#" class="btn-create-class" onclick="alert('Fitur Buat Kelas Baru tersedia di versi berikutnya.')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Buat Kelas Baru
                        </a>
                    </div>

                    <?php if (empty($classesDetail)): ?>
                        <div style="background-color: #FFFFFF; border-radius: 16px; padding: 32px; text-align: center; border: 1px solid #F1F5F9; color: #64748B;">
                            Belum ada kelas aktif yang terplot untuk Anda.
                        </div>
                    <?php else: ?>
                        <?php foreach ($classesDetail as $cdx => $cdetail): ?>
                            <?php
                            // Determine thumb color dynamically based on index to ensure exact match with Figma
                            if ($cdx == 0) {
                                $thumbColor = '#1F3A69'; // Dark Blue
                            } elseif ($cdx == 1) {
                                $thumbColor = '#0A5A48'; // Dark Green
                            } else {
                                $thumbColor = '#734308'; // Brown
                            }

                            // Parse meeting progress
                            $currentMeeting = 6;
                            $totalMeetings = 8;
                            if (preg_match('/(\d+)\s*\/\s*(\d+)/', $cdetail['pertemuan_progress'] ?? '', $matches)) {
                                $currentMeeting = (int)$matches[1];
                                $totalMeetings = (int)$matches[2];
                            } else {
                                $currentMeeting = (int)($cdetail['pertemuan_progress'] ?? 6);
                            }
                            $percent = ($totalMeetings > 0) ? ($currentMeeting / $totalMeetings) * 100 : 75;
                            ?>
                            <div class="class-card">
                                <div class="class-card-body-row" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; width: 100%;">
                                    <!-- Left: Icon & Center: Info -->
                                    <div style="display: flex; align-items: center; gap: 20px;">
                                        <div class="class-thumb" style="background-color: <?= $thumbColor ?>; color: #FFFFFF;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                        </div>
                                        <div class="class-info">
                                            <h4 class="class-name"><?= htmlspecialchars($cdetail['class_name']) ?></h4>
                                            <span class="class-meta">
                                                <?= htmlspecialchars($cdetail['schedule']) ?> &middot; 
                                                <?= htmlspecialchars($cdetail['student_count']) ?> mhs &middot; 
                                                Pertemuan <?= htmlspecialchars($cdetail['pertemuan_progress']) ?> &middot; 
                                                Asisten Dosen : <?= htmlspecialchars(explode(" ", $cdetail['assistant_name'])[0]) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <!-- Right: Badges -->
                                    <div class="class-card-right">
                                        <?php if ($cdetail['repeating_count'] > 0): ?>
                                            <span class="badge-tag badge-tag-danger"><?= htmlspecialchars($cdetail['repeating_count']) ?> mengulang</span>
                                        <?php endif; ?>
                                        
                                        <?php if ($cdetail['passed_count'] > 0): ?>
                                            <span class="badge-tag badge-tag-success"><?= htmlspecialchars($cdetail['passed_count']) ?> lulus</span>
                                        <?php endif; ?>
                                        
                                        <span class="badge-tag badge-tag-success">Aktif</span>
                                    </div>
                                </div>
                                <!-- Progress Bar positioned absolutely matching Figma (x=26, y=82.5, width=1040, card size 1096x95) -->
                                <div class="class-progress-container" style="position: absolute; left: 26px; right: 26px; bottom: 12px; height: 3px; background-color: #D8D8D8; border-radius: 999px; overflow: hidden;">
                                    <div class="class-progress-bar" style="width: <?= $percent ?>%; background-color: #364087; height: 100%; transition: width 0.3s ease;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Widgets Section (Bottom Horizontal Box) -->
                <div class="widgets-section-horizontal">
                    <!-- Widget: Aktivitas Terbaru -->
                    <div class="widget-card">
                        <h3 class="widget-title">Aktivitas Terbaru</h3>
                        <div class="widget-list">
                            <?php foreach ($activities as $index => $act): ?>
                                <?php if ($index > 0): ?>
                                    <div class="widget-divider"></div>
                                <?php endif; ?>
                                <div class="widget-item">
                                    <?php
                                    $iconClass = 'widget-icon-green';
                                    // Default: grade (dokumen + pulpen)
                                    $svgIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M4.8 5.6L4.8 4L12 4L12 5.6L4.8 5.6ZM4.8 8L4.8 6.4L12 6.4L12 8L4.8 8ZM7.2 16L2.4 16C1.73 16 1.17 15.77 0.7 15.3C0.23 14.83 0 14.27 0 13.6L0 11.2L2.4 11.2L2.4 0L14.4 0L14.4 7.22C14.13 7.19 13.86 7.2 13.59 7.25C13.32 7.3 13.05 7.38 12.8 7.5L12.8 1.6L4 1.6L4 11.2L8.8 11.2L7.2 12.8L1.6 12.8L1.6 13.6C1.6 13.83 1.68 14.02 1.83 14.17C1.98 14.32 2.17 14.4 2.4 14.4L7.2 14.4L7.2 16ZM8.8 16L8.8 13.54L13.22 9.14C13.34 9.02 13.47 8.93 13.62 8.88C13.77 8.83 13.91 8.8 14.06 8.8C14.22 8.8 14.37 8.83 14.52 8.89C14.67 8.95 14.8 9.04 14.92 9.16L15.66 9.9C15.77 10.02 15.85 10.15 15.91 10.3C15.97 10.45 16 10.59 16 10.74C16 10.89 15.97 11.04 15.92 11.19C15.87 11.34 15.78 11.48 15.66 11.6L11.26 16L8.8 16ZM10 14.8L10.76 14.8L13.18 12.36L12.82 11.98L12.44 11.62L10 14.04L10 14.8ZM12.82 11.98L12.44 11.62L13.18 12.36L12.82 11.98Z" /></svg>';
                                    if ($act['type'] === 'dispute') {
                                        $iconClass = 'widget-icon-blue';
                                        // dispute (balon chat)
                                        $svgIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M0 16L0 1.6C0 1.16 0.16 0.78 0.47 0.47C0.78 0.16 1.16 0 1.6 0L14.4 0C14.84 0 15.22 0.16 15.53 0.47C15.84 0.78 16 1.16 16 1.6L16 11.2C16 11.64 15.84 12.02 15.53 12.33C15.22 12.64 14.84 12.8 14.4 12.8L3.2 12.8L0 16ZM2.52 11.2L14.4 11.2L14.4 1.6L1.6 1.6L1.6 12.1L2.52 11.2Z" /></svg>';
                                    } elseif ($act['type'] === 'system') {
                                        $iconClass = 'widget-icon-red';
                                        // system (gembok)
                                        $svgIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 21" fill="currentColor"><path d="M2 21C1.45 21 0.98 20.8 0.59 20.41C0.2 20.02 0 19.55 0 19L0 9C0 8.45 0.2 7.98 0.59 7.59C0.98 7.2 1.45 7 2 7L3 7L3 5C3 3.62 3.49 2.44 4.46 1.46C5.44 0.49 6.62 0 8 0C9.38 0 10.56 0.49 11.54 1.46C12.51 2.44 13 3.62 13 5L13 7L14 7C14.55 7 15.02 7.2 15.41 7.59C15.8 7.98 16 8.45 16 9L16 19C16 19.55 15.8 20.02 15.41 20.41C15.02 20.8 14.55 21 14 21L2 21ZM2 19L14 19L14 9L2 9L2 19ZM9.41 15.41C9.8 15.02 10 14.55 10 14C10 13.45 9.8 12.98 9.41 12.59C9.02 12.2 8.55 12 8 12C7.45 12 6.98 12.2 6.59 12.59C6.2 12.98 6 13.45 6 14C6 14.55 6.2 15.02 6.59 15.41C6.98 15.8 7.45 16 8 16C8.55 16 9.02 15.8 9.41 15.41ZM5 7L11 7L11 5C11 4.17 10.71 3.46 10.13 2.88C9.54 2.29 8.83 2 8 2C7.17 2 6.46 2.29 5.88 2.88C5.29 3.46 5 4.17 5 5L5 7Z" /></svg>';
                                    }
                                    ?>
                                    <div class="widget-icon <?= $iconClass ?>">
                                        <?= $svgIcon ?>
                                    </div>
                                    <div class="widget-item-info">
                                        <span class="widget-item-title"><?= htmlspecialchars($act['title']) ?></span>
                                        <span class="widget-item-subtitle"><?= htmlspecialchars($act['subtitle']) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Widget: Ringkasan Kelulusan -->
                    <div class="widget-card">
                        <h3 class="widget-title">Ringkasan Kelulusan</h3>
                        <div class="widget-list" style="gap: 12px;">
                            <?php if (empty($classesDetail)): ?>
                                <div style="font-size: 13px; color: #94A3B8;">Belum ada data kelulusan.</div>
                            <?php else: ?>
                                <?php foreach ($classesDetail as $index => $cdetail): ?>
                                    <?php if ($index > 0): ?>
                                        <div class="widget-divider" style="margin: 2px 0;"></div>
                                    <?php endif; ?>
                                    <div class="summary-item" style="display: flex; flex-direction: column; gap: 8px; width: 100%; align-items: stretch;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                            <span class="summary-name" style="font-size: 13px; font-weight: 600; color: #929292;"><?= htmlspecialchars($cdetail['class_name']) ?></span>
                                            <span class="summary-status" style="font-size: 13px; font-weight: bold; color: #929292;">Belum final</span>
                                        </div>
                                        <?php
                                        $passRate = ($cdetail['student_count'] > 0) ? ($cdetail['passed_count'] / $cdetail['student_count']) * 100 : 0;
                                        ?>
                                        <div class="summary-progress-container" style="width: 100%; height: 4px; background-color: #E2E2E2; border-radius: 999px; overflow: hidden;">
                                            <div class="summary-progress-bar" style="width: <?= $passRate ?>%; background-color: #29316B; height: 100%; transition: width 0.3s ease;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
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
