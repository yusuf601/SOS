<?php
// ==============================================================================
// EduLab UHO - Sanggah Nilai View (Student & Staff)
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
    <title>Sanggah Nilai - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        .sanggah-card {
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .sanggah-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-color-dark);
            margin-bottom: 20px;
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
            width: 110px;
        }

        .badge-selesai { background-color: #DEF7EC; color: #03543F; }
        .badge-sanggah { background-color: #FDE8E8; color: #9B1C1C; }
        .badge-pending { background-color: #FEF3C7; color: #D97706; }

        /* Course Cards Grid */
        .matkul-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        @media (max-width: 768px) {
            .matkul-grid {
                grid-template-columns: 1fr;
            }
        }

        .matkul-card {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05);
            border: 0.5px solid rgba(54, 64, 135, 0.2);
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: relative;
        }

        .matkul-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #000000;
        }

        .matkul-status-box {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 4px;
        }

        .matkul-status-success {
            background-color: #EAF3DE;
            color: #27500A;
        }

        .matkul-status-danger {
            background-color: #FCEBEB;
            color: #791F1F;
        }

        .matkul-stats-divider {
            border: none;
            border-top: 0.5px solid #E2E8F0;
            margin: 4px 0;
        }

        .matkul-stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        .matkul-stat-label {
            color: #8F8F8F;
            font-weight: 500;
        }

        .matkul-stat-val {
            color: #000000;
            font-weight: 600;
        }

        .matkul-final-grade-block {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 4px;
        }

        .matkul-final-label {
            font-size: 14px;
            font-weight: 600;
            color: #000000;
        }

        .matkul-final-val {
            font-size: 28px;
            font-weight: 700;
            color: #000000;
            line-height: 1;
        }

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

        .btn-sanggah {
            background-color: #DC2626;
            color: white;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.2);
        }

        .btn-sanggah:hover {
            background-color: #b91c1c;
            transform: translateY(-1px);
        }

        .btn-tanggapi {
            background-color: var(--btn-primary);
            color: white;
            box-shadow: 0 2px 6px rgba(54, 64, 135, 0.2);
        }

        .btn-tanggapi:hover {
            background-color: #2b336b;
            transform: translateY(-1px);
        }

        .btn-action-outline {
            background-color: #FFFFFF;
            color: #334155;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            height: 32px;
            padding: 0 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-action-outline:hover {
            background-color: #F8FAFC;
            border-color: #94A3B8;
            color: #0F172A;
        }

        .btn-action-outline:disabled {
            background-color: #F1F5F9;
            color: #94A3B8;
            border-color: #E2E8F0;
            cursor: not-allowed;
            box-shadow: none;
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

        .modal-header.danger {
            background-color: #DC2626;
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
            background-color: #FFFFFF;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--btn-primary);
        }

        textarea.form-control {
            height: 120px;
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

        .btn-confirm {
            height: 42px;
            padding: 0 20px;
            background-color: var(--btn-primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-confirm.danger {
            background-color: #DC2626;
        }

        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

        /* CSS untuk Layout Kartu Tinjau Sanggahan (Asisten Dosen) */
        .sanggah-list-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-top: 16px;
        }

        .sanggah-card-asdos {
            background-color: #FFFFFF;
            border: 1px solid #C8C8C8;
            border-radius: 10px;
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: relative;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }

        .sanggah-card-asdos.pending {
            border-left: 6px solid #D78F00;
        }

        .sanggah-card-asdos.resolved {
            border-left: 6px solid #58922E;
        }

        .card-header-asdos {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 12px;
        }

        .card-profile-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-avatar-asdos {
            width: 47px;
            height: 47px;
            border-radius: 50%;
            background-color: #7C94B8;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
        }

        .card-user-meta {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .card-student-name {
            font-size: 20px;
            font-weight: 600;
            color: #000000;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-student-name span.module-label {
            font-size: 15px;
            font-weight: 500;
            color: #8C8C8C;
            margin-left: 8px;
        }

        .card-student-sub {
            font-size: 13px;
            font-weight: 500;
            color: #8C8C8C;
        }

        .card-status-badge {
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-status-badge.pending {
            background-color: #FFF7D3;
            color: #92400E;
        }

        .card-status-badge.resolved {
            background-color: #EAF3DE;
            color: #2F6100;
        }

        .card-appeal-content {
            font-size: 15px;
            font-weight: 600;
            line-height: 1.5;
            color: #6C6C6C;
            margin: 8px 0 4px;
            text-align: left;
        }

        .card-meta-line {
            font-size: 14px;
            color: #6C6C6C;
            font-weight: 500;
            text-align: left;
        }

        .card-form-asdos {
            display: flex;
            flex-direction: column;
            gap: 16px;
            border-top: 1px dashed #E2E8F0;
            padding-top: 16px;
        }

        .card-textarea-wrapper {
            display: flex;
            flex-direction: column;
            gap: 6px;
            text-align: left;
        }

        .card-textarea-label {
            font-size: 14px;
            font-weight: 600;
            color: #6C6C6C;
        }

        .card-textarea-asdos {
            width: 100%;
            min-height: 80px;
            background-color: #FFFFFF;
            border: 1px solid #C8C8C8;
            border-radius: 8px;
            padding: 12px;
            font-size: 15px;
            font-weight: 500;
            color: #334155;
            outline: none;
            resize: vertical;
            transition: border-color 0.2s ease;
        }

        .card-textarea-asdos:focus {
            border-color: #364087;
        }

        .card-action-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .card-input-score-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-input-score-label {
            font-size: 13.5px;
            font-weight: 600;
            color: #475569;
        }

        .card-input-score {
            width: 90px;
            padding: 8px;
            border: 1px solid #C8C8C8;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            outline: none;
        }

        .card-input-score:focus {
            border-color: #364087;
        }

        .card-buttons-group {
            display: flex;
            gap: 12px;
        }

        .btn-card-action {
            height: 38px;
            padding: 0 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #FFFFFF;
            transition: all 0.2s ease;
        }

        .btn-card-action.approve {
            background-color: #2F6100;
            box-shadow: 0 2px 4px rgba(47, 97, 0, 0.2);
        }

        .btn-card-action.approve:hover {
            background-color: #224700;
            transform: translateY(-0.5px);
        }

        .btn-card-action.reject {
            background-color: #A32D2D;
            box-shadow: 0 2px 4px rgba(163, 45, 45, 0.2);
        }

        .btn-card-action.reject:hover {
            background-color: #822222;
            transform: translateY(-0.5px);
        }

        .response-readonly-box {
            background-color: #EAF3DE;
            border: 1px solid #C8DDC0;
            border-radius: 8px;
            padding: 14px 18px;
            margin-top: 12px;
            text-align: left;
        }

        .response-readonly-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #2F6100;
            margin-bottom: 4px;
        }

        .response-readonly-text {
            font-size: 14px;
            font-weight: 500;
            color: #475569;
            line-height: 1.4;
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
                        <li class="sidebar-menu-item active">
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
                            <li class="sidebar-menu-item">
                                <a href="/rpl/public/index.php?action=kelulusan">
                                    <span>Export Rekapitulasi</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                    </span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item active">
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
                            <li class="sidebar-menu-item">
                                <a href="/rpl/public/index.php?action=kelulusan">
                                    <span>Input Nilai</span>
                                    <span class="sidebar-menu-item-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                                    </span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item active">
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
            <h2 class="navbar-title"><?= ($role === 'Mahasiswa') ? 'Lihat Nilai' : 'Tanggapan Sanggah Nilai' ?></h2>
            <div class="navbar-profile">
                <button type="button" style="background:none; border:none; color:white; cursor:pointer;" aria-label="Notifikasi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </button>
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">
            
            <!-- Success/Error Alert -->
            <?php if (isset($_SESSION['sanggah_success'])): ?>
                <div style="background-color: #DEF7EC; color: #03543F; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 600; border: 1px solid #BCF0DA; margin-bottom: 20px;">
                    <?= htmlspecialchars($_SESSION['sanggah_success']); unset($_SESSION['sanggah_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['sanggah_error'])): ?>
                <div style="background-color: #FEE2E2; color: #DC2626; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 600; border: 1px solid #FCA5A5; margin-bottom: 20px;">
                    <?= htmlspecialchars($_SESSION['sanggah_error']); unset($_SESSION['sanggah_error']); ?>
                </div>
            <?php endif; ?>

            <?php if ($role === 'Mahasiswa'): ?>
                <!-- STUDENT VIEW -->
                <?php
                $className = $classInfo['Nama_Kelas'] ?? 'Sistem Digital A';
                
                // Calculate dynamic stats
                $avgScoreVal = $progress ? number_format((float)$progress['average_score'], 1) : '82.4';
                $finalGradeVal = $progress ? number_format((float)$progress['average_score'], 1) : '83.0';
                $presensiVal = ($attendanceRate !== null) ? round($attendanceRate) . '%' : '85%';
                $isPass = $progress ? ((float)$progress['average_score'] >= 70) : true;

                $monthsIndo = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                ?>

                <!-- Grid of Course Cards (nilai matkul) at the TOP -->
                <div class="matkul-grid">
                    <!-- Card 1: Student's Actual Class (Dynamic card) -->
                    <article class="matkul-card">
                        <span class="matkul-card-title"><?= htmlspecialchars($className) ?></span>
                        <?php if ($isPass): ?>
                            <div class="matkul-status-box matkul-status-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Di atas nilai minimum (70)</span>
                            </div>
                        <?php else: ?>
                            <div class="matkul-status-box matkul-status-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                <span>Di bawah nilai minimum (70)</span>
                            </div>
                        <?php endif; ?>
                        <hr class="matkul-stats-divider">
                        <div class="matkul-stat-row">
                            <span class="matkul-stat-label">Rata-rata tugas</span>
                            <span class="matkul-stat-val"><?= $avgScoreVal ?></span>
                        </div>
                        <div class="matkul-stat-row">
                            <span class="matkul-stat-label">Nilai presensi (30%)</span>
                            <span class="matkul-stat-val"><?= $presensiVal ?></span>
                        </div>
                        <div class="matkul-final-grade-block">
                            <span class="matkul-final-label">Nilai Akhir</span>
                            <span class="matkul-final-val" style="color: <?= $isPass ? '#16A34A' : '#DC2626' ?>;"><?= $finalGradeVal ?></span>
                        </div>
                    </article>

                    <!-- Card 2: Jaringan Komputer A (Static/Placeholder warning card) -->
                    <article class="matkul-card">
                        <span class="matkul-card-title">Jarigan Komputer A</span>
                        <div class="matkul-status-box matkul-status-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                            <span>Di atas nilai minimum (70)</span>
                        </div>
                        <hr class="matkul-stats-divider">
                        <div class="matkul-stat-row">
                            <span class="matkul-stat-label">Rata-rata tugas</span>
                            <span class="matkul-stat-val">55.0</span>
                        </div>
                        <div class="matkul-stat-row">
                            <span class="matkul-stat-label">Nilai presensi (30%)</span>
                            <span class="matkul-stat-val">58%</span>
                        </div>
                        <div class="matkul-final-grade-block">
                            <span class="matkul-final-label">Nilai Akhir</span>
                            <span class="matkul-final-val" style="color: #DC2626;">56.0</span>
                        </div>
                    </article>

                    <!-- Card 3: Student's Actual Class Duplicate (matching mockup) -->
                    <article class="matkul-card">
                        <span class="matkul-card-title"><?= htmlspecialchars($className) ?></span>
                        <?php if ($isPass): ?>
                            <div class="matkul-status-box matkul-status-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Di atas nilai minimum (70)</span>
                            </div>
                        <?php else: ?>
                            <div class="matkul-status-box matkul-status-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                <span>Di bawah nilai minimum (70)</span>
                            </div>
                        <?php endif; ?>
                        <hr class="matkul-stats-divider">
                        <div class="matkul-stat-row">
                            <span class="matkul-stat-label">Rata-rata tugas</span>
                            <span class="matkul-stat-val"><?= $avgScoreVal ?></span>
                        </div>
                        <div class="matkul-stat-row">
                            <span class="matkul-stat-label">Nilai presensi (30%)</span>
                            <span class="matkul-stat-val"><?= $presensiVal ?></span>
                        </div>
                        <div class="matkul-final-grade-block">
                            <span class="matkul-final-label">Nilai Akhir</span>
                            <span class="matkul-final-val" style="color: <?= $isPass ? '#16A34A' : '#DC2626' ?>;"><?= $finalGradeVal ?></span>
                        </div>
                    </article>
                </div>

                <section class="sanggah-card">
                    <h3 class="sanggah-title" style="font-size: 20px; font-weight: 700; color: #000000; margin-bottom: 24px; text-align: left;"><?= htmlspecialchars($className) ?></h3>
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th style="width: 8%;">Modul</th>
                                    <th style="width: 50%;">Judul</th>
                                    <th style="width: 12%; text-align: center;">Nilai</th>
                                    <th style="width: 20%;">Feedback</th>
                                    <th style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($gradesList)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: #94A3B8; padding: 24px;">Belum ada nilai tugas yang diterbitkan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($gradesList as $idx => $grade): ?>
                                        <?php
                                        $dateVal = '-';
                                        if (!empty($grade['Waktu_Submit'])) {
                                            $time = strtotime($grade['Waktu_Submit']);
                                            $d = date('j', $time);
                                            $m = $monthsIndo[(int)date('n', $time)] ?? date('F', $time);
                                            $y = date('Y', $time);
                                            $dateVal = "$d $m $y";
                                        }
                                        $score = (float)$grade['Nilai_Angka'];
                                        ?>
                                        <tr>
                                            <td style="color: #64748B; font-weight: 600;"><?= $idx + 1 ?></td>
                                            <td>
                                                <div style="font-weight: 600; color: #1E293B; display: flex; align-items: center; gap: 8px;">
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; background-color: #FEE2E2; color: #DC2626; border-radius: 4px; padding: 2px 6px; font-size: 10px; font-weight: 800; font-family: monospace; border: 1px solid #FCA5A5; line-height: 1;">PDF</span>
                                                    <?= htmlspecialchars($grade['Judul_Modul']) ?>
                                                </div>
                                                
                                                <!-- Dynamic Feedback & Appeal Details -->
                                                <div style="font-size: 12px; color: #64748B; margin-top: 6px; display: flex; flex-direction: column; gap: 4px; padding-left: 42px;">
                                                    <div>
                                                        File: <a href="/rpl/public/index.php?action=view_tugas&file=<?= urlencode($grade['File_Tugas']) ?>" target="_blank" style="color: var(--btn-primary); text-decoration: underline;"><?= htmlspecialchars(basename($grade['File_Tugas'])) ?></a>
                                                    </div>
                                                    <?php if (!empty($grade['Feedback'])): ?>
                                                        <div style="color: #475569; background-color: #F8FAFC; padding: 6px 10px; border-radius: 6px; border-left: 3px solid #CBD5E1; margin-top: 4px; max-width: 90%;">
                                                            <strong>Feedback Asisten:</strong> <?= htmlspecialchars($grade['Feedback']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($grade['Alasan_Sanggah'])): ?>
                                                        <div style="color: #991B1B; background-color: #FEF2F2; padding: 6px 10px; border-radius: 6px; border-left: 3px solid #FCA5A5; margin-top: 4px; max-width: 90%;">
                                                            <strong>Sanggahan Anda:</strong> <?= htmlspecialchars($grade['Alasan_Sanggah']) ?>
                                                            <?php if (!empty($grade['Tanggapan_Sanggah'])): ?>
                                                                <div style="margin-top: 4px; padding-top: 4px; border-top: 1px dashed #FCA5A5; color: var(--btn-primary);">
                                                                    <strong>Jawaban Asisten:</strong> <?= htmlspecialchars($grade['Tanggapan_Sanggah']) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td style="font-size: 16px; font-weight: 700; text-align: center; color: #1E293B;"><?= htmlspecialchars(round($grade['Nilai_Angka'])) ?></td>
                                            <td><?= htmlspecialchars($dateVal) ?></td>
                                            <td style="text-align: center;">
                                                <?php if ($score >= 70): ?>
                                                    <a href="/rpl/public/index.php?action=view_tugas&file=<?= urlencode($grade['File_Tugas']) ?>" target="_blank" class="btn-action-outline">Lihat</a>
                                                <?php else: ?>
                                                    <?php if (empty($grade['Alasan_Sanggah'])): ?>
                                                        <button type="button" class="btn-action-outline" 
                                                                onclick="openSanggahModal(<?= $grade['ID_Nilai'] ?>, '<?= htmlspecialchars(addslashes($grade['Judul_Modul'])) ?>', <?= $grade['Nilai_Angka'] ?>)">
                                                            Sanggah
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn-action-outline" disabled style="opacity: 0.6; cursor: not-allowed;">
                                                            Sanggah
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Student Sanggah Modal -->
                <div id="sanggahModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header danger">
                            <span class="modal-title-text" id="modalTitle">Ajukan Sanggah Nilai</span>
                            <button class="close-btn" onclick="closeSanggahModal()">&times;</button>
                        </div>
                        <form method="POST" action="/rpl/public/index.php?action=submit_sanggah">
                            <input type="hidden" name="id_nilai" id="sanggah_id_nilai">
                            <div class="modal-body">
                                <div style="background-color: #FEF2F2; border: 1px solid #FEE2E2; padding: 12px; border-radius: 8px; font-size: 13px; color: #991B1B;">
                                    <strong>Modul:</strong> <span id="sanggah_judul_modul"></span><br>
                                    <strong>Nilai Saat Ini:</strong> <span id="sanggah_nilai_lama"></span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="alasan_sanggah">Alasan Sanggahan</label>
                                    <textarea name="alasan_sanggah" id="alasan_sanggah" class="form-control" placeholder="Tuliskan argumen atau alasan logis Anda menyanggah nilai ini..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-cancel" onclick="closeSanggahModal()">Batal</button>
                                <button type="submit" class="btn-confirm danger">Kirim Sanggahan</button>
                            </div>
                        </form>
                    </div>
                </div>


                <script>
                    function openSanggahModal(idNilai, judulModul, nilaiLama) {
                        document.getElementById('sanggah_id_nilai').value = idNilai;
                        document.getElementById('sanggah_judul_modul').innerText = judulModul;
                        document.getElementById('sanggah_nilai_lama').innerText = nilaiLama;
                        document.getElementById('sanggahModal').style.display = 'flex';
                    }

                    function closeSanggahModal() {
                        document.getElementById('sanggahModal').style.display = 'none';
                    }

                    window.onclick = function(event) {
                        var modal = document.getElementById('sanggahModal');
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    }
                </script>

            <?php else: ?>
                <!-- DOSEN / ASISTEN VIEW -->
                <section class="sanggah-card">
                    <h3 class="sanggah-title">Daftar Pengajuan Sanggah Nilai Mahasiswa</h3>
                    
                    <div class="sanggah-list-container">
                        <?php if (empty($gradesList)): ?>
                            <div style="text-align: center; color: #94A3B8; padding: 48px; background-color: #F8FAFC; border: 1px dashed #E2E8F0; border-radius: 12px;">
                                Tidak ada pengajuan sanggah nilai aktif saat ini.
                            </div>
                        <?php else: ?>
                            <?php foreach ($gradesList as $grade): 
                                $hasResponded = !empty($grade['Tanggapan_Sanggah']);
                                $cardStatusClass = $hasResponded ? 'resolved' : 'pending';
                                
                                // Get student initials
                                $words = explode(" ", $grade['Nama_Mahasiswa']);
                                $stdInitials = "";
                                foreach ($words as $w) {
                                    $stdInitials .= strtoupper($w[0] ?? '');
                                }
                                $stdInitials = substr($stdInitials, 0, 2);
                            ?>
                                <div class="sanggah-card-asdos <?= $cardStatusClass ?>">
                                    <!-- Header Info -->
                                    <div class="card-header-asdos">
                                        <div class="card-profile-group">
                                            <div class="card-avatar-asdos"><?= htmlspecialchars($stdInitials) ?></div>
                                            <div class="card-user-meta">
                                                <div class="card-student-name">
                                                    <?= htmlspecialchars($grade['Nama_Mahasiswa']) ?> 
                                                    <span class="module-label"><?= htmlspecialchars($grade['Judul_Modul']) ?></span>
                                                </div>
                                                <div class="card-student-sub">
                                                    <?= htmlspecialchars($grade['NIM']) ?> &middot; <?= htmlspecialchars($grade['Nama_Kelas']) ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Status Badge -->
                                        <?php if ($hasResponded): ?>
                                            <span class="card-status-badge resolved">Telah Ditanggapi</span>
                                        <?php else: ?>
                                            <span class="card-status-badge pending">Menunggu Respon</span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Appeal Content -->
                                    <div class="card-appeal-content">
                                        &ldquo;<?= htmlspecialchars($grade['Alasan_Sanggah']) ?>&rdquo;
                                    </div>
                                    
                                    <div class="card-meta-line">
                                        Nilai tercatat: <strong><?= htmlspecialchars($grade['Nilai_Angka']) ?></strong> &middot; Modul: <?= htmlspecialchars($grade['Judul_Modul']) ?> &middot; 
                                        File Tugas: <a href="/rpl/public/index.php?action=view_tugas&file=<?= urlencode($grade['File_Tugas']) ?>" target="_blank" style="color: var(--btn-primary); text-decoration: underline; font-weight: 600;"><?= htmlspecialchars(basename($grade['File_Tugas'])) ?></a>
                                    </div>

                                    <!-- Response Form or Readonly Response -->
                                    <?php if ($hasResponded): ?>
                                        <div class="response-readonly-box">
                                            <div class="response-readonly-title">Responmu:</div>
                                            <div class="response-readonly-text">
                                                <?= htmlspecialchars($grade['Tanggapan_Sanggah']) ?>
                                                <span style="font-size: 12px; color: #64748B; display: block; margin-top: 4px;">
                                                    Status Tugas: <strong><?= htmlspecialchars($grade['Status_Tugas'] ?? 'Selesai') ?></strong>
                                                </span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <form method="POST" action="/rpl/public/index.php?action=respond_sanggah" class="card-form-asdos">
                                            <input type="hidden" name="id_nilai" value="<?= $grade['ID_Nilai'] ?>">
                                            <input type="hidden" name="nilai_baru" class="card-hidden-score" value="<?= htmlspecialchars($grade['Nilai_Angka']) ?>">
                                            <input type="hidden" name="status_tugas" value="Selesai">
                                            
                                            <div class="card-textarea-wrapper">
                                                <label class="card-textarea-label">Responmu:</label>
                                                <textarea name="tanggapan_sanggah" class="card-textarea-asdos" placeholder="Tulis respons, koreksi, atau alasan penolakan..." required></textarea>
                                            </div>

                                            <div class="card-action-row">
                                                <!-- Right: Actions -->
                                                <div class="card-buttons-group" style="margin-left: auto;">
                                                    <!-- Decline Button -->
                                                    <button type="submit" class="btn-card-action reject">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                                                        </svg>
                                                        Tolak Sanggahan
                                                    </button>

                                                    <!-- Approve Button -->
                                                    <button type="button" class="btn-card-action approve" onclick="handleApproveClick(this, '<?= htmlspecialchars(addslashes($grade['Nama_Mahasiswa'])) ?>', '<?= htmlspecialchars(addslashes($grade['Judul_Modul'])) ?>', '<?= htmlspecialchars($grade['Nilai_Angka']) ?>')">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="20 6 9 17 4 12"/>
                                                        </svg>
                                                        Setujui & Koreksi Nilai
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Koreksi Nilai Modal -->
                <div id="koreksiNilaiModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #58922E;">
                            <span class="modal-title-text">Koreksi Nilai Sanggahan</span>
                            <button class="close-btn" onclick="closeKoreksiModal()">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; padding: 16px; border-radius: 12px; font-size: 14px; color: #475569; display: flex; flex-direction: column; gap: 8px;">
                                <div style="text-align: left;"><strong>Mahasiswa:</strong> <span id="koreksi_nama_mahasiswa"></span></div>
                                <div style="text-align: left;"><strong>Modul:</strong> <span id="koreksi_judul_modul"></span></div>
                                <div style="text-align: left;"><strong>Nilai Saat Ini:</strong> <span id="koreksi_nilai_lama" style="font-weight: 700; color: #0F172A;"></span></div>
                            </div>
                            <div class="form-group" style="text-align: left;">
                                <label class="form-label" for="koreksi_nilai_baru">Koreksi Nilai Baru:</label>
                                <input type="number" id="koreksi_nilai_baru" class="form-control" min="0" max="100" step="0.01" required placeholder="Masukkan nilai baru (0-100)">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" onclick="closeKoreksiModal()">Batal</button>
                            <button type="button" class="btn-confirm" style="background-color: #2F6100;" onclick="submitApproveSanggah()">Simpan & Koreksi</button>
                        </div>
                    </div>
                </div>

                <script>
                    let activeFormElement = null;

                    function handleApproveClick(buttonElement, studentName, moduleTitle, currentScore) {
                        const form = buttonElement.closest('form');
                        if (!form) return;
                        
                        const textarea = form.querySelector('.card-textarea-asdos');
                        if (textarea && !textarea.reportValidity()) {
                            return;
                        }
                        
                        activeFormElement = form;
                        
                        document.getElementById('koreksi_nama_mahasiswa').innerText = studentName;
                        document.getElementById('koreksi_judul_modul').innerText = moduleTitle;
                        document.getElementById('koreksi_nilai_lama').innerText = currentScore;
                        
                        const newScoreInput = document.getElementById('koreksi_nilai_baru');
                        newScoreInput.value = currentScore;
                        
                        document.getElementById('koreksiNilaiModal').style.display = 'flex';
                    }

                    function closeKoreksiModal() {
                        document.getElementById('koreksiNilaiModal').style.display = 'none';
                        activeFormElement = null;
                    }

                    function submitApproveSanggah() {
                        if (!activeFormElement) return;
                        
                        const newScoreInput = document.getElementById('koreksi_nilai_baru');
                        const newScore = parseFloat(newScoreInput.value);
                        
                        if (isNaN(newScore) || newScore < 0 || newScore > 100) {
                            alert('Harap masukkan nilai yang valid antara 0 dan 100.');
                            return;
                        }
                        
                        const hiddenScoreInput = activeFormElement.querySelector('.card-hidden-score');
                        if (hiddenScoreInput) {
                            hiddenScoreInput.value = newScore;
                        }
                        
                        activeFormElement.submit();
                    }

                    // Global window click to close modal
                    const originalWindowClick = window.onclick;
                    window.onclick = function(event) {
                        if (originalWindowClick) {
                            originalWindowClick(event);
                        }
                        const koreksiModal = document.getElementById('koreksiNilaiModal');
                        if (event.target == koreksiModal) {
                            closeKoreksiModal();
                        }
                    }
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
