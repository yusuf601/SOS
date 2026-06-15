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

        .badge-selesai { background-color: rgba(22, 163, 74, 0.1); color: #16A34A; }
        .badge-sanggah { background-color: rgba(220, 38, 38, 0.1); color: #DC2626; }
        .badge-pending { background-color: rgba(202, 138, 4, 0.1); color: #CA8A04; }

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
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=presensi">
                                <span>Data Presensi</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item active">
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
                        <li class="sidebar-menu-item">
                            <a href="/rpl/public/index.php?action=presensi">
                                <span>Input Presensi</span>
                                <span class="sidebar-menu-item-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                </span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item active">
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
            <h2 class="navbar-title"><?= ($role === 'Mahasiswa') ? 'Sanggah Nilai' : 'Tanggapan Sanggah Nilai' ?></h2>
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
                <section class="sanggah-card">
                    <h3 class="sanggah-title">Daftar Nilai & Sanggahan Tugas</h3>
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Modul</th>
                                    <th>Nilai</th>
                                    <th>Status</th>
                                    <th>Feedback Asisten</th>
                                    <th>Detail Sanggahan</th>
                                    <th style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($gradesList)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; color: #94A3B8; padding: 24px;">Belum ada nilai tugas yang diterbitkan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($gradesList as $idx => $grade): ?>
                                        <tr>
                                            <td><?= $idx + 1 ?></td>
                                            <td>
                                                <div style="font-weight: 600;"><?= htmlspecialchars($grade['Judul_Modul']) ?></div>
                                                <div style="font-size: 12px; color: #64748B; margin-top: 2px;">
                                                    File: <a href="/rpl/public/assets/uploads/tugas/<?= htmlspecialchars($grade['File_Tugas']) ?>" target="_blank" style="color: var(--btn-primary); text-decoration: underline;"><?= htmlspecialchars(basename($grade['File_Tugas'])) ?></a>
                                                </div>
                                            </td>
                                            <td style="font-size: 16px; font-weight: 700;"><?= htmlspecialchars($grade['Nilai_Angka']) ?></td>
                                            <td>
                                                <span class="badge-status badge-<?= strtolower($grade['Status_Tugas']) ?>">
                                                    <?= htmlspecialchars($grade['Status_Tugas']) ?>
                                                </span>
                                            </td>
                                            <td style="max-width: 200px; font-size: 13px; color: #475569;">
                                                <?= !empty($grade['Feedback']) ? htmlspecialchars($grade['Feedback']) : '<span style="color: #94A3B8; font-style: italic;">Tidak ada feedback</span>' ?>
                                            </td>
                                            <td style="max-width: 250px; font-size: 13px;">
                                                <?php if (!empty($grade['Alasan_Sanggah'])): ?>
                                                    <div><strong>Sanggahan Anda:</strong> <?= htmlspecialchars($grade['Alasan_Sanggah']) ?></div>
                                                    <?php if (!empty($grade['Tanggapan_Sanggah'])): ?>
                                                        <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed #E2E8F0; color: var(--btn-primary);">
                                                            <strong>Balasan Asisten:</strong> <?= htmlspecialchars($grade['Tanggapan_Sanggah']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span style="color: #94A3B8; font-style: italic;">Belum mengajukan sanggahan</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($grade['Status_Tugas'] === 'Selesai' && empty($grade['Alasan_Sanggah'])): ?>
                                                    <button type="button" class="btn-action-table btn-sanggah" 
                                                            onclick="openSanggahModal(<?= $grade['ID_Nilai'] ?>, '<?= htmlspecialchars(addslashes($grade['Judul_Modul'])) ?>', <?= $grade['Nilai_Angka'] ?>)">
                                                        Sanggah
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" class="btn-action-table" disabled style="background-color: #E2E8F0; color: #94A3B8; cursor: not-allowed;">
                                                        Locked
                                                    </button>
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
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>Kelas & Modul</th>
                                    <th>Nilai Lama</th>
                                    <th>Alasan Sanggah</th>
                                    <th style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($gradesList)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: #94A3B8; padding: 24px;">Tidak ada pengajuan sanggah nilai aktif.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($gradesList as $grade): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600;"><?= htmlspecialchars($grade['Nama_Mahasiswa']) ?></div>
                                                <div style="font-size: 12px; color: #64748B;"><?= htmlspecialchars($grade['NIM']) ?></div>
                                            </td>
                                            <td>
                                                <div><?= htmlspecialchars($grade['Nama_Kelas']) ?></div>
                                                <div style="font-size: 13px; color: #475569; font-weight: 600;"><?= htmlspecialchars($grade['Judul_Modul']) ?></div>
                                                <div style="font-size: 12px; margin-top: 2px;">
                                                    File Tugas: <a href="/rpl/public/assets/uploads/tugas/<?= htmlspecialchars($grade['File_Tugas']) ?>" target="_blank" style="color: var(--btn-primary); text-decoration: underline;"><?= htmlspecialchars(basename($grade['File_Tugas'])) ?></a>
                                                </div>
                                            </td>
                                            <td style="font-size: 16px; font-weight: 700; color: #DC2626;"><?= htmlspecialchars($grade['Nilai_Angka']) ?></td>
                                            <td style="max-width: 300px; font-size: 13px; background-color: #FEF2F2; color: #990000; padding: 10px; border-radius: 8px; border: 1px solid #FEE2E2;">
                                                <?= htmlspecialchars($grade['Alasan_Sanggah']) ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn-action-table btn-tanggapi"
                                                        onclick="openTanggapanModal(<?= $grade['ID_Nilai'] ?>, '<?= htmlspecialchars(addslashes($grade['Nama_Mahasiswa'])) ?>', '<?= htmlspecialchars(addslashes($grade['Judul_Modul'])) ?>', <?= $grade['Nilai_Angka'] ?>)">
                                                    Tanggapi
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Dosen/Asisten Response Modal -->
                <div id="tanggapanModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <span class="modal-title-text">Tanggapi Sanggah Nilai</span>
                            <button class="close-btn" onclick="closeTanggapanModal()">&times;</button>
                        </div>
                        <form method="POST" action="/rpl/public/index.php?action=respond_sanggah">
                            <input type="hidden" name="id_nilai" id="tanggapan_id_nilai">
                            <div class="modal-body">
                                <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; padding: 12px; border-radius: 8px; font-size: 13px;">
                                    <strong>Mahasiswa:</strong> <span id="tanggapan_nama_mhs"></span><br>
                                    <strong>Modul:</strong> <span id="tanggapan_judul_modul"></span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="nilai_baru">Nilai Baru (Angka)</label>
                                    <input type="number" name="nilai_baru" id="nilai_baru" class="form-control" min="0" max="100" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="tanggapan_sanggah">Tanggapan / Catatan Penyesuaian</label>
                                    <textarea name="tanggapan_sanggah" id="tanggapan_sanggah" class="form-control" placeholder="Tuliskan umpan balik atau alasan perubahan/penolakan nilai..." required></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="status_tugas">Status Tugas</label>
                                    <select name="status_tugas" id="status_tugas" class="form-control">
                                        <option value="Selesai">Selesai (Nilai Diperbarui)</option>
                                        <option value="Revisi">Revisi (Minta Mahasiswa Ulang/Perbaiki)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-cancel" onclick="closeTanggapanModal()">Batal</button>
                                <button type="submit" class="btn-confirm">Simpan & Selesaikan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    function openTanggapanModal(idNilai, namaMhs, judulModul, nilaiLama) {
                        document.getElementById('tanggapan_id_nilai').value = idNilai;
                        document.getElementById('tanggapan_nama_mhs').innerText = namaMhs;
                        document.getElementById('tanggapan_judul_modul').innerText = judulModul;
                        document.getElementById('nilai_baru').value = nilaiLama;
                        document.getElementById('tanggapanModal').style.display = 'flex';
                    }

                    function closeTanggapanModal() {
                        document.getElementById('tanggapanModal').style.display = 'none';
                    }

                    window.onclick = function(event) {
                        var modal = document.getElementById('tanggapanModal');
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    }
                </script>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>
