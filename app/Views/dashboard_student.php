<?php
$fullName = $_SESSION['name'] ?? 'Guest';
$firstName = explode(" ", $fullName)[0];

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
    <title>Dashboard Mahasiswa - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
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
                <div class="sidebar-user-role"><?php $r = $role ?? $_SESSION['active_role'] ?? ''; echo $r === 'Asisten' ? 'Asisten Dosen' : htmlspecialchars($r); ?></div>
            </div>

            <!-- Menu Navigation -->
            <?php include __DIR__ . '/components/sidebar_menu.php'; ?>
    </aside>

    <!-- Main Workspace -->
    <main class="main-workspace">
        <!-- Top Navbar -->
        <header class="workspace-navbar">
            <h2 class="navbar-title">Dashboard</h2>
            <div class="navbar-profile">
                
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">
            <?php if (isset($_SESSION['settings_success'])): ?>
                <div style="background-color: #D1FAE5; color: #065F46; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500; border: 1px solid #34D399;">
                    <?= htmlspecialchars($_SESSION['settings_success']) ?>
                </div>
                <?php unset($_SESSION['settings_success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['settings_error'])): ?>
                <div style="background-color: #FEE2E2; color: #991B1B; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500; border: 1px solid #F87171;">
                    <?= htmlspecialchars($_SESSION['settings_error']) ?>
                </div>
                <?php unset($_SESSION['settings_error']); ?>
            <?php endif; ?>

            <!-- Welcome Banner -->
            <section class="welcome-banner">
                <h1 class="welcome-text">Selamat datang, <?= htmlspecialchars($firstName) ?>!</h1>
                <p class="welcome-subtext">Kamu memiliki <?= htmlspecialchars($stats['pending_tasks'] ?? 0) ?> tugas pending/belum selesai saat ini.</p>
            </section>

            <!-- Metric Cards -->
            <section class="stats-metrics-grid">
                <!-- Metric Card 1 -->
                <div class="metric-card">
                    <div class="metric-icon-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    </div>
                    <div>
                        <div class="metric-value"><?= htmlspecialchars($stats['active_classes'] ?? 0) ?></div>
                        <div class="metric-label">Kelas Aktif</div>
                    </div>
                </div>

                <!-- Metric Card 2 -->
                <div class="metric-card">
                    <div class="metric-icon-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div>
                        <div class="metric-value"><?= htmlspecialchars($stats['pending_tasks'] ?? 0) ?></div>
                        <div class="metric-label">Tugas Pending</div>
                    </div>
                </div>

                <!-- Metric Card 3 -->
                <div class="metric-card">
                    <div class="metric-icon-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    </div>
                    <div>
                        <div class="metric-value"><?= htmlspecialchars($stats['average_score'] ?? 0) ?></div>
                        <div class="metric-label">Rata-rata Nilai</div>
                    </div>
                </div>

                <!-- Metric Card 4 -->
                <div class="metric-card">
                    <div class="metric-icon-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <div>
                        <div class="metric-value"><?= htmlspecialchars($stats['attendance'] ?? 100) ?>%</div>
                        <div class="metric-label">Kehadiran</div>
                    </div>
                </div>
            </section>

            <!-- Classes Section -->
            <section style="display: flex; flex-direction: column; gap: 20px;">
                <div class="section-heading-row" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="section-title" style="margin: 0;">Kelas Saya</h3>
                    <div style="display: flex; gap: 16px; align-items: center;">
                        <form method="POST" action="/rpl/public/index.php?action=join_class" style="display: flex; gap: 8px; margin: 0;">
                            <input type="text" name="token_kelas" placeholder="Token Kelas..." required style="padding: 8px 12px; border: 1px solid #D9E2EC; border-radius: 6px; font-family: 'Inter', sans-serif; font-size: 13px; width: 150px; outline: none;">
                            <button type="submit" style="background-color: #29316B; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 500; cursor: pointer; font-size: 13px; font-family: 'Inter', sans-serif; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#1e2450'" onmouseout="this.style.backgroundColor='#29316B'">Gabung</button>
                        </form>
                        <a href="/rpl/public/index.php?action=my_classes" class="btn-see-all" style="margin: 0;">
                            <span>Lihat semua</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                </div>

                <!-- Classes Grid -->
                <div class="classes-grid">
                    <?php if (!empty($classCards)): ?>
                        <?php foreach ($classCards as $cardData): 
                            $cInfo = $cardData['classInfo'];
                            $cProg = $cardData['progress'];
                        ?>
                        <!-- Dynamic Class Card from DB -->
                        <a href="/rpl/public/index.php?action=bank_modul&class_id=<?= $cInfo['ID_Kelas'] ?>" style="text-decoration: none; color: inherit; display: block;">
                        <div class="class-card" style="cursor: pointer; transition: transform 0.2s; border: 1px solid transparent;" onmouseover="this.style.transform='translateY(-4px)'; this.style.borderColor='#3b82f6';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='transparent';">
                            <div class="class-card-header class-header-blue">
                                <div class="class-card-title-row">
                                    <h4 class="class-title"><?= htmlspecialchars($cInfo['Nama_Kelas']) ?></h4>
                                    <span class="class-badge"><?= htmlspecialchars($cInfo['Nama_Kelompok'] ?? 'Kelompok') ?></span>
                                </div>
                                <div class="class-lecturer-info">
                                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'Mahasiswa') ?>
                                </div>
                            </div>
                            <div class="class-card-body">
                                <!-- Progress Section -->
                                <div class="progress-section">
                                    <div class="progress-label-row">
                                        <span class="progress-title">Progress tugas</span>
                                        <span class="progress-ratio"><?= $cProg['submitted'] ?? 0 ?>/<?= $cProg['total_tasks'] ?? 0 ?></span>
                                    </div>
                                    <?php
                                    $pct = ($cProg['total_tasks'] ?? 0) > 0
                                        ? round(($cProg['submitted'] / $cProg['total_tasks']) * 100, 1)
                                        : 0;
                                    ?>
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill" style="width: <?= $pct ?>%;"></div>
                                    </div>
                                </div>

                                <!-- Badges Stack -->
                                <div class="card-tags-row">
                                    <span class="tag-pill tag-kelompok"><?= htmlspecialchars($cInfo['Nama_Kelompok'] ?? '-') ?></span>
                                    <span class="tag-pill tag-deadline">Tugas pending: <?= $cProg['pending'] ?? 0 ?></span>
                                </div>

                                <!-- Footer Specs -->
                                <div class="card-footer-stats">
                                    <div class="footer-stat-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                        <span>Nilai rata-rata: <?= $cProg['average_score'] ?? '-' ?></span>
                                    </div>
                                    <div class="footer-stat-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>
                                        <span>Min. lulus: 70</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <div style="color: #94A3B8; font-size: 14px; padding: 20px 0;">
                        Anda belum terdaftar di kelas manapun.
                    </div>
                    <?php endif; ?>
                </div>





                </div>
            </section>
        </div>
    </main>
</div>


    </div>
</div>

</body>
</html>
