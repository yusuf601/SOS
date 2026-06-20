<?php
// ==============================================================================
// EduLab UHO - Monitoring Kelas View (Opsi B: Group-by Kelompok)
// ==============================================================================

$fullName = $_SESSION['name'] ?? 'Guest';
$role     = $_SESSION['active_role'] ?? 'Dosen';

$words    = explode(" ", $fullName);
$initials = "";
foreach ($words as $w) { $initials .= strtoupper($w[0] ?? ''); }
$initials = substr($initials, 0, 2);

$activeClassName = "Tidak Ada Kelas";
foreach ($myClasses as $cls) {
    if ($cls['ID_Kelas'] == $selectedClassId) {
        $activeClassName = $cls['Nama_Kelas'];
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Kelas - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        /* ── Summary Stats ── */
        .mon-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .mon-stat-card {
            background: #FFFFFF;
            border-radius: 14px;
            padding: 20px 24px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .mon-stat-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .mon-stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #1E293B;
        }
        .mon-stat-value span {
            font-size: 14px;
            font-weight: 500;
            color: #94A3B8;
        }

        /* ── Group Section ── */
        .group-section {
            background: #FFFFFF;
            border-radius: 16px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .group-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 24px;
            background: #364087;
            cursor: pointer;
            user-select: none;
        }
        .group-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .group-name {
            font-size: 16px;
            font-weight: 700;
            color: #FFFFFF;
        }
        .group-asisten {
            font-size: 13px;
            color: rgba(255,255,255,0.7);
        }
        .group-badge {
            background: rgba(255,255,255,0.15);
            color: #FFFFFF;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }
        .group-summary-pills {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .group-pill {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(255,255,255,0.15);
            color: #FFFFFF;
        }
        .group-toggle-icon {
            color: rgba(255,255,255,0.8);
            transition: transform 0.25s ease;
        }
        .group-toggle-icon.collapsed { transform: rotate(-90deg); }

        /* ── Table ── */
        .group-table-wrap {
            overflow-x: auto;
        }
        .group-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .group-table th {
            background: #F8FAFC;
            color: #475569;
            font-weight: 600;
            padding: 13px 20px;
            border-bottom: 1px solid #E2E8F0;
            white-space: nowrap;
            text-align: left;
        }
        .group-table td {
            padding: 14px 20px;
            border-bottom: 1px solid #F1F5F9;
            color: #334155;
            white-space: nowrap;
        }
        .group-table tbody tr:last-child td { border-bottom: none; }
        .group-table tbody tr:hover td { background: #F8FAFC; }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-lulus    { background: #ECFDF5; color: #059669; }
        .badge-gagal    { background: #FEF2F2; color: #DC2626; }
        .badge-none     { background: #F1F5F9; color: #94A3B8; }

        .nilai-num      { font-weight: 700; }
        .nilai-lulus    { color: #059669; }
        .nilai-gagal    { color: #DC2626; }
        .nilai-none     { color: #CBD5E1; }

        .kehadiran-bar-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .kehadiran-bar {
            width: 60px;
            height: 6px;
            background: #E2E8F0;
            border-radius: 3px;
            overflow: hidden;
        }
        .kehadiran-bar-fill {
            height: 100%;
            border-radius: 3px;
            background: #364087;
        }
        .kehadiran-text {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            min-width: 36px;
        }

        /* ── Navbar select ── */
        .navbar-select-form { display: flex; align-items: center; }
        .navbar-select-wrapper {
            position: relative;
            background: #364087;
            border-radius: 8px;
            padding: 2px 8px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .navbar-select {
            background: transparent;
            border: none;
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 600;
            padding: 6px 28px 6px 12px;
            cursor: pointer;
            outline: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        .navbar-select-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #FFFFFF;
            pointer-events: none;
        }

        .empty-group {
            text-align: center;
            color: #94A3B8;
            padding: 32px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <div class="sidebar-header">
                <img src="/rpl/public/assets/images/logo_uho.png" alt="Logo UHO" class="sidebar-logo">
                <span class="sidebar-brand-name">EduLab</span>
            </div>
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
            <h2 class="navbar-title">Monitoring Kelas</h2>
            <div style="display:flex; align-items:center; gap:24px;">
                <!-- Class dropdown -->
                <form method="GET" action="/rpl/public/index.php" id="classSelectForm" class="navbar-select-form">
                    <input type="hidden" name="action" value="monitoring_kelas">
                    <div class="navbar-select-wrapper">
                        <select name="class_id" class="navbar-select" onchange="document.getElementById('classSelectForm').submit()">
                            <?php foreach ($myClasses as $cls): ?>
                                <option value="<?= $cls['ID_Kelas'] ?>" <?= ($selectedClassId == $cls['ID_Kelas']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cls['Nama_Kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="navbar-select-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </span>
                    </div>
                </form>
                <div class="navbar-profile">
                    <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content">

            <!-- 3 Summary Stat Cards -->
            <div class="mon-stats">
                <div class="mon-stat-card">
                    <div class="mon-stat-label">Total Mahasiswa</div>
                    <div class="mon-stat-value"><?= $classSummary['total_students'] ?> <span>mhs</span></div>
                </div>
                <div class="mon-stat-card">
                    <div class="mon-stat-label">Rata-rata Nilai Kelas</div>
                    <div class="mon-stat-value"><?= $classSummary['avg_nilai'] ?> <span>/ 100</span></div>
                </div>
                <div class="mon-stat-card">
                    <div class="mon-stat-label">Rata-rata Kehadiran</div>
                    <div class="mon-stat-value"><?= $classSummary['avg_kehadiran'] ?><span>%</span></div>
                </div>
            </div>

            <!-- Group Sections -->
            <?php if (empty($groupedData)): ?>
                <div class="group-section">
                    <div class="empty-group">Belum ada kelompok terdaftar di kelas ini.</div>
                </div>
            <?php else: ?>
                <?php foreach ($groupedData as $idx => $grp): ?>
                    <div class="group-section">
                        <div class="group-header" onclick="toggleGroup(<?= $idx ?>)">
                            <div class="group-header-left">
                                <div>
                                    <div class="group-name"><?= htmlspecialchars($grp['group_name']) ?></div>
                                    <div class="group-asisten">Asisten: <?= htmlspecialchars($grp['asisten']) ?></div>
                                </div>
                                <span class="group-badge"><?= count($grp['students']) ?> mhs</span>
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <?php if ($grp['avg_nilai'] !== null): ?>
                                    <span class="group-pill">Avg Nilai: <?= $grp['avg_nilai'] ?></span>
                                <?php endif; ?>
                                <?php if ($grp['avg_kehadiran'] !== null): ?>
                                    <span class="group-pill">Kehadiran: <?= $grp['avg_kehadiran'] ?>%</span>
                                <?php endif; ?>
                                <svg class="group-toggle-icon" id="toggle-icon-<?= $idx ?>" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>

                        <div class="group-table-wrap" id="group-body-<?= $idx ?>">
                            <?php if (empty($grp['students'])): ?>
                                <div class="empty-group">Belum ada mahasiswa di kelompok ini.</div>
                            <?php else: ?>
                                <table class="group-table">
                                    <thead>
                                        <tr>
                                            <th style="width:15%">NIM</th>
                                            <th style="width:35%">Nama</th>
                                            <th style="width:20%; text-align:center;">Kehadiran</th>
                                            <th style="width:15%; text-align:center;">Rata-rata Nilai</th>
                                            <th style="width:15%; text-align:center;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($grp['students'] as $s): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($s['nim']) ?></strong></td>
                                                <td><?= htmlspecialchars($s['name']) ?></td>
                                                <td style="text-align:center;">
                                                    <?php if ($s['kehadiran'] !== null): ?>
                                                        <div class="kehadiran-bar-wrap" style="justify-content:center;">
                                                            <div class="kehadiran-bar">
                                                                <div class="kehadiran-bar-fill" style="width:<?= min($s['kehadiran'], 100) ?>%;"></div>
                                                            </div>
                                                            <span class="kehadiran-text"><?= $s['kehadiran'] ?>%</span>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="nilai-none">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align:center;">
                                                    <?php if ($s['avg_nilai'] !== null): ?>
                                                        <span class="nilai-num <?= $s['avg_nilai'] >= 70 ? 'nilai-lulus' : 'nilai-gagal' ?>">
                                                            <?= $s['avg_nilai'] ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="nilai-none">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align:center;">
                                                    <?php
                                                        $badgeClass = match($s['status']) {
                                                            'Lulus'         => 'badge-lulus',
                                                            'Tidak Lulus'   => 'badge-gagal',
                                                            default         => 'badge-none',
                                                        };
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($s['status']) ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div><!-- /workspace-content -->
    </main>
</div>

<script>
function toggleGroup(idx) {
    const body = document.getElementById('group-body-' + idx);
    const icon = document.getElementById('toggle-icon-' + idx);
    if (body.style.display === 'none') {
        body.style.display = '';
        icon.classList.remove('collapsed');
    } else {
        body.style.display = 'none';
        icon.classList.add('collapsed');
    }
}
</script>

</body>
</html>
