<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Tugas - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* CSS Extension specifically for Upload Tugas layout */
        .class-selector-card {
            background-color: #FFFFFF; /* Pure white card */
            border-radius: 15px;
            padding: 32px; /* Increased whitespace */
            border: none; /* Removed border */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05), 0 2px 10px rgba(0, 0, 0, 0.02); /* Elevated shadow */
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
            background-color: #FFFFFF; /* Pure white select background */
            border: 1px solid #E2E8F0; /* Softer border */
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

        /* Progress Header Panel */
        .progress-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .summary-card {
            background-color: #FFFFFF; /* Pure white card */
            border-radius: 12px;
            padding: 20px 24px; /* Increased padding */
            border: none; /* Removed border */
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04), 0 2px 8px rgba(0, 0, 0, 0.01); /* Elevated shadow */
        }

        .summary-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .icon-success { background-color: rgba(22, 163, 74, 0.1); color: #16A34A; }
        .icon-warning { background-color: rgba(202, 138, 4, 0.1); color: #CA8A04; }
        .icon-danger { background-color: rgba(220, 38, 38, 0.1); color: #DC2626; }

        .summary-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: 700;
            color: #000000;
        }

        .summary-label {
            font-size: 13px;
            font-weight: 500;
            color: #8A8A8A;
        }

        /* Task Cards */
        .tasks-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .task-card {
            background-color: #FFFFFF; /* Pure white card */
            border-radius: 15px;
            border: none; /* Removed border */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05), 0 2px 10px rgba(0, 0, 0, 0.02); /* Elevated shadow */
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all var(--transition-speed) ease;
        }

        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08), 0 4px 15px rgba(0, 0, 0, 0.04); /* Elevated hover shadow */
        }

        /* Left status borders */
        .task-card-graded { border-left: 5px solid #16A34A; }
        .task-card-pending { border-left: 5px solid #CA8A04; }
        .task-card-missing { border-left: 5px solid #DC2626; }
        .task-card-revision { border-left: 5px solid #EA580C; }

        .task-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 32px; /* Increased padding */
            background-color: #FFFFFF; /* Pure white header */
            border-bottom: none; /* Removed border */
            flex-wrap: wrap;
            gap: 16px;
        }

        .task-identity {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .task-number-badge {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 15px;
            font-weight: 700;
            flex-shrink: 0;
            color: white;
        }

        .badge-graded { background-color: #16A34A; box-shadow: 0px 4px 8px rgba(22, 163, 74, 0.2); }
        .badge-pending { background-color: #CA8A04; box-shadow: 0px 4px 8px rgba(202, 138, 4, 0.2); }
        .badge-missing { background-color: #DC2626; box-shadow: 0px 4px 8px rgba(220, 38, 38, 0.2); }
        .badge-revision { background-color: #EA580C; box-shadow: 0px 4px 8px rgba(234, 88, 12, 0.2); }

        .task-title-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .task-title {
            font-size: 18px;
            font-weight: 600;
            color: #000000;
        }

        .task-deadline {
            font-size: 13px;
            font-weight: 600;
        }

        .deadline-normal { color: #8A8A8A; }
        .deadline-urgent { color: #DC2626; }

        .status-badge {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        .status-badge-graded { background-color: rgba(22, 163, 74, 0.1); color: #16A34A; }
        .status-badge-pending { background-color: rgba(202, 138, 4, 0.1); color: #CA8A04; }
        .status-badge-missing { background-color: rgba(220, 38, 38, 0.1); color: #DC2626; }
        .status-badge-revision { background-color: rgba(234, 88, 12, 0.1); color: #EA580C; }

        .task-card-body {
            padding: 28px 32px; /* Increased padding */
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .task-instruction-box {
            background-color: #FAFBFC; /* Lighter container background */
            border-radius: 8px;
            padding: 14px 18px;
            border: 1px solid #F1F5F9; /* Softer border */
        }

        .instruction-title {
            font-size: 12px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: block;
        }

        .instruction-content {
            font-size: 15px;
            color: var(--text-color-dark);
            line-height: 1.5;
            font-weight: 500;
        }

        /* Submitted File Row */
        .submitted-file-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: rgba(54, 64, 135, 0.03);
            border: 1px solid rgba(54, 64, 135, 0.1);
            border-radius: 8px;
            padding: 12px 18px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .submitted-file-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .file-icon {
            color: var(--btn-primary);
        }

        .file-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .file-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-color-dark);
        }

        .file-date {
            font-size: 12px;
            color: #8A8A8A;
            font-weight: 500;
        }

        /* Action Buttons */
        .actions-row {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 8px;
        }

        .btn-action {
            height: 42px;
            padding: 0 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-upload {
            background-color: var(--btn-primary);
            color: white;
            border: none;
            box-shadow: 0px 4px 10px rgba(54, 64, 135, 0.15);
        }

        .btn-upload:hover {
            background-color: #2b336b;
            transform: translateY(-1px);
        }

        .btn-reupload {
            background-color: rgba(54, 64, 135, 0.08);
            color: var(--btn-primary);
            border: 1px solid rgba(54, 64, 135, 0.2);
        }

        .btn-reupload:hover {
            background-color: rgba(54, 64, 135, 0.15);
        }

        .btn-cancel-submit {
            background-color: transparent;
            color: #FF8A8A;
            border: 1px solid rgba(255, 138, 138, 0.3);
        }

        .btn-cancel-submit:hover {
            background-color: rgba(255, 138, 138, 0.05);
        }

        /* Revision Comments */
        .revision-box {
            background-color: rgba(234, 88, 12, 0.04);
            border-left: 4px solid #EA580C;
            border-radius: 0 8px 8px 0;
            padding: 14px 18px;
        }

        .revision-header {
            font-size: 13px;
            font-weight: 700;
            color: #EA580C;
            text-transform: uppercase;
            margin-bottom: 4px;
            display: block;
        }

        .revision-comment {
            font-size: 14px;
            color: #1E293B;
            line-height: 1.4;
            font-style: italic;
            font-weight: 500;
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

        .file-icon-modal {
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
            <h2 class="navbar-title">Upload Tugas</h2>
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
            </section>

            <!-- Progress Summary Row -->
            <section class="progress-summary-grid" id="progressSummary">
                <div class="summary-card">
                    <div class="summary-icon-wrapper icon-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div class="summary-details">
                        <span class="summary-value" id="valGraded">5 / 7</span>
                        <span class="summary-label">Tugas Dinilai</span>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon-wrapper icon-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div class="summary-details">
                        <span class="summary-value" id="valPending">1 / 7</span>
                        <span class="summary-label">Menunggu Penilaian</span>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon-wrapper icon-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <div class="summary-details">
                        <span class="summary-value" id="valMissing">1 / 7</span>
                        <span class="summary-label">Belum Mengumpulkan</span>
                    </div>
                </div>
            </section>

            <!-- Tasks List -->
            <section class="tasks-container" id="tasksContainer">
                <!-- Populated dynamically by JS -->
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
                    <span class="file-icon-modal">
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
    // Class metadata & tasks mock data
    const classData = {
        web: {
            summary: { graded: "5 / 7", pending: "1 / 7", missing: "1 / 7" },
            tasks: [
                { id: 1, num: "01", title: "Dasar HTML & Struktur Web", instr: "Buat halaman profil diri sederhana menggunakan tag HTML5 semantik.", deadline: "Deadline: 15 Sep 2025, 23:59 WITA", status: "graded", file: "laporan_html_m1.zip", date: "Diunggah pada: 14 Sep 2025, 20:30 WITA" },
                { id: 2, num: "02", title: "CSS Styling & Layouting Flexbox", instr: "Terapkan CSS Flexbox untuk membuat tata letak navbar dan layout 3 kolom responsif.", deadline: "Deadline: 22 Sep 2025, 23:59 WITA", status: "graded", file: "laporan_css_m2.zip", date: "Diunggah pada: 21 Sep 2025, 18:45 WITA" },
                { id: 3, num: "03", title: "CSS Grid & Responsive Design", instr: "Buat halaman galeri foto menggunakan CSS Grid dan Media Queries untuk mobile breakpoint.", deadline: "Deadline: 29 Sep 2025, 23:59 WITA", status: "graded", file: "laporan_grid_m3.zip", date: "Diunggah pada: 28 Sep 2025, 22:15 WITA" },
                { id: 4, num: "04", title: "JavaScript DOM Manipulation & Events", instr: "Buat aplikasi To-Do List interaktif dengan fitur tambah, hapus, dan tandai selesai menggunakan JS DOM.", deadline: "Deadline: 06 Okt 2025, 23:59 WITA", status: "graded", file: "laporan_js_m4.zip", date: "Diunggah pada: 05 Okt 2025, 21:00 WITA" },
                { id: 5, num: "05", title: "PHP Scripting Basics", instr: "Buat script kalkulator IPK mahasiswa menggunakan percabangan dan perulangan array di PHP.", deadline: "Deadline: 13 Okt 2025, 23:59 WITA", status: "graded", file: "laporan_php_m5.zip", date: "Diunggah pada: 12 Okt 2025, 23:10 WITA" },
                { id: 6, num: "06", title: "Form Handling & Database Connection", instr: "Buat sistem CRUD sederhana untuk manajemen data mahasiswa terintegrasi MariaDB.", deadline: "Deadline: 20 Okt 2025, 23:59 WITA", status: "pending", file: "laporan_crud_m6.zip", date: "Diunggah pada: 19 Okt 2025, 14:00 WITA" },
                { id: 7, num: "07", title: "Implementasi MVC Arsitektur di PHP", instr: "Refactor kode CRUD Modul 06 ke dalam struktur MVC PHP Native (Model, View, Controller).", deadline: "Deadline: 25 Jun 2026, 23:59 WITA", status: "missing" }
            ]
        },
        database: {
            summary: { graded: "3 / 4", pending: "0 / 4", missing: "1 / 4" },
            tasks: [
                { id: 1, num: "01", title: "Entity-Relationship Diagram (ERD)", instr: "Rancang ERD untuk studi kasus sistem manajemen perpustakaan kampus.", deadline: "Deadline: 17 Sep 2025, 23:59 WITA", status: "graded", file: "erd_library_m1.zip", date: "Diunggah pada: 15 Sep 2025, 10:15 WITA" },
                { id: 2, num: "02", title: "DDL & DML Dasar SQL", instr: "Buat script SQL DDL untuk skema ERD yang telah dirancang sebelumnya.", deadline: "Deadline: 24 Sep 2025, 23:59 WITA", status: "graded", file: "ddl_dml_m2.zip", date: "Diunggah pada: 23 Sep 2025, 18:20 WITA" },
                { id: 3, num: "03", title: "Querying & Join Table", instr: "Buat query untuk menampilkan statistik peminjaman buku per mahasiswa menggunakan JOIN & GROUP BY.", deadline: "Deadline: 01 Okt 2025, 23:59 WITA", status: "graded", file: "queries_join_m3.zip", date: "Diunggah pada: 30 Sep 2025, 22:50 WITA" },
                { id: 4, num: "04", title: "Stored Procedure & Triggers", instr: "Buat trigger untuk mengurangkan stok buku secara otomatis ketika ada peminjaman baru.", deadline: "Deadline: 08 Okt 2025, 23:59 WITA", status: "missing" }
            ]
        },
        network: {
            summary: { graded: "1 / 2", pending: "0 / 2", missing: "1 / 2" },
            tasks: [
                { id: 1, num: "01", title: "IP Address Subnetting (CIDR)", instr: "Lakukan subnetting kelas C untuk pembagian 4 gedung di fakultas.", deadline: "Deadline: 19 Sep 2025, 23:59 WITA", status: "graded", file: "subnetting_m1.zip", date: "Diunggah pada: 18 Sep 2025, 19:40 WITA" },
                { id: 2, num: "02", title: "Routing Dinamis RIPv2 & OSPF", instr: "Simulasikan perutean dinamis RIPv2 pada topologi 3 router di Cisco Packet Tracer.", deadline: "Deadline: 26 Sep 2025, 23:59 WITA", status: "missing" }
            ]
        }
    };

    // DOM Elements
    const classSelector = document.getElementById('classSelector');
    const valGraded = document.getElementById('valGraded');
    const valPending = document.getElementById('valPending');
    const valMissing = document.getElementById('valMissing');
    const tasksContainer = document.getElementById('tasksContainer');

    // Handle Class Selection
    classSelector.addEventListener('change', function() {
        const selectedClass = this.value;
        const data = classData[selectedClass];

        // Update summary values
        valGraded.textContent = data.summary.graded;
        valPending.textContent = data.summary.pending;
        valMissing.textContent = data.summary.missing;

        // Render Tasks
        renderTasks(data.tasks);
    });

    // Render Tasks Function
    function renderTasks(tasks) {
        tasksContainer.innerHTML = '';

        tasks.forEach(task => {
            let statusText = '';
            let borderClass = '';
            let badgeClass = '';
            let fileRow = '';
            let actionsBlock = '';

            if (task.status === 'graded') {
                statusText = 'Sudah Dinilai';
                borderClass = 'task-card-graded';
                badgeClass = 'badge-graded';
                fileRow = `
                    <div class="submitted-file-row">
                        <div class="submitted-file-info">
                            <span class="file-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            </span>
                            <div class="file-details">
                                <span class="file-name">${task.file}</span>
                                <span class="file-date">${task.date}</span>
                            </div>
                        </div>
                    </div>
                `;
                actionsBlock = ''; // No action needed once graded
            } else if (task.status === 'pending') {
                statusText = 'Menunggu Penilaian';
                borderClass = 'task-card-pending';
                badgeClass = 'badge-pending';
                fileRow = `
                    <div class="submitted-file-row">
                        <div class="submitted-file-info">
                            <span class="file-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            </span>
                            <div class="file-details">
                                <span class="file-name">${task.file}</span>
                                <span class="file-date">${task.date}</span>
                            </div>
                        </div>
                    </div>
                `;
                actionsBlock = `
                    <div class="actions-row">
                        <button type="button" class="btn-action btn-cancel-submit" onclick="cancelSubmission(${task.id})">Batalkan Pengumpulan</button>
                        <button type="button" class="btn-action btn-reupload" onclick="openUploadModal(${task.id}, '${task.title}')">Kumpul Ulang</button>
                    </div>
                `;
            } else if (task.status === 'missing') {
                statusText = 'Belum Mengumpulkan';
                borderClass = 'task-card-missing';
                badgeClass = 'badge-missing';
                fileRow = '';
                actionsBlock = `
                    <div class="actions-row">
                        <button type="button" class="btn-action btn-upload" onclick="openUploadModal(${task.id}, '${task.title}')">Kumpulkan Tugas</button>
                    </div>
                `;
            } else if (task.status === 'revision') {
                statusText = 'Revisi';
                borderClass = 'task-card-revision';
                badgeClass = 'badge-revision';
                fileRow = `
                    <div class="revision-box">
                        <span class="revision-header">Catatan Revisi:</span>
                        <p class="revision-comment">"${task.comment}"</p>
                    </div>
                `;
                actionsBlock = `
                    <div class="actions-row">
                        <button type="button" class="btn-action btn-upload" onclick="openUploadModal(${task.id}, '${task.title}')">Kumpulkan Revisi</button>
                    </div>
                `;
            }

            const cardHtml = `
                <article class="task-card ${borderClass}" id="task-${task.id}">
                    <header class="task-card-header">
                        <div class="task-identity">
                            <div class="task-number-badge ${badgeClass}">${task.num}</div>
                            <div class="task-title-block">
                                <h3 class="task-title">${task.title}</h3>
                                <span class="task-deadline ${task.status === 'missing' ? 'deadline-urgent' : 'deadline-normal'}">${task.deadline}</span>
                            </div>
                        </div>
                        <span class="status-badge status-badge-${task.status}">${statusText}</span>
                    </header>
                    <div class="task-card-body">
                        <div class="task-instruction-box">
                            <span class="instruction-title">Instruksi Tugas:</span>
                            <p class="instruction-content">${task.instr}</p>
                        </div>
                        ${fileRow}
                        ${actionsBlock}
                    </div>
                </article>
            `;
            tasksContainer.innerHTML += cardHtml;
        });
    }

    // Modal & Upload Logic
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

    // Drag & Drop
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

    // Handle Form Submit (Mock backend response)
    function handleFormSubmit(event) {
        event.preventDefault();
        const id = parseInt(submitModulId.value);
        const fileObj = fileInput.files[0];
        closeUploadModal();
        
        // Find task in local data to simulate database update
        const selectedClass = classSelector.value;
        const tasks = classData[selectedClass].tasks;
        const task = tasks.find(t => t.id === id);
        
        if (task) {
            // Update model state in mock data
            const wasMissing = task.status === 'missing';
            task.status = 'pending';
            task.file = fileObj ? fileObj.name : 'laporan_tugas.zip';
            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
            task.date = `Diunggah pada: ${dateStr}, ${timeStr} WITA`;

            // Adjust counters
            if (wasMissing) {
                const partsGraded = classData[selectedClass].summary.graded.split(' / ');
                const partsPending = classData[selectedClass].summary.pending.split(' / ');
                const partsMissing = classData[selectedClass].summary.missing.split(' / ');
                
                const newPending = parseInt(partsPending[0]) + 1;
                const newMissing = Math.max(0, parseInt(partsMissing[0]) - 1);
                
                classData[selectedClass].summary.pending = `${newPending} / ${partsPending[1]}`;
                classData[selectedClass].summary.missing = `${newMissing} / ${partsMissing[1]}`;

                // Update UI Summary counters
                valPending.textContent = classData[selectedClass].summary.pending;
                valMissing.textContent = classData[selectedClass].summary.missing;
            }

            // Re-render
            renderTasks(tasks);
        }

        showToast("Tugas berhasil diunggah dan disimpan ke server.");
    }

    // Cancel Submission Mock
    function cancelSubmission(id) {
        if (confirm("Apakah Anda yakin ingin membatalkan pengumpulan tugas ini?")) {
            const selectedClass = classSelector.value;
            const tasks = classData[selectedClass].tasks;
            const task = tasks.find(t => t.id === id);

            if (task) {
                task.status = 'missing';
                task.file = '';
                task.date = '';

                // Adjust counters
                const partsPending = classData[selectedClass].summary.pending.split(' / ');
                const partsMissing = classData[selectedClass].summary.missing.split(' / ');
                
                const newPending = Math.max(0, parseInt(partsPending[0]) - 1);
                const newMissing = parseInt(partsMissing[0]) + 1;

                classData[selectedClass].summary.pending = `${newPending} / ${partsPending[1]}`;
                classData[selectedClass].summary.missing = `${newMissing} / ${partsMissing[1]}`;

                // Update UI counters
                valPending.textContent = classData[selectedClass].summary.pending;
                valMissing.textContent = classData[selectedClass].summary.missing;

                // Re-render
                renderTasks(tasks);
                showToast("Pengumpulan tugas dibatalkan.");
            }
        }
    }

    // Show Toast
    function showToast(message) {
        toastMessage.textContent = message;
        toast.className = "toast show";
        setTimeout(() => {
            toast.className = toast.className.replace("show", "");
        }, 3000);
    }

    // Initial Load
    renderTasks(classData.web.tasks);
</script>

</body>
</html>
