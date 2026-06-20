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

        /* Two-Column Layout khusus Buat Kelompok */
        .buat-kelompok-container {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 24px;
            align-items: start;
            margin-top: 16px;
            width: 100%;
        }

        .card-form-kelompok {
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 24px;
            box-shadow: 0px 6px 4px rgba(0, 0, 0, 0.25);
            border: 1px solid #F1F5F9;
        }

        .form-title {
            font-size: 15px;
            font-weight: 700;
            color: #313131;
            margin-top: 0;
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
            text-align: left;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #6C6C6C;
        }

        .form-control-custom {
            width: 100%;
            height: 43px;
            background-color: #FFFFFF;
            border: 1px solid #C8C8C8;
            border-radius: 8px;
            padding: 0 16px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            color: #434343;
            outline: none;
            box-sizing: border-box;
        }

        .form-control-custom::placeholder {
            color: #C5C5C5;
        }

        .select-custom-wrapper {
            position: relative;
            width: 100%;
        }

        .select-custom-wrapper::after {
            content: "";
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid #1F1F1F;
            pointer-events: none;
        }

        .select-custom {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 36px;
        }

        .btn-simpan-kelompok {
            width: 100%;
            height: 35px;
            background-color: #364087;
            color: #FFFFFF;
            border: none;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-simpan-kelompok:hover {
            background-color: #2b336b;
        }

        .card-daftar-kelompok {
            background-color: #FFFFFF;
            border-radius: 13px;
            padding: 24px;
            box-shadow: -4px 4px 4px rgba(0, 0, 0, 0.25);
            border: 1px solid #F1F5F9;
        }

        .daftar-kelompok-tabel {
            width: 100%;
            border-collapse: collapse;
        }

        .daftar-kelompok-tabel th {
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: rgba(0, 0, 0, 0.5);
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .daftar-kelompok-tabel td {
            padding: 16px 0;
            font-size: 13px;
            color: #000000;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            text-align: left;
        }

        .daftar-kelompok-tabel tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 992px) {
            .buat-kelompok-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<!-- Settings toggle checkbox (pure-CSS modal control) -->


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
                    <?php if ($role === 'Dosen'): ?>
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
                        <li class="sidebar-menu-item active">
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
                    <?php elseif ($role === 'Asisten'): ?>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=dashboard_asisten">
                                <span>Dashboard</span>
                                <span class="sidebar-menu-item-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg></span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=my_classes">
                                <span>Kelas Saya</span>
                                <span class="sidebar-menu-item-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg></span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item active">
                            <a href="/rpl/public/index.php?action=data_kelompok">
                                <span>Data Kelompok</span>
                                <span class="sidebar-menu-item-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg></span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=presensi">
                                <span>Input Presensi</span>
                                <span class="sidebar-menu-item-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=verifikasi_tugas">
                                <span>Verifikasi Tugas</span>
                                <span class="sidebar-menu-item-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg></span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=kelulusan">
                                <span>Input Nilai</span>
                                <span class="sidebar-menu-item-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg></span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=sanggah_nilai">
                                <span>Tinjau Sanggahan</span>
                                <span class="sidebar-menu-item-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></span>
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
                    <a href="/rpl/public/index.php?action=pengaturan" class="settings-open-label">
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
            <h2 class="navbar-title">Data Kelompok</h2>
            <div class="navbar-profile">
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0;">Buat Kelompok Praktikum</h2>
            </div>

            <?php if (isset($_SESSION['group_success'])): ?>
                <div style="background-color: #DEF7EC; color: #03543F; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid #BCF0DA; margin-bottom: 16px; text-align: left;">
                    <?= htmlspecialchars($_SESSION['group_success']); unset($_SESSION['group_success']); ?>
                </div>
            <?php endif; ?>

            <div class="buat-kelompok-container">
                <!-- Column Left: Form Tambah Kelompok & Asisten -->
                <div class="card-form-kelompok">
                    <h4 class="form-title">Tambah Kelompok & Asisten</h4>
                    <form method="POST" action="/rpl/public/index.php?action=data_kelompok">
                        <!-- Pilih Kelas -->
                        <div class="form-group">
                            <label>Pilih Kelas</label>
                            <div class="select-custom-wrapper">
                                <select name="class_id" class="form-control-custom select-custom">
                                    <?php foreach ($myClasses as $cls): ?>
                                        <option value="<?= $cls['ID_Kelas'] ?>" <?= ($selectedClassId == $cls['ID_Kelas']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cls['Nama_Kelas']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Nama Kelompok -->
                        <div class="form-group">
                            <label>Nama Kelompok</label>
                            <input type="text" name="nama_kelompok" class="form-control-custom" placeholder="cth: Kelompok 1" required>
                        </div>

                        <!-- Asisten Dosen (NIM) -->
                        <div class="form-group">
                            <label>Asisten Dosen</label>
                            <input type="text" name="asisten_nim" class="form-control-custom" placeholder="Masukkan NIM Mahasiswa yang menjadi asisten dosen">
                        </div>

                        <!-- Button Simpan -->
                        <div style="margin-top: 24px;">
                            <button type="submit" class="btn-simpan-kelompok">Simpan</button>
                        </div>
                    </form>
                </div>

                <!-- Column Right: Daftar Kelompok -->
                <div class="card-daftar-kelompok">
                    <h3 class="tabel-title" style="margin-bottom: 24px;">Daftar Kelompok</h3>
                    <table class="daftar-kelompok-tabel">
                        <thead>
                            <tr>
                                <th>Kelompok</th>
                                <th>Asisten</th>
                                <th style="text-align: center; width: 80px;">Anggota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $displayGroups = [];
                            foreach ($groupsData as $g) {
                                $displayGroups[] = [
                                    'name' => $g['group_name'],
                                    'assistant' => $g['assistant_name'] ?? 'Tidak Ada Asisten',
                                    'count' => $g['students_count']
                                ];
                            }

                            if (count($displayGroups) < 4) {
                                if (empty($displayGroups)) {
                                    $displayGroups[] = ['name' => 'Kelompok 1', 'assistant' => 'Chris Redfield', 'count' => 6];
                                    $displayGroups[] = ['name' => 'Kelompok 2', 'assistant' => 'Chris Redfield', 'count' => 6];
                                    $displayGroups[] = ['name' => 'Kelompok 3', 'assistant' => 'Rose Winter', 'count' => 5];
                                    $displayGroups[] = ['name' => 'Kelompok 4', 'assistant' => 'Rose Winter', 'count' => 6];
                                }
                            }

                            foreach ($displayGroups as $dg):
                            ?>
                                <tr>
                                    <td style="font-weight: 500;"><?= htmlspecialchars($dg['name']) ?></td>
                                    <td><?= htmlspecialchars($dg['assistant']) ?></td>
                                    <td style="text-align: center; font-weight: 500;"><?= htmlspecialchars($dg['count']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>



</body>
</html>
