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
            // Get student's class (default first class)
            $studentClasses = $this->kelasModel->getStudentClasses($userId);
            
            if (isset($_GET['class_id'])) {
                $requestedId = (int)$_GET['class_id'];
                foreach ($studentClasses as $sc) {
                    if ($sc['ID_Kelas'] === $requestedId) {
                        $classInfo = $sc;
                        break;
                    }
                }
                // Fallback if not found
                if (!$classInfo && !empty($studentClasses)) $classInfo = $studentClasses[0];
            } else {
                if (!empty($studentClasses)) $classInfo = $studentClasses[0];
            }
        } else {
            // Dosen/Asisten see only their plotted classes
            $allClasses = $this->kelasModel->getClassesByUserId($userId);
            if (isset($_GET['class_id'])) {
                $requestedId = (int)$_GET['class_id'];
                foreach ($allClasses as $ac) {
                    if ($ac['ID_Kelas'] === $requestedId) {
                        $classInfo = $ac;
                        break;
                    }
                }
                if (!$classInfo && !empty($allClasses)) $classInfo = $allClasses[0];
            } else {
                if (!empty($allClasses)) $classInfo = $allClasses[0];
            }
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
            $schedule = $classInfo['Jadwal'] ?? "Belum Diatur";
        }

        // Fetch all modules enriched with task deadline and student grade
        $modules = [];
        if ($classInfo) {
            $classId = $classInfo['ID_Kelas'];
            $db = (new Database())->getConnection();
            $query = "SELECT m.*, t.ID_Tugas, t.Deadline_Upload, p.ID_Pengumpulan, n.Nilai_Angka, n.Status_Tugas
                      FROM Tabel_Modul m
                      LEFT JOIN Tabel_Tugas t ON m.ID_Modul = t.ID_Modul
                      LEFT JOIN Tabel_Pengumpulan p ON t.ID_Tugas = p.ID_Tugas AND p.ID_User = :userId
                      LEFT JOIN Tabel_Nilai n ON p.ID_Pengumpulan = n.ID_Pengumpulan
                      WHERE m.ID_Kelas = :classId
                      ORDER BY m.ID_Modul ASC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':classId', $classId);
            $stmt->execute();
            $modules = $stmt->fetchAll();
        }

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

    // Render Upload Modul View & Handle Upload Request (Dosen)
    public function uploadModul() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['active_role'] ?? '', ['Dosen', 'Asisten'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['active_role'];
        $db = (new Database())->getConnection();

        // Handle POST form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $judulModul = trim($_POST['judul_modul'] ?? '');
            $pertemuan = trim($_POST['pertemuan'] ?? '');
            $classId = (int)($_POST['class_id'] ?? 0);
            $deadline = trim($_POST['deadline'] ?? '');

            // Access Control: Verify the Dosen/Asisten is plotted to this class
            $isPlotted = false;
            $allClasses = $this->kelasModel->getClassesByUserId($userId);
            foreach ($allClasses as $cls) {
                if ($cls['ID_Kelas'] === $classId) {
                    $isPlotted = true;
                    break;
                }
            }
            if (!$isPlotted) {
                $_SESSION['upload_error'] = "Akses ditolak: Anda tidak ditugaskan pada kelas ini.";
                header('Location: /rpl/public/index.php?action=bank_modul');
                exit;
            }

            if ($judulModul && $deadline && isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['file_materi']['tmp_name'];
                $fileName = $_FILES['file_materi']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if ($fileExtension === 'pdf') {
                    // Safe filename
                    $safeFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $fileName);
                    $uploadFileDir = __DIR__ . '/../../public/assets/uploads/materi/';

                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0777, true);
                    }

                    $dest_path = $uploadFileDir . $safeFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        try {
                            $db->beginTransaction();

                            // 1. Insert to Tabel_Modul
                            $stmt = $db->prepare("INSERT INTO Tabel_Modul (ID_Kelas, Judul_Modul, File_Materi) VALUES (:classId, :judul, :file)");
                            $stmt->execute([
                                ':classId' => $classId,
                                ':judul' => $judulModul,
                                ':file' => $safeFileName
                            ]);
                            $modulId = $db->lastInsertId();

                            // 2. Insert to Tabel_Tugas
                            $instruksi = "Kumpulkan tugas untuk " . $judulModul;
                            $stmtTugas = $db->prepare("INSERT INTO Tabel_Tugas (ID_Modul, Instruksi_Tugas, Deadline_Upload) VALUES (:modulId, :instruksi, :deadline)");
                            $stmtTugas->execute([
                                ':modulId' => $modulId,
                                ':instruksi' => $instruksi,
                                ':deadline' => $deadline
                            ]);

                            $db->commit();
                            $_SESSION['upload_success'] = "Modul dan tugas berhasil diunggah!";
                        } catch (Exception $e) {
                            $db->rollBack();
                            $_SESSION['upload_error'] = "Gagal menyimpan ke database: " . $e->getMessage();
                        }
                    } else {
                        $_SESSION['upload_error'] = "Gagal memindahkan file ke folder tujuan.";
                    }
                } else {
                    $_SESSION['upload_error'] = "Hanya file PDF yang diperbolehkan.";
                }
            } else {
                $_SESSION['upload_error'] = "Semua field wajib diisi dan file harus diunggah.";
            }

            header('Location: /rpl/public/index.php?action=upload_modul');
            exit;
        }

        // GET request - fetch data for view
        $myClasses = $this->kelasModel->getClassesByUserId($userId);
        if (empty($myClasses)) {
            $myClasses = $this->kelasModel->getAllClasses();
        }

        // Fetch all modules with deadline
        $query = "SELECT m.*, t.Deadline_Upload 
                  FROM Tabel_Modul m
                  LEFT JOIN Tabel_Tugas t ON m.ID_Modul = t.ID_Modul
                  ORDER BY m.ID_Modul ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $savedModuls = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/upload_modul.php';
    }
}
