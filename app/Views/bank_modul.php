<?php
// ==============================================================================
// EduLab UHO - Bank Modul View
// ==============================================================================

$fullName = $_SESSION['name'] ?? 'Guest';
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
    <title>Bank Modul - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* CSS Extension specifically for Bank Modul layout */
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

        .class-meta-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            padding-top: 16px;
            border-top: 1px solid #FAFBFD; /* Softer border */
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
            background-color: #FFFFFF; /* Pure white card */
            border-radius: 15px;
            border: none; /* Removed border */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05), 0 2px 10px rgba(0, 0, 0, 0.02); /* Elevated shadow */
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all var(--transition-speed) ease;
        }

        .modul-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08), 0 4px 15px rgba(0, 0, 0, 0.04); /* Elevated hover shadow */
        }

        .modul-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 32px; /* Increased padding */
            background-color: #FFFFFF; /* Pure white header */
            border-bottom: none; /* Removed border */
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

        .modul-card-body {
            padding: 28px 32px; /* Increased padding */
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .modul-description {
            font-size: 15px;
            color: #475569;
            line-height: 1.6;
            font-weight: 500;
        }

        .download-actions-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
            border-top: 1px solid #FAFBFD; /* Softer border */
            padding-top: 16px;
        }

        .modul-download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            padding: 0 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all var(--transition-speed) ease;
        }

        .btn-materi {
            background-color: rgba(54, 64, 135, 0.1);
            color: var(--btn-primary);
        }

        .btn-materi:hover {
            background-color: var(--btn-primary);
            color: var(--text-color-light);
        }

        .btn-petunjuk {
            background-color: rgba(219, 36, 30, 0.08);
            color: #DB241E;
        }

        .btn-petunjuk:hover {
            background-color: #DB241E;
            color: var(--text-color-light);
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
                <div class="sidebar-user-name"><?= htmlspecialchars($fullName) ?></div>
                <div class="sidebar-user-role"><?= htmlspecialchars($_SESSION['active_role'] ?? 'Mahasiswa') ?></div>
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
                    <li class="sidebar-menu-item active">
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
                
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
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
                            <?php if ($_SESSION['active_role'] === 'Mahasiswa' && $classInfo): ?>
                                <option value="<?= htmlspecialchars($classInfo['ID_Kelas']) ?>" selected><?= htmlspecialchars($classInfo['Nama_Kelas']) ?></option>
                            <?php else: ?>
                                <?php foreach ($allClasses as $cls): ?>
                                    <option value="<?= htmlspecialchars($cls['ID_Kelas']) ?>"><?= htmlspecialchars($cls['Nama_Kelas']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Class Meta Details -->
                <div class="class-meta-info">
                    <div class="meta-info-item">
                        <span class="meta-info-label">Dosen Pengampu</span>
                        <span class="meta-info-value" id="lecturerName"><?= htmlspecialchars($lecturerName) ?></span>
                    </div>
                    <div class="meta-info-item">
                        <span class="meta-info-label">Asisten Praktikum</span>
                        <span class="meta-info-value" id="assistantName"><?= htmlspecialchars($assistantName) ?></span>
                    </div>
                    <div class="meta-info-item">
                        <span class="meta-info-label">Jadwal & Ruang</span>
                        <span class="meta-info-value" id="classSchedule"><?= htmlspecialchars($schedule) ?></span>
                    </div>
                    <div class="meta-info-item">
                        <span class="meta-info-label">Kelompok Kamu</span>
                        <span class="meta-info-value" id="groupName"><?= htmlspecialchars($classInfo['Nama_Kelompok'] ?? 'Tidak Terdaftar') ?></span>
                    </div>
                </div>
            </section>

            <!-- Download Error Alert -->
            <?php if (isset($_SESSION['download_error'])): ?>
                <div style="background-color: #FEE2E2; color: #DC2626; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 600; border: 1px solid #FCA5A5; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.05);">
                    <?= htmlspecialchars($_SESSION['download_error']); unset($_SESSION['download_error']); ?>
                </div>
            <?php endif; ?>

            <!-- Moduls list -->
            <section class="moduls-container" id="modulsContainer">
                <?php foreach ($modules as $index => $modul): ?>
                    <article class="modul-card" id="modul-<?= $modul['ID_Modul'] ?>">
                        <header class="modul-card-header">
                            <div class="modul-identity">
                                <div class="modul-number-badge"><?= sprintf("%02d", $index + 1) ?></div>
                                <div class="modul-title-block">
                                    <h3 class="modul-title"><?= htmlspecialchars($modul['Judul_Modul']) ?></h3>
                                    <span class="modul-date">Diunggah pada: <?= date('d M Y', strtotime('2025-09-08 + ' . ($index * 7) . ' days')) ?></span>
                                </div>
                            </div>
                        </header>
                        <div class="modul-card-body">
                            <p class="modul-description">
                                <?php
                                $desc = "";
                                if ($modul['ID_Modul'] == 1) {
                                    $desc = "Mempelajari sintaks dasar HTML5, struktur tag dokumen web, form input element, dan pengelompokan konten secara semantik.";
                                } elseif ($modul['ID_Modul'] == 2) {
                                    $desc = "Mengatur gaya tampilan halaman web dengan CSS, pemodelan box model, pewarnaan, tipografi, serta penataan tata letak menggunakan Flexbox.";
                                } elseif ($modul['ID_Modul'] == 3) {
                                    $desc = "Penerapan CSS Grid Layout untuk desain grid 2 dimensi kompleks, serta media queries untuk pembuatan layout yang responsif di berbagai perangkat.";
                                } else {
                                    $desc = "Mempelajari konsep modul lanjutan untuk platform manajemen tugas praktikum EduLab UHO.";
                                }
                                echo htmlspecialchars($desc);
                                ?>
                            </p>
                            
                            <div class="download-actions-row">
                                <a href="/rpl/public/index.php?action=download_materi&file=<?= urlencode($modul['File_Materi']) ?>" class="modul-download-btn btn-materi">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    <span>Unduh Modul</span>
                                </a>
                                <a href="#" class="modul-download-btn btn-petunjuk" onclick="event.preventDefault(); showToast('Petunjuk tugas terintegrasi di modul materi.');">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    <span>Unduh Petunjuk Tugas</span>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        </div>
    </main>
</div>

<!-- Success Toast Notification -->
<div id="toastNotification" class="toast">
    <span id="toastMessage">Unduh dimulai...</span>
</div>

<script>
    const toast = document.getElementById('toastNotification');
    const toastMessage = document.getElementById('toastMessage');

    // Toast Notification helper
    function showToast(message) {
        toastMessage.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg> ${message}`;
        toast.className = "toast show";
        setTimeout(() => {
            toast.className = toast.className.replace("show", "");
        }, 3000);
    }
</script>

</body>
</html>
