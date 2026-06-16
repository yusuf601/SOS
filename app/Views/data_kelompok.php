<?php
// ==============================================================================
// EduLab UHO - Data Kelompok View
// ==============================================================================

$fullName = $_SESSION['name'] ?? 'Guest';
$words = explode(" ", $fullName);
$initials = "";
foreach ($words as $w) {
    $initials .= strtoupper($w[0] ?? '');
}
$initials = substr($initials, 0, 2);
$role = $_SESSION['active_role'] ?? 'Asisten';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelompok - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* Local Table styling matching the design */
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

        .group-table td.bold-val {
            font-weight: 700;
        }

        .group-table th.col-nilai, .group-table td.col-nilai {
            text-align: center;
            width: 80px;
        }

        .group-table th.col-attendance, .group-table td.col-attendance {
            text-align: center;
            width: 120px;
        }

        /* Practicum Selection Dropdown Styles */
        .workspace-header-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 24px;
            width: 100%;
        }

        .practicum-select-wrapper {
            position: relative;
            width: 204px;
            height: 32px;
        }

        .practicum-select {
            width: 100%;
            height: 100%;
            background-color: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            padding: 0 32px 0 16px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: #292929;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
            outline: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            text-align: left;
        }

        .practicum-select-wrapper::after {
            content: "";
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid #5E5E5E;
            pointer-events: none;
        }

        .practicum-select:hover {
            border-color: rgba(0, 0, 0, 0.2);
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
                <div class="sidebar-user-role"><?= $role === 'Asisten' ? 'Asisten Dosen' : htmlspecialchars($role) ?></div>
            </div>

            <!-- Menu Navigation -->
            <nav>
                <div class="sidebar-menu-title">Menu Utama</div>
                <ul class="sidebar-menu-list">
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
                        <a href="/rpl/public/index.php?action=data_kelompok">
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
            <h2 class="navbar-title">Data Kelompok</h2>
            <div class="navbar-profile">
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content my-classes-page-bg">
            <?php if (!empty($myClasses)): ?>
                <div class="workspace-header-row">
                    <form method="GET" action="/rpl/public/index.php" id="practicumForm">
                        <input type="hidden" name="action" value="data_kelompok">
                        <div class="practicum-select-wrapper">
                            <select name="class_id" class="practicum-select" onchange="document.getElementById('practicumForm').submit()">
                                <?php foreach ($myClasses as $cls): ?>
                                    <option value="<?= $cls['ID_Kelas'] ?>" <?= ($selectedClassId == $cls['ID_Kelas']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cls['Nama_Kelas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

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
                            <span class="group-card-badge"><?= htmlspecialchars($group['students_count']) ?> Mahasiswa</span>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="group-table">
                                <thead>
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th class="col-attendance">Kehadiran</th>
                                        <th class="col-nilai">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($group['students'])): ?>
                                        <tr>
                                            <td colspan="4" style="text-align: center; color: #8A8A8A; padding: 16px;">
                                                Tidak ada mahasiswa di kelompok ini.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($group['students'] as $student): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($student['nim']) ?></td>
                                                <td><?= htmlspecialchars($student['name']) ?></td>
                                                <td class="col-attendance"><?= htmlspecialchars($student['attendance']) ?></td>
                                                <td class="col-nilai bold-val"><?= htmlspecialchars($student['grade']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- ====== Settings Modal ====== -->
<div class="settings-backdrop">
    <!-- settings modal contents (same as standard) -->
</div>

</body>
</html>
