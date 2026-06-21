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
                <h2 style="font-size: 20px; font-weight: 600; color: #000000; margin: 0;">Buat Kelompok Praktikum</h2>
            </div>

            <?php if (isset($_SESSION['group_success'])): ?>
                <div style="background-color: #DEF7EC; color: #03543F; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid #BCF0DA; margin-bottom: 16px; text-align: left;">
                    <?= htmlspecialchars($_SESSION['group_success']); unset($_SESSION['group_success']); ?>
                </div>
            <?php endif; ?>

            <div class="buat-kelompok-container">
                <!-- Column Left: Form Tambah Kelompok & Asisten -->
                <div class="card-form-kelompok">
                    <h4 class="form-title">Tambah Kelompok & Asisten</h4>
                    <form method="POST" action="/rpl/public/index.php?action=buat_kelompok">
                        <!-- Pilih Kelas -->
                        <div class="form-group">
                            <label>Pilih Kelas</label>
                            <div class="select-custom-wrapper">
                                <select name="class_id" id="select-class-id" class="form-control-custom select-custom">
                                    <?php foreach ($myClasses as $cls): ?>
                                        <option value="<?= $cls['ID_Kelas'] ?>" <?= ($selectedClassId == $cls['ID_Kelas']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cls['Nama_Kelas']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Nama Kelompok -->
                        <div class="form-group">
                            <label>Nama Kelompok</label>
                            <input type="text" name="nama_kelompok" class="form-control-custom" placeholder="cth: Kelompok 1" required>
                        </div>

                        <!-- Anggota Kelompok -->
                        <div class="form-group">
                            <label>Anggota Kelompok</label>
                            <div id="anggota-container" style="max-height: 200px; overflow-y: auto; border: 1px solid #C8C8C8; border-radius: 8px; padding: 12px; background-color: #FFFFFF;">
                                <span style="font-size: 13px; color: #6C6C6C;">Pilih kelas terlebih dahulu...</span>
                            </div>
                        </div>

                        <!-- Button Simpan -->
                        <div style="margin-top: 24px;">
                            <button type="submit" class="btn-simpan-kelompok">Simpan</button>
                        </div>
                    </form>
                </div>

                <!-- Column Right: Daftar Kelompok -->
                <div style="display: flex; flex-direction: column; gap: 24px;">
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
                                    'name' => $g['group_name'],
                                    'assistant' => $g['assistant_name'] ?? 'Tidak Ada Asisten',
                                    'count' => $g['students_count']
                                ];
                            }

                            if (count($displayGroups) < 4) {
                                if (empty($displayGroups)) {
                                    $displayGroups[] = ['name' => 'Kelompok 1', 'assistant' => 'Chris Redfield', 'count' => 6];
                                    $displayGroups[] = ['name' => 'Kelompok 2', 'assistant' => 'Chris Redfield', 'count' => 6];
                                    $displayGroups[] = ['name' => 'Kelompok 3', 'assistant' => 'Rose Winter', 'count' => 5];
                                    $displayGroups[] = ['name' => 'Kelompok 4', 'assistant' => 'Rose Winter', 'count' => 6];
                                }
                            }

                            foreach ($displayGroups as $dg):
                            ?>
                                <tr>
                                    <td style="font-weight: 500;"><?= htmlspecialchars($dg['name']) ?></td>
                                    <td><?= htmlspecialchars($dg['assistant']) ?></td>
                                    <td style="text-align: center; font-weight: 500;"><?= htmlspecialchars($dg['count']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Detailed Kelompok Card -->
                <div class="card-daftar-kelompok" style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-size: 18px; font-weight: 600; color: #3B3B3B; margin: 0;">Kelompok 1</h3>
                        <span style="font-size: 12px; font-weight: 600; color: rgba(0,0,0,0.5);">6 anggota</span>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <div style="width: 35px; height: 35px; border-radius: 50%; background-color: #7C94B8; box-sizing: border-box; z-index: 1;"></div>
                        <div style="width: 35px; height: 35px; border-radius: 50%; background-color: #7C94B8; border: 3.5px solid #FFFFFF; box-sizing: border-box; margin-left: -12px; z-index: 2;"></div>
                        <div style="width: 35px; height: 35px; border-radius: 50%; background-color: #7C94B8; border: 3.5px solid #FFFFFF; box-sizing: border-box; margin-left: -12px; z-index: 3;"></div>
                        <div style="width: 35px; height: 35px; border-radius: 50%; background-color: #7C94B8; border: 3.5px solid #FFFFFF; box-sizing: border-box; margin-left: -12px; z-index: 4;"></div>
                        <div style="width: 35px; height: 35px; border-radius: 50%; background-color: #7C94B8; border: 3.5px solid #FFFFFF; box-sizing: border-box; margin-left: -12px; z-index: 5;"></div>
                        <div style="width: 35px; height: 35px; border-radius: 50%; background-color: #C8C8C8; border: 3.5px solid #FFFFFF; box-sizing: border-box; margin-left: -12px; z-index: 6; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 500; color: #6C6C6C;">+1</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('select-class-id');
    const container = document.getElementById('anggota-container');

    function fetchMahasiswa() {
        const classId = classSelect.value;
        if (!classId) return;

        container.innerHTML = '<span style="font-size: 13px; color: #6C6C6C;">Memuat data...</span>';

        fetch('/rpl/public/index.php?action=api_get_mahasiswa_kelas&class_id=' + classId)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = '';
                if (data.error) {
                    container.innerHTML = '<span style="font-size: 13px; color: #E02424;">Gagal mengambil data.</span>';
                    return;
                }
                
                if (data.length === 0) {
                    container.innerHTML = '<span style="font-size: 13px; color: #6C6C6C;">Tidak ada mahasiswa yang tersedia.</span>';
                    return;
                }

                data.forEach(mhs => {
                    const div = document.createElement('div');
                    div.style.marginBottom = '8px';
                    
                    const label = document.createElement('label');
                    label.style.display = 'flex';
                    label.style.alignItems = 'center';
                    label.style.gap = '8px';
                    label.style.fontSize = '13px';
                    label.style.color = '#313131';
                    label.style.cursor = 'pointer';
                    label.style.fontWeight = '500';

                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'anggota_nim[]';
                    checkbox.value = mhs.ID_User;
                    checkbox.style.cursor = 'pointer';

                    label.appendChild(checkbox);
                    label.appendChild(document.createTextNode(`${mhs.NIM} - ${mhs.Nama_Lengkap}`));
                    
                    div.appendChild(label);
                    container.appendChild(div);
                });
            })
            .catch(err => {
                container.innerHTML = '<span style="font-size: 13px; color: #E02424;">Terjadi kesalahan saat memuat data.</span>';
            });
    }

    classSelect.addEventListener('change', fetchMahasiswa);
    // Fetch on initial load
    fetchMahasiswa();
});
</script>
</body>
</html>
