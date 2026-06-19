<?php
// ==============================================================================
// EduLab UHO - Verifikasi Tugas View
// ==============================================================================

$fullName = $_SESSION['full_name'] ?? 'Chris Redfield';
$role = $_SESSION['active_role'] ?? 'Asisten';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tugas - EduLab</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #EEEEEE;
            --sidebar-bg: #29316B;
            --sidebar-active: #364087;
            --btn-primary: #364087;
            --text-main: #000000;
            --text-muted: #797979;
            --card-bg: #FFFFFF;
            --border-color: rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar Container */
        .sidebar {
            width: 282px;
            background-color: var(--sidebar-bg);
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
            padding: 20px 16px;
            min-height: 100vh;
            flex-shrink: 0;
        }

        /* Logo Area */
        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 8px;
            margin-bottom: 24px;
        }

        .logo-img {
            width: 40px;
            height: 40px;
            background-color: #FFFFFF;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 800;
            color: #FFFFFF;
        }

        /* User Card */
        .user-card {
            background-color: #7C94B8;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            color: #1E293B;
        }

        .user-name {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 13px;
            font-weight: 500;
            color: #FFFFFF;
        }

        .menu-section-title {
            font-size: 12px;
            font-weight: 700;
            color: #7C94B8;
            margin-bottom: 12px;
            padding-left: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Sidebar Menu */
        .menu-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-grow: 1;
        }

        .menu-item a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .menu-item a:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #FFFFFF;
        }

        .menu-item.active a {
            background-color: var(--sidebar-active);
            color: #FFFFFF;
        }

        .menu-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.1);
            margin: 20px 0;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Header Navbar */
        .navbar {
            height: 95px;
            background-color: #364087;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            color: #FFFFFF;
            flex-shrink: 0;
        }

        .navbar-title {
            font-size: 32px;
            font-weight: 600;
        }

        .navbar-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-avatar {
            width: 55px;
            height: 55px;
            background-color: #7C94B8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            color: #FFFFFF;
        }

        /* Page Container */
        .page-container {
            padding: 40px;
            display: flex;
            flex-direction: column;
            gap: 32px;
            overflow-y: auto;
            flex-grow: 1;
        }

        /* Filters Bar */
        .filters-container {
            display: flex;
            justify-content: flex-end;
            gap: 16px;
        }

        .filter-select {
            padding: 8px 36px 8px 16px;
            border-radius: 8px;
            border: 1px solid #CBD5E1;
            background-color: #FFFFFF;
            font-size: 14px;
            font-weight: 600;
            color: #1E293B;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            min-width: 160px;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--btn-primary);
            box-shadow: 0 0 0 2px rgba(54, 64, 135, 0.1);
        }

        /* Table Card */
        .card {
            background-color: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 24px 32px;
            width: 100%;
        }

        .card-header {
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #000000;
        }

        /* Table Design */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .task-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .task-table th {
            font-size: 14px;
            font-weight: 600;
            color: rgba(0, 0, 0, 0.5);
            padding: 16px 20px;
            border-bottom: 1.5px solid rgba(0, 0, 0, 0.1);
        }

        .task-table td {
            font-size: 13px;
            font-weight: 500;
            color: #000000;
            padding: 20px;
            border-bottom: 0.5px solid rgba(0, 0, 0, 0.08);
            vertical-align: middle;
        }

        .task-table tr:last-child td {
            border-bottom: none;
        }

        .student-name {
            font-weight: 600;
            color: #1E293B;
        }

        /* Badges styling */
        .badge-group {
            background-color: #E6F0FA;
            color: #1A56DB;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-status {
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            text-align: center;
        }

        .badge-status.not-submitted {
            background-color: #FCEBEB;
            color: #791F1F;
        }

        .badge-status.pending {
            background-color: #FFEECC;
            color: #D38C00;
        }

        .badge-status.graded {
            background-color: #EAF3DE;
            color: #27500A;
        }

        .badge-status.revision {
            background-color: #FFEDD5;
            color: #C2410C;
        }

        /* PDF Icon / Download Link */
        .pdf-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background-color: #FCEBEB;
            border: 1px solid #FCD3D3;
            border-radius: 8px;
            color: #791F1F;
            transition: all 0.2s ease;
        }

        .pdf-link:hover {
            background-color: #FCA5A5;
            color: #991B1B;
        }

        .pdf-icon {
            width: 20px;
            height: 20px;
        }

        /* Action button */
        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: transparent;
            border: 1px solid #CBD5E1;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #1E293B;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-view:hover {
            background-color: #F1F5F9;
            border-color: #94A3B8;
        }

        .btn-view svg {
            width: 14px;
            height: 14px;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            color: var(--text-muted);
            padding: 48px;
        }

        /* Grading Modal */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: #FFFFFF;
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title-text {
            font-size: 18px;
            font-weight: 700;
            color: #1E293B;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            color: #64748B;
            cursor: pointer;
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
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #CBD5E1;
            font-size: 14px;
            color: #1E293B;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--btn-primary);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
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
            background-color: transparent;
            border: 1px solid #CBD5E1;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
        }

        .btn-cancel:hover {
            background-color: #F1F5F9;
        }

        .btn-submit {
            background-color: var(--btn-primary);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #FFFFFF;
            cursor: pointer;
            transition: opacity 0.2s ease;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }

        /* Success/Error Alerts */
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 600;
        }

        .alert-success {
            background-color: #DEF7EC;
            color: #03543F;
            border: 1px solid #BCF0DA;
        }

        .alert-error {
            background-color: #FDE8E8;
            color: #9B1C1C;
            border: 1px solid #FBD5D5;
        }
    </style>
