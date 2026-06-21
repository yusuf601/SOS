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
                <div class=\"sidebar-user-role\"><?php $r = $role ?? $_SESSION['active_role'] ?? ''; echo $r === 'Asisten' ? 'Asisten Dosen' : htmlspecialchars($r); ?></div>
            </div>

            <!-- Menu Navigation -->
            <?php include __DIR__ . '/components/sidebar_menu.php'; ?>
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
                <h2 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0;">Data Kelompok Praktikum</h2>
            </div>

            <?php if (isset($_SESSION['group_success'])): ?>
                <div style="background-color: #DEF7EC; color: #03543F; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid #BCF0DA; margin-bottom: 16px; text-align: left;">
                    <?= htmlspecialchars($_SESSION['group_success']); unset($_SESSION['group_success']); ?>
                </div>
            <?php endif; ?>

            <div>
                <!-- Column Right: Daftar Kelompok -->
                <div style="display: flex; flex-direction: column; gap: 24px; max-width: 1000px; margin: 0 auto;">
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
                                    'id' => md5($g['group_name']),
                                    'name' => $g['group_name'],
                                    'assistant' => $g['assistant_name'] ?? 'Tidak Ada Asisten',
                                    'count' => $g['students_count'],
                                    'students' => $g['students'] ?? []
                                ];
                            }

                            if (count($displayGroups) < 4) {
                                if (empty($displayGroups)) {
                                    $displayGroups[] = ['id' => md5('Kelompok 1'), 'name' => 'Kelompok 1', 'assistant' => 'Chris Redfield', 'count' => 6, 'students' => []];
                                    $displayGroups[] = ['id' => md5('Kelompok 2'), 'name' => 'Kelompok 2', 'assistant' => 'Chris Redfield', 'count' => 6, 'students' => []];
                                    $displayGroups[] = ['id' => md5('Kelompok 3'), 'name' => 'Kelompok 3', 'assistant' => 'Rose Winter', 'count' => 5, 'students' => []];
                                    $displayGroups[] = ['id' => md5('Kelompok 4'), 'name' => 'Kelompok 4', 'assistant' => 'Rose Winter', 'count' => 6, 'students' => []];
                                }
                            }

                            foreach ($displayGroups as $dg):
                            ?>
                                <tr style="cursor: pointer; transition: background-color 0.2s;" onclick="toggleAccordion('<?= $dg['id'] ?>')" onmouseover="this.style.backgroundColor='#F1F5F9'" onmouseout="this.style.backgroundColor='transparent'">
                                    <td style="font-weight: 500;">
                                        <div style="display: flex; align-items: center; gap: 4px;">
                                            <img src="/rpl/public/assets/icons/arrow_drop_down_24dp_1F1F1F_FILL0_wght400_GRAD0_opsz24.svg" id="icon-<?= $dg['id'] ?>" style="transition: transform 0.3s ease; width: 20px; height: 20px;" alt="Toggle">
                                            <?= htmlspecialchars($dg['name']) ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($dg['assistant']) ?></td>
                                    <td style="text-align: center; font-weight: 500;"><?= htmlspecialchars($dg['count']) ?></td>
                                </tr>
                                <tr id="details-<?= $dg['id'] ?>" style="display: none; background-color: #F8FAFC;">
                                    <td colspan="3" style="padding: 16px 36px; border-top: none;">
                                        <div style="font-size: 13px; color: #475569;">
                                            <strong style="color: #333;">Daftar Anggota Kelompok:</strong>
                                            <ul style="margin-top: 8px; padding-left: 16px; margin-bottom: 0;">
                                                <?php if (!empty($dg['students'])): ?>
                                                    <?php foreach ($dg['students'] as $mhs): ?>
                                                        <li style="margin-bottom: 6px;"><?= htmlspecialchars($mhs['nim']) ?> &nbsp;&mdash;&nbsp; <?= htmlspecialchars($mhs['name']) ?></li>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <li style="color: #94A3B8; font-style: italic;">Belum ada anggota.</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>



<script>
function toggleAccordion(id) {
    const detailsRow = document.getElementById('details-' + id);
    const icon = document.getElementById('icon-' + id);
    if (detailsRow.style.display === 'none') {
        detailsRow.style.display = 'table-row';
        icon.style.transform = 'rotate(-180deg)';
    } else {
        detailsRow.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>

</body>
</html>
