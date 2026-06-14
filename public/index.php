<?php
// ==============================================================================
// EduLab UHO - Front Controller & Router
// ==============================================================================

// Start Session for managing user login state
session_start();

// Simple routing based on action query parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'login':
        // Temporary handler for POST login requests (before backend controllers are fully implemented)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identity = $_POST['identity'] ?? '';
            $password = $_POST['password'] ?? '';

            // Redirect to role switcher page to simulate a multi-role user logging in
            header('Location: /rpl/public/index.php?action=role_switcher');
            exit;
        }
        break;

    case 'role_switcher':
        // Render role selection view
        require_once __DIR__ . '/../app/Views/role_switcher.php';
        break;

    case 'set_role':
        // Set selected role in session
        $role = $_GET['role'] ?? 'Mahasiswa';
        $_SESSION['active_role'] = $role;

        // Redirect to student dashboard if role is Mahasiswa
        if ($role === 'Mahasiswa') {
            header('Location: /rpl/public/index.php?action=dashboard_student');
        } else {
            // Placeholder for other roles
            echo "Role successfully set to: " . htmlspecialchars($role) . ". (Dashboard for " . htmlspecialchars($role) . " to be implemented). <a href='/rpl/public/index.php?action=logout'>Log out</a>";
        }
        exit;

    case 'dashboard_student':
        // Render student dashboard view
        require_once __DIR__ . '/../app/Views/dashboard_student.php';
        break;

    case 'my_classes':
        // Render student's classes list view
        require_once __DIR__ . '/../app/Views/my_classes.php';
        break;

    case 'bank_modul':
        // Render bank modul view
        require_once __DIR__ . '/../app/Views/bank_modul.php';
        break;

    case 'logout':
        // Clear all session data
        $_SESSION = [];
        session_destroy();
        header('Location: /rpl/public/index.php');
        exit;

    default:
        // Render login view as the default entry page
        require_once __DIR__ . '/../app/Views/login.php';
        break;
}