</head>
<body>

    <!-- Sidebar Menu -->
    <aside class="sidebar">
        <div class="logo-container">
            <div class="logo-img">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#29316B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"></path></svg>
            </div>
            <span class="logo-text">EduLab</span>
        </div>

        <div class="user-card">
            <div class="user-name"><?= htmlspecialchars($fullName) ?></div>
            <div class="user-role"><?= htmlspecialchars($role) ?></div>
        </div>

        <nav class="menu-list">
            <div class="menu-section-title">Menu Utama</div>
            
            <li class="menu-item">
                <a href="/rpl/public/index.php?action=dashboard_<?= strtolower($role) ?>">
                    <span>Dashboard</span>
                    <span class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                    </span>
                </a>
            </li>
            
            <li class="menu-item">
                <a href="/rpl/public/index.php?action=my_classes">
                    <span>Kelas Saya</span>
                    <span class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    </span>
                </a>
            </li>

            <li class="menu-item">
                <a href="/rpl/public/index.php?action=data_kelompok">
                    <span>Data Kelompok</span>
                    <span class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    </span>
                </a>
            </li>

            <li class="menu-item">
                <a href="/rpl/public/index.php?action=presensi">
                    <span>Input Presensi</span>
                    <span class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </span>
                </a>
            </li>

            <li class="menu-item active">
                <a href="/rpl/public/index.php?action=verifikasi_tugas">
                    <span>Verifikasi Tugas</span>
                    <span class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    </span>
                </a>
            </li>

            <li class="menu-item">
                <a href="/rpl/public/index.php?action=kelulusan">
                    <span>Input Nilai</span>
                    <span class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                    </span>
                </a>
            </li>

            <li class="menu-item">
                <a href="/rpl/public/index.php?action=sanggah_nilai">
                    <span>Tinjau Sanggahan</span>
                    <span class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </span>
                </a>
            </li>

            <div class="menu-divider"></div>

            <li class="menu-item">
                <a href="/rpl/public/index.php?action=pengaturan">
                    <span>Pengaturan</span>
                    <span class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    </span>
                </a>
            </li>

            <li class="menu-item">
                <a href="/rpl/public/index.php?action=logout" style="color: #FF8A8A;">
                    <span>Keluar</span>
                    <span class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    </span>
                </a>
            </li>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <header class="navbar">
            <h1 class="navbar-title">Verifikasi Tugas</h1>
            <div class="navbar-profile">
                <span style="font-weight: 500; font-size: 16px; margin-right: 8px;"><?= htmlspecialchars($fullName) ?></span>
                <div class="profile-avatar">
                    <?php 
                        $nameParts = explode(' ', $fullName);
                        $initials = '';
                        foreach ($nameParts as $part) {
                            $initials .= strtoupper(substr($part, 0, 1));
                        }
                        echo htmlspecialchars(substr($initials, 0, 2));
                    ?>
                </div>
            </div>
        </header>

        <div class="page-container">
            <!-- Filter Dropdowns -->
            <div class="filters-container">
                <form id="filterForm" method="GET" action="/rpl/public/index.php" style="display: flex; gap: 16px;">
                    <input type="hidden" name="action" value="verifikasi_tugas">
                    
                    <select name="class_id" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <?php foreach ($allClasses as $c): ?>
                            <option value="<?= $c['ID_Kelas'] ?>" <?= $selectedClass === (int)$c['ID_Kelas'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['Nama_Kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="modul_id" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <?php foreach ($moduls as $m): ?>
                            <option value="<?= $m['ID_Modul'] ?>" <?= $selectedModul === (int)$m['ID_Modul'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['Judul_Modul']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <!-- Grade Success/Error Feedback Alerts -->
            <?php if (isset($_SESSION['grade_success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['grade_success']) ?>
                </div>
                <?php unset($_SESSION['grade_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['grade_error'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['grade_error']) ?>
                </div>
                <?php unset($_SESSION['grade_error']); ?>
            <?php endif; ?>

            <!-- Table Card -->
            <section class="card">
                <div class="card-header">
                    <h2 class="card-title">Daftar Tugas Masuk</h2>
                </div>

                <div class="table-responsive">
                    <table class="task-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Nama</th>
                                <th style="width: 15%;">Kelompok</th>
                                <th style="width: 20%;">Waktu Kumpul</th>
                                <th style="width: 15%; text-align: center;">File</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 10%; text-align: right;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($studentsData)): ?>
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        Tidak ada data mahasiswa untuk kelas ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($studentsData as $student): 
                                    // Determine submission status and display
                                    $waktuKumpul = '-';
                                    $fileCell = '-';
                                    $statusBadgeClass = 'not-submitted';
                                    $statusText = 'Tidak Kumpul';
                                    $hasSubmitted = !empty($student['ID_Pengumpulan']);

                                    if ($hasSubmitted) {
                                        $waktuKumpul = date('d M,H:i', strtotime($student['Waktu_Submit']));
                                        
                                        // PDF File Link
                                        $fileCell = '<a href="/rpl/public/index.php?action=view_tugas&file=' . urlencode($student['File_Tugas']) . '" class="pdf-link" target="_blank" title="Lihat file tugas">
                                                        <svg class="pdf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                            <polyline points="14 2 14 8 20 8"></polyline>
                                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                                            <polyline points="10 9 9 9 8 9"></polyline>
                                                        </svg>
                                                     </a>';

                                        if (empty($student['Status_Tugas'])) {
                                            $statusBadgeClass = 'pending';
                                            $statusText = 'Belum dinilai';
                                        } else {
                                            if ($student['Status_Tugas'] === 'Selesai') {
                                                $statusBadgeClass = 'graded';
                                                $statusText = 'Selesai';
                                            } elseif ($student['Status_Tugas'] === 'Revisi') {
                                                $statusBadgeClass = 'revision';
                                                $statusText = 'Revisi';
                                            } else {
                                                $statusBadgeClass = 'pending';
                                                $statusText = htmlspecialchars($student['Status_Tugas']);
                                            }
                                        }
                                    } else {
                                        $waktuKumpul = '<span style="color: var(--text-muted);">Tidak mengumpulkan</span>';
                                    }
                                ?>
                                    <tr>
                                        <td>
                                            <div class="student-name"><?= htmlspecialchars($student['Nama_Lengkap']) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge-group"><?= htmlspecialchars($student['Nama_Kelompok'] ?? 'Tanpa Kelompok') ?></span>
                                        </td>
                                        <td>
                                            <?= $waktuKumpul ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <?= $fileCell ?>
                                        </td>
                                        <td>
                                            <span class="badge-status <?= $statusBadgeClass ?>"><?= $statusText ?></span>
                                        </td>
                                        <td style="text-align: right;">
                                            <?php if ($hasSubmitted): ?>
                                                <?php 
                                                    // Get the module title for selected module
                                                    $modulTitle = 'Modul';
                                                    foreach ($moduls as $m) {
                                                        if ((int)$m['ID_Modul'] === $selectedModul) {
                                                            $modulTitle = $m['Judul_Modul'];
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <button type="button" class="btn-view" onclick="openGradeModal(
                                                    <?= $student['ID_Pengumpulan'] ?>, 
                                                    '<?= htmlspecialchars(addslashes($student['Nama_Lengkap'])) ?>', 
                                                    '<?= htmlspecialchars(addslashes($modulTitle)) ?>', 
                                                    '<?= $student['Nilai_Angka'] ?? '' ?>', 
                                                    '<?= htmlspecialchars(addslashes($student['Feedback'] ?? '')) ?>', 
                                                    '<?= $student['Status_Tugas'] ?? 'Selesai' ?>'
                                                )">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    Lihat
                                                </button>
                                            <?php else: ?>
                                                <span style="color: var(--text-muted); padding-right: 20px;">-</span>
                                            <?php endif; ?>
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

    <!-- Verification Modal (Opsi A - Sederhana) -->
    <div class="modal" id="gradeModal" aria-hidden="true" role="dialog">
        <div class="modal-content">
            <header class="modal-header">
                <h3 class="modal-title-text" id="modalTitle">Verifikasi Tugas</h3>
                <button type="button" class="close-btn" onclick="closeGradeModal()">&times;</button>
            </header>
            <form id="gradeForm" action="/rpl/public/index.php?action=submit_verification" method="POST">
                <input type="hidden" name="id_pengumpulan" id="submitPengumpulanId" value="">
                <!-- Pass redirect url back to this page with filters preserved -->
                <input type="hidden" name="redirect_url" value="/rpl/public/index.php?action=verifikasi_tugas&class_id=<?= $selectedClass ?>&modul_id=<?= $selectedModul ?>">
                
                <div class="modal-body">
                    <div style="background-color: #F8FAFC; padding: 12px; border-radius: 8px; border: 1px solid #E2E8F0; margin-bottom: 12px;">
                        <div style="font-size: 13px; color: #64748B; font-weight: 500;">Mahasiswa:</div>
                        <div id="mhsName" style="font-size: 15px; font-weight: 700; color: #1E293B; margin-bottom: 8px;">-</div>
                        <div style="font-size: 13px; color: #64748B; font-weight: 500;">Modul:</div>
                        <div id="modulTitleVal" style="font-size: 14px; font-weight: 600; color: #475569;">-</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="statusTugas">Status Verifikasi:</label>
                        <select name="status_tugas" id="statusTugas" class="form-control" style="padding: 8px;" required>
                            <option value="Selesai">Disetujui (Selesai)</option>
                            <option value="Revisi">Ditolak (Revisi)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="feedback">Umpan Balik / Catatan:</label>
                        <textarea name="feedback" id="feedback" class="form-control" placeholder="Tulis catatan revisi atau saran perbaikan di sini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeGradeModal()">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Verifikasi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const gradeModal = document.getElementById('gradeModal');
        const submitPengumpulanId = document.getElementById('submitPengumpulanId');
        const mhsName = document.getElementById('mhsName');
        const modulTitleVal = document.getElementById('modulTitleVal');
        const statusTugas = document.getElementById('statusTugas');
        const feedback = document.getElementById('feedback');

        function openGradeModal(pengumpulanId, studentName, modulName, score, comments, status) {
            submitPengumpulanId.value = pengumpulanId;
            mhsName.textContent = studentName;
            modulTitleVal.textContent = modulName;
            feedback.value = comments;
            statusTugas.value = status || 'Selesai';
            
            gradeModal.classList.add('active');
        }

        function closeGradeModal() {
            gradeModal.classList.remove('active');
            submitPengumpulanId.value = '';
            mhsName.textContent = '-';
            modulTitleVal.textContent = '-';
            feedback.value = '';
            statusTugas.value = 'Selesai';
        }

        // Close modal when clicking outside content
        window.onclick = function(event) {
            if (event.target === gradeModal) {
                closeGradeModal();
            }
        }
    </script>
</body>
</html>
