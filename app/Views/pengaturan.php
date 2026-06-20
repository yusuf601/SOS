<?php
// ==============================================================================
// EduLab UHO - Pengaturan (Settings) View
// ==============================================================================

$fullName = $_SESSION['name'] ?? ($userData['Nama_Lengkap'] ?? 'Guest');
$role = $_SESSION['active_role'] ?? 'Mahasiswa';
$username = $userData['Username'] ?? '';
$email = $userData['Email'] ?? '';
$phone = $userData['No_Telepon'] ?? '';

// Generate initials for Avatar
$words = explode(" ", $fullName);
$initials = "";
foreach ($words as $w) {
    $initials .= strtoupper($w[0] ?? '');
}
$initials = substr($initials, 0, 2);

// Subtitle based on role
$subtitle = "";
if ($role === 'Mahasiswa') {
    $semester = 'Semester 6'; // Default fallback
    if (isset($classInfo['Nama_Kelas'])) {
        // Simple heuristic for semester based on class
        $semester = 'Semester 6';
    }
    $subtitle = htmlspecialchars($username) . " · Mahasiswa · " . $semester;
} elseif ($role === 'Dosen') {
    $subtitle = htmlspecialchars($username) . " · Dosen · Staff";
} elseif ($role === 'Asisten') {
    $subtitle = htmlspecialchars($username) . " · Asisten · Staff";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* Modern Premium CSS for Full Page Settings */
        .settings-container {
            background: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03);
            border: 1px solid #F1F5F9;
            padding: 32px;
            margin-top: 24px;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .settings-section {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .settings-section-title {
            font-size: 20px;
            font-weight: 700;
            color: #1E293B;
            margin: 0;
            position: relative;
        }

        .settings-divider {
            height: 1px;
            background-color: #E2E8F0;
            border: none;
            margin: 8px 0;
        }

        /* Profile Row Layout */
        .profile-avatar-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            background-color: #F8FAFC;
            padding: 20px 24px;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
        }

        .profile-avatar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profile-avatar-large {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: linear-gradient(135deg, #364087, #4A58B8);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(54, 64, 135, 0.2);
            flex-shrink: 0;
        }

        .profile-meta-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .profile-meta-name {
            font-size: 18px;
            font-weight: 700;
            color: #1E293B;
        }

        .profile-meta-sub {
            font-size: 13px;
            color: #64748B;
            font-weight: 500;
        }

        .btn-change-photo {
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            color: #334155;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .btn-change-photo:hover {
            background: #F1F5F9;
            border-color: #94A3B8;
        }

        /* Form Grid styling */
        .settings-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 640px) {
            .settings-form-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group-full {
            grid-column: span 2;
        }

        @media (max-width: 640px) {
            .form-group-full {
                grid-column: span 1;
            }
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
        }

        .form-input {
            padding: 10px 14px;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            font-size: 14px;
            color: #1E293B;
            background-color: #FFFFFF;
            transition: all 0.2s;
            width: 100%;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: #364087;
            box-shadow: 0 0 0 3px rgba(54, 64, 135, 0.1);
        }

        .form-input:disabled {
            background-color: #F1F5F9;
            color: #64748B;
            cursor: not-allowed;
            border-color: #E2E8F0;
        }

        /* Buttons styling */
        .btn-action-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 12px;
        }

        .btn-primary-action {
            background-color: #364087;
            color: #FFFFFF;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(54, 64, 135, 0.1);
        }

        .btn-primary-action:hover {
            background-color: #2b336c;
            box-shadow: 0 4px 12px rgba(54, 64, 135, 0.2);
        }

        /* Warning/Info Banner */
        .warning-banner {
            background-color: #FFF9EB;
            border: 1px solid #FFE0B2;
            border-radius: 8px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .warning-banner-icon {
            color: #B76E00;
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }

        .warning-banner-text {
            font-size: 13.5px;
            color: #B76E00;
            font-weight: 550;
            line-height: 1.4;
        }

        /* Notifications Grid Layout (2 columns) */
        .notif-section-desc {
            font-size: 14px;
            color: #64748B;
            margin: -12px 0 12px 0;
        }

        .notif-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 1024px) {
            .notif-grid {
                grid-template-columns: 1fr;
            }
        }

        .notif-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            background: #FFFFFF;
            transition: all 0.2s ease;
            gap: 16px;
        }

        .notif-row:hover {
            border-color: #CBD5E1;
            background: #F8FAFC;
        }

        .notif-info-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .notif-item-title {
            font-size: 15px;
            font-weight: 600;
            color: #1E293B;
        }

        .notif-item-desc {
            font-size: 13px;
            color: #64748B;
            line-height: 1.4;
        }

        /* Custom Toggle Switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 25px;
            flex-shrink: 0;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #E2E8F0;
            transition: .3s;
            border-radius: 25px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 19px;
            width: 19px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        input:checked + .toggle-slider {
            background-color: #364087;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(23px);
        }

        /* Floating Toast Styling */
        .alert-toast {
            position: fixed;
            top: 24px;
            right: 24px;
            padding: 16px 24px;
            border-radius: 12px;
            color: #FFFFFF;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .alert-toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .alert-toast-success {
            background: #10B981;
        }

        .alert-toast-error {
            background: #EF4444;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar Navigation -->
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
                <div class=\"sidebar-user-role\"><?php $r = $role ?? $_SESSION['active_role'] ?? ''; echo $r === 'Asisten' ? 'Asisten Dosen' : htmlspecialchars($r); ?></div>
            </div>

            <!-- Menu Navigation -->
            <?php include __DIR__ . '/components/sidebar_menu.php'; ?>
    </aside>

    <!-- Main Workspace -->
    <main class="main-workspace">
        <!-- Top Navbar -->
        <header class="workspace-navbar">
            <h2 class="navbar-title">Pengaturan Akun</h2>
            <div class="navbar-profile">
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">
            
            <!-- Settings Main Panel -->
            <section class="settings-container">
                
                <!-- SECTION 1: PROFIL -->
                <div class="settings-section">
                    <h3 class="settings-section-title">Profil</h3>
                    
                    <div class="profile-avatar-row">
                        <div class="profile-avatar-left">
                            <div class="profile-avatar-large"><?= htmlspecialchars($initials) ?></div>
                            <div class="profile-meta-info">
                                <span class="profile-meta-name"><?= htmlspecialchars($fullName) ?></span>
                                <span class="profile-meta-sub"><?= $subtitle ?></span>
                            </div>
                        </div>
                        <button type="button" class="btn-change-photo" onclick="triggerPhotoUpload()">Ganti Foto</button>
                        <input type="file" id="photo-upload-input" style="display:none;" accept="image/*" onchange="simulatePhotoUpload()">
                    </div>

                    <!-- Profile Form -->
                    <form action="/rpl/public/index.php?action=update_profil" method="POST">
                        <div class="settings-form-grid">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($fullName) ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($email) ?>" placeholder="cth: <?= strtolower(str_replace(' ', '.', $fullName)) ?>@<?= ($role === 'Mahasiswa') ? 'student.edu' : 'uho.ac.id' ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label"><?= ($role === 'Dosen') ? 'NIDN' : 'NIM' ?></label>
                                <input type="text" class="form-input" value="<?= htmlspecialchars($username) ?>" disabled>
                            </div>

                            <div class="form-group">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="phone" class="form-input" value="<?= htmlspecialchars($phone) ?>" placeholder="cth: 08223466752">
                            </div>
                        </div>

                        <div class="btn-action-container">
                            <button type="submit" class="btn-primary-action">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>

                <hr class="settings-divider">

                <!-- SECTION 2: KEAMANAN -->
                <div class="settings-section">
                    <h3 class="settings-section-title">Keamanan</h3>

                    <!-- Password Update Form -->
                    <form action="/rpl/public/index.php?action=ubah_password" method="POST">
                        <div class="settings-form-grid">
                            <div class="form-group form-group-full">
                                <label class="form-label">Password Lama</label>
                                <input type="password" name="old_password" class="form-input" placeholder="Masukkan password lama" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="new_password" class="form-input" placeholder="Minimal 8 karakter" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_password" class="form-input" placeholder="Ulangi password baru" required>
                            </div>

                            <div class="form-group form-group-full" style="margin-top: 8px;">
                                <!-- Alert Banner -->
                                <div class="warning-banner">
                                    <div class="warning-banner-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    </div>
                                    <div class="warning-banner-text">
                                        Password baru minimal 8 karakter, kombinasi huruf dan angka.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="btn-action-container">
                            <button type="submit" class="btn-primary-action">Ubah Password</button>
                        </div>
                    </form>
                </div>

                <hr class="settings-divider">

                <!-- SECTION 3: NOTIFIKASI -->
                <div class="settings-section">
                    <h3 class="settings-section-title">Notifikasi</h3>
                    <p class="notif-section-desc">Atur jenis notifikasi yang ingin kamu terima</p>

                    <div class="notif-grid">
                        <?php if ($role === 'Mahasiswa'): ?>
                            
                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pembaruan Nilai Akademik</span>
                                    <span class="notif-item-desc">Notifikasi ketika nilai baru dipublikasikan oleh dosen atau asisten dosen.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_nilai" onchange="toggleNotification('nilai')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Tanggapan atas Sanggahan</span>
                                    <span class="notif-item-desc">Notifikasi ketika dosen atau asisten dosen memberikan tanggapan terhadap sanggahan yang diajukan.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_sanggah" onchange="toggleNotification('sanggah')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pengingat Batas Waktu Tugas</span>
                                    <span class="notif-item-desc">Notifikasi satu hari sebelum batas waktu pengumpulan tugas berakhir.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_deadline" onchange="toggleNotification('deadline')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pemberitahuan Modul Baru</span>
                                    <span class="notif-item-desc">Notifikasi ketika dosen mengunggah atau menerbitkan modul pembelajaran baru.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_modul" onchange="toggleNotification('modul')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                        <?php else: ?>
                            <!-- Dosen / Asisten Notification list -->
                            
                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pengumpulan Tugas Mahasiswa</span>
                                    <span class="notif-item-desc">Notifikasi ketika mahasiswa mengirimkan pengumpulan tugas baru di kelas yang Anda ampu.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_pengumpulan" onchange="toggleNotification('pengumpulan')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pengajuan Sanggahan Baru</span>
                                    <span class="notif-item-desc">Notifikasi ketika mahasiswa mengajukan sanggah nilai baru yang memerlukan respon Anda.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_sanggah_masuk" onchange="toggleNotification('sanggah_masuk')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pembaruan Modul Pembelajaran</span>
                                    <span class="notif-item-desc">Notifikasi konfirmasi saat modul pembelajaran berhasil diunggah atau diedit.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_modul_update" onchange="toggleNotification('modul_update')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="notif-row">
                                <div class="notif-info-block">
                                    <span class="notif-item-title">Pemberitahuan Sistem</span>
                                    <span class="notif-item-desc">Notifikasi mengenai pengumuman akademik, pemeliharaan sistem, atau pembaruan platform.</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notif_system" onchange="toggleNotification('system')" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>

            </section>

        </div>
    </main>
</div>

<!-- Float Toast Notification Container -->
<div id="settings-toast" class="alert-toast">
    <span id="settings-toast-icon"></span>
    <span id="settings-toast-msg"></span>
</div>

<!-- Core JS logic for Notifications LocalStorage & Alert handling -->
<script>
    // Helper to display floating toasts
    function showToast(message, type = 'success') {
        const toast = document.getElementById('settings-toast');
        const iconContainer = document.getElementById('settings-toast-icon');
        const msgContainer = document.getElementById('settings-toast-msg');

        // Reset classes
        toast.className = 'alert-toast';
        toast.classList.add(`alert-toast-${type}`);

        // Set Icons
        if (type === 'success') {
            iconContainer.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
        } else {
            iconContainer.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;
        }

        msgContainer.innerText = message;
        toast.classList.add('show');

        // Autohide after 3.5 seconds
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3500);
    }

    // Photo Upload Simulation
    function triggerPhotoUpload() {
        document.getElementById('photo-upload-input').click();
    }

    function simulatePhotoUpload() {
        const fileInput = document.getElementById('photo-upload-input');
        if (fileInput.files && fileInput.files[0]) {
            showToast('Simulasi ganti foto profil berhasil!', 'success');
            fileInput.value = ''; // Reset input
        }
    }

    // LocalStorage Notification persistent state
    function loadNotificationPreferences() {
        const toggles = document.querySelectorAll('.toggle-switch input');
        toggles.forEach(toggle => {
            const key = `notif_${toggle.id}`;
            const val = localStorage.getItem(key);
            if (val !== null) {
                toggle.checked = val === 'true';
            }
        });
    }

    function toggleNotification(type) {
        const targetToggle = document.getElementById(`notif_${type}`);
        if (targetToggle) {
            const key = `notif_${targetToggle.id}`;
            localStorage.setItem(key, targetToggle.checked);
            showToast('Preferensi notifikasi diperbarui!', 'success');
        }
    }

    // Handle initialization & redirect messages
    window.addEventListener('DOMContentLoaded', () => {
        loadNotificationPreferences();

        // Check PHP Session messaging triggers
        <?php if (isset($_SESSION['settings_success'])): ?>
            showToast("<?= $_SESSION['settings_success'] ?>", 'success');
            <?php unset($_SESSION['settings_success']); ?>
        <?php elseif (isset($_SESSION['settings_error'])): ?>
            showToast("<?= $_SESSION['settings_error'] ?>", 'error');
            <?php unset($_SESSION['settings_error']); ?>
        <?php endif; ?>
    });
</script>

</body>
</html>
