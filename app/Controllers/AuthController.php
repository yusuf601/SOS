<?php
// ==============================================================================
// EduLab UHO - Authentication Controller
// ==============================================================================

require_once __DIR__ . '/../Models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // Process Login Request
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $identity = trim($_POST['identity'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($identity) || empty($password)) {
            $_SESSION['login_error'] = "Username/NIM/NIDN dan password harus diisi!";
            header('Location: /rpl/public/index.php');
            exit;
        }

        // Fetch user
        $user = $this->userModel->getUserByUsername($identity);

        if (!$user || !$this->userModel->verifyPassword($password, $user['Password'])) {
            $_SESSION['login_error'] = "NIM/NIDN atau password salah!";
            header('Location: /rpl/public/index.php');
            exit;
        }

        // Login success, initialize session
        $_SESSION['user_id'] = $user['ID_User'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['name'] = $user['Nama_Lengkap'];
        $_SESSION['base_role'] = $user['Role'];

        // Check for multiple available roles
        $availableRoles = $this->userModel->getAvailableRoles($user['ID_User'], $user['Role']);
        $_SESSION['available_roles'] = $availableRoles;

        if (count($availableRoles) > 1) {
            // User has multiple roles, redirect to role switcher
            header('Location: /rpl/public/index.php?action=role_switcher');
        } else {
            // Only one role, set active role and redirect to dashboard
            $_SESSION['active_role'] = $user['Role'];
            $this->redirectToDashboard($user['Role']);
        }
        exit;
    }

    // Render Role Switcher Page
    public function roleSwitcher() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['available_roles'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        require_once __DIR__ . '/../Views/role_switcher.php';
    }

    // Set Active Role from Switcher
    public function setRole() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['available_roles'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $selectedRole = $_GET['role'] ?? '';

        if (in_array($selectedRole, $_SESSION['available_roles'])) {
            $_SESSION['active_role'] = $selectedRole;
            $this->redirectToDashboard($selectedRole);
        } else {
            $_SESSION['login_error'] = "Peran tidak valid!";
            header('Location: /rpl/public/index.php');
        }
        exit;
    }

    // Process Logout
    public function logout() {
        $_SESSION = [];
        session_destroy();
        header('Location: /rpl/public/index.php');
        exit;
    }

    // Helper to redirect based on active role
    private function redirectToDashboard($role) {
        if ($role === 'Mahasiswa') {
            header('Location: /rpl/public/index.php?action=dashboard_student');
        } elseif ($role === 'Dosen') {
            header('Location: /rpl/public/index.php?action=dashboard_dosen');
        } elseif ($role === 'Asisten') {
            header('Location: /rpl/public/index.php?action=dashboard_asisten');
        } else {
            header('Location: /rpl/public/index.php');
        }
        exit;
    }
}
