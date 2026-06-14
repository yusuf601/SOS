<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Modul - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* CSS Extension specifically for Bank Modul layout */
        .class-selector-card {
            background-color: var(--text-color-light);
            border-radius: 15px;
            padding: 24px;
            border: 0.5px solid #DCDCDC;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 24px;
        }

        .class-selector-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .class-selector-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-color-dark);
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
            background-color: #F8FAFC;
            border: 1px solid var(--input-border);
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
            border-top: 1px solid #F3F4F6;
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

        /* Modul Cards */
        .moduls-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .modul-card {
            background-color: var(--text-color-light);
            border-radius: 15px;
            border: 0.5px solid #DCDCDC;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all var(--transition-speed) ease;
        }

        .modul-card:hover {
            transform: translateY(-2px);
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.04);
        }

        .modul-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background-color: #F8FAFC;
            border-bottom: 0.5px solid #E3E3E3;
            flex-wrap: wrap;
            gap: 16px;
        }

        .modul-identity {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .modul-number-badge {
            background-color: var(--btn-primary);
            color: var(--text-color-light);
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 15px;
            font-weight: 700;
            flex-shrink: 0;
            box-shadow: 0px 4px 8px rgba(54, 64, 135, 0.2);
        }

        .modul-title-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .modul-title {
            font-size: 18px;
            font-weight: 600;
            color: #000000;
        }

        .modul-date {
            font-size: 13px;
            color: #9B9B9B;
            font-weight: 500;
        }

        .modul-download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 40px;
            padding: 0 16px;
            background-color: rgba(54, 64, 135, 0.1);
            color: var(--btn-primary);
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all var(--transition-speed) ease;
        }

        .modul-download-btn:hover {
            background-color: var(--btn-primary);
            color: var(--text-color-light);
        }

        .modul-card-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .assignment-section {
            border: 1px dashed var(--btn-primary);
            border-radius: 12px;
            padding: 16px 20px;
            background-color: rgba(54, 64, 135, 0.02);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .assignment-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .assignment-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--btn-primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .assignment-instruction {
            font-size: 15px;
            font-weight: 500;
            color: var(--text-color-dark);
            line-height: 1.4;
        }

        .assignment-deadline {
            font-size: 13px;
            color: #FF8A8A;
            font-weight: 600;
        }

        .submission-status-block {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* Graded Feedback Panel */
        .grade-feedback-box {
            background-color: #F0FDF4; /* Light green */
            border-left: 4px solid #16A34A;
            border-radius: 0 8px 8px 0;
            padding: 14px 18px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .grade-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .grade-title {
            font-size: 13px;
            font-weight: 700;
            color: #16A34A;
            text-transform: uppercase;
        }

        .grade-score {
            background-color: #16A34A;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .feedback-text {
            font-size: 14px;
            color: #1E293B;
            line-height: 1.4;
            font-weight: 500;
            font-style: italic;
        }

        /* Badges for Submissions */
        .status-badge {
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        .status-badge-graded {
            background-color: rgba(22, 163, 74, 0.1);
            color: #16A34A;
        }

        .status-badge-pending {
            background-color: rgba(202, 138, 4, 0.1);
            color: #CA8A04;
        }

        .status-badge-missing {
            background-color: rgba(220, 38, 38, 0.1);
            color: #DC2626;
        }

        /* Buttons */
        .btn-submit {
            height: 42px;
            padding: 0 20px;
            background-color: var(--btn-primary);
            color: var(--text-color-light);
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0px 4px 10px rgba(54, 64, 135, 0.15);
            transition: all var(--transition-speed) ease;
        }

        .btn-submit:hover {
            background-color: #2b336b;
            transform: translateY(-1px);
        }

        /* Upload Modal Styles */
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
            max-width: 550px;
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
            gap: 20px;
        }

        .upload-drag-area {
            border: 2px dashed #CBD5E1;
            border-radius: 12px;
            padding: 32px 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            background-color: #F8FAFC;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
        }

        .upload-drag-area:hover, .upload-drag-area.dragover {
            border-color: var(--btn-primary);
            background-color: rgba(54, 64, 135, 0.02);
        }

        .upload-icon {
            color: var(--btn-primary);
        }

        .upload-text-primary {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-color-dark);
        }

        .upload-text-secondary {
            font-size: 13px;
            color: #9B9B9B;
            font-weight: 500;
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
        }

        .file-icon {
            color: var(--btn-primary);
        }

        .file-name-text {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-color-dark);
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .remove-file-btn {
            background: none;
            border: none;
            color: #FF8A8A;
            cursor: pointer;
            font-size: 18px;
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
            transition: background-color var(--transition-speed) ease;
        }

        .btn-cancel:hover {
            background-color: #CBD5E1;
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
                <div class="sidebar-user-name">John Doe</div>
                <div class="sidebar-user-role">Mahasiswa</div>
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
                        <a href="#">
                            <span>Upload Tugas</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#">
                            <span>Lihat Nilai</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#">
                            <span>Data Presensi</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#">
                            <span>Sanggah Nilai</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            </span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="#">
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
            <h2 class="navbar-title">Bank Modul</h2>
            <div class="navbar-profile">
                <!-- User Notification Button -->
                <button type="button" style="background:none; border:none; color:white; cursor:pointer;" aria-label="Notifikasi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </button>
                
                <div class="navbar-avatar">JD</div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">
            <!-- Class Selector -->
            <section class="class-selector-card">
                <div class="class-selector-header">
                    <span class="class-selector-title">Pilih Kelas Praktikum:</span>
                    <div class="custom-select-wrapper">
                        <select class="class-select" id="classSelector" aria-label="Pilih kelas praktikum">
                            <option value="web" selected>Praktikum Pemrograman Web (Kelas A)</option>
                            <option value="database">Praktikum Basis Data (Kelas B)</option>
                            <option value="network">Praktikum Jaringan Komputer (Kelas C)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Class Meta Details -->
                <div class="class-meta-info">
                    <div class="meta-info-item">
                        <span class="meta-info-label">Dosen Pengampu</span>
                        <span class="meta-info-value" id="lecturerName">Prof. Albert Wesker</span>
                    </div>
                    <div class="meta-info-item">
                        <span class="meta-info-label">Asisten Praktikum</span>
                        <span class="meta-info-value" id="assistantName">Chris Redfield</span>
                    </div>
                    <div class="meta-info-item">
                        <span class="meta-info-label">Jadwal & Ruang</span>
                        <span class="meta-info-value" id="classSchedule">Senin 08:00 - 10:00 (Lab Komputer 2)</span>
                    </div>
                    <div class="meta-info-item">
                        <span class="meta-info-label">Kelompok Kamu</span>
                        <span class="meta-info-value" id="groupName">Kelompok 1 (Kel. 1)</span>
                    </div>
                </div>
            </section>

            <!-- Moduls list -->
            <section class="moduls-container" id="modulsContainer">
                
                <!-- Modul 1 -->
                <article class="modul-card" id="modul-1">
                    <header class="modul-card-header">
                        <div class="modul-identity">
                            <div class="modul-number-badge">01</div>
                            <div class="modul-title-block">
                                <h3 class="modul-title">Dasar HTML & Struktur Web</h3>
                                <span class="modul-date">Diunggah pada: 08 Sep 2025</span>
                            </div>
                        </div>
                        <a href="#" class="modul-download-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>Unduh Materi</span>
                        </a>
                    </header>
                    <div class="modul-card-body">
                        <div class="assignment-section">
                            <div class="assignment-details">
                                <span class="assignment-label">Tugas Modul 01</span>
                                <p class="assignment-instruction">Buat halaman profil diri sederhana menggunakan tag HTML5 semantik.</p>
                                <span class="assignment-deadline">Deadline: 15 Sep 2025, 23:59 WITA</span>
                            </div>
                            <div class="submission-status-block">
                                <span class="status-badge status-badge-graded">Sudah Dinilai</span>
                            </div>
                        </div>
                        <!-- Grade & Feedback -->
                        <div class="grade-feedback-box">
                            <div class="grade-header">
                                <span class="grade-title">Nilai Tugas</span>
                                <span class="grade-score">90 / 100</span>
                            </div>
                            <p class="feedback-text">"Pekerjaan bagus! Struktur HTML sangat rapi, navigasi bekerja, dan tag semantik diimplementasikan dengan tepat."</p>
                        </div>
                    </div>
                </article>

                <!-- Modul 2 -->
                <article class="modul-card" id="modul-2">
                    <header class="modul-card-header">
                        <div class="modul-identity">
                            <div class="modul-number-badge">02</div>
                            <div class="modul-title-block">
                                <h3 class="modul-title">CSS styling & Layouting Flexbox</h3>
                                <span class="modul-date">Diunggah pada: 15 Sep 2025</span>
                            </div>
                        </div>
                        <a href="#" class="modul-download-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>Unduh Materi</span>
                        </a>
                    </header>
                    <div class="modul-card-body">
                        <div class="assignment-section">
                            <div class="assignment-details">
                                <span class="assignment-label">Tugas Modul 02</span>
                                <p class="assignment-instruction">Terapkan CSS Flexbox untuk membuat tata letak navbar dan layout 3 kolom responsif.</p>
                                <span class="assignment-deadline">Deadline: 22 Sep 2025, 23:59 WITA</span>
                            </div>
                            <div class="submission-status-block">
                                <span class="status-badge status-badge-graded">Sudah Dinilai</span>
                            </div>
                        </div>
                        <div class="grade-feedback-box">
                            <div class="grade-header">
                                <span class="grade-title">Nilai Tugas</span>
                                <span class="grade-score">85 / 100</span>
                            </div>
                            <p class="feedback-text">"Desain sudah responsif, tapi perhatikan margin dan padding agar konsisten di berbagai ukuran layar."</p>
                        </div>
                    </div>
                </article>

                <!-- Modul 3 -->
                <article class="modul-card" id="modul-3">
                    <header class="modul-card-header">
                        <div class="modul-identity">
                            <div class="modul-number-badge">03</div>
                            <div class="modul-title-block">
                                <h3 class="modul-title">CSS Grid & Responsive Design</h3>
                                <span class="modul-date">Diunggah pada: 22 Sep 2025</span>
                            </div>
                        </div>
                        <a href="#" class="modul-download-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>Unduh Materi</span>
                        </a>
                    </header>
                    <div class="modul-card-body">
                        <div class="assignment-section">
                            <div class="assignment-details">
                                <span class="assignment-label">Tugas Modul 03</span>
                                <p class="assignment-instruction">Buat halaman galeri foto menggunakan CSS Grid dan Media Queries untuk mobile breakpoint.</p>
                                <span class="assignment-deadline">Deadline: 29 Sep 2025, 23:59 WITA</span>
                            </div>
                            <div class="submission-status-block">
                                <span class="status-badge status-badge-graded">Sudah Dinilai</span>
                            </div>
                        </div>
                        <div class="grade-feedback-box">
                            <div class="grade-header">
                                <span class="grade-title">Nilai Tugas</span>
                                <span class="grade-score">95 / 100</span>
                            </div>
                            <p class="feedback-text">"Luar biasa! Kombinasi CSS Grid dan transisi hover yang halus. Responsivitas di layar kecil bekerja tanpa cacat."</p>
                        </div>
                    </div>
                </article>

                <!-- Modul 4 -->
                <article class="modul-card" id="modul-4">
                    <header class="modul-card-header">
                        <div class="modul-identity">
                            <div class="modul-number-badge">04</div>
                            <div class="modul-title-block">
                                <h3 class="modul-title">JavaScript DOM Manipulation & Events</h3>
                                <span class="modul-date">Diunggah pada: 29 Sep 2025</span>
                            </div>
                        </div>
                        <a href="#" class="modul-download-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>Unduh Materi</span>
                        </a>
                    </header>
                    <div class="modul-card-body">
                        <div class="assignment-section">
                            <div class="assignment-details">
                                <span class="assignment-label">Tugas Modul 04</span>
                                <p class="assignment-instruction">Buat aplikasi To-Do List interaktif dengan fitur tambah, hapus, dan tandai selesai menggunakan JS DOM.</p>
                                <span class="assignment-deadline">Deadline: 06 Okt 2025, 23:59 WITA</span>
                            </div>
                            <div class="submission-status-block">
                                <span class="status-badge status-badge-graded">Sudah Dinilai</span>
                            </div>
                        </div>
                        <div class="grade-feedback-box">
                            <div class="grade-header">
                                <span class="grade-title">Nilai Tugas</span>
                                <span class="grade-score">88 / 100</span>
                            </div>
                            <p class="feedback-text">"Logika JS bekerja dengan baik. Sedikit saran, simpan data ke LocalStorage agar tidak hilang ketika di-refresh."</p>
                        </div>
                    </div>
                </article>

                <!-- Modul 5 -->
                <article class="modul-card" id="modul-5">
                    <header class="modul-card-header">
                        <div class="modul-identity">
                            <div class="modul-number-badge">05</div>
                            <div class="modul-title-block">
                                <h3 class="modul-title">PHP Scripting Basics</h3>
                                <span class="modul-date">Diunggah pada: 06 Okt 2025</span>
                            </div>
                        </div>
                        <a href="#" class="modul-download-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>Unduh Materi</span>
                        </a>
                    </header>
                    <div class="modul-card-body">
                        <div class="assignment-section">
                            <div class="assignment-details">
                                <span class="assignment-label">Tugas Modul 05</span>
                                <p class="assignment-instruction">Buat script kalkulator IPK mahasiswa menggunakan percabangan dan perulangan array di PHP.</p>
                                <span class="assignment-deadline">Deadline: 13 Okt 2025, 23:59 WITA</span>
                            </div>
                            <div class="submission-status-block">
                                <span class="status-badge status-badge-graded">Sudah Dinilai</span>
                            </div>
                        </div>
                        <div class="grade-feedback-box">
                            <div class="grade-header">
                                <span class="grade-title">Nilai Tugas</span>
                                <span class="grade-score">92 / 100</span>
                            </div>
                            <p class="feedback-text">"Array multidimensi digunakan secara efisien. Output tabel tercetak dengan rapi di halaman browser."</p>
                        </div>
                    </div>
                </article>

                <!-- Modul 6 -->
                <article class="modul-card" id="modul-6">
                    <header class="modul-card-header">
                        <div class="modul-identity">
                            <div class="modul-number-badge">06</div>
                            <div class="modul-title-block">
                                <h3 class="modul-title">Form Handling & Database Connection</h3>
                                <span class="modul-date">Diunggah pada: 13 Okt 2025</span>
                            </div>
                        </div>
                        <a href="#" class="modul-download-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>Unduh Materi</span>
                        </a>
                    </header>
                    <div class="modul-card-body">
                        <div class="assignment-section">
                            <div class="assignment-details">
                                <span class="assignment-label">Tugas Modul 06</span>
                                <p class="assignment-instruction">Buat sistem CRUD sederhana untuk manajemen data mahasiswa terintegrasi MariaDB.</p>
                                <span class="assignment-deadline">Deadline: 20 Okt 2025, 23:59 WITA</span>
                            </div>
                            <div class="submission-status-block" id="status-container-6">
                                <span class="status-badge status-badge-pending">Menunggu Penilaian</span>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Modul 7 -->
                <article class="modul-card" id="modul-7">
                    <header class="modul-card-header">
                        <div class="modul-identity">
                            <div class="modul-number-badge">07</div>
                            <div class="modul-title-block">
                                <h3 class="modul-title">Implementasi MVC Arsitektur di PHP</h3>
                                <span class="modul-date">Diunggah pada: 20 Okt 2025</span>
                            </div>
                        </div>
                        <a href="#" class="modul-download-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>Unduh Materi</span>
                        </a>
                    </header>
                    <div class="modul-card-body">
                        <div class="assignment-section">
                            <div class="assignment-details">
                                <span class="assignment-label">Tugas Modul 07</span>
                                <p class="assignment-instruction">Refactor kode CRUD Modul 06 ke dalam struktur MVC PHP Native (Model, View, Controller).</p>
                                <span class="assignment-deadline" id="deadline-val-7">Deadline: 25 Jun 2026, 23:59 WITA</span>
                            </div>
                            <div class="submission-status-block" id="status-container-7">
                                <button type="button" class="btn-submit" onclick="openUploadModal(7, 'Implementasi MVC Arsitektur di PHP')">Kumpulkan Tugas</button>
                            </div>
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </main>
</div>

<!-- Upload Modal -->
<div class="modal" id="uploadModal" aria-hidden="true" role="dialog">
    <div class="modal-content">
        <header class="modal-header">
            <h3 class="modal-title-text" id="modalTitle">Unggah Tugas: Modul 07</h3>
            <button type="button" class="close-btn" onclick="closeUploadModal()">&times;</button>
        </header>
        <form id="uploadForm" onsubmit="handleFormSubmit(event)">
            <input type="hidden" id="submitModulId" value="">
            <div class="modal-body">
                <p style="font-size: 14px; color: #475569; font-weight: 500;">Silakan unggah file laporan tugas praktikum Anda (format .zip atau .pdf, maksimal 10MB).</p>
                
                <!-- Drag and drop zone -->
                <div class="upload-drag-area" id="dragArea" onclick="triggerFileSelect()">
                    <span class="upload-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    </span>
                    <span class="upload-text-primary">Tarik & lepas file di sini atau klik untuk mencari</span>
                    <span class="upload-text-secondary">Mendukung file ZIP, RAR, PDF hingga 10MB</span>
                    <input type="file" id="fileInput" class="file-input-hidden" accept=".zip,.rar,.pdf" onchange="handleFileSelect(event)">
                </div>

                <!-- Display selected file info -->
                <div class="selected-file-display" id="fileDisplay">
                    <span class="file-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    </span>
                    <span class="file-name-text" id="fileName">laporan_praktikum_web_m7.zip</span>
                    <button type="button" class="remove-file-btn" onclick="removeSelectedFile(event)">&times;</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeUploadModal()">Batal</button>
                <button type="submit" class="btn-submit" id="btnSubmitModal" disabled>Kirim Tugas</button>
            </div>
        </form>
    </div>
</div>

<!-- Success Toast Notification -->
<div id="toastNotification" class="toast">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
    <span id="toastMessage">Tugas berhasil diunggah!</span>
</div>

<script>
    // Class metadata switcher logic (to simulate DB querying)
    const classData = {
        web: {
            lecturer: "Prof. Albert Wesker",
            assistant: "Chris Redfield",
            schedule: "Senin 08:00 - 10:00 (Lab Komputer 2)",
            group: "Kelompok 1 (Kel. 1)",
            modules: [
                { id: 1, num: "01", title: "Dasar HTML & Struktur Web", date: "Diunggah pada: 08 Sep 2025", task: "Buat halaman profil diri sederhana menggunakan tag HTML5 semantik.", deadline: "Deadline: 15 Sep 2025, 23:59 WITA", status: "graded", score: "90 / 100", feedback: "Pekerjaan bagus! Struktur HTML sangat rapi, navigasi bekerja, dan tag semantik diimplementasikan dengan tepat." },
                { id: 2, num: "02", title: "CSS styling & Layouting Flexbox", date: "Diunggah pada: 15 Sep 2025", task: "Terapkan CSS Flexbox untuk membuat tata letak navbar dan layout 3 kolom responsif.", deadline: "Deadline: 22 Sep 2025, 23:59 WITA", status: "graded", score: "85 / 100", feedback: "Desain sudah responsif, tapi perhatikan margin dan padding agar konsisten di berbagai ukuran layar." },
                { id: 3, num: "03", title: "CSS Grid & Responsive Design", date: "Diunggah pada: 22 Sep 2025", task: "Buat halaman galeri foto menggunakan CSS Grid dan Media Queries untuk mobile breakpoint.", deadline: "Deadline: 29 Sep 2025, 23:59 WITA", status: "graded", score: "95 / 100", feedback: "Luar biasa! Kombinasi CSS Grid dan transisi hover yang halus. Responsivitas di layar kecil bekerja tanpa cacat." },
                { id: 4, num: "04", title: "JavaScript DOM Manipulation & Events", date: "Diunggah pada: 29 Sep 2025", task: "Buat aplikasi To-Do List interaktif dengan fitur tambah, hapus, dan tandai selesai menggunakan JS DOM.", deadline: "Deadline: 06 Okt 2025, 23:59 WITA", status: "graded", score: "88 / 100", feedback: "Logika JS bekerja dengan baik. Sedikit saran, simpan data ke LocalStorage agar tidak hilang ketika di-refresh." },
                { id: 5, num: "05", title: "PHP Scripting Basics", date: "Diunggah pada: 06 Okt 2025", task: "Buat script kalkulator IPK mahasiswa menggunakan percabangan dan perulangan array di PHP.", deadline: "Deadline: 13 Okt 2025, 23:59 WITA", status: "graded", score: "92 / 100", feedback: "Array multidimensi digunakan secara efisien. Output tabel tercetak dengan rapi di halaman browser." },
                { id: 6, num: "06", title: "Form Handling & Database Connection", date: "Diunggah pada: 13 Okt 2025", task: "Buat sistem CRUD sederhana untuk manajemen data mahasiswa terintegrasi MariaDB.", deadline: "Deadline: 20 Okt 2025, 23:59 WITA", status: "pending" },
                { id: 7, num: "07", title: "Implementasi MVC Arsitektur di PHP", date: "Diunggah pada: 20 Okt 2025", task: "Refactor kode CRUD Modul 06 ke dalam struktur MVC PHP Native (Model, View, Controller).", deadline: "Deadline: 25 Jun 2026, 23:59 WITA", status: "submit" }
            ]
        },
        database: {
            lecturer: "Dr. Jill Valentine",
            assistant: "Claire Redfield",
            schedule: "Rabu 10:00 - 12:00 (Lab Komputer 1)",
            group: "Kelompok 3 (Kel. 3)",
            modules: [
                { id: 1, num: "01", title: "Entity-Relationship Diagram (ERD)", date: "Diunggah pada: 10 Sep 2025", task: "Rancang ERD untuk studi kasus sistem manajemen perpustakaan kampus.", deadline: "Deadline: 17 Sep 2025, 23:59 WITA", status: "graded", score: "92 / 100", feedback: "Kardinalitas hubungan antar entitas dideskripsikan secara akurat. Relasi M-N diselesaikan secara tepat." },
                { id: 2, num: "02", title: "DDL & DML Dasar", date: "Diunggah pada: 17 Sep 2025", task: "Buat script SQL DDL untuk skema ERD yang telah dirancang sebelumnya.", deadline: "Deadline: 24 Sep 2025, 23:59 WITA", status: "graded", score: "87 / 100", feedback: "Script SQL berjalan dengan baik, pastikan tipe data dan primary key terdefinisi secara jelas." },
                { id: 3, num: "03", title: "Querying & Join Table", date: "Diunggah pada: 24 Sep 2025", task: "Buat query untuk menampilkan statistik peminjaman buku per mahasiswa menggunakan JOIN & GROUP BY.", deadline: "Deadline: 01 Okt 2025, 23:59 WITA", status: "graded", score: "90 / 100", feedback: "Query JOIN sangat optimal, penanganan GROUP BY dan agregasi COUNT selesai dengan tepat." },
                { id: 4, num: "04", title: "Stored Procedure & Triggers", date: "Diunggah pada: 01 Okt 2025", task: "Buat trigger untuk mengurangkan stok buku secara otomatis ketika ada peminjaman baru.", deadline: "Deadline: 08 Okt 2025, 23:59 WITA", status: "submit" }
            ]
        },
        network: {
            lecturer: "Prof. Leon S. Kennedy",
            assistant: "Ada Wong",
            schedule: "Jumat 14:00 - 16:00 (Lab Jaringan)",
            group: "Kelompok 2 (Kel. 2)",
            modules: [
                { id: 1, num: "01", title: "IP Address Subnetting (CIDR)", date: "Diunggah pada: 12 Sep 2025", task: "Lakukan subnetting kelas C untuk pembagian 4 gedung di fakultas.", deadline: "Deadline: 19 Sep 2025, 23:59 WITA", status: "graded", score: "96 / 100", feedback: "Subnet mask dan range IP host dihitung dengan presisi matematika yang sempurna." },
                { id: 2, num: "02", title: "Instalasi & Konfigurasi Cisco Packet Tracer", date: "Diunggah pada: 19 Sep 2025", task: "Simulasikan perutean dinamis RIPv2 pada topologi 3 router.", deadline: "Deadline: 26 Sep 2025, 23:59 WITA", status: "submit" }
            ]
        }
    };

    // DOM Elements
    const classSelector = document.getElementById('classSelector');
    const lecturerName = document.getElementById('lecturerName');
    const assistantName = document.getElementById('assistantName');
    const classSchedule = document.getElementById('classSchedule');
    const groupName = document.getElementById('groupName');
    const modulsContainer = document.getElementById('modulsContainer');

    // Handle Class Selection Change
    classSelector.addEventListener('change', function() {
        const selectedClass = this.value;
        const data = classData[selectedClass];
        
        // Update details
        lecturerName.textContent = data.lecturer;
        assistantName.textContent = data.assistant;
        classSchedule.textContent = data.schedule;
        groupName.textContent = data.group;

        // Render Modules
        renderModules(data.modules);
    });

    // Function to render modules list
    function renderModules(modules) {
        modulsContainer.innerHTML = '';
        
        modules.forEach(modul => {
            let actionBlock = '';
            let gradeBlock = '';

            if (modul.status === 'graded') {
                actionBlock = `<span class="status-badge status-badge-graded">Sudah Dinilai</span>`;
                gradeBlock = `
                    <div class="grade-feedback-box">
                        <div class="grade-header">
                            <span class="grade-title">Nilai Tugas</span>
                            <span class="grade-score">${modul.score}</span>
                        </div>
                        <p class="feedback-text">"${modul.feedback}"</p>
                    </div>
                `;
            } else if (modul.status === 'pending') {
                actionBlock = `<span class="status-badge status-badge-pending">Menunggu Penilaian</span>`;
            } else if (modul.status === 'submit') {
                actionBlock = `<button type="button" class="btn-submit" onclick="openUploadModal(${modul.id}, '${modul.title}')">Kumpulkan Tugas</button>`;
            } else {
                actionBlock = `<span class="status-badge status-badge-missing">Belum Mengumpulkan</span>`;
            }

            const cardHtml = `
                <article class="modul-card" id="modul-${modul.id}">
                    <header class="modul-card-header">
                        <div class="modul-identity">
                            <div class="modul-number-badge">${modul.num}</div>
                            <div class="modul-title-block">
                                <h3 class="modul-title">${modul.title}</h3>
                                <span class="modul-date">${modul.date}</span>
                            </div>
                        </div>
                        <a href="#" class="modul-download-btn" onclick="event.preventDefault(); showToast('Mengunduh materi: ${modul.title}...');">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>Unduh Materi</span>
                        </a>
                    </header>
                    <div class="modul-card-body">
                        <div class="assignment-section">
                            <div class="assignment-details">
                                <span class="assignment-label">Tugas Modul ${modul.num}</span>
                                <p class="assignment-instruction">${modul.task}</p>
                                <span class="assignment-deadline">${modul.deadline}</span>
                            </div>
                            <div class="submission-status-block" id="status-container-${modul.id}">
                                ${actionBlock}
                            </div>
                        </div>
                        ${gradeBlock}
                    </div>
                </article>
            `;
            modulsContainer.innerHTML += cardHtml;
        });
    }

    // Modal & Upload Actions
    const uploadModal = document.getElementById('uploadModal');
    const modalTitle = document.getElementById('modalTitle');
    const submitModulId = document.getElementById('submitModulId');
    const dragArea = document.getElementById('dragArea');
    const fileInput = document.getElementById('fileInput');
    const fileDisplay = document.getElementById('fileDisplay');
    const fileName = document.getElementById('fileName');
    const btnSubmitModal = document.getElementById('btnSubmitModal');
    const toast = document.getElementById('toastNotification');
    const toastMessage = document.getElementById('toastMessage');

    function openUploadModal(id, title) {
        submitModulId.value = id;
        modalTitle.textContent = `Unggah Tugas: Modul ${String(id).padStart(2, '0')}`;
        
        // Reset file input
        fileInput.value = '';
        fileDisplay.style.display = 'none';
        dragArea.style.display = 'flex';
        btnSubmitModal.disabled = true;

        uploadModal.style.display = 'flex';
        uploadModal.setAttribute('aria-hidden', 'false');
    }

    function closeUploadModal() {
        uploadModal.style.display = 'none';
        uploadModal.setAttribute('aria-hidden', 'true');
    }

    function triggerFileSelect() {
        fileInput.click();
    }

    function handleFileSelect(event) {
        const files = event.target.files;
        if (files.length > 0) {
            displaySelectedFile(files[0]);
        }
    }

    function displaySelectedFile(file) {
        fileName.textContent = file.name;
        dragArea.style.display = 'none';
        fileDisplay.style.display = 'flex';
        btnSubmitModal.disabled = false;
    }

    function removeSelectedFile(event) {
        event.stopPropagation();
        fileInput.value = '';
        fileDisplay.style.display = 'none';
        dragArea.style.display = 'flex';
        btnSubmitModal.disabled = true;
    }

    // Drag and Drop implementation
    ['dragenter', 'dragover'].forEach(eventName => {
        dragArea.addEventListener(eventName, e => {
            e.preventDefault();
            dragArea.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dragArea.addEventListener(eventName, e => {
            e.preventDefault();
            dragArea.classList.remove('dragover');
        }, false);
    });

    dragArea.addEventListener('drop', e => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            fileInput.files = files;
            displaySelectedFile(files[0]);
        }
    });

    // Handle Form Submit
    function handleFormSubmit(event) {
        event.preventDefault();
        const id = submitModulId.value;
        closeUploadModal();
        
        // Update the status on the page to "Menunggu Penilaian"
        const container = document.getElementById(`status-container-${id}`);
        if (container) {
            container.innerHTML = `<span class="status-badge status-badge-pending">Menunggu Penilaian</span>`;
        }

        // Show Toast
        showToast("Tugas Modul " + String(id).padStart(2, '0') + " berhasil diunggah!");
    }

    // Toast Notification helper
    function showToast(message) {
        toastMessage.textContent = message;
        toast.className = "toast show";
        setTimeout(() => {
            toast.className = toast.className.replace("show", "");
        }, 3000);
    }
</script>

</body>
</html>
