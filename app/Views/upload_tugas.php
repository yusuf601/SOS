<?php
// ==============================================================================
// EduLab UHO - Upload Tugas View
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
    <title>Upload Tugas - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* CSS Extension specifically for Upload Tugas layout matching Figma design */
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

        /* Banner Danger Bar */
        .alert-danger-bar {
            background-color: #FFF1F2;
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            border: 0.5px solid rgba(220, 38, 38, 0.3);
            margin-bottom: 8px;
        }

        .alert-danger-icon-wrapper {
            background-color: #EF4444;
            color: #FFFFFF;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .alert-danger-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .alert-danger-title {
            color: #991B1B;
            font-size: 14px;
            font-weight: 700;
        }

        .alert-danger-subtitle {
            color: #7F1D1D;
            font-size: 12px;
            font-weight: 500;
        }

        .alert-close-btn {
            background: none;
            border: none;
            cursor: pointer;
            margin-left: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            border-radius: 4px;
            transition: background-color var(--transition-speed) ease;
        }

        .alert-close-btn:hover {
            background-color: rgba(239, 68, 68, 0.1);
        }

        /* Active Upload Card */
        .upload-card {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 24px 32px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05);
            border: 0.5px solid rgba(54, 64, 135, 0.2);
            text-align: center;
        }

        .upload-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            text-align: left;
        }

        .upload-card-title-block {
            display: flex;
            flex-direction: column;
        }

        .upload-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #000000;
        }

        .upload-card-subtitle {
            font-size: 12px;
            color: #6B7280;
            margin-top: 4px;
        }

        .badge-yellow {
            background-color: #FFF3BF;
            color: #000000;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 30px;
            align-self: flex-start;
        }

        /* Drag & Drop */
        .upload-drag-area {
            border: 2px dashed #CBD5E1;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            background-color: #FFFFFF;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            max-width: 800px;
            margin: 0 auto;
        }

        .upload-drag-area:hover, .upload-drag-area.dragover {
            border-color: #2B577F;
            background-color: rgba(54, 64, 135, 0.02);
        }

        .upload-icon {
            color: #6B7280;
            margin-bottom: 4px;
        }

        .upload-text-primary {
            font-size: 12px;
            font-weight: 600;
            color: #4B5563;
        }

        .upload-text-secondary {
            font-size: 10px;
            color: #9CA3AF;
        }

        .file-input-hidden {
            display: none;
        }

        .selected-file-display {
            display: none;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background-color: rgba(54, 64, 135, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(54, 64, 135, 0.1);
            max-width: 600px;
            margin: 8px auto 0 auto;
        }

        .file-icon-modal {
            color: #2B577F;
        }

        .file-name-text {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: left;
        }

        .remove-file-btn {
            background: none;
            border: none;
            color: #EF4444;
            cursor: pointer;
            font-size: 18px;
        }

        .btn-submit-upload {
            background-color: #2B577F;
            color: #FFFFFF;
            border: 0.2px solid rgba(0, 0, 0, 0.3);
            border-radius: 5px;
            height: 32px;
            padding: 0 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            margin-top: 16px;
            transition: background-color var(--transition-speed) ease;
        }

        .btn-submit-upload:hover {
            background-color: #1e3f5c;
        }

        .btn-submit-upload:disabled {
            background-color: #E2E8F0;
            color: #9CA3AF;
            cursor: not-allowed;
            border: none;
        }

        /* Riwayat Pengumpulan Card */
        .riwayat-card {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 32px 24px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05);
            border: 0.5px solid rgba(54, 64, 135, 0.2);
        }

        .riwayat-title {
            font-size: 20px;
            font-weight: 600;
            color: #000000;
            margin-bottom: 24px;
            text-align: left;
        }

        .riwayat-table-wrapper {
            overflow-x: auto;
            width: 100%;
        }

        .riwayat-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .riwayat-table th {
            color: #64748B;
            font-size: 12px;
            font-weight: 600;
            padding: 12px 16px;
            border-bottom: 1px solid #E2E8F0;
            background-color: #F8FAFC;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .riwayat-table td {
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            padding: 12px 16px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            transition: background-color var(--transition-speed) ease;
        }

        .riwayat-table tr:hover td {
            background-color: #F8FAFC;
        }

        .riwayat-table tr:last-child td {
            border-bottom: none;
        }

        /* Status Badges */
        .status-badge-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 9999px;
            text-align: center;
            width: 110px;
        }

        .status-dikumpulkan {
            background-color: #DEF7EC;
            color: #03543F;
        }

        .status-tidak-kumpul {
            background-color: #FDE8E8;
            color: #9B1C1C;
        }

        .status-pending {
            background-color: #FEF3C7;
            color: #D97706;
        }

        .grade-value {
            font-weight: 700;
            color: #1E293B;
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
                    <li class="sidebar-menu-item active">
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
                    <a href="#">
                        <span>Pengaturan</span>
                        <span class="sidebar-menu-item-icon">
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
            <h2 class="navbar-title">Upload Tugas</h2>
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
            // Parse tasks to find active and missed tasks
            $rejectedTugas = null;
            $rejectedTugasIndex = -1;
            $activeTugasItem = null;
            $activeTugasIndex = -1;

            $months = [
                'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
            ];

            $monthsShort = [
                'January' => 'Jan', 'February' => 'Feb', 'March' => 'Mar',
                'April' => 'Apr', 'May' => 'Mei', 'June' => 'Jun',
                'July' => 'Jul', 'August' => 'Agu', 'September' => 'Sep',
                'October' => 'Okt', 'November' => 'Nov', 'December' => 'Des'
            ];

            foreach ($tasksData as $index => $item) {
                $tugas = $item['tugas'];
                $sub = $item['submission'];
                
                $deadlinePassed = time() > strtotime($tugas['Deadline_Upload']);
                $hasSubmitted = ($sub !== null && ($sub['Status_Tugas'] === 'Selesai' || $sub['Status_Tugas'] === 'Pending' || $sub['Status_Tugas'] === 'Revisi' || $sub['Status_Tugas'] === 'Sanggah'));
                
                if (!$deadlinePassed && $activeTugasItem === null) {
                    $activeTugasItem = $item;
                    $activeTugasIndex = $index;
                }
                
                if ($deadlinePassed && !$hasSubmitted) {
                    $rejectedTugas = $tugas;
                    $rejectedTugasIndex = $index;
                }
            }

            // Hide Class Selector and Progress Summary for Mahasiswa
            $isMahasiswa = ($_SESSION['active_role'] ?? 'Mahasiswa') === 'Mahasiswa';
            ?>

            <!-- Class Selector (Hidden for Mahasiswa) -->
            <?php if (!$isMahasiswa): ?>
            <section class="class-selector-card">
                <div class="class-selector-header">
                    <span class="class-selector-title">Pilih Kelas Praktikum:</span>
                    <div class="custom-select-wrapper">
                        <select class="class-select" id="classSelector" aria-label="Pilih kelas praktikum">
                            <?php if ($classInfo): ?>
                                <option value="<?= htmlspecialchars($classInfo['ID_Kelas']) ?>" selected><?= htmlspecialchars($classInfo['Nama_Kelas']) ?></option>
                            <?php else: ?>
                                <?php foreach ($allClasses as $cls): ?>
                                    <option value="<?= htmlspecialchars($cls['ID_Kelas']) ?>"><?= htmlspecialchars($cls['Nama_Kelas']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- Success/Error Alert -->
            <?php if (isset($_SESSION['upload_success'])): ?>
                <div style="background-color: #DEF7EC; color: #03543F; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 600; border: 1px solid #BCF0DA; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(3, 84, 63, 0.05);">
                    <?= htmlspecialchars($_SESSION['upload_success']); unset($_SESSION['upload_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['upload_error'])): ?>
                <div style="background-color: #FEE2E2; color: #DC2626; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 600; border: 1px solid #FCA5A5; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.05);">
                    <?= htmlspecialchars($_SESSION['upload_error']); unset($_SESSION['upload_error']); ?>
                </div>
            <?php endif; ?>

            <!-- Progress Summary Row (Hidden for Mahasiswa) -->
            <?php if (!$isMahasiswa): ?>
            <section class="progress-summary-grid" id="progressSummary">
                <div class="summary-card">
                    <div class="summary-icon-wrapper icon-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div class="summary-details">
                        <span class="summary-value" id="valGraded"><?= htmlspecialchars($progress['graded']) ?> / <?= htmlspecialchars($progress['total_tasks']) ?></span>
                        <span class="summary-label">Tugas Dinilai (Rata-rata: <?= htmlspecialchars($progress['average_score']) ?>)</span>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon-wrapper icon-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div class="summary-details">
                        <span class="summary-value" id="valPending"><?= htmlspecialchars($progress['pending']) ?> / <?= htmlspecialchars($progress['total_tasks']) ?></span>
                        <span class="summary-label">Menunggu Penilaian</span>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon-wrapper icon-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <div class="summary-details">
                        <span class="summary-value" id="valMissing"><?= htmlspecialchars($progress['missing']) ?> / <?= htmlspecialchars($progress['total_tasks']) ?></span>
                        <span class="summary-label">Belum Mengumpulkan</span>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- Red Alert Banner for Missed Module -->
            <?php if ($isMahasiswa && $rejectedTugas): 
                $engMonth = date('F', strtotime($rejectedTugas['Deadline_Upload']));
                $indShortMonth = $monthsShort[$engMonth] ?? date('M', strtotime($rejectedTugas['Deadline_Upload']));
                $formattedRejectedDeadline = date('j', strtotime($rejectedTugas['Deadline_Upload'])) . ' ' . $indShortMonth . ' ' . date('Y', strtotime($rejectedTugas['Deadline_Upload'])) . ', ' . date('H:i', strtotime($rejectedTugas['Deadline_Upload']));
            ?>
            <div class="alert-danger-bar">
                <div class="alert-danger-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                </div>
                <div class="alert-danger-details">
                    <span class="alert-danger-title">Modul <?= $rejectedTugasIndex + 1 ?>-Upload ditolak</span>
                    <span class="alert-danger-subtitle">Deadline berakhir <?= $formattedRejectedDeadline ?></span>
                </div>
                <button type="button" class="alert-close-btn" onclick="closeAlertBanner(this)" aria-label="Tutup">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#991B1B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <?php endif; ?>

            <!-- Active Upload Area (Only for Mahasiswa and when there is an active task) -->
            <?php if ($isMahasiswa): ?>
                <?php if ($activeTugasItem): 
                    $activeTugas = $activeTugasItem['tugas'];
                    $activeSub = $activeTugasItem['submission'];
                    
                    // Format active task deadline
                    $engMonth = date('F', strtotime($activeTugas['Deadline_Upload']));
                    $indShortMonth = $monthsShort[$engMonth] ?? date('M', strtotime($activeTugas['Deadline_Upload']));
                    $formattedActiveDeadline = date('j', strtotime($activeTugas['Deadline_Upload'])) . ' ' . $indShortMonth . ' ' . date('Y', strtotime($activeTugas['Deadline_Upload'])) . ', ' . date('H:i', strtotime($activeTugas['Deadline_Upload']));
                    
                    // Calculate countdown badge
                    $badgeText = "";
                    $timeDiff = strtotime($activeTugas['Deadline_Upload']) - time();
                    if ($timeDiff > 0) {
                        $daysLeft = ceil($timeDiff / 86400);
                        if ($daysLeft > 1) {
                            $badgeText = "$daysLeft hari lagi";
                        } elseif ($daysLeft == 1) {
                            $hoursLeft = ceil($timeDiff / 3600);
                            if ($hoursLeft > 1) {
                                $badgeText = "$hoursLeft jam lagi";
                            } else {
                                $minsLeft = ceil($timeDiff / 60);
                                $badgeText = "$minsLeft menit lagi";
                            }
                        }
                    }
                ?>
                <section class="upload-card" style="margin-bottom: 24px;">
                    <div class="upload-card-header">
                        <div class="upload-card-title-block">
                            <span class="upload-card-title">Modul <?= $activeTugasIndex + 1 ?>-<?= htmlspecialchars($activeTugas['Judul_Modul']) ?></span>
                            <span class="upload-card-subtitle">Deadline Tugas: <?= $formattedActiveDeadline ?></span>
                        </div>
                        <?php if (!empty($badgeText)): ?>
                            <span class="badge-yellow"><?= $badgeText ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <form id="inlineUploadForm" action="/rpl/public/index.php?action=submit_tugas" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_tugas" value="<?= $activeTugas['ID_Tugas'] ?>">
                        
                        <!-- Drag and drop zone -->
                        <div class="upload-drag-area" id="inlineDragArea" onclick="triggerInlineFileSelect()">
                            <span class="upload-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </span>
                            <span class="upload-text-primary">Klik atau seret file PDF laporan ke sini</span>
                            <span class="upload-text-secondary">Format PDF - Maks. 20MB</span>
                            <input type="file" name="file_tugas" id="inlineFileInput" class="file-input-hidden" accept=".pdf" onchange="handleInlineFileSelect(event)">
                        </div>

                        <!-- Display selected file info -->
                        <div class="selected-file-display" id="inlineFileDisplay">
                            <span class="file-icon-modal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            </span>
                            <span class="file-name-text" id="inlineFileName">laporan_praktikum.pdf</span>
                            <button type="button" class="remove-file-btn" onclick="removeInlineSelectedFile(event)">&times;</button>
                        </div>

                        <?php if ($activeSub): ?>
                            <div style="margin-top: 12px; font-size: 13px; color: #16A34A; font-weight: 600;">
                                Tugas Terkirim: <a href="/rpl/public/assets/uploads/tugas/<?= htmlspecialchars($activeSub['File_Tugas']) ?>" target="_blank" style="color: #2B577F; text-decoration: underline;"><?= htmlspecialchars($activeSub['File_Tugas']) ?></a> (Diunggah pada: <?= date('d M Y, H:i', strtotime($activeSub['Waktu_Submit'])) ?> WITA)
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="btn-submit-upload" id="btnSubmitInlineUpload" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            Upload
                        </button>
                    </form>
                </section>
                <?php else: ?>
                <section class="upload-card" style="margin-bottom: 24px;">
                    <p style="text-align: center; color: #9B9B9B; font-weight: 600; padding: 32px; margin: 0;">Tidak ada modul tugas aktif yang memerlukan pengumpulan saat ini.</p>
                </section>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Riwayat Pengumpulan Tugas Table -->
            <section class="riwayat-card">
                <h3 class="riwayat-title">Riwayat Pengumpulan Tugas</h3>
                <?php if (empty($tasksData)): ?>
                    <p style="text-align: center; color: #9B9B9B; font-weight: 600; padding: 32px; margin: 0;">Belum ada riwayat pengumpulan tugas.</p>
                <?php else: ?>
                    <div class="riwayat-table-wrapper">
                        <table class="riwayat-table">
                            <thead>
                                <tr>
                                    <th style="width: 6%; text-align: center;">No</th>
                                    <th style="width: 24%;">Kelas</th>
                                    <th style="width: 12%; text-align: center;">Modul</th>
                                    <th style="width: 28%;">Deadline Tugas</th>
                                    <th style="width: 15%; text-align: center;">Nilaimu</th>
                                    <th style="width: 15%; text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tasksData as $index => $item): 
                                    $tugas = $item['tugas'];
                                    $sub = $item['submission'];
                                    
                                    // Determine status and style
                                    $statusClass = 'status-tidak-kumpul';
                                    $statusText = 'Tidak Kumpul';
                                    $scoreText = '-';
                                    
                                    if ($sub) {
                                        $statusClass = 'status-dikumpulkan';
                                        $statusText = 'Dikumpulkan';
                                        if (!empty($sub['Nilai_Angka'])) {
                                            $scoreText = round($sub['Nilai_Angka']);
                                        }
                                    } else {
                                        $deadlinePassed = time() > strtotime($tugas['Deadline_Upload']);
                                        if (!$deadlinePassed) {
                                            $statusClass = 'status-pending';
                                            $statusText = 'Belum Kumpul';
                                        }
                                    }
                                    
                                    // Format deadline
                                    $engMonth = date('F', strtotime($tugas['Deadline_Upload']));
                                    $indMonth = $months[$engMonth] ?? $engMonth;
                                    $formattedDeadline = date('j', strtotime($tugas['Deadline_Upload'])) . ' ' . $indMonth . ' ' . date('Y', strtotime($tugas['Deadline_Upload']));
                                    
                                    // Class name
                                    $className = $classInfo['Nama_Kelas'] ?? 'Pemrograman Web';
                                ?>
                                    <tr>
                                        <td style="text-align: center; color: #64748B; font-weight: 600;"><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($className) ?></td>
                                        <td style="text-align: center;"><?= $index + 1 ?></td>
                                        <td><?= $formattedDeadline ?></td>
                                        <td class="grade-value" style="text-align: center;"><?= $scoreText ?></td>
                                        <td style="text-align: center;"><span class="status-badge-pill <?= $statusClass ?>"><?= $statusText ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
</div>

<!-- Success Toast Notification -->
<div id="toastNotification" class="toast">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
    <span id="toastMessage">Tugas berhasil diunggah!</span>
</div>

<script>
    // DOM Elements
    const classSelector = document.getElementById('classSelector');
    const toast = document.getElementById('toastNotification');
    const toastMessage = document.getElementById('toastMessage');

    // Handle Class Selection
    if (classSelector) {
        classSelector.addEventListener('change', function() {
            showToast("Menampilkan data kelas: " + this.options[this.selectedIndex].text);
        });
    }

    // Inline Form Elements
    const inlineDragArea = document.getElementById('inlineDragArea');
    const inlineFileInput = document.getElementById('inlineFileInput');
    const inlineFileDisplay = document.getElementById('inlineFileDisplay');
    const inlineFileName = document.getElementById('inlineFileName');
    const btnSubmitInlineUpload = document.getElementById('btnSubmitInlineUpload');

    function triggerInlineFileSelect() {
        if (inlineFileInput) inlineFileInput.click();
    }

    function handleInlineFileSelect(event) {
        const files = event.target.files;
        if (files && files.length > 0) {
            displayInlineSelectedFile(files[0]);
        }
    }

    function displayInlineSelectedFile(file) {
        if (inlineFileName) inlineFileName.textContent = file.name;
        if (inlineDragArea) inlineDragArea.style.display = 'none';
        if (inlineFileDisplay) inlineFileDisplay.style.display = 'flex';
        if (btnSubmitInlineUpload) btnSubmitInlineUpload.disabled = false;
    }

    function removeInlineSelectedFile(event) {
        if (event) event.stopPropagation();
        if (inlineFileInput) inlineFileInput.value = '';
        if (inlineFileDisplay) inlineFileDisplay.style.display = 'none';
        if (inlineDragArea) inlineDragArea.style.display = 'flex';
        if (btnSubmitInlineUpload) btnSubmitInlineUpload.disabled = true;
    }

    // Drag & Drop for Inline Drag Area
    if (inlineDragArea) {
        ['dragenter', 'dragover'].forEach(eventName => {
            inlineDragArea.addEventListener(eventName, e => {
                e.preventDefault();
                inlineDragArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            inlineDragArea.addEventListener(eventName, e => {
                e.preventDefault();
                inlineDragArea.classList.remove('dragover');
            }, false);
        });

        inlineDragArea.addEventListener('drop', e => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                inlineFileInput.files = files;
                displayInlineSelectedFile(files[0]);
            }
        });
    }

    // Show Toast
    function showToast(message) {
        if (toastMessage && toast) {
            toastMessage.textContent = message;
            toast.className = "toast show";
            setTimeout(() => {
                toast.className = toast.className.replace("show", "");
            }, 3000);
        }
    }

    // Close Alert Banner
    function closeAlertBanner(btn) {
        const banner = btn.closest('.alert-danger-bar');
        if (banner) {
            banner.style.display = 'none';
        }
    }
</script>

</body>
</html>
