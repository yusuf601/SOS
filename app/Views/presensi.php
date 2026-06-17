<?php
// ==============================================================================
// EduLab UHO - Data Presensi View (Student & Staff)
// ==============================================================================

$fullName = $_SESSION['name'] ?? 'Guest';
$role = $_SESSION['active_role'] ?? 'Mahasiswa';

$words = explode(" ", $fullName);
$initials = "";
foreach ($words as $w) {
    $initials .= strtoupper($w[0] ?? '');
}
$initials = substr($initials, 0, 2);

// Calculate stats for student
$hadirCount = 0;
$sakitCount = 0;
$izinCount = 0;
$alfaCount = 0;
$totalRecorded = 0;
$attendanceRate = 100;

if ($role === 'Mahasiswa' && !empty($attendance)) {
    foreach ($attendance as $att) {
        if (!empty($att['Status_Kehadiran'])) {
            $totalRecorded++;
            switch ($att['Status_Kehadiran']) {
                case 'Hadir': $hadirCount++; break;
                case 'Sakit': $sakitCount++; break;
                case 'Izin': $izinCount++; break;
                case 'Alfa':
                case 'Alpa': $alfaCount++; break;
            }
        }
    }
    if ($totalRecorded > 0) {
        $attendanceRate = round(($hadirCount / $totalRecorded) * 100);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Presensi - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        .presensi-card {
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .presensi-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-color-dark);
            margin-bottom: 20px;
        }

        .stats-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        @media (max-width: 992px) {
            .stats-summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .stats-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        .stat-mini-card {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05);
            border: 0.5px solid rgba(54, 64, 135, 0.1);
            position: relative;
        }

        .stat-mini-icon-box {
            width: 60px;
            height: 60px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .stat-mini-val {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 4px;
            text-align: center;
        }

        .stat-mini-lbl {
            font-size: 14px;
            font-weight: 600;
            color: #64748B;
            text-align: center;
        }

        .filter-section {
            background-color: #F8FAFC;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid #E2E8F0;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            align-items: flex-end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
        }

        .form-control {
            height: 44px;
            padding: 0 12px;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            background-color: #FFFFFF;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--btn-primary);
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .custom-table th {
            color: #64748B;
            font-size: 13px;
            font-weight: 600;
            padding: 16px 20px;
            border-bottom: 1px solid #E2E8F0;
            background-color: #FFFFFF;
            text-transform: none;
            letter-spacing: normal;
        }

        .custom-table td {
            font-size: 14px;
            font-weight: 500;
            color: #334155;
            padding: 16px 20px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            transition: background-color var(--transition-speed) ease;
        }

        .custom-table tr:hover td {
            background-color: #F8FAFC;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 9999px;
            text-align: center;
            width: 80px;
        }

        .badge-hadir { background-color: #EAF3DE; color: #2F6100; }
        .badge-sakit { background-color: #E7D9FF; color: #8161BC; }
        .badge-izin { background-color: #FFEECC; color: #D38C00; }
        .badge-alfa { background-color: #FCEBEB; color: #791F1F; }

        .radio-group {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .radio-option {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }

        .radio-option input {
            cursor: pointer;
        }

        .btn-submit-presensi {
            background-color: var(--btn-primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(54, 64, 135, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit-presensi:hover {
            background-color: #2b336b;
            transform: translateY(-1px);
        }

        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

        /* Dosen / Asisten Group Cards & Scrolling Layout */
        .group-card {
            background-color: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 24px 32px;
            margin-bottom: 32px;
            max-width: 760px;
            width: 100%;
        }

        .group-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding-bottom: 16px;
            margin-bottom: 16px;
        }

        .group-card-title {
            font-size: 18px;
            font-weight: 600;
            color: #000000;
            margin: 0;
        }

        .group-card-badge {
            background-color: rgba(149, 149, 149, 0.18);
            color: #6F6F6F;
            padding: 3px 12px;
            border-radius: 30px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.4px;
        }

        .group-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .group-table th {
            font-size: 12px;
            font-weight: 600;
            color: rgba(0, 0, 0, 0.5);
            padding: 12px 8px;
            text-transform: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .group-table td {
            font-size: 13px;
            font-weight: 500;
            color: #000000;
            padding: 14px 8px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            vertical-align: middle;
        }

        .group-table tr:last-child td {
            border-bottom: none;
        }

        /* Sticky Table Header & Scroll Container */
        .table-scrollable-container {
            max-height: 260px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .table-scrollable-container::-webkit-scrollbar {
            width: 6px;
        }
        .table-scrollable-container::-webkit-scrollbar-track {
            background: #F3F4F6;
            border-radius: 4px;
        }
        .table-scrollable-container::-webkit-scrollbar-thumb {
            background: #D1D5DB;
            border-radius: 4px;
        }
        .table-scrollable-container::-webkit-scrollbar-thumb:hover {
            background: #9CA3AF;
        }

        .group-table thead th {
            position: sticky;
            top: 0;
            background-color: #FFFFFF;
            z-index: 10;
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.05);
        }

        /* Top-Right Dropdowns & Summary Header Row */
        .presensi-header-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 24px;
            width: 100%;
        }

        .workspace-header-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            width: 100%;
            flex-wrap: wrap;
            gap: 12px;
        }

        .presensi-filter-select-wrapper {
            position: relative;
            height: 32px;
        }

        .presensi-select-practicum {
            width: 134px;
        }

        .presensi-select-module {
            width: 115px;
        }

        .presensi-select {
            width: 100%;
            height: 100%;
            background-color: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            padding: 0 28px 0 12px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #292929;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
            outline: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            text-align: left;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }

        .presensi-filter-select-wrapper::after {
            content: "";
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid #5E5E5E;
            pointer-events: none;
        }

        .presensi-select:hover {
            border-color: rgba(0, 0, 0, 0.2);
        }

        .presensi-summary-row {
            display: flex;
            gap: 8px;
        }

        .summary-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            height: 23px;
        }

        .summary-badge-hadir {
            background-color: #EAF3DE;
            color: #27500A;
        }

        .summary-badge-alpa {
            background-color: #FCEBEB;
            color: #791F1F;
        }

        .summary-badge-izin {
            background-color: #FFF7D3;
            color: #D78F00;
        }

        .summary-badge-sakit {
            background-color: #D3DBFF;
            color: #8161BC;
        }

        /* Form Info Banner (Purple) */
        .presensi-info-banner {
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: rgba(94, 86, 184, 0.12);
            border: 1px solid rgba(94, 86, 184, 0.25);
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 24px;
            width: 100%;
        }

        .presensi-info-icon-bg {
            width: 24px;
            height: 24px;
            background-color: #5E56B8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .presensi-info-icon {
            color: #FFFFFF;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
        }

        .presensi-info-text {
            color: #3C3672;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.4;
        }

        /* Marking Badges ("Tandai") */
        .tandai-group {
            display: flex;
            gap: 6px;
            justify-content: center;
        }

        .tandai-option {
            cursor: pointer;
            position: relative;
            display: inline-block;
        }

        .tandai-option input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .tandai-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 3px 10px;
            border-radius: 30px;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 0.45px;
            color: #8A8A8A;
            background-color: rgba(0, 0, 0, 0.05);
            border: 1px solid transparent;
            transition: all 0.2s ease;
            height: 16px;
            user-select: none;
        }

        .tandai-option input:checked + .tandai-badge {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            border-width: 1px;
        }

        .tandai-option input:checked + .tandai-badge-hadir,
        .tandai-option:hover .tandai-badge-hadir {
            background-color: #EAF3DE;
            color: #27500A;
            border-color: rgba(39, 80, 10, 0.3);
        }

        .tandai-option input:checked + .tandai-badge-alpa,
        .tandai-option:hover .tandai-badge-alpa {
            background-color: #FCEBEB;
            color: #791F1F;
            border-color: rgba(121, 31, 31, 0.3);
        }

        .tandai-option input:checked + .tandai-badge-izin,
        .tandai-option:hover .tandai-badge-izin {
            background-color: #FFF7D3;
            color: #D78F00;
            border-color: rgba(215, 143, 0, 0.3);
        }

        .tandai-option input:checked + .tandai-badge-sakit,
        .tandai-option:hover .tandai-badge-sakit {
            background-color: #D3DBFF;
            color: #8161BC;
            border-color: rgba(129, 97, 188, 0.3);
        }

        /* Status badges */
        .status-display-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 3px 12px;
            border-radius: 30px;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 0.45px;
            height: 15px;
            text-align: center;
        }

        .status-display-hadir {
            background-color: #EAF3DE;
            color: #27500A;
        }

        .status-display-alpa {
            background-color: #FCEBEB;
            color: #791F1F;
        }

        .status-display-izin {
            background-color: #FFF7D3;
            color: #D78F00;
        }

        .status-display-sakit {
            background-color: #D3DBFF;
            color: #8161BC;
        }

        /* Footer Reset & Save Buttons */
        .presensi-footer-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            max-width: 760px;
            width: 100%;
            margin-top: 16px;
            margin-bottom: 48px;
        }

        .btn-presensi-reset {
            height: 32px;
            padding: 0 16px;
            background-color: #FFFFFF;
            border: 1px solid #2B577F;
            border-radius: 5px;
            color: #2B577F;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none;
        }

        .btn-presensi-reset:hover {
            background-color: rgba(43, 87, 127, 0.05);
        }

        .btn-presensi-save {
            height: 32px;
            padding: 0 20px;
            background-color: #2B577F;
            border: 1px solid transparent;
            border-radius: 5px;
            color: #FFFFFF;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            outline: none;
        }

        .btn-presensi-save:hover {
            background-color: #20415F;
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
                        <li class="sidebar-menu-item active">
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
                            <li class="sidebar-menu-item active">
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
                            <li class="sidebar-menu-item active">
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
            <h2 class="navbar-title"><?= ($role === 'Mahasiswa') ? 'Data Presensi' : 'Input Presensi' ?></h2>
            <div class="navbar-profile">
                <button type="button" style="background:none; border:none; color:white; cursor:pointer;" aria-label="Notifikasi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </button>
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">
            
            <!-- Alerts -->
            <?php if (isset($_SESSION['presensi_success'])): ?>
                <div style="background-color: #DEF7EC; color: #03543F; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 600; border: 1px solid #BCF0DA; margin-bottom: 20px;">
                    <?= htmlspecialchars($_SESSION['presensi_success']); unset($_SESSION['presensi_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['presensi_error'])): ?>
                <div style="background-color: #FEE2E2; color: #DC2626; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 600; border: 1px solid #FCA5A5; margin-bottom: 20px;">
                    <?= htmlspecialchars($_SESSION['presensi_error']); unset($_SESSION['presensi_error']); ?>
                </div>
            <?php endif; ?>

            <?php if ($role === 'Mahasiswa'): ?>
                <!-- STUDENT PRESENSI VIEW -->
                <?php
                $className = $classInfo['Nama_Kelas'] ?? 'Sistem Digital A';

                $monthsIndo = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                ?>
                
                <div class="stats-summary-grid">
                    <!-- Card 1: Hadir -->
                    <div class="stat-mini-card">
                        <div class="stat-mini-icon-box" style="background-color: #EAF3DE;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2F6100" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><polyline points="9 14 11 16 15 12"></polyline></svg>
                        </div>
                        <div class="stat-mini-val" style="color: #000000;"><?= $hadirCount ?></div>
                        <div class="stat-mini-lbl" style="color: #000000;">Hadir</div>
                    </div>

                    <!-- Card 2: Alpa -->
                    <div class="stat-mini-card">
                        <div class="stat-mini-icon-box" style="background-color: #FCEBEB;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#791F1F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><line x1="10" y1="13" x2="14" y2="17"></line><line x1="14" y1="13" x2="10" y2="17"></line></svg>
                        </div>
                        <div class="stat-mini-val" style="color: #791F1F;"><?= $alfaCount ?></div>
                        <div class="stat-mini-lbl" style="color: #791F1F;">Alpa</div>
                    </div>

                    <!-- Card 3: Izin -->
                    <div class="stat-mini-card">
                        <div class="stat-mini-icon-box" style="background-color: #FFEECC;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D38C00" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><line x1="12" y1="13" x2="12" y2="15"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        </div>
                        <div class="stat-mini-val" style="color: #D38C00;"><?= $izinCount ?></div>
                        <div class="stat-mini-lbl" style="color: #D38C00;">Izin</div>
                    </div>

                    <!-- Card 4: Kehadiran -->
                    <div class="stat-mini-card">
                        <div class="stat-mini-icon-box" style="background-color: #E7D9FF;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#8161BC" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><circle cx="9.5" cy="13.5" r="1.5"></circle><circle cx="14.5" cy="16.5" r="1.5"></circle><line x1="14" y1="13" x2="10" y2="17"></line></svg>
                        </div>
                        <div class="stat-mini-val" style="color: #8161BC;"><?= $attendanceRate ?>%</div>
                        <div class="stat-mini-lbl" style="color: #8161BC;">Kehadiran</div>
                    </div>
                </div>

                <section class="presensi-card">
                    <h3 class="presensi-title" style="margin-bottom: 4px;"><?= htmlspecialchars($className) ?></h3>
                    <div style="font-size: 14px; font-weight: 600; color: #797979; margin-bottom: 24px;">Riwayat Kehadiran</div>
                    
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">Pertemuan</th>
                                    <th style="width: 50%;">Materi</th>
                                    <th style="width: 25%;">Tanggal</th>
                                    <th style="width: 15%; text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($attendance)): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; color: #94A3B8; padding: 24px;">Belum ada riwayat kehadiran tercatat.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($attendance as $index => $row): ?>
                                        <?php
                                        $dateVal = '';
                                        if (!empty($row['Tanggal'])) {
                                            $time = strtotime($row['Tanggal']);
                                            $d = date('j', $time);
                                            $m = $monthsIndo[(int)date('n', $time)] ?? date('F', $time);
                                            $y = date('Y', $time);
                                            $dateVal = "$d $m $y";
                                        }
                                        ?>
                                        <tr>
                                            <td style="color: #64748B; font-weight: 600;"><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($row['Judul_Modul']) ?></td>
                                            <td><?= htmlspecialchars($dateVal) ?></td>
                                            <td style="text-align: center;">
                                                <?php if (!empty($row['Status_Kehadiran'])): ?>
                                                    <span class="badge-status badge-<?= strtolower($row['Status_Kehadiran']) ?>">
                                                        <?= htmlspecialchars($row['Status_Kehadiran']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

            <?php else: ?>
                <!-- DOSEN / ASISTEN PRESENSI VIEW -->
                <?php
                $groupsListNames = [];
                if (!empty($groupsData)) {
                    foreach ($groupsData as $g) {
                        $groupsListNames[] = $g['group_name'];
                    }
                }
                
                $formattedGroups = 'Tidak Ada Kelompok';
                $groupCount = count($groupsListNames);
                if ($groupCount > 0) {
                    if ($groupCount == 1) {
                        $formattedGroups = $groupsListNames[0];
                    } elseif ($groupCount <= 3) {
                        $lastGroup = array_pop($groupsListNames);
                        $formattedGroups = implode(', ', $groupsListNames) . ' & ' . $lastGroup;
                    } else {
                        $numbers = [];
                        foreach ($groupsListNames as $name) {
                            if (preg_match('/Kelompok\s+(\d+)/i', $name, $matches)) {
                                $numbers[] = (int)$matches[1];
                            }
                        }
                        if (count($numbers) == $groupCount && min($numbers) == 1 && max($numbers) == $groupCount) {
                            $formattedGroups = 'Kelompok ' . min($numbers) . ' - ' . max($numbers);
                        } else {
                            $formattedGroups = $groupCount . ' Kelompok di kelas ini';
                        }
                    }
                }
                ?>

                <!-- Info Banner (Dynamic Cerdas) -->
                <?php if (!empty($groupsData)): ?>
                    <div class="presensi-info-banner">
                        <div class="presensi-info-icon-bg">
                            <span class="presensi-info-icon">i</span>
                        </div>
                        <span class="presensi-info-text">Kamu hanya bisa mengisi presensi untuk <?= htmlspecialchars($formattedGroups) ?>. Tandai status setiap mahasiswa lalu simpan.</span>
                    </div>
                <?php endif; ?>

                <!-- Top-Right Dropdowns and Summary -->
                <div class="presensi-header-right">
                    <div class="workspace-header-row">
                        <form method="GET" action="/rpl/public/index.php" id="filterForm">
                            <input type="hidden" name="action" value="presensi">
                            <div style="display: flex; gap: 12px;">
                                <!-- Practicum Select -->
                                <div class="presensi-filter-select-wrapper presensi-select-practicum">
                                    <select name="class_id" class="presensi-select" onchange="document.getElementById('filterForm').submit()">
                                        <?php foreach ($allClasses as $cls): ?>
                                            <option value="<?= $cls['ID_Kelas'] ?>" <?= ($selectedClass == $cls['ID_Kelas']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cls['Nama_Kelas']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- Module Select -->
                                <div class="presensi-filter-select-wrapper presensi-select-module">
                                    <select name="modul_id" class="presensi-select" onchange="document.getElementById('filterForm').submit()">
                                        <?php foreach ($moduls as $mdl): ?>
                                            <option value="<?= $mdl['ID_Modul'] ?>" <?= ($selectedModul == $mdl['ID_Modul']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($mdl['Judul_Modul']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Summary Row -->
                    <div class="presensi-summary-row">
                        <span class="summary-badge summary-badge-hadir" id="summary-hadir">Hadir: 0</span>
                        <span class="summary-badge summary-badge-alpa" id="summary-alpa">Alpa: 0</span>
                        <span class="summary-badge summary-badge-izin" id="summary-izin">Izin: 0</span>
                        <span class="summary-badge summary-badge-sakit" id="summary-sakit">Sakit: 0</span>
                    </div>
                </div>

                <!-- Group Cards Form -->
                <form method="POST" action="/rpl/public/index.php?action=submit_presensi" id="presensiForm">
                    <input type="hidden" name="class_id" value="<?= htmlspecialchars($selectedClass) ?>">
                    <input type="hidden" name="modul_id" value="<?= htmlspecialchars($selectedModul) ?>">

                    <?php if (empty($groupsData)): ?>
                        <div class="group-card">
                            <p style="text-align: center; color: #9B9B9B; font-weight: 600; padding: 24px;">
                                Belum ada data kelompok yang terdaftar untuk kelas Anda.
                            </p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($groupsData as $group): ?>
                            <div class="group-card">
                                <div class="group-card-header">
                                    <h3 class="group-card-title"><?= htmlspecialchars($group['group_name']) ?></h3>
                                    <span class="group-card-badge"><?= count($group['students']) ?> Mahasiswa</span>
                                </div>

                                <div class="table-scrollable-container">
                                    <table class="group-table">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th style="width: 100px;">Status</th>
                                                <th style="width: 200px; text-align: center;">Tandai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($group['students'])): ?>
                                                <tr>
                                                    <td colspan="3" style="text-align: center; color: #8A8A8A; padding: 16px;">
                                                        Tidak ada mahasiswa di kelompok ini.
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($group['students'] as $student): ?>
                                                    <?php
                                                    $savedStatus = $student['Status_Kehadiran'] ?? 'Hadir'; // default to Hadir if null
                                                    ?>
                                                    <tr data-student-id="<?= $student['ID_User'] ?>">
                                                        <td><?= htmlspecialchars($student['Nama_Lengkap']) ?></td>
                                                        <td>
                                                            <!-- Current saved status badge -->
                                                            <span class="status-display-badge status-display-<?= strtolower($savedStatus) ?>" data-display-id="<?= $student['ID_User'] ?>">
                                                                <?= htmlspecialchars($savedStatus) ?>
                                                            </span>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <div class="tandai-group">
                                                                <!-- Radio Option: Hadir -->
                                                                <label class="tandai-option">
                                                                    <input type="radio" 
                                                                           name="attendance[<?= $student['ID_User'] ?>]" 
                                                                           value="Hadir" 
                                                                           data-initial="<?= $savedStatus ?>"
                                                                           <?= ($savedStatus === 'Hadir') ? 'checked' : '' ?>
                                                                           onchange="updateRealtimePresensi(<?= $student['ID_User'] ?>, 'Hadir')">
                                                                    <span class="tandai-badge tandai-badge-hadir">Hadir</span>
                                                                </label>
                                                                <!-- Radio Option: Alpa -->
                                                                <label class="tandai-option">
                                                                    <input type="radio" 
                                                                           name="attendance[<?= $student['ID_User'] ?>]" 
                                                                           value="Alpa" 
                                                                           data-initial="<?= $savedStatus ?>"
                                                                           <?= ($savedStatus === 'Alpa') ? 'checked' : '' ?>
                                                                           onchange="updateRealtimePresensi(<?= $student['ID_User'] ?>, 'Alpa')">
                                                                    <span class="tandai-badge tandai-badge-alpa">Alpa</span>
                                                                </label>
                                                                <!-- Radio Option: Izin -->
                                                                <label class="tandai-option">
                                                                    <input type="radio" 
                                                                           name="attendance[<?= $student['ID_User'] ?>]" 
                                                                           value="Izin" 
                                                                           data-initial="<?= $savedStatus ?>"
                                                                           <?= ($savedStatus === 'Izin') ? 'checked' : '' ?>
                                                                           onchange="updateRealtimePresensi(<?= $student['ID_User'] ?>, 'Izin')">
                                                                    <span class="tandai-badge tandai-badge-izin">Izin</span>
                                                                </label>
                                                                <!-- Radio Option: Sakit -->
                                                                <label class="tandai-option">
                                                                    <input type="radio" 
                                                                           name="attendance[<?= $student['ID_User'] ?>]" 
                                                                           value="Sakit" 
                                                                           data-initial="<?= $savedStatus ?>"
                                                                           <?= ($savedStatus === 'Sakit') ? 'checked' : '' ?>
                                                                           onchange="updateRealtimePresensi(<?= $student['ID_User'] ?>, 'Sakit')">
                                                                    <span class="tandai-badge tandai-badge-sakit">Sakit</span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Footer Actions (Reset & Save) -->
                    <?php if (!empty($groupsData)): ?>
                        <div class="presensi-footer-actions">
                            <button type="button" class="btn-presensi-reset" onclick="resetPresensiForm()">Reset</button>
                            <button type="submit" class="btn-presensi-save">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                                Simpan Presensi
                            </button>
                        </div>
                    <?php endif; ?>
                </form>

                <script>
                function updateRealtimePresensi(studentId, status) {
                    const displayBadge = document.querySelector(`[data-display-id="${studentId}"]`);
                    if (displayBadge) {
                        displayBadge.textContent = status;
                        displayBadge.className = `status-display-badge status-display-${status.toLowerCase()}`;
                    }
                    calculateSummaryStats();
                }

                function calculateSummaryStats() {
                    let hadir = 0;
                    let alpa = 0;
                    let izin = 0;
                    let sakit = 0;

                    const checkedRadios = document.querySelectorAll('input[type="radio"]:checked');
                    checkedRadios.forEach(radio => {
                        if (radio.value === 'Hadir') hadir++;
                        else if (radio.value === 'Alpa') alpa++;
                        else if (radio.value === 'Izin') izin++;
                        else if (radio.value === 'Sakit') sakit++;
                    });

                    document.getElementById('summary-hadir').textContent = `Hadir: ${hadir}`;
                    document.getElementById('summary-alpa').textContent = `Alpa: ${alpa}`;
                    document.getElementById('summary-izin').textContent = `Izin: ${izin}`;
                    document.getElementById('summary-sakit').textContent = `Sakit: ${sakit}`;
                }

                function resetPresensiForm() {
                    const radios = document.querySelectorAll('input[type="radio"]');
                    radios.forEach(radio => {
                        const initialValue = radio.getAttribute('data-initial');
                        if (radio.value === initialValue) {
                            radio.checked = true;
                            const nameAttr = radio.name;
                            const studentId = nameAttr.match(/\d+/)[0];
                            updateRealtimePresensi(studentId, initialValue);
                        }
                    });
                }

                document.addEventListener('DOMContentLoaded', () => {
                    calculateSummaryStats();
                });
                </script>
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
