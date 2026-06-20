<?php
// ==============================================================================
// EduLab UHO - Upload Modul View
// ==============================================================================

$fullName = $_SESSION['name'] ?? $_SESSION['full_name'] ?? 'Guest';
$words = explode(" ", $fullName);
$initials = "";
foreach ($words as $w) {
    $initials .= strtoupper($w[0] ?? '');
}
$initials = substr($initials, 0, 2);
$role = $_SESSION['active_role'] ?? 'Dosen';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Modul - EduLab UHO</title>
    <link rel="stylesheet" href="/rpl/public/assets/css/style.css">
    <style>
        .upload-modul-container {
            max-width: 1080px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-top: 16px;
        }

        .card-upload-baru {
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 24px 32px;
            box-shadow: -6px 6px 4px rgba(0, 0, 0, 0.25);
            border: 1px solid #F1F5F9;
        }

        .card-modul-tersimpan {
            background-color: #FFFFFF;
            border-radius: 20px;
            padding: 24px 32px;
            box-shadow: -6px 6px 4px rgba(0, 0, 0, 0.25);
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

        .form-row-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .upload-dropzone {
            border: 1.5px dashed #E8E8E8;
            background-color: #F8F8F8;
            border-radius: 20px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: border-color 0.2s, background-color 0.2s;
            margin-bottom: 24px;
        }

        .upload-dropzone:hover {
            border-color: #364087;
            background-color: rgba(54, 64, 135, 0.02);
        }

        .dropzone-text {
            font-size: 11px;
            font-weight: 500;
            color: rgba(82, 82, 82, 0.5);
            margin: 8px 0 4px;
        }

        .dropzone-subtext {
            font-size: 8px;
            color: rgba(109, 109, 109, 0.5);
            margin: 0;
        }

        .btn-upload-modul {
            width: 100%;
            max-width: 536px;
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
            margin: 0 auto;
        }

        .btn-upload-modul:hover {
            background-color: #2b336b;
        }

        .table-title {
            font-size: 20px;
            font-weight: 600;
            color: #000000;
            margin-top: 0;
            margin-bottom: 24px;
            text-align: left;
        }

        .modul-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modul-table th {
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: rgba(0, 0, 0, 0.5);
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .modul-table td {
            padding: 16px 8px;
            font-size: 13px;
            color: #000000;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            text-align: left;
            vertical-align: middle;
        }

        .modul-table tr:last-child td {
            border-bottom: none;
        }

        .badge-status {
            background-color: #DEF7EC;
            color: #03543F;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .pdf-icon-inline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #000000;
            text-decoration: none;
        }

        .pdf-icon-inline:hover {
            color: #364087;
        }

        @media (max-width: 768px) {
            .form-row-grid {
                grid-template-columns: 1fr;
                gap: 0;
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
            <h2 class="navbar-title">Upload Modul</h2>
            <div class="navbar-profile">
                <div class="navbar-avatar"><?= htmlspecialchars($initials) ?></div>
            </div>
        </header>

        <!-- Content Body -->
        <div class="workspace-content" style="padding: 24px;">
            <div class="upload-modul-container">
                <!-- Notifications -->
                <?php if (isset($_SESSION['upload_success'])): ?>
                    <div style="background-color: #DEF7EC; color: #03543F; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid #BCF0DA; text-align: left; margin-bottom: 16px;">
                        <?= htmlspecialchars($_SESSION['upload_success']); unset($_SESSION['upload_success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['upload_error'])): ?>
                    <div style="background-color: #FDE8E8; color: #9B1C1C; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid #FBD5D5; text-align: left; margin-bottom: 16px;">
                        <?= htmlspecialchars($_SESSION['upload_error']); unset($_SESSION['upload_error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Card 1: Upload Modul Baru -->
                <div class="card-upload-baru">
                    <h4 class="form-title">Upload Modul Baru</h4>
                    <form method="POST" action="/rpl/public/index.php?action=upload_modul" enctype="multipart/form-data">
                        <!-- Judul Modul -->
                        <div class="form-group">
                            <label>Judul Modul</label>
                            <input type="text" name="judul_modul" class="form-control-custom" placeholder="cth: Modul 1: If Else" required>
                        </div>

                        <!-- Class & Pertemuan (Grid) -->
                        <div class="form-row-grid">
                            <!-- Pilih Kelas -->
                            <div class="form-group">
                                <label>Pilih Kelas</label>
                                <div class="select-custom-wrapper">
                                    <select name="class_id" class="form-control-custom select-custom">
                                        <?php foreach ($myClasses as $cls): ?>
                                            <option value="<?= $cls['ID_Kelas'] ?>">
                                                <?= htmlspecialchars($cls['Nama_Kelas']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Pertemuan ke- -->
                            <div class="form-group">
                                <label>Pertemuan ke-</label>
                                <div class="select-custom-wrapper">
                                    <select name="pertemuan" class="form-control-custom select-custom">
                                        <?php for ($i = 1; $i <= 16; $i++): ?>
                                            <option value="<?= $i ?>" <?= ($i == 8) ? 'selected' : '' ?>><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Deadline Pengumpulan Tugas -->
                        <div class="form-group">
                            <label>Deadline Pengumpulan Tugas</label>
                            <input type="datetime-local" name="deadline" class="form-control-custom" required>
                        </div>

                        <!-- Dashed Upload Area -->
                        <div class="form-group">
                            <label>File Modul (PDF)</label>
                            <div class="upload-dropzone" onclick="document.getElementById('file-input').click()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#B1BBC8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 8px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                <p class="dropzone-text">Klik atau seret file PDF laporan ke sini</p>
                                <p class="dropzone-subtext">Format PDF · Maks. 20MB</p>
                                <input type="file" id="file-input" name="file_materi" accept="application/pdf" style="display: none;" onchange="updateFileName(this)" required>
                                <p id="file-name-display" style="margin-top: 8px; font-size: 12px; color: #364087; font-weight: 600;"></p>
                            </div>
                        </div>

                        <!-- Button Upload -->
                        <div style="margin-top: 16px;">
                            <button type="submit" class="btn-upload-modul">Upload</button>
                        </div>
                    </form>
                </div>

                <!-- Card 2: Modul Tersimpan -->
                <div class="card-modul-tersimpan">
                    <h3 class="table-title">Modul Tersimpan</h3>
                    <table class="modul-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Modul</th>
                                <th>Judul</th>
                                <th style="width: 150px;">Deadline</th>
                                <th style="width: 120px; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($savedModuls)): ?>
                                <!-- Fallback mock data matching Figma if no DB modules exist yet -->
                                <?php
                                $mockModuls = [
                                    ['ID_Modul' => 1, 'Judul_Modul' => 'Modul 1-Input dan Output', 'Deadline_Upload' => '2026-03-01 23:59:59'],
                                    ['ID_Modul' => 2, 'Judul_Modul' => 'Modul 2-Percabangan', 'Deadline_Upload' => '2026-03-07 23:59:59'],
                                    ['ID_Modul' => 3, 'Judul_Modul' => 'Modul 3-Perulangan', 'Deadline_Upload' => '2026-03-14 23:59:59'],
                                    ['ID_Modul' => 4, 'Judul_Modul' => 'Modul 4-Fungsi dan Prosedur', 'Deadline_Upload' => '2026-03-21 23:59:59']
                                ];
                                foreach ($mockModuls as $dg):
                                ?>
                                    <tr>
                                        <td><?= $dg['ID_Modul'] ?></td>
                                        <td>
                                            <span class="pdf-icon-inline">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(0,0,0,0.5)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                                <?= htmlspecialchars($dg['Judul_Modul']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('j M', strtotime($dg['Deadline_Upload'])) ?></td>
                                        <td style="text-align: center;"><span class="badge-status">Selesai</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php foreach ($savedModuls as $idx => $dg): ?>
                                    <tr>
                                        <td><?= $idx + 1 ?></td>
                                        <td>
                                            <a href="/rpl/public/index.php?action=download_materi&file=<?= urlencode($dg['File_Materi']) ?>" class="pdf-icon-inline" title="Unduh Modul">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#364087" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                                <?= htmlspecialchars($dg['Judul_Modul']) ?>
                                            </a>
                                        </td>
                                        <td><?= $dg['Deadline_Upload'] ? date('j M', strtotime($dg['Deadline_Upload'])) : '-' ?></td>
                                        <td style="text-align: center;"><span class="badge-status">Selesai</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>



<script>
function updateFileName(input) {
    const display = document.getElementById('file-name-display');
    if (input.files && input.files[0]) {
        display.textContent = 'Selected: ' + input.files[0].name;
    } else {
        display.textContent = '';
    }
}
</script>

</body>
</html>
