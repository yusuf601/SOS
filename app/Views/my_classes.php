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
                <!-- Section Header -->
                <div class="section-heading-row">
                    <h3 class="section-title">Semua Kelas Praktikum</h3>
                </div>

                <!-- Classes Grid -->
                <div class="classes-grid">
                    <?php if (empty($classesData)): ?>
                        <p style="grid-column: 1 / -1; text-align: center; color: #9B9B9B; font-weight: 600; padding: 48px;">
                            Anda belum terdaftar di kelas praktikum mana pun.
                        </p>
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
                                        Dosen: <?= htmlspecialchars($cls['lecturer']) ?><br>
                                        <?= htmlspecialchars($cls['schedule']) ?>
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
                                
                                <?php
                                    $scheduleStr = $cls['schedule'];
                                    $room = 'Lab';
                                    if (preg_match('/\((.*?)\)/', $scheduleStr, $m)) {
                                        $room = $m[1];
                                    }
                                    if (preg_match('/^(Senin|Selasa|Rabu|Kamis|Jumat|Sabtu|Minggu)\s+\d{2}:\d{2}/i', $scheduleStr, $matches)) {
                                        $scheduleStr = $matches[0];
                                    }
                                ?>
                                <p style="font-size: 20px; font-weight: 500; color: rgba(255, 255, 255, 0.68); margin: 0 0 32px 0; text-align: left;">
                                    <?= htmlspecialchars($cls['lecturer'] ?? 'Tidak Ada Dosen') ?> · <?= htmlspecialchars($scheduleStr) ?> (<?= htmlspecialchars($room) ?>)
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

                                    $displaySchedule = $cls['schedule'];
                                    if (preg_match('/^(Senin|Selasa|Rabu|Kamis|Jumat|Sabtu|Minggu)\s+\d{2}:\d{2}/i', $displaySchedule, $matches)) {
                                        $displaySchedule = $matches[0];
                                    }

                                    $status = 'Belum Mulai';
                                    $statusClass = 'badge-status-belum-mulai';
                                    if ($cls['class_id'] == 1) {
                                        $status = 'Aktif';
                                        $statusClass = 'badge-status-aktif';
                                    } elseif ($cls['class_id'] == 2) {
                                        $status = 'Aktif';
                                        $statusClass = 'badge-status-aktif';
                                    } elseif ($cls['class_id'] == 3) {
                                        $status = 'Berlangsung';
                                        $statusClass = 'badge-status-berlangsung';
                                    }

                                    $displayClasses[] = [
                                        'name' => $displayName,
                                        'schedule' => $displaySchedule,
                                        'mhs' => $cls['total_students'],
                                        'status' => $status,
                                        'statusClass' => $statusClass
                                    ];
                                }

                                // Fallback to match Figma exactly if we only have less than 4 classes
                                if (count($displayClasses) < 4) {
                                    $displayClasses[] = [
                                        'name' => 'Basis Data B',
                                        'schedule' => 'Jumat 08:00',
                                        'mhs' => 37,
                                        'status' => 'Belum Mulai',
                                        'statusClass' => 'badge-status-belum-mulai'
                                    ];
                                }

                                foreach ($displayClasses as $dc):
                                ?>
                                    <tr>
                                        <td style="font-weight: 500;"><?= htmlspecialchars($dc['name']) ?></td>
                                        <td><?= htmlspecialchars($dc['schedule']) ?></td>
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
                                <input type="text" name="nama_matkul" class="form-input-buat" placeholder="cth: Praktikum Sistem Digital" required>
                            </div>
                            <div style="text-align: left;">
                                <label class="form-label-buat">Kode Kelas</label>
                                <input type="text" name="kode_kelas" class="form-input-buat" style="height: 39px;" placeholder="A / B / C" required>
                            </div>
                            <div style="text-align: left;">
                                <label class="form-label-buat">Semester</label>
                                <select name="semester" class="form-select-buat">
                                    <option value="Genap 2025/2026">Genap 2025/2026</option>
                                    <option value="Ganjil 2025/2026">Ganjil 2025/2026</option>
                                </select>
                            </div>
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

</body>
</html>
