<?php
$currentAction = $_GET['action'] ?? 'dashboard_dosen';
$r = $role ?? $_SESSION['active_role'] ?? '';
?>
<!-- Menu Navigation -->
<nav>
    <div class="sidebar-menu-title">Menu Utama</div>
    <ul class="sidebar-menu-list">
        <?php if ($r === 'Asisten'): ?>
            <!-- ASISTEN MENUS -->
            <li class="sidebar-menu-item <?= $currentAction === 'dashboard_dosen' || $currentAction === 'dashboard_asisten' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=dashboard_dosen">
                    <span>Dashboard</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'my_classes' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=my_classes">
                    <span>Kelas Saya</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'buat_kelompok' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=buat_kelompok">
                    <span>Buat Kelompok</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'data_kelompok' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=data_kelompok">
                    <span>Data Kelompok</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'presensi' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=presensi">
                    <span>Data Presensi</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'verifikasi_tugas' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=verifikasi_tugas">
                    <span>Verifikasi Tugas</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'kelulusan' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=kelulusan">
                    <span>Input Nilai</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="14 2 18 6 7 17 3 17 3 13 14 2"></polygon><line x1="3" y1="22" x2="21" y2="22"></line></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'sanggah_nilai' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=sanggah_nilai">
                    <span>Tinjau Sanggahan</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </span>
                </a>
            </li>
        <?php elseif ($r === 'Mahasiswa'): ?>
            <!-- MAHASISWA MENUS -->
            <li class="sidebar-menu-item <?= $currentAction === 'dashboard_student' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=dashboard_student">
                    <span>Dashboard</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'my_classes' || $currentAction === 'bank_modul' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=my_classes">
                    <span>Kelas Saya</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    </span>
                </a>
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-item <?= $currentAction === 'bank_modul' ? 'active' : '' ?>">
                        <a href="/rpl/public/index.php?action=bank_modul">
                            <span>Bank Modul</span>
                            <span class="sidebar-menu-item-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'upload_tugas' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=upload_tugas">
                    <span>Upload Tugas</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'sanggah_nilai' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=sanggah_nilai">
                    <span>Lihat Nilai</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'sanggah_form' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=sanggah_form">
                    <span>Sanggah Nilai</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'kelulusan' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=kelulusan">
                    <span>Status Kelulusan</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                    </span>
                </a>
            </li>
        <?php else: ?>
            <!-- DOSEN MENUS -->
            <li class="sidebar-menu-item <?= $currentAction === 'dashboard_dosen' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=dashboard_dosen">
                    <span>Dashboard</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'my_classes' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=my_classes">
                    <span>Buat Kelas</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'upload_modul' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=upload_modul">
                    <span>Upload Modul</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'monitoring_kelas' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=monitoring_kelas">
                    <span>Monitoring Kelas</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-item <?= $currentAction === 'kelulusan' ? 'active' : '' ?>">
                <a href="/rpl/public/index.php?action=kelulusan">
                    <span>Export Rekapitulasi</span>
                    <span class="sidebar-menu-item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                    </span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<div style="flex-grow: 1;"></div>
<div>
    <div class="sidebar-divider" style="<?= $r === 'Mahasiswa' ? 'display: none;' : '' ?>"></div>
    <ul class="sidebar-menu-list">
        <li class="sidebar-menu-item <?= $currentAction === 'pengaturan' ? 'active' : '' ?>">
            <a href="/rpl/public/index.php?action=pengaturan" class="settings-open-label" style="display: flex; justify-content: flex-start; gap: 10px; padding: 10px 16px; color: rgba(255, 255, 255, 0.8) !important; text-decoration: none; font-size: 15px; font-weight: 600; cursor: pointer; border-radius: 8px; transition: all 0.2s;">
                <span>Pengaturan</span>
                <span class="sidebar-menu-item-icon" style="order: -1; flex-shrink: 0;">
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
</nav>
</div>
