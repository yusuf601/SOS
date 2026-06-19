<?php
// ==============================================================================
// EduLab UHO - Pengaturan (Settings) View
// ==============================================================================

$fullName = $_SESSION['name'] ?? ($userData['Nama_Lengkap'] ?? 'Guest');
$role = $_SESSION['active_role'] ?? 'Mahasiswa';
$username = $userData['Username'] ?? '';
$email = $userData['Email'] ?? '';
$phone = $userData['No_Telepon'] ?? '';

// Generate initials for Avatar
$words = explode(" ", $fullName);
$initials = "";
foreach ($words as $w) {
    $initials .= strtoupper($w[0] ?? '');
}
$initials = substr($initials, 0, 2);

// Subtitle based on role
$subtitle = "";
if ($role === 'Mahasiswa') {
    $semester = 'Semester 6'; // Default fallback
    if (isset($classInfo['Nama_Kelas'])) {
        // Simple heuristic for semester based on class
        $semester = 'Semester 6';
    }
    $subtitle = htmlspecialchars($username) . " · Mahasiswa · " . $semester;
} elseif ($role === 'Dosen') {
    $subtitle = htmlspecialchars($username) . " · Dosen · Staff";
} elseif ($role === 'Asisten') {
    $subtitle = htmlspecialchars($username) . " · Asisten · Staff";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* Modern Premium CSS for Full Page Settings */
        .settings-container {
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03);
            border: 1px solid #F1F5F9;
            padding: 32px;
            margin-top: 24px;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .settings-section {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .settings-section-title {
            font-size: 20px;
            font-weight: 700;
            color: #1E293B;
            margin: 0;
            position: relative;
        }

        .settings-divider {
            height: 1px;
            background-color: #E2E8F0;
            border: none;
            margin: 8px 0;
        }

        /* Profile Row Layout */
        .profile-avatar-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            background-color: #F8FAFC;
            padding: 20px 24px;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
        }

        .profile-avatar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profile-avatar-large {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: linear-gradient(135deg, #364087, #4A58B8);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(54, 64, 135, 0.2);
            flex-shrink: 0;
        }

        .profile-meta-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .profile-meta-name {
            font-size: 18px;
            font-weight: 700;
            color: #1E293B;
        }

        .profile-meta-sub {
            font-size: 13px;
            color: #64748B;
            font-weight: 500;
        }

        .btn-change-photo {
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            color: #334155;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .btn-change-photo:hover {
            background: #F1F5F9;
            border-color: #94A3B8;
        }

        /* Form Grid styling */
        .settings-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 640px) {
            .settings-form-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group-full {
            grid-column: span 2;
        }

        @media (max-width: 640px) {
            .form-group-full {
                grid-column: span 1;
            }
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
        }

        .form-input {
            padding: 10px 14px;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            font-size: 14px;
            color: #1E293B;
            background-color: #FFFFFF;
            transition: all 0.2s;
            width: 100%;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: #364087;
            box-shadow: 0 0 0 3px rgba(54, 64, 135, 0.1);
        }

        .form-input:disabled {
            background-color: #F1F5F9;
            color: #64748B;
            cursor: not-allowed;
            border-color: #E2E8F0;
        }

        /* Buttons styling */
        .btn-action-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 12px;
        }

        .btn-primary-action {
            background-color: #364087;
            color: #FFFFFF;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(54, 64, 135, 0.1);
        }

        .btn-primary-action:hover {
            background-color: #2b336c;
            box-shadow: 0 4px 12px rgba(54, 64, 135, 0.2);
        }

        /* Warning/Info Banner */
        .warning-banner {
            background-color: #FFF9EB;
            border: 1px solid #FFE0B2;
            border-radius: 8px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .warning-banner-icon {
            color: #B76E00;
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }

        .warning-banner-text {
            font-size: 13.5px;
            color: #B76E00;
            font-weight: 550;
            line-height: 1.4;
        }

        /* Notifications Grid Layout (2 columns) */
        .notif-section-desc {
            font-size: 14px;
            color: #64748B;
            margin: -12px 0 12px 0;
        }

        .notif-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 1024px) {
            .notif-grid {
                grid-template-columns: 1fr;
            }
        }

        .notif-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            background: #FFFFFF;
            transition: all 0.2s ease;
            gap: 16px;
        }

        .notif-row:hover {
            border-color: #CBD5E1;
            background: #F8FAFC;
        }

        .notif-info-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .notif-item-title {
            font-size: 15px;
            font-weight: 600;
            color: #1E293B;
        }

        .notif-item-desc {
            font-size: 13px;
            color: #64748B;
            line-height: 1.4;
        }

        /* Custom Toggle Switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 25px;
            flex-shrink: 0;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #E2E8F0;
            transition: .3s;
            border-radius: 25px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 19px;
            width: 19px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        input:checked + .toggle-slider {
            background-color: #364087;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(23px);
        }

        /* Floating Toast Styling */
        .alert-toast {
            position: fixed;
            top: 24px;
            right: 24px;
            padding: 16px 24px;
            border-radius: 12px;
            color: #FFFFFF;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .alert-toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .alert-toast-success {
            background: #10B981;
        }

        .alert-toast-error {
            background: #EF4444;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar Navigation -->
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
                        <!-- Mahasiswa Sidebar Links -->
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
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=kelulusan">
                                <span>Status Kelulusan</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                </span>
                            </a>
                        </li>
                    <?php elseif ($role === 'Dosen'): ?>
                        <!-- Dosen Sidebar Links -->
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=dashboard_dosen">
                                <span>Dashboard</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
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
                            <a href="/rpl/public/index.php?action=monitoring_kelas">
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
                    <?php elseif ($role === 'Asisten'): ?>
                        <!-- Asisten Sidebar Links -->
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
                </ul>
            </nav>
        </div>

        <div>
            <div class="sidebar-divider"></div>
            <ul class="sidebar-menu-list">
                <li class="sidebar-menu-item active">
                    <a href="/rpl/public/index.php?action=pengaturan">
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
            <h2 class="navbar-title">Pengaturan Akun</h2>
            <div class="navbar-profile">
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">
            
            <!-- Settings Main Panel -->
            <section class="settings-container">
                
                <!-- SECTION 1: PROFIL -->
                <div class="settings-section">
                    <h3 class="settings-section-title">Profil</h3>
                    
                    <div class="profile-avatar-row">
                        <div class="profile-avatar-left">
                            <div class="profile-avatar-large"><?= htmlspecialchars($initials) ?></div>
                            <div class="profile-meta-info">
                                <span class="profile-meta-name"><?= htmlspecialchars($fullName) ?></span>
                                <span class="profile-meta-sub"><?= $subtitle ?></span>
                            </div>
                        </div>
                        <button type="button" class="btn-change-photo" onclick="triggerPhotoUpload()">Ganti Foto</button>
                        <input type="file" id="photo-upload-input" style="display:none;" accept="image/*" onchange="simulatePhotoUpload()">
                    </div>

                    <!-- Profile Form -->
                    <form action="/rpl/public/index.php?action=update_profil" method="POST">
                        <div class="settings-form-grid">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($fullName) ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($email) ?>" placeholder="cth: <?= strtolower(str_replace(' ', '.', $fullName)) ?>@<?= ($role === 'Mahasiswa') ? 'student.edu' : 'uho.ac.id' ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label"><?= ($role === 'Dosen') ? 'NIDN' : 'NIM' ?></label>
                                <input type="text" class="form-input" value="<?= htmlspecialchars($username) ?>" disabled>
                            </div>

                            <div class="form-group">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="phone" class="form-input" value="<?= htmlspecialchars($phone) ?>" placeholder="cth: 08223466752">
                            </div>
                        </div>

                        <div class="btn-action-container">
                            <button type="submit" class="btn-primary-action">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>

                <hr class="settings-divider">

                <!-- SECTION 2: KEAMANAN -->
                <div class="settings-section">
                    <h3 class="settings-section-title">Keamanan</h3>

                    <!-- Password Update Form -->
                    <form action="/rpl/public/index.php?action=ubah_password" method="POST">
                        <div class="settings-form-grid">
                            <div class="form-group form-group-full">
                                <label class="form-label">Password Lama</label>
                                <input type="password" name="old_password" class="form-input" placeholder="Masukkan password lama" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="new_password" class="form-input" placeholder="Minimal 8 karakter" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_password" class="form-input" placeholder="Ulangi password baru" required>
                            </div>

                            <div class="form-group form-group-full" style="margin-top: 8px;">
                                <!-- Alert Banner -->
                                <div class="warning-banner">
                                    <div class="warning-banner-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    </div>
                                    <div class="warning-banner-text">
                                        Password baru minimal 8 karakter, kombinasi huruf dan angka.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="btn-action-container">
                            <button type="submit" class="btn-primary-action">Ubah Password</button>
                        </div>
                    </form>
                </div>

                <hr class="settings-divider">

                <!-- SECTION 3: NOTIFIKASI -->
                <div class="settings-section">
                    <h3 class="settings-section-title">Notifikasi</h3>
                    <p class="notif-section-desc">Atur jenis notifikasi yang ingin kamu terima</p>

                    <div class="notif-grid">
                        <?php if ($role === 'Mahasiswa'): ?>
                            
                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pembaruan Nilai Akademik</span>
                                    <span class="notif-item-desc">Notifikasi ketika nilai baru dipublikasikan oleh dosen atau asisten dosen.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_nilai" onchange="toggleNotification('nilai')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Tanggapan atas Sanggahan</span>
                                    <span class="notif-item-desc">Notifikasi ketika dosen atau asisten dosen memberikan tanggapan terhadap sanggahan yang diajukan.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_sanggah" onchange="toggleNotification('sanggah')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pengingat Batas Waktu Tugas</span>
                                    <span class="notif-item-desc">Notifikasi satu hari sebelum batas waktu pengumpulan tugas berakhir.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_deadline" onchange="toggleNotification('deadline')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pemberitahuan Modul Baru</span>
                                    <span class="notif-item-desc">Notifikasi ketika dosen mengunggah atau menerbitkan modul pembelajaran baru.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_modul" onchange="toggleNotification('modul')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                        <?php else: ?>
                            <!-- Dosen / Asisten Notification list -->
                            
                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pengumpulan Tugas Mahasiswa</span>
                                    <span class="notif-item-desc">Notifikasi ketika mahasiswa mengirimkan pengumpulan tugas baru di kelas yang Anda ampu.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_pengumpulan" onchange="toggleNotification('pengumpulan')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pengajuan Sanggahan Baru</span>
                                    <span class="notif-item-desc">Notifikasi ketika mahasiswa mengajukan sanggah nilai baru yang memerlukan respon Anda.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_sanggah_masuk" onchange="toggleNotification('sanggah_masuk')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pembaruan Modul Pembelajaran</span>
                                    <span class="notif-item-desc">Notifikasi konfirmasi saat modul pembelajaran berhasil diunggah atau diedit.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_modul_update" onchange="toggleNotification('modul_update')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pemberitahuan Sistem</span>
                                    <span class="notif-item-desc">Notifikasi mengenai pengumuman akademik, pemeliharaan sistem, atau pembaruan platform.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_system" onchange="toggleNotification('system')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>

            </section>

        </div>
    </main>
</div>

<!-- Float Toast Notification Container -->
<div id="settings-toast" class="alert-toast">
    <span id="settings-toast-icon"></span>
    <span id="settings-toast-msg"></span>
</div>

<!-- Core JS logic for Notifications LocalStorage & Alert handling -->
<script>
    // Helper to display floating toasts
    function showToast(message, type = 'success') {
        const toast = document.getElementById('settings-toast');
        const iconContainer = document.getElementById('settings-toast-icon');
        const msgContainer = document.getElementById('settings-toast-msg');

        // Reset classes
        toast.className = 'alert-toast';
        toast.classList.add(`alert-toast-${type}`);

        // Set Icons
        if (type === 'success') {
            iconContainer.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
        } else {
            iconContainer.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;
        }

        msgContainer.innerText = message;
        toast.classList.add('show');

        // Autohide after 3.5 seconds
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3500);
    }

    // Photo Upload Simulation
    function triggerPhotoUpload() {
        document.getElementById('photo-upload-input').click();
    }

    function simulatePhotoUpload() {
        const fileInput = document.getElementById('photo-upload-input');
        if (fileInput.files && fileInput.files[0]) {
            showToast('Simulasi ganti foto profil berhasil!', 'success');
            fileInput.value = ''; // Reset input
        }
    }

    // LocalStorage Notification persistent state
    function loadNotificationPreferences() {
        const toggles = document.querySelectorAll('.toggle-switch input');
        toggles.forEach(toggle => {
            const key = `notif_${toggle.id}`;
            const val = localStorage.getItem(key);
            if (val !== null) {
                toggle.checked = val === 'true';
            }
        });
    }

    function toggleNotification(type) {
        const targetToggle = document.getElementById(`notif_${type}`);
        if (targetToggle) {
            const key = `notif_${targetToggle.id}`;
            localStorage.setItem(key, targetToggle.checked);
            showToast('Preferensi notifikasi diperbarui!', 'success');
        }
    }

    // Handle initialization & redirect messages
    window.addEventListener('DOMContentLoaded', () => {
        loadNotificationPreferences();

        // Check PHP Session messaging triggers
        <?php if (isset($_SESSION['settings_success'])): ?>
            showToast("<?= $_SESSION['settings_success'] ?>", 'success');
            <?php unset($_SESSION['settings_success']); ?>
        <?php elseif (isset($_SESSION['settings_error'])): ?>
            showToast("<?= $_SESSION['settings_error'] ?>", 'error');
            <?php unset($_SESSION['settings_error']); ?>
        <?php endif; ?>
    });
</script>

</body>
</html>
