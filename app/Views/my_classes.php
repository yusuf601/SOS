<?php
// ==============================================================================
// EduLab UHO - Kelas Saya View
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
    <title>Kelas Saya - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* Layout khusus Buat Kelas (Dosen / Asisten) */
        .buat-kelas-container {
            display: grid;
            grid-template-columns: 2.3fr 1fr;
            gap: 24px;
            align-items: start;
            margin-top: 16px;
        }

        .card-tabel-kelas {
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 24px;
            box-shadow: -4px 4px 4px rgba(0, 0, 0, 0.25);
            border: 1px solid #F1F5F9;
        }

        .tabel-title {
            font-size: 18px;
            font-weight: 600;
            color: #3B3B3B;
            margin-top: 0;
            margin-bottom: 24px;
            text-align: left;
        }

        .buat-kelas-tabel {
            width: 100%;
            border-collapse: collapse;
        }

        .buat-kelas-tabel th {
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: rgba(0, 0, 0, 0.5);
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .buat-kelas-tabel td {
            padding: 16px 0;
            font-size: 13px;
            color: #000000;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            text-align: left;
        }

        .buat-kelas-tabel tr:last-child td {
            border-bottom: none;
        }

        .badge-status {
            padding: 3px 10px;
            border-radius: 30px;
            font-size: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            letter-spacing: 0.45px;
        }

        .badge-status-aktif {
            background-color: #EAF3DE;
            color: #27500A;
        }

        .badge-status-berlangsung {
            background-color: #FFF7D3;
            color: #92400E;
        }

        .badge-status-belum-mulai {
            background-color: #E4E4E4;
            color: #787878;
        }

        .card-buat-baru {
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 24px;
            box-shadow: -6px 6px 4px rgba(0, 0, 0, 0.25);
            border: 1px solid #F1F5F9;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .form-label-buat {
            font-size: 13px;
            font-weight: 600;
            color: #6C6C6C;
            margin-bottom: 6px;
            display: block;
        }
        
        .form-input-buat {
            width: 100%;
            height: 43px;
            padding: 0 16px;
            border: 1px solid #C8C8C8;
            border-radius: 8px;
            font-size: 13px;
            color: #313131;
            box-sizing: border-box;
        }
        
        .form-input-buat::placeholder {
            color: #B5B5B5;
            font-weight: 500;
        }
        
        .form-select-buat {
            width: 100%;
            height: 39px;
            padding: 0 16px;
            border: 1px solid #C8C8C8;
            border-radius: 8px;
            font-size: 13px;
            color: #313131;
            font-weight: 500;
            box-sizing: border-box;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='9' viewBox='0 0 14 9' fill='none'%3E%3Cpath d='M1 1L7 7L13 1' stroke='%231F1F1F' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
        }

        .buat-baru-title {
            font-size: 14px;
            font-weight: 700;
            color: #313131;
            margin: 0;
        }

        .btn-plus-besar {
            width: 100%;
            height: 32px;
            background-color: #29316B;
            color: #FFFFFF;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background-color 0.2s;
        }

        .btn-plus-besar:hover {
            background-color: #1e2450;
        }

        @media (max-width: 992px) {
            .buat-kelas-container {
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
                <div class=\"sidebar-user-role\"><?php $r = $role ?? $_SESSION['active_role'] ?? ''; echo $r === 'Asisten' ? 'Asisten Dosen' : htmlspecialchars($r); ?></div>
            </div>

            <!-- Menu Navigation -->
            <?php include __DIR__ . '/components/sidebar_menu.php'; ?>
    </aside>

    <!-- Main Workspace -->
    <main class="main-workspace">
        <!-- Top Navbar -->
        <header class="workspace-navbar">
            <h2 class="navbar-title">Kelas Saya</h2>
            <div class="navbar-profile">
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">


            <?php if ($role === 'Mahasiswa'): ?>
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

                <!-- Section Header -->
                <div class="section-heading-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h3 class="section-title">Semua Kelas Praktikum</h3>
                    <form method="POST" action="/rpl/public/index.php?action=join_class" style="display: flex; gap: 8px;">
                        <input type="text" name="token_kelas" placeholder="Token Kelas..." required style="padding: 8px 12px; border: 1px solid #D9E2EC; border-radius: 6px; font-family: 'Inter', sans-serif; font-size: 13px; width: 160px; outline: none;">
                        <button type="submit" style="background-color: #29316B; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 500; cursor: pointer; font-size: 13px; font-family: 'Inter', sans-serif; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#1e2450'" onmouseout="this.style.backgroundColor='#29316B'">Gabung</button>
                    </form>
                </div>

                <!-- Classes Grid -->
                <div class="classes-grid">
                    <?php if (empty($classesData)): ?>
                        <div style="grid-column: 1 / -1; text-align: center; background: white; padding: 64px 24px; border-radius: 12px; border: 1px solid #E2E8F0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#A0AEC0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 16px;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            <h3 style="margin: 0 0 8px 0; color: #2D3748; font-size: 18px; font-weight: 600;">Belum Ada Kelas</h3>
                            <p style="margin: 0; color: #718096; font-size: 14px;">Anda belum bergabung di kelas praktikum manapun.<br>Silakan masukkan Token Kelas dari Dosen Anda di atas.</p>
                        </div>
                    <?php else: ?>
                        <?php 
                        $headers = ['class-header-blue', 'class-header-orange', 'class-header-green'];
                        foreach ($classesData as $index => $cls): 
                            $headerStyle = $headers[$index % count($headers)];
                            
                            $className = $cls['class_name'];
                            $classBadge = 'Praktikum';
                            if ($cls['class_id'] == 1) {
                                $classBadge = 'Kelas A';
                            } elseif ($cls['class_id'] == 2) {
                                $classBadge = 'Kelas B';
                            } elseif ($cls['class_id'] == 3) {
                                $classBadge = 'Kelas C';
                            }
                        ?>
                            <div class="class-card">
                                <div class="class-card-header <?= $headerStyle ?>">
                                    <div class="class-card-title-row">
                                        <h4 class="class-title"><?= htmlspecialchars($className) ?></h4>
                                        <span class="class-badge"><?= htmlspecialchars($classBadge) ?></span>
                                    </div>
                                    <div class="class-lecturer-info">
                                        Dosen: <?= htmlspecialchars($cls['lecturer']) ?>
                                    </div>
                                </div>
                                <div class="class-card-body">
                                    <!-- Progress Section -->
                                    <div class="progress-section">
                                        <div class="progress-label-row">
                                            <span class="progress-title">Progress Pertemuan</span>
                                            <span class="progress-ratio"><?= htmlspecialchars($cls['total_modules']) ?> / 16</span>
                                        </div>
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" style="width: <?= ($cls['total_modules'] / 16) * 100 ?>%;"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Badges Stack -->
                                    <div class="card-tags-row">
                                        <span class="tag-pill tag-kelompok"><?= htmlspecialchars($cls['group_name']) ?></span>
                                        <span class="tag-pill tag-asdos">Asdos: <?= htmlspecialchars($cls['assistant']) ?></span>
                                    </div>
                                    
                                    <!-- Footer Specs -->
                                    <div class="card-footer-stats">
                                        <div class="footer-stat-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                            <span><?= htmlspecialchars($cls['total_students']) ?> mhs</span>
                                        </div>
                                        <div class="footer-stat-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                            <span><?= htmlspecialchars($cls['total_modules']) ?> modul</span>
                                        </div>
                                        <div class="footer-stat-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>
                                            <span>Min. Lulus: 70</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php elseif ($role === 'Asisten'): ?>
                <!-- Asisten Layout for Kelas Saya (Dashboard View) -->
                <div class="classes-asisten-container" style="padding-top: 10px;">
                    <!-- Alert Message -->
                    <div style="background-color: #D4C9FF57; border-radius: 5px; padding: 14px; display: flex; align-items: center; gap: 12px; margin-bottom: 32px;">
                        <div style="width: 23px; height: 23px; background-color: #8E87E5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px; flex-shrink: 0;">i</div>
                        <div style="color: #5D56B8; font-weight: 500; font-size: 15px; letter-spacing: 0.05px;">Kamu hanya bisa mengakses data kelompok yang kamu ampu. Kelas lain tidak dapat diakses.</div>
                    </div>

                    <?php if (empty($classesData)): ?>
                        <p style="text-align: center; color: #9B9B9B; font-weight: 600; padding: 48px;">Anda belum ditugaskan pada kelas mana pun.</p>
                    <?php else: ?>
                        <?php foreach ($classesData as $cls): ?>
                        <!-- Class Card -->
                        <div style="background-color: #364087; border-radius: 20px; padding: 40px 48px; color: white; position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: center; min-height: 209px; margin-bottom: 24px;">
                            
                            <!-- Vector Decoration -->
                            <div style="position: absolute; right: 24px; bottom: 12px; opacity: 0.35;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="146" height="146" viewBox="0 -960 960 960" fill="white">
                                    <path d="M40-120v-80h880v80H40Zm120-120q-33 0-56.5-23.5T80-320v-440q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v440q0 33-23.5 56.5T800-240H160Zm0-80h640v-440H160v440Zm0 0v-440 440Z"/>
                                </svg>
                            </div>

                            <!-- Badge Kelas A -->
                            <div style="position: absolute; right: 32px; top: 32px; background-color: rgba(255, 255, 255, 0.3); padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; letter-spacing: 0.45px; z-index: 2;">
                                <?php 
                                    $badgeName = 'Praktikum';
                                    if ($cls['class_id'] == 1) $badgeName = 'Kelas A';
                                    elseif ($cls['class_id'] == 2) $badgeName = 'Kelas B';
                                    elseif ($cls['class_id'] == 3) $badgeName = 'Kelas C';
                                    echo $badgeName;
                                ?>
                            </div>

                            <!-- Class Title & Info -->
                            <div style="position: relative; z-index: 2;">
                                <h2 style="font-size: 40px; font-weight: bold; margin: 0 0 12px 0; text-align: left; line-height: 1.2;"><?= htmlspecialchars($cls['class_name']) ?></h2>
                                
                                <p style="font-size: 20px; font-weight: 500; color: rgba(255, 255, 255, 0.68); margin: 0 0 32px 0; text-align: left;">
                                    <?= htmlspecialchars($cls['lecturer'] ?? 'Tidak Ada Dosen') ?>
                                </p>

                                <!-- Stats Row -->
                                <div style="display: flex; align-items: center; justify-content: flex-start; gap: 48px;">
                                    <div style="display: flex; flex-direction: column; align-items: flex-start;">
                                        <div style="font-size: 32px; font-weight: bold; line-height: 1.2; margin-bottom: 4px;"><?= htmlspecialchars($cls['total_groups']) ?></div>
                                        <div style="font-size: 16px; font-weight: 500; opacity: 0.9;">Kelompok diampu</div>
                                    </div>
                                    
                                    <div style="width: 1px; height: 45px; background-color: rgba(255, 255, 255, 0.3);"></div>
                                    
                                    <div style="display: flex; flex-direction: column; align-items: flex-start;">
                                        <div style="font-size: 32px; font-weight: bold; line-height: 1.2; margin-bottom: 4px;"><?= htmlspecialchars($cls['total_students']) ?></div>
                                        <div style="font-size: 16px; font-weight: 500; opacity: 0.9;">Mahasiswa</div>
                                    </div>
                                    
                                    <div style="width: 1px; height: 45px; background-color: rgba(255, 255, 255, 0.3);"></div>
                                    
                                    <div style="display: flex; flex-direction: column; align-items: flex-start;">
                                        <div style="font-size: 32px; font-weight: bold; line-height: 1.2; margin-bottom: 4px;"><?= htmlspecialchars(min(7, $cls['total_modules'] ?? 14)) ?>/14</div>
                                        <div style="font-size: 16px; font-weight: 500; opacity: 0.9;">Pertemuan</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            <?php elseif ($role === 'Dosen'): ?>
                <!-- Dosen Two-Column Layout -->
                <div class="buat-kelas-container">
                    <!-- Column Left: Kelas yang Sudah Dibuat -->
                    <div class="card-tabel-kelas">
                        <h3 class="tabel-title">Kelas yang Sudah Dibuat</h3>
                        <table class="buat-kelas-tabel">
                            <thead>
                                <tr>
                                    <th>Nama Kelas</th>
                                    <th>Jadwal</th>
                                    <th>Mhs</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $displayClasses = [];
                                foreach ($classesData as $cls) {
                                    $displayName = $cls['class_name'];
                                    // Clean " (Kelas A)" or similar to match Figma "Sistem Digital A"
                                    $displayName = preg_replace('/\s*\(Kelas\s+([A-Z])\)/i', ' $1', $displayName);

                                    if ($cls['total_students'] > 0) {
                                        $status = 'Aktif';
                                        $statusClass = 'badge-status-aktif';
                                    } else {
                                        $status = 'Belum Mulai';
                                        $statusClass = 'badge-status-belum-mulai';
                                    }

                                    $displayClasses[] = [
                                        'name' => $displayName,
                                        'jadwal' => $cls['schedule'] ?? 'Belum Diatur',
                                        'mhs' => $cls['total_students'],
                                        'status' => $status,
                                        'statusClass' => $statusClass,
                                        'token' => $cls['token']
                                    ];
                                }

                                foreach ($displayClasses as $dc):
                                ?>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            <?= htmlspecialchars($dc['name']) ?>
                                            <br>
                                            <span style="font-size: 11px; color: #6C6C6C; background: #F0F4F8; padding: 4px 8px; border-radius: 6px; border: 1px solid #D9E2EC; margin-top: 6px; display: inline-flex; align-items: center; gap: 6px; position: relative;">
                                                Token: <strong style="color: #111827; letter-spacing: 1px; font-family: monospace; font-size: 12px;"><?= htmlspecialchars($dc['token'] ?? '-') ?></strong>
                                                <?php if (($dc['token'] ?? '-') !== '-'): ?>
                                                <button type="button" onclick="copyTokenFromList('<?= htmlspecialchars($dc['token']) ?>', this)" style="background: none; border: none; cursor: pointer; padding: 2px; color: #4F46E5; display: flex; align-items: center;" title="Salin Token">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                                </button>
                                                <span class="copy-feedback" style="position: absolute; right: -50px; top: 50%; transform: translateY(-50%); font-size: 10px; color: #10B981; font-weight: bold; opacity: 0; transition: opacity 0.2s;">Tersalin!</span>
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td style="font-size: 13px; color: #4B5563;"><?= htmlspecialchars($dc['jadwal']) ?></td>
                                        <td><?= htmlspecialchars($dc['mhs']) ?></td>
                                        <td>
                                            <span class="badge-status <?= $dc['statusClass'] ?>">
                                                <?= htmlspecialchars($dc['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Column Right: Buat Kelas Baru -->
                    <div class="card-buat-baru">
                        <h4 class="buat-baru-title">Buat Kelas Baru</h4>
                        <form method="POST" action="/rpl/public/index.php?action=submit_class" style="display: flex; flex-direction: column; gap: 16px; margin-top: 8px; width: 100%;">
                            <div style="text-align: left;">
                                <label class="form-label-buat">Nama Mata Kuliah Praktikum</label>
                                <select name="nama_matkul" class="form-select-buat" required>
                                    <option value="" disabled selected>-- Pilih Mata Kuliah --</option>
                                    <option value="Sistem Digital">Sistem Digital</option>
                                    <option value="Organisasi dan Arsitektur Komputer">Organisasi dan Arsitektur Komputer</option>
                                    <option value="Statistika">Statistika</option>
                                    <option value="Aljabar Linear">Aljabar Linear</option>
                                    <option value="Metode Numerik">Metode Numerik</option>
                                    <option value="Keamanan Data dan Informasi (Cyber Security)">Keamanan Data dan Informasi (Cyber Security)</option>
                                </select>
                            </div>
                            <div style="display: flex; gap: 16px;">
                                <div style="text-align: left; flex: 1;">
                                    <label class="form-label-buat">Kode Kelas</label>
                                    <select name="kode_kelas" class="form-select-buat" required>
                                        <option value="" disabled selected>-- Kelas --</option>
                                        <option value="A">Kelas A</option>
                                        <option value="B">Kelas B</option>
                                        <option value="C">Kelas C</option>
                                        <option value="D">Kelas D</option>
                                        <option value="E">Kelas E</option>
                                    </select>
                                </div>
                            </div>
                            <div style="text-align: left;">
                                <label class="form-label-buat">Semester</label>
                                <select name="semester" class="form-select-buat">
                                    <option value="Genap 2025/2026">Genap 2025/2026</option>
                                    <option value="Ganjil 2025/2026">Ganjil 2025/2026</option>
                                </select>
                            </div>

                            <?php if (isset($listAsisten) && !empty($listAsisten)): ?>
                            <div style="text-align: left; display: flex; flex-direction: column; gap: 8px;">
                                <label class="form-label-buat">Pilih Asisten (Bisa lebih dari 1)</label>
                                <div style="border: 1px solid #CBD5E1; border-radius: 8px; padding: 12px; background-color: #F8FAFC;">
                                    <input type="text" id="searchAsisten" class="form-input-buat" placeholder="Cari NIM atau nama calon asisten..." style="height: 35px; margin-bottom: 12px;" onkeyup="filterAsisten()">
                                    <div id="asistenListContainer" style="max-height: 140px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; padding-right: 4px;">
                                        <?php foreach ($listAsisten as $asisten): ?>
                                            <label class="asisten-checkbox-item" style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #334155; cursor: pointer; padding: 4px 0;">
                                                <input type="checkbox" name="asisten[]" value="<?= $asisten['ID_User'] ?>" style="width: 16px; height: 16px; cursor: pointer;">
                                                <span class="asisten-name"><strong><?= htmlspecialchars($asisten['Username']) ?></strong> - <?= htmlspecialchars($asisten['Nama_Lengkap']) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <script>
                            function filterAsisten() {
                                let input = document.getElementById('searchAsisten').value.toLowerCase();
                                let items = document.querySelectorAll('.asisten-checkbox-item');
                                items.forEach(item => {
                                    let text = item.querySelector('.asisten-name').textContent || item.innerText;
                                    item.style.display = text.toLowerCase().indexOf(input) > -1 ? 'flex' : 'none';
                                });
                            }
                            </script>
                            <?php endif; ?>
                            <div style="margin-top: 8px;">
                                <button type="submit" class="btn-plus-besar" style="font-size: 14px; font-weight: 600; background-color: #29316B; border-radius: 8px; height: 35px;">Buat Kelas</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>


    </div>
</div>

<!-- Token Popup Overlay -->
<?php if (isset($_SESSION['new_class_token'])): ?>
<div id="tokenPopup" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px);">
    <div style="background: #FFFFFF; width: 420px; padding: 40px 32px; border-radius: 20px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); position: relative; animation: slideUpFade 0.4s cubic-bezier(0.16, 1, 0.3, 1);">
        <div style="width: 64px; height: 64px; background: #EEF2FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        </div>
        <h2 style="margin: 0 0 12px 0; font-family: 'Inter', sans-serif; font-size: 22px; font-weight: 700; color: #111827;">Kelas Berhasil Dibuat!</h2>
        <p style="margin: 0 0 28px 0; font-family: 'Inter', sans-serif; font-size: 14px; color: #6B7280; line-height: 1.6;">Silakan salin dan bagikan token ini kepada Mahasiswa agar mereka dapat bergabung ke dalam kelas praktikum.</p>
        
        <div style="background: #F9FAFB; padding: 20px; border-radius: 12px; border: 2px dashed #D1D5DB; margin-bottom: 32px; position: relative; cursor: pointer; transition: all 0.2s;" onclick="copyToken('<?= htmlspecialchars($_SESSION['new_class_token']) ?>')" id="tokenBox" title="Klik untuk menyalin">
            <span style="font-family: 'Courier New', Courier, monospace; font-size: 32px; font-weight: 800; letter-spacing: 6px; color: #1F2937;"><?= htmlspecialchars($_SESSION['new_class_token']) ?></span>
            <div id="copyTooltip" style="position: absolute; top: -35px; left: 50%; transform: translateX(-50%); background: #111827; color: white; padding: 6px 14px; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; font-weight: 500; opacity: 0; transition: opacity 0.2s; pointer-events: none; white-space: nowrap; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">✓ Token Tersalin!</div>
        </div>

        <button onclick="document.getElementById('tokenPopup').style.display='none'" style="background: #364087; color: white; border: none; padding: 14px 24px; border-radius: 10px; font-family: 'Inter', sans-serif; font-size: 15px; font-weight: 600; cursor: pointer; width: 100%; transition: background 0.2s;" onmouseover="this.style.background='#2b336b'" onmouseout="this.style.background='#364087'">Saya Sudah Menyalinnya</button>
    </div>
</div>
<style>
@keyframes slideUpFade {
    0% { opacity: 0; transform: translateY(30px) scale(0.95); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
#tokenBox:hover { 
    background: #F3F4F6 !important; 
    border-color: #9CA3AF !important;
}
#tokenBox:active {
    transform: scale(0.98);
}
</style>
<?php unset($_SESSION['new_class_token']); ?>
<?php endif; ?>

<script>
function copyToken(token) {
    navigator.clipboard.writeText(token).then(() => {
        const tooltip = document.getElementById('copyTooltip');
        if (tooltip) {
            tooltip.style.opacity = '1';
            setTimeout(() => { tooltip.style.opacity = '0'; }, 2000);
        }
    });
}

function copyTokenFromList(token, btn) {
    navigator.clipboard.writeText(token).then(() => {
        const feedback = btn.nextElementSibling;
        if (feedback && feedback.classList.contains('copy-feedback')) {
            feedback.style.opacity = '1';
            setTimeout(() => { feedback.style.opacity = '0'; }, 2000);
        }
    });
}
</script>

</body>
</html>
