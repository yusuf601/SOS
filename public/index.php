<?php
// ==============================================================================
// EduLab UHO - Front Controller & Router
// ==============================================================================

// Start Session for managing user login state
session_start();

// Require controllers
require_once __DIR__ . '/../app/Controllers/AuthController.php';

// Instantiate AuthController
$authController = new AuthController();

// Simple routing based on action query parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Authentication Guard Middleware
$publicActions = ['', 'login', 'help'];
if (!in_array($action, $publicActions) && !isset($_SESSION['user_id'])) {
    // Redirect to login if user is not logged in and action is not public
    header('Location: /rpl/public/index.php');
    exit;
}

switch ($action) {
    case 'login':
        $authController->login();
        break;

    case 'role_switcher':
        $authController->roleSwitcher();
        break;

    case 'set_role':
        $authController->setRole();
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'dashboard_student':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->studentIndex();
        break;

    case 'dashboard_dosen':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->staffIndex();
        break;

    case 'dashboard_asisten':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->staffIndex();
        break;

    case 'submit_grade':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->submitGrade();
        break;

    case 'submit_verification':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->submitVerification();
        break;

    case 'my_classes':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->myClasses();
        break;

    case 'submit_class':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->submitClass();
        break;

    case 'buat_kelompok':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->buatKelompok();
        break;

    case 'api_get_mahasiswa_kelas':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->apiGetMahasiswaKelas();
        break;

    case 'data_kelompok':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->dataKelompok();
        break;

    case 'bank_modul':
        require_once __DIR__ . '/../app/Controllers/ModulController.php';
        $modulController = new ModulController();
        $modulController->index();
        break;

    case 'upload_modul':
        require_once __DIR__ . '/../app/Controllers/ModulController.php';
        $modulController = new ModulController();
        $modulController->uploadModul();
        break;

    case 'download_materi':
        require_once __DIR__ . '/../app/Controllers/ModulController.php';
        $modulController = new ModulController();
        $modulController->download();
        break;

    case 'view_tugas':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->viewFile();
        break;

    case 'upload_tugas':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->index();
        break;

    case 'submit_tugas':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->upload();
        break;

    case 'cancel_tugas':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->cancel();
        break;

    case 'presensi':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->presensi();
        break;

    case 'submit_presensi':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->submitPresensi();
        break;

    case 'verifikasi_tugas':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->verifikasiTugas();
        break;

    case 'sanggah_nilai':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->sanggahNilai();
        break;

    case 'sanggah_form':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->sanggahForm();
        break;

    case 'submit_sanggah':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->submitSanggah();
        break;

    case 'respond_sanggah':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->respondSanggah();
        break;

    case 'kelulusan':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->kelulusan();
        break;

    case 'export_rekap':
        require_once __DIR__ . '/../app/Controllers/TugasController.php';
        $tugasController = new TugasController();
        $tugasController->exportRekap();
        break;

    case 'pengaturan':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->pengaturan();
        break;

    case 'update_profil':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->updateProfil();
        break;

    case 'ubah_password':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->ubahPassword();
        break;

    case 'monitoring_kelas':
        require_once __DIR__ . '/../app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->monitoringKelas();
        break;

    case 'help':
        require_once __DIR__ . '/../app/Views/help.php';
        break;

    default:
        // If already logged in, redirect to active dashboard
        if (isset($_SESSION['user_id']) && isset($_SESSION['active_role'])) {
            if ($_SESSION['active_role'] === 'Mahasiswa') {
                header('Location: /rpl/public/index.php?action=dashboard_student');
            } elseif ($_SESSION['active_role'] === 'Dosen') {
                header('Location: /rpl/public/index.php?action=dashboard_dosen');
            } elseif ($_SESSION['active_role'] === 'Asisten') {
                header('Location: /rpl/public/index.php?action=dashboard_asisten');
            }
            exit;
        }
        // Otherwise, render login view
        require_once __DIR__ . '/../app/Views/login.php';
        break;
}
