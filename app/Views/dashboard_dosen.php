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
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-top: 16px;
        }

        @media (max-width: 1024px) {
            .dosen-dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        .class-card {
            background-color: #FFFFFF;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            border: 1px solid #F1F5F9;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .class-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        }

        .class-card-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .class-thumb {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background-color: #F1F5F9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748B;
        }

        .class-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .class-name {
            font-size: 18px;
            font-weight: 600;
            color: #0F172A;
            margin: 0;
        }

        .class-meta {
            font-size: 14px;
            color: #64748B;
            font-weight: 500;
        }

        .class-card-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-tag {
            padding: 4px 10px;
            border-radius: 8px;
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
            background-color: #ABDAE8;
            color: #1A5C70;
        }

        .widget-icon-red {
            background-color: #FCEBEB;
            color: #791F1F;
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
                            <span>Kelas Saya</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#">
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
                        <a href="#">
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
                <!-- Left Column: Semua Kelas Aktif -->
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
                        <?php foreach ($classesDetail as $cdetail): ?>
                            <div class="class-card">
                                <div class="class-card-left">
                                    <div class="class-thumb">
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
                                <div class="class-card-right">
                                    <?php if ($cdetail['repeating_count'] > 0): ?>
                                        <span class="badge-tag badge-tag-danger"><?= htmlspecialchars($cdetail['repeating_count']) ?> mengulang</span>
                                    <?php endif; ?>
                                    
                                    <?php if ($cdetail['passed_count'] > 0): ?>
                                        <span class="badge-tag badge-tag-success"><?= htmlspecialchars($cdetail['passed_count']) ?> lulus</span>
                                    <?php else: ?>
                                        <span class="badge-tag badge-tag-success">Aktif</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Sidebar Widgets -->
                <div class="widgets-section">
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
                                    $svgIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                                    if ($act['type'] === 'dispute') {
                                        $iconClass = 'widget-icon-blue';
                                        $svgIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line><circle cx="12" cy="12" r="10"></circle></svg>';
                                    } elseif ($act['type'] === 'system') {
                                        $iconClass = 'widget-icon-red';
                                        $svgIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>';
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
                            <?php foreach ($graduationSummary as $index => $sum): ?>
                                <?php if ($index > 0): ?>
                                    <div class="widget-divider" style="margin: 2px 0;"></div>
                                <?php endif; ?>
                                <div class="summary-item">
                                    <span class="summary-name"><?= htmlspecialchars($sum['class_name']) ?></span>
                                    <span class="summary-status"><?= htmlspecialchars($sum['status']) ?></span>
                                </div>
                            <?php endforeach; ?>
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
