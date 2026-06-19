<?php
// ==============================================================================
// EduLab UHO - Sanggah Nilai Form & History View (Student Only)
// ==============================================================================

$fullName = $_SESSION['name'] ?? 'Guest';
$role = $_SESSION['active_role'] ?? 'Mahasiswa';

$words = explode(" ", $fullName);
$initials = "";
foreach ($words as $w) {
    $initials .= strtoupper($w[0] ?? '');
}
$initials = substr($initials, 0, 2);

$monthsIndo = [
    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
    5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
    9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanggah Nilai - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        .form-card {
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
            max-width: 1078px;
            border: 0.5px solid rgba(54, 64, 135, 0.1);
        }

        .form-title {
            font-size: 20px;
            font-weight: 700;
            color: #313131;
            margin-bottom: 24px;
            text-align: left;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-label {
            font-size: 15px;
            font-weight: 600;
            color: #6C6C6C;
        }

        .form-select, .form-input, .form-textarea {
            width: 100%;
            height: 44px;
            padding: 0 16px;
            border: 1px solid #C8C8C8;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            color: #313131;
            background-color: #FFFFFF;
            outline: none;
            transition: border-color var(--transition-speed);
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231F1F1F' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 14px;
            padding-right: 40px;
        }

        .form-select:focus, .form-input:focus, .form-textarea:focus {
            border-color: var(--btn-primary);
        }

        .form-input-readonly {
            background-color: #F8FAFC;
            border-color: #E2E8F0;
            color: #64748B;
            cursor: not-allowed;
            width: 138px;
        }

        .form-textarea {
            height: 139px;
            padding: 12px 16px;
            resize: none;
        }

        .btn-submit-container {
            display: flex;
            justify-content: center;
            margin-top: 24px;
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 726px;
            height: 47px;
            background-color: #364087;
            color: #FFFFFF;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .btn-submit:hover {
            background-color: #2b336b;
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        /* History Section */
        .history-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1078px;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .history-title {
            font-size: 28px;
            font-weight: 700;
            color: #313131;
        }

        .btn-see-all {
            font-size: 15px;
            font-weight: 500;
            color: #364087;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: opacity 0.2s;
        }

        .btn-see-all:hover {
            opacity: 0.8;
        }

        .history-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
            max-width: 1078px;
            margin-bottom: 40px;
        }

        .history-card {
            background-color: #FFFFFF;
            border-radius: 12px;
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 4px rgba(0, 0, 0, 0.05);
            border: 0.5px solid rgba(0, 0, 0, 0.1);
        }

        .history-card-left {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .history-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .history-modul-title {
            font-size: 24px;
            font-weight: 700;
            color: #000000;
        }

        .history-class-name {
            font-size: 20px;
            font-weight: 700;
            color: #9E9E9E;
        }

        .history-date {
            font-size: 20px;
            font-weight: 500;
            color: #9E9E9E;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            font-size: 15px;
            font-weight: 700;
            border-radius: 9999px;
            text-align: center;
            min-width: 116px;
            height: 40px;
        }

        .badge-disetujui {
            background-color: #EAF3DE;
            color: #39502C;
        }

        .badge-diproses {
            background-color: #FEF3C7;
            color: #D97706;
        }

        .badge-revisi {
            background-color: #FDE8E8;
            color: #9B1C1C;
        }

        .warning-msg {
            background-color: #FEF2F2;
            border: 1px solid #FCA5A5;
            color: #B91C1C;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            display: none;
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
                <div class="sidebar-user-role"><?= htmlspecialchars($role) ?></div>
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
        <header class="workspace-navbar" style="background-color: #364087;">
            <h2 class="navbar-title" style="color: #FFFFFF; font-size: 32px; font-weight: 600;">Sanggah Nilai</h2>
            <div class="navbar-profile">
                <div class="navbar-avatar" style="background-color: #7C94B8; color: #FFFFFF; font-size: 25px; font-weight: bold;"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content" style="background-color: #EEEEEE; padding: 28px;">
            
            <!-- Success/Error Alert -->
            <?php if (isset($_SESSION['sanggah_success'])): ?>
                <div style="background-color: #DEF7EC; color: #03543F; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 600; border: 1px solid #BCF0DA; margin-bottom: 20px; max-width: 1078px;">
                    <?= htmlspecialchars($_SESSION['sanggah_success']); unset($_SESSION['sanggah_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['sanggah_error'])): ?>
                <div style="background-color: #FEE2E2; color: #DC2626; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 600; border: 1px solid #FCA5A5; margin-bottom: 20px; max-width: 1078px;">
                    <?= htmlspecialchars($_SESSION['sanggah_error']); unset($_SESSION['sanggah_error']); ?>
                </div>
            <?php endif; ?>

            <!-- Ajukan Sanggah Baru Form Card -->
            <section class="form-card">
                <h3 class="form-title">Ajukan Sanggah Baru</h3>
                
                <div id="warning_message" class="warning-msg"></div>

                <form method="POST" action="/rpl/public/index.php?action=submit_sanggah">
                    <input type="hidden" name="id_nilai" id="id_nilai_hidden">
                    
                    <div class="form-group">
                        <label class="form-label" for="kelas_select">Kelas</label>
                        <select id="kelas_select" class="form-select" disabled>
                            <option value="1"><?= htmlspecialchars($classInfo['Nama_Kelas'] ?? 'Sistem Digital') ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="modul_select">Modul yang disanggah</label>
                        <select id="modul_select" class="form-select" required>
                            <option value="">-- Pilih Modul --</option>
                            <?php foreach ($gradesList as $grade): ?>
                                <option value="<?= $grade['ID_Nilai'] ?>"><?= htmlspecialchars($grade['Judul_Modul']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="nilai_tercatat">Nilai yang tercatat</label>
                        <input type="text" id="nilai_tercatat" class="form-input form-input-readonly" readonly placeholder="-">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="alasan_sanggah">Alasan sanggah</label>
                        <textarea name="alasan_sanggah" id="alasan_sanggah" class="form-textarea" placeholder="Jelaskan alasan kamu mengajukan sanggah" required></textarea>
                    </div>

                    <div class="btn-submit-container">
                        <button type="submit" id="btn_submit" class="btn-submit">
                            <svg width="21" height="18" viewBox="0 0 21 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20.5 1L1 7.5L8.5 10M20.5 1L14 17L8.5 10M20.5 1L8.5 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Kirim Sanggahan</span>
                        </button>
                    </div>
                </form>
            </section>

            <!-- Riwayat Sanggah Section -->
            <section class="history-section-header">
                <h2 class="history-title">Riwayat Sanggah</h2>
                <a href="#" class="btn-see-all">
                    <span>Lihat semua</span>
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 7.5H14M14 7.5L7.5 1M14 7.5L7.5 14" stroke="#364087" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </section>

            <div class="history-list">
                <?php if (empty($appealsHistory)): ?>
                    <div class="history-card" style="justify-content: center; color: #9E9E9E; font-weight: 500; font-size: 16px; padding: 32px;">
                        Belum ada riwayat pengajuan sanggahan.
                    </div>
                <?php else: ?>
                    <?php foreach ($appealsHistory as $appeal): ?>
                        <?php
                        $dateStr = '-';
                        if (!empty($appeal['Waktu_Submit'])) {
                            $time = strtotime($appeal['Waktu_Submit']);
                            $d = date('j', $time);
                            $m = $monthsIndo[(int)date('n', $time)] ?? date('M', $time);
                            $y = date('Y', $time);
                            $dateStr = "Diajukan $d $m $y";
                        }
                        
                        $statusClass = 'badge-diproses';
                        $statusText = 'Diproses';
                        if ($appeal['Status_Tugas'] === 'Selesai') {
                            $statusClass = 'badge-disetujui';
                            $statusText = 'Disetujui';
                        } elseif ($appeal['Status_Tugas'] === 'Revisi') {
                            $statusClass = 'badge-revisi';
                            $statusText = 'Revisi';
                        }
                        ?>
                        <article class="history-card">
                            <div class="history-card-left">
                                <div class="history-card-header">
                                    <span class="history-modul-title"><?= htmlspecialchars($appeal['Judul_Modul']) ?></span>
                                    <span class="history-class-name"><?= htmlspecialchars($appeal['Nama_Kelas']) ?></span>
                                </div>
                                <span class="history-date"><?= $dateStr ?></span>
                                <?php if (!empty($appeal['Tanggapan_Sanggah'])): ?>
                                    <div style="font-size: 14px; color: #475569; background-color: #F8FAFC; padding: 8px 12px; border-radius: 6px; border-left: 3px solid var(--btn-primary); margin-top: 8px;">
                                        <strong>Tanggapan Asisten:</strong> <?= htmlspecialchars($appeal['Tanggapan_Sanggah']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="badge-status <?= $statusClass ?>">
                                <?= $statusText ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

<script>
    const gradesMap = <?php echo json_encode($gradesList); ?>;
    const selectModul = document.getElementById('modul_select');
    const inputNilai = document.getElementById('nilai_tercatat');
    const hiddenIdNilai = document.getElementById('id_nilai_hidden');
    const textareaAlasan = document.getElementById('alasan_sanggah');
    const btnSubmit = document.getElementById('btn_submit');
    const warningMsg = document.getElementById('warning_message');

    selectModul.addEventListener('change', function() {
        const idNilai = this.value;
        const selected = gradesMap.find(g => g.ID_Nilai == idNilai);
        if (selected) {
            inputNilai.value = Math.round(selected.Nilai_Angka);
            hiddenIdNilai.value = selected.ID_Nilai;
            
            if (selected.Alasan_Sanggah) {
                textareaAlasan.value = selected.Alasan_Sanggah;
                textareaAlasan.disabled = true;
                btnSubmit.disabled = true;
                btnSubmit.style.opacity = '0.5';
                btnSubmit.style.cursor = 'not-allowed';
                warningMsg.style.display = 'block';
                
                let IndonesianStatus = 'Diproses';
                if (selected.Status_Tugas === 'Selesai') {
                    IndonesianStatus = 'Disetujui';
                } else if (selected.Status_Tugas === 'Revisi') {
                    IndonesianStatus = 'Revisi';
                }
                warningMsg.innerText = 'Modul ini sudah disanggah (Status: ' + IndonesianStatus + ')';
            } else {
                textareaAlasan.value = '';
                textareaAlasan.disabled = false;
                btnSubmit.disabled = false;
                btnSubmit.style.opacity = '1';
                btnSubmit.style.cursor = 'pointer';
                warningMsg.style.display = 'none';
            }
        } else {
            inputNilai.value = '';
            hiddenIdNilai.value = '';
            textareaAlasan.value = '';
            textareaAlasan.disabled = false;
            btnSubmit.disabled = false;
            btnSubmit.style.opacity = '1';
            btnSubmit.style.cursor = 'pointer';
            warningMsg.style.display = 'none';
        }
    });
</script>


    </div>
</div>

</body>
</html>
