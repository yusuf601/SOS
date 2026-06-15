<?php
// ==============================================================================
// EduLab UHO - Dashboard Controller
// ==============================================================================

require_once __DIR__ . '/../Models/TugasModel.php';
require_once __DIR__ . '/../Models/KelasModel.php';
require_once __DIR__ . '/../Models/UserModel.php';

class DashboardController {
    private $tugasModel;
    private $kelasModel;
    private $userModel;

    public function __construct() {
        $this->tugasModel = new TugasModel();
        $this->kelasModel = new KelasModel();
        $this->userModel = new UserModel();
    }

    // Render Student Dashboard
    public function studentIndex() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['active_role'] ?? '') !== 'Mahasiswa') {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Get class info
        $classInfo = $this->kelasModel->getStudentClass($userId);
        
        // Get progress metrics
        $progress = $this->tugasModel->getStudentProgress($userId);

        // Get attendance rate
        $attendanceRate = $this->userModel->getAttendanceRate($userId);

        // Add metrics to array for easy view binding
        $stats = [
            'active_classes' => $classInfo ? 1 : 0,
            'pending_tasks'  => $progress['pending'] + $progress['missing'], // Total tasks incomplete or awaiting review
            'average_score'  => $progress['average_score'],
            'attendance'     => $attendanceRate
        ];

        // Pass variables to view
        require_once __DIR__ . '/../Views/dashboard_student.php';
    }
}
