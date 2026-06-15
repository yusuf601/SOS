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
$publicActions = ['', 'login'];
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
        require_once __DIR__ . '/../app/Views/dashboard_student.php';
        break;

    case 'dashboard_dosen':
        $role = $_SESSION['active_role'] ?? 'Dosen';
        echo "<h3>Dashboard Dosen (EduLab UHO)</h3>";
        echo "Selamat datang, " . htmlspecialchars($_SESSION['name'] ?? '') . ".<br>";
        echo "Halaman ini sedang dikembangkan.<br><br>";
        echo "<a href='/rpl/public/index.php?action=logout'>Log out</a>";
        break;

    case 'dashboard_asisten':
        $role = $_SESSION['active_role'] ?? 'Asisten';
        echo "<h3>Dashboard Asisten (EduLab UHO)</h3>";
        echo "Selamat datang, " . htmlspecialchars($_SESSION['name'] ?? '') . ".<br>";
        echo "Halaman ini sedang dikembangkan.<br><br>";
        echo "<a href='/rpl/public/index.php?action=logout'>Log out</a>";
        break;

    case 'my_classes':
        require_once __DIR__ . '/../app/Views/my_classes.php';
        break;

    case 'bank_modul':
        require_once __DIR__ . '/../app/Views/bank_modul.php';
        break;

    case 'upload_tugas':
        require_once __DIR__ . '/../app/Views/upload_tugas.php';
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
