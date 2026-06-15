<?php
// ==============================================================================
// EduLab UHO - Modul Controller
// ==============================================================================

require_once __DIR__ . '/../Models/ModulModel.php';
require_once __DIR__ . '/../Models/KelasModel.php';

class ModulController {
    private $modulModel;
    private $kelasModel;

    public function __construct() {
        $this->modulModel = new ModulModel();
        $this->kelasModel = new KelasModel();
    }

    // Render Bank Modul View
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['active_role'] ?? 'Mahasiswa';

        $classInfo = null;
        $allClasses = [];

        if ($role === 'Mahasiswa') {
            // Get student's class
            $classInfo = $this->kelasModel->getStudentClass($userId);
        } else {
            // Dosen/Asisten see all classes
            $allClasses = $this->kelasModel->getAllClasses();
        }

        // Fetch plotted lecturer and assistant details
        $lecturerName = "Tidak Ada";
        $assistantName = "Tidak Ada";
        $schedule = "Belum Diatur";

        if ($classInfo) {
            $classId = $classInfo['ID_Kelas'];
            // Fetch users plotted for this class
            $db = (new Database())->getConnection();
            $query = "SELECT u.Nama_Lengkap, u.Role FROM Tabel_User u
                      JOIN Tabel_Plotting_Asisten p ON u.ID_User = p.ID_User
                      WHERE p.ID_Kelas = :classId";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':classId', $classId);
            $stmt->execute();
            $plottedUsers = $stmt->fetchAll();

            foreach ($plottedUsers as $u) {
                if ($u['Role'] === 'Dosen') {
                    $lecturerName = $u['Nama_Lengkap'];
                } elseif ($u['Role'] === 'Asisten') {
                    $assistantName = $u['Nama_Lengkap'];
                }
            }

            // Dummy schedule mapped for layout
            if ($classId == 1) {
                $schedule = "Senin 08:00 - 10:00 (Lab Komputer 2)";
            } elseif ($classId == 2) {
                $schedule = "Rabu 10:00 - 12:00 (Lab Komputer 1)";
            } else {
                $schedule = "Jumat 14:00 - 16:00 (Lab Jaringan)";
            }
        }

        // Fetch all modules
        $modules = $this->modulModel->getAllModuls();

        require_once __DIR__ . '/../Views/bank_modul.php';
    }

    // Handle Material File Download Safely
    public function download() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $filename = basename($_GET['file'] ?? '');
        $filepath = __DIR__ . '/../../public/assets/uploads/materi/' . $filename;

        if (!empty($filename) && file_exists($filepath)) {
            // Clean output buffer
            if (ob_get_level()) ob_end_clean();

            // Set headers for download
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            
            readfile($filepath);
            exit;
        } else {
            $_SESSION['download_error'] = "File materi " . htmlspecialchars($filename) . " belum diunggah atau tidak ditemukan di server!";
            header('Location: /rpl/public/index.php?action=bank_modul');
            exit;
        }
    }
}
