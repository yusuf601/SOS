<?php
// ==============================================================================
// EduLab UHO - Bank Modul View
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
    <title>Bank Modul - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* CSS Extension specifically for Bank Modul layout matching Figma design */
        .workspace-content {
            background-color: #F0F0F0;
            padding: 32px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Class Selector Card (Only shown to Dosen/Asisten) */
        .class-selector-card {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05);
            border: 0.5px solid rgba(54, 64, 135, 0.2);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .class-selector-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .class-selector-title {
            font-size: 16px;
            font-weight: 600;
            color: #364087;
        }

        .custom-select-wrapper {
            position: relative;
            width: 320px;
            max-width: 100%;
        }

        .class-select {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-color-dark);
            outline: none;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            transition: all var(--transition-speed) ease;
        }

        .class-select:focus {
            border-color: var(--btn-primary);
            box-shadow: 0 0 0 3px rgba(54, 64, 135, 0.1);
        }

        .custom-select-wrapper::after {
            content: '';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-top-color: var(--text-color-dark);
            pointer-events: none;
        }

        .class-meta-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            padding-top: 16px;
            border-top: 1px solid #E2E8F0;
        }

        .meta-info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .meta-info-label {
            font-size: 12px;
            font-weight: 600;
            color: #9B9B9B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-info-value {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-color-dark);
        }

        /* Alert Info Bar */
        .alert-info-bar {
            background-color: rgba(212, 201, 255, 0.34);
            border-radius: 5px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .alert-info-icon-wrapper {
            background-color: #7C5DFA;
            color: #FFFFFF;
            width: 23px;
            height: 23px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            flex-shrink: 0;
        }

        .alert-info-text {
            color: #5D56B8;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.2;
        }

        /* Modul Berlangsung */
        .modul-berlangsung-card {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05);
            border: 0.5px solid rgba(54, 64, 135, 0.2);
        }

        .modul-berlangsung-header {
            font-size: 16px;
            font-weight: 600;
            color: #364087;
            margin-bottom: 12px;
        }

        .modul-berlangsung-strip {
            background-color: rgba(215, 229, 242, 0.61);
            border-radius: 5px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .modul-berlangsung-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .modul-berlangsung-icon {
            background-color: #2B587F;
            color: #FFFFFF;
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
        }

        .modul-berlangsung-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .modul-berlangsung-title {
            font-size: 14px;
            font-weight: 600;
            color: #000000;
            line-height: 1.2;
        }

        .modul-berlangsung-meta {
            font-size: 12px;
            color: #4B5563;
        }

        .modul-berlangsung-meta-date {
            color: #D97706; /* orange */
            font-weight: 600;
        }

        .modul-berlangsung-badges {
            display: flex;
            gap: 8px;
            margin-top: 2px;
        }

        .badge-yellow {
            background-color: #FFF3BF;
            color: #000000;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 30px;
        }

        .badge-red {
            background-color: #FEE2E2;
            color: #DC2626;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 30px;
        }

        .badge-green {
            background-color: #DEF7EC;
            color: #03543F;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 30px;
        }

        .modul-berlangsung-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 120px;
        }

        .btn-download-modul {
            background-color: #2B577F;
            color: #FFFFFF;
            font-size: 10px;
            font-weight: 600;
            border: 0.2px solid rgba(0, 0, 0, 0.3);
            border-radius: 5px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color var(--transition-speed);
        }

        .btn-download-modul:hover {
            background-color: #1e3f5c;
        }

        .btn-upload-tugas {
            background-color: #FFFFFF;
            color: #2B577F;
            font-size: 10px;
            font-weight: 600;
            border: 0.5px solid #2B577F;
            border-radius: 5px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            text-decoration: none;
            cursor: pointer;
            transition: all var(--transition-speed);
        }

        .btn-upload-tugas:hover {
            background-color: #2B577F;
            color: #FFFFFF;
        }

        /* Modul Selesai Table styling */
        .modul-selesai-card {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 32px 24px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05);
            border: 0.5px solid rgba(54, 64, 135, 0.2);
        }

        .modul-selesai-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .modul-selesai-title {
            font-size: 20px;
            font-weight: 600;
            color: #000000;
        }

        .modul-selesai-subtitle {
            font-size: 14px;
            color: rgba(0, 0, 0, 0.5);
            font-weight: 600;
        }

        .modul-table-wrapper {
            overflow-x: auto;
            width: 100%;
        }

        .modul-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .modul-table th {
            color: rgba(0, 0, 0, 0.5);
            font-size: 14px;
            font-weight: 600;
            padding: 12px 16px;
            border-bottom: 0.5px solid rgba(0, 0, 0, 0.4);
        }

        .modul-table td {
            font-size: 12px;
            font-weight: 500;
            color: #000000;
            padding: 16px;
            border-bottom: 0.2px solid rgba(0, 0, 0, 0.4);
            vertical-align: middle;
        }

        .modul-table tr:last-child td {
            border-bottom: none;
        }

        .grade-value {
            font-weight: 700;
            color: #000000;
        }

        .grade-red {
            font-weight: 700;
            color: #DC2626;
        }

        .grade-pending {
            font-weight: 600;
            color: #D97706;
        }

        .btn-table-download {
            background: none;
            border: 1px solid #E2E8F0;
            border-radius: 5px;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #4B5563;
            cursor: pointer;
            transition: all var(--transition-speed);
        }

        .btn-table-download:hover {
            background-color: #F3F4F6;
            color: #111827;
        }

        /* Toast Styling */
        .toast {
            visibility: hidden;
            min-width: 280px;
            background-color: #1E293B;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 16px;
            position: fixed;
            z-index: 150;
            left: 50%;
            bottom: 30px;
            transform: translateX(-50%);
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        @keyframes fadein {
            from { bottom: 0; opacity: 0; }
            to { bottom: 30px; opacity: 1; }
        }

        @keyframes fadeout {
            from { bottom: 30px; opacity: 1; }
            to { bottom: 0; opacity: 0; }
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
                <div class="sidebar-user-role"><?= htmlspecialchars($_SESSION['active_role'] ?? 'Mahasiswa') ?></div>
            </div>

            <!-- Menu Navigation -->
            <nav>
                <div class="sidebar-menu-title">Menu Utama</div>
                <ul class="sidebar-menu-list">
                    <li class="sidebar-menu-item">
                        <a href="/rpl/public/index.php?action=dashboard_student">
                            <span>Dashboard</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item active">
                        <a href="/rpl/public/index.php?action=my_classes">
                            <span>Kelas Saya</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            </span>
                        </a>
                        <ul class="sidebar-submenu-list">
                            <li class="sidebar-submenu-item active">
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
                    <li class="sidebar-menu-item">
                        <a href="/rpl/public/index.php?action=kelulusan">
                            <span>Status Kelulusan</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
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
            <h2 class="navbar-title">Bank Modul</h2>
            <div class="navbar-profile">
                <!-- User Notification Button -->
                <button type="button" style="background:none; border:none; color:white; cursor:pointer;" aria-label="Notifikasi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </button>
                
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">
            <?php
            // Group modules into active (Modul Berlangsung) and completed (Modul Selesai)
            $activeModul = null;
            $activeModulIndex = -1;
            $completedModules = [];
            
            $now = time();
            foreach ($modules as $index => $modul) {
                $deadline = $modul['Deadline_Upload'] ? strtotime($modul['Deadline_Upload']) : null;
                
                // Active modul is the first module with a future deadline
                if ($deadline && $deadline > $now && !$activeModul) {
                    $activeModul = $modul;
                    $activeModulIndex = $index;
                } else {
                    $completedModules[] = [
                        'index' => $index + 1,
                        'data' => $modul
                    ];
                }
            }

            // Fallback: If no future deadline is found but we have modules, the last module is active (if role is Student)
            if (!$activeModul && !empty($modules) && $_SESSION['active_role'] === 'Mahasiswa') {
                $activeModul = end($modules);
                $activeModulIndex = count($modules) - 1;
                array_pop($completedModules);
            }
            ?>

            <!-- Class Selector (Only shown to Dosen and Asisten, hidden for Mahasiswa as per Figma screenshot) -->
            <?php if ($_SESSION['active_role'] !== 'Mahasiswa'): ?>
                <section class="class-selector-card">
                    <div class="class-selector-header">
                        <span class="class-selector-title">Pilih Kelas Praktikum:</span>
                        <div class="custom-select-wrapper">
                            <select class="class-select" id="classSelector" aria-label="Pilih kelas praktikum">
                                <?php foreach ($allClasses as $cls): ?>
                                    <option value="<?= htmlspecialchars($cls['ID_Kelas']) ?>"><?= htmlspecialchars($cls['Nama_Kelas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="class-meta-info">
                        <div class="meta-info-item">
                            <span class="meta-info-label">Dosen Pengampu</span>
                            <span class="meta-info-value" id="lecturerName"><?= htmlspecialchars($lecturerName) ?></span>
                        </div>
                        <div class="meta-info-item">
                            <span class="meta-info-label">Asisten Praktikum</span>
                            <span class="meta-info-value" id="assistantName"><?= htmlspecialchars($assistantName) ?></span>
                        </div>
                        <div class="meta-info-item">
                            <span class="meta-info-label">Jadwal & Ruang</span>
                            <span class="meta-info-value" id="classSchedule"><?= htmlspecialchars($schedule) ?></span>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Download Error Alert -->
            <?php if (isset($_SESSION['download_error'])): ?>
                <div style="background-color: #FEE2E2; color: #DC2626; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 600; border: 1px solid #FCA5A5; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.05); margin-bottom: 8px;">
                    <?= htmlspecialchars($_SESSION['download_error']); unset($_SESSION['download_error']); ?>
                </div>
            <?php endif; ?>

            <!-- Info Bar (Figma Sibling Group 29) -->
            <div class="alert-info-bar">
                <div class="alert-info-icon-wrapper">i</div>
                <div class="alert-info-text">
                    Klik tombol Unduh untuk mengunduh modul. Modul yang belum dirilis dosen tidak bisa diakses.
                </div>
            </div>

            <!-- Modul Berlangsung (Figma Sibling Group 44) -->
            <?php if ($activeModul): ?>
                <section class="modul-berlangsung-card">
                    <h3 class="modul-berlangsung-header">Modul Berlangsung</h3>
                    <div class="modul-berlangsung-strip">
                        <div class="modul-berlangsung-left">
                            <div class="modul-berlangsung-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <div class="modul-berlangsung-details">
                                <h4 class="modul-berlangsung-title">Modul <?= $activeModulIndex + 1 ?>: <?= htmlspecialchars($activeModul['Judul_Modul']) ?></h4>
                                <span class="modul-berlangsung-meta">
                                    Pertemuan <?= $activeModulIndex + 1 ?> · Deadline: 
                                    <span class="modul-berlangsung-meta-date">
                                        <?= $activeModul['Deadline_Upload'] ? date('d M Y, H:i', strtotime($activeModul['Deadline_Upload'])) : 'Belum Diatur' ?>
                                    </span>
                                </span>
                                <?php if ($_SESSION['active_role'] === 'Mahasiswa'): ?>
                                    <div class="modul-berlangsung-badges">
                                        <?php
                                        if ($activeModul['Deadline_Upload']) {
                                            $diff = strtotime($activeModul['Deadline_Upload']) - time();
                                            $days = ceil($diff / (3600 * 24));
                                            if ($days > 0) {
                                                echo '<span class="badge-yellow">' . $days . ' hari lagi</span>';
                                            } else {
                                                echo '<span class="badge-red">Terlambat</span>';
                                            }
                                        }
                                        
                                        if ($activeModul['ID_Pengumpulan']) {
                                            echo '<span class="badge-green">Sudah dikumpulkan</span>';
                                        } else {
                                            echo '<span class="badge-red">Belum dikumpulkan</span>';
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="modul-berlangsung-actions">
                            <a href="/rpl/public/index.php?action=download_materi&file=<?= urlencode($activeModul['File_Materi']) ?>" class="btn-download-modul">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <span>Unduh Modul</span>
                            </a>
                            <?php if ($_SESSION['active_role'] === 'Mahasiswa'): ?>
                                <a href="/rpl/public/index.php?action=upload_tugas" class="btn-upload-tugas">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <span>Upload Tugas</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Modul Selesai (Figma Sibling Group 30) -->
            <section class="modul-selesai-card">
                <div class="modul-selesai-header">
                    <h3 class="modul-selesai-title">Modul Selesai</h3>
                    <span class="modul-selesai-subtitle">Pertemuan 1-<?= count($completedModules) > 0 ? end($completedModules)['index'] : '6' ?></span>
                </div>
                <div class="modul-table-wrapper">
                    <table class="modul-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Modul</th>
                                <th>Pertemuan</th>
                                <th>Deadline Tugas</th>
                                <?php if ($_SESSION['active_role'] === 'Mahasiswa'): ?>
                                    <th>Nilaimu</th>
                                <?php endif; ?>
                                <th>Unduh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($completedModules)): ?>
                                <tr>
                                    <td colspan="<?= $_SESSION['active_role'] === 'Mahasiswa' ? 6 : 5 ?>" style="text-align: center; color: rgba(0,0,0,0.5);">Belum ada modul yang selesai.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($completedModules as $item): 
                                    $idx = $item['index'];
                                    $m = $item['data'];
                                    $deadlineStr = $m['Deadline_Upload'] ? date('d M Y', strtotime($m['Deadline_Upload'])) : '-';
                                    
                                    // Determine the grade display (Only for students)
                                    $gradeDisplay = '-';
                                    if ($_SESSION['active_role'] === 'Mahasiswa') {
                                        if ($m['ID_Pengumpulan']) {
                                            if ($m['Nilai_Angka'] !== null) {
                                                $gradeDisplay = '<span class="grade-value">' . round($m['Nilai_Angka']) . '</span>';
                                            } else {
                                                $gradeDisplay = '<span class="grade-pending">Diproses</span>';
                                            }
                                        } else {
                                            // If deadline has passed and no submission
                                            $deadlineTime = $m['Deadline_Upload'] ? strtotime($m['Deadline_Upload']) : null;
                                            if ($deadlineTime && $deadlineTime < $now) {
                                                $gradeDisplay = '<span class="grade-red">Tidak Kumpul</span>';
                                            }
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td><?= $idx ?></td>
                                        <td>
                                            <!-- Red PDF Icon -->
                                            <svg class="pdf-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 8px;">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                                <polyline points="10 9 9 9 8 9"></polyline>
                                            </svg>
                                            <span style="vertical-align: middle;"><?= htmlspecialchars($m['Judul_Modul']) ?></span>
                                        </td>
                                        <td><?= $idx ?></td>
                                        <td><?= $deadlineStr ?></td>
                                        <?php if ($_SESSION['active_role'] === 'Mahasiswa'): ?>
                                            <td><?= $gradeDisplay ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <a href="/rpl/public/index.php?action=download_materi&file=<?= urlencode($m['File_Materi']) ?>" class="btn-table-download" aria-label="Unduh modul">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            </a>
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

<!-- Success Toast Notification -->
<div id="toastNotification" class="toast">
    <span id="toastMessage">Unduh dimulai...</span>
</div>

<script>
    const toast = document.getElementById('toastNotification');
    const toastMessage = document.getElementById('toastMessage');

    // Toast Notification helper
    function showToast(message) {
        toastMessage.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg> ${message}`;
        toast.className = "toast show";
        setTimeout(() => {
            toast.className = toast.className.replace("show", "");
        }, 3000);
    }
</script>

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
