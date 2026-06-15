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
$totalPresensi = 0;
$attendanceRate = 100;

if ($role === 'Mahasiswa' && !empty($attendance)) {
    $totalPresensi = count($attendance);
    foreach ($attendance as $att) {
        switch ($att['Status_Kehadiran']) {
            case 'Hadir': $hadirCount++; break;
            case 'Sakit': $sakitCount++; break;
            case 'Izin': $izinCount++; break;
            case 'Alfa': $alfaCount++; break;
        }
    }
    if ($totalPresensi > 0) {
        $attendanceRate = round(($hadirCount / $totalPresensi) * 100);
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
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-mini-card {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }

        .stat-mini-val {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .stat-mini-lbl {
            font-size: 13px;
            color: #64748B;
            font-weight: 600;
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
            padding: 14px 16px;
            background-color: #F8FAFC;
            color: #475569;
            font-weight: 600;
            border-bottom: 2px solid #E2E8F0;
        }

        .custom-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #E2E8F0;
            color: #1E293B;
            font-weight: 500;
        }

        .custom-table tr:hover {
            background-color: #F8FAFC;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-hadir { background-color: rgba(22, 163, 74, 0.1); color: #16A34A; }
        .badge-sakit { background-color: rgba(59, 130, 246, 0.1); color: #3B82F6; }
        .badge-izin { background-color: rgba(234, 179, 8, 0.1); color: #EAB308; }
        .badge-alfa { background-color: rgba(22, 163, 74, 0.1); color: #DC2626; } /* Wait, let's fix color to red! */

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
                            <a href="/rpl/public/index.php?action=sanggah_nilai">
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
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=dashboard_<?= strtolower($role) ?>">
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
                        <li class="sidebar-menu-item active">
                            <a href="/rpl/public/index.php?action=presensi">
                                <span>Input Presensi</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=sanggah_nilai">
                                <span>Tanggapan Sanggah</span>
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
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div>
            <div class="sidebar-divider"></div>
            <ul class="sidebar-menu-list">
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
                <section class="presensi-card">
                    <h3 class="presensi-title">Ringkasan Kehadiran Anda</h3>
                    
                    <div class="stats-summary-grid">
                        <div class="stat-mini-card" style="border-left: 4px solid #16A34A;">
                            <div class="stat-mini-val" style="color: #16A34A;"><?= $attendanceRate ?>%</div>
                            <div class="stat-mini-lbl">Persentase Kehadiran</div>
                        </div>
                        <div class="stat-mini-card">
                            <div class="stat-mini-val" style="color: #16A34A;"><?= $hadirCount ?></div>
                            <div class="stat-mini-lbl">Hadir</div>
                        </div>
                        <div class="stat-mini-card">
                            <div class="stat-mini-val" style="color: #3B82F6;"><?= $sakitCount ?></div>
                            <div class="stat-mini-lbl">Sakit</div>
                        </div>
                        <div class="stat-mini-card">
                            <div class="stat-mini-val" style="color: #EAB308;"><?= $izinCount ?></div>
                            <div class="stat-mini-lbl">Izin</div>
                        </div>
                        <div class="stat-mini-card">
                            <div class="stat-mini-val" style="color: #DC2626;"><?= $alfaCount ?></div>
                            <div class="stat-mini-lbl">Alfa / Tanpa Keterangan</div>
                        </div>
                    </div>
                </section>

                <section class="presensi-card">
                    <h3 class="presensi-title">Detail Log Kehadiran</h3>
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Materi/Modul</th>
                                    <th>Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($attendance)): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; color: #94A3B8; padding: 24px;">Belum ada riwayat kehadiran tercatat.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($attendance as $index => $row): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars(date('d M Y', strtotime($row['Tanggal']))) ?></td>
                                            <td><?= htmlspecialchars($row['Judul_Modul']) ?></td>
                                            <td>
                                                <span class="badge-status badge-<?= strtolower($row['Status_Kehadiran']) ?>">
                                                    <?= htmlspecialchars($row['Status_Kehadiran']) ?>
                                                </span>
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
                <section class="presensi-card">
                    <h3 class="presensi-title">Filter Kelas & Modul</h3>
                    <div class="filter-section">
                        <form method="GET" action="/rpl/public/index.php" id="filterForm">
                            <input type="hidden" name="action" value="presensi">
                            <div class="filter-grid">
                                <div class="form-group">
                                    <label class="form-label" for="class_id">Pilih Kelas</label>
                                    <select name="class_id" id="class_id" class="form-control" onchange="document.getElementById('filterForm').submit()">
                                        <?php foreach ($allClasses as $cls): ?>
                                            <option value="<?= $cls['ID_Kelas'] ?>" <?= ($selectedClass == $cls['ID_Kelas']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cls['Nama_Kelas']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="modul_id">Pilih Modul Praktikum</label>
                                    <select name="modul_id" id="modul_id" class="form-control" onchange="document.getElementById('filterForm').submit()">
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
                </section>

                <section class="presensi-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 class="presensi-title" style="margin-bottom: 0;">Pengisian Absensi Mahasiswa</h3>
                        <span class="badge-status badge-sakit" style="font-size: 14px; padding: 6px 12px;">
                            Tanggal Hari Ini: <?= date('d M Y') ?>
                        </span>
                    </div>

                    <form method="POST" action="/rpl/public/index.php?action=submit_presensi">
                        <input type="hidden" name="class_id" value="<?= htmlspecialchars($selectedClass) ?>">
                        <input type="hidden" name="modul_id" value="<?= htmlspecialchars($selectedModul) ?>">

                        <div class="table-responsive" style="margin-bottom: 24px;">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama Lengkap</th>
                                        <th>Kelompok</th>
                                        <th style="width: 320px;">Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($studentsList)): ?>
                                        <tr>
                                            <td colspan="4" style="text-align: center; color: #94A3B8; padding: 24px;">Tidak ada mahasiswa terdaftar di kelas ini.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($studentsList as $mhs): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($mhs['NIM']) ?></td>
                                                <td><?= htmlspecialchars($mhs['Nama_Lengkap']) ?></td>
                                                <td><?= htmlspecialchars($mhs['Nama_Kelompok']) ?></td>
                                                <td>
                                                    <div class="radio-group">
                                                        <label class="radio-option">
                                                            <input type="radio" name="attendance[<?= $mhs['ID_User'] ?>]" value="Hadir" <?= ($mhs['Status_Kehadiran'] === 'Hadir' || is_null($mhs['Status_Kehadiran'])) ? 'checked' : '' ?>>
                                                            <span class="badge-status badge-hadir">Hadir</span>
                                                        </label>
                                                        <label class="radio-option">
                                                            <input type="radio" name="attendance[<?= $mhs['ID_User'] ?>]" value="Sakit" <?= ($mhs['Status_Kehadiran'] === 'Sakit') ? 'checked' : '' ?>>
                                                            <span class="badge-status badge-sakit">Sakit</span>
                                                        </label>
                                                        <label class="radio-option">
                                                            <input type="radio" name="attendance[<?= $mhs['ID_User'] ?>]" value="Izin" <?= ($mhs['Status_Kehadiran'] === 'Izin') ? 'checked' : '' ?>>
                                                            <span class="badge-status badge-izin">Izin</span>
                                                        </label>
                                                        <label class="radio-option">
                                                            <input type="radio" name="attendance[<?= $mhs['ID_User'] ?>]" value="Alfa" <?= ($mhs['Status_Kehadiran'] === 'Alfa') ? 'checked' : '' ?>>
                                                            <span class="badge-status badge-alfa">Alfa</span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (!empty($studentsList)): ?>
                            <div style="text-align: right;">
                                <button type="submit" class="btn-submit-presensi">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                                    Simpan Data Presensi
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </section>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>
