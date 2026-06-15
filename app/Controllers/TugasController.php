<?php
// ==============================================================================
// EduLab UHO - Tugas Controller
// ==============================================================================

require_once __DIR__ . '/../Models/TugasModel.php';
require_once __DIR__ . '/../Models/KelasModel.php';

class TugasController {
    private $tugasModel;
    private $kelasModel;

    public function __construct() {
        $this->tugasModel = new TugasModel();
        $this->kelasModel = new KelasModel();
    }

    // Render Upload Tugas View
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
            $classInfo = $this->kelasModel->getStudentClass($userId);
        } else {
            $allClasses = $this->kelasModel->getAllClasses();
        }

        // Fetch all tasks
        $allTugas = $this->tugasModel->getAllTugas();

        // Get submission details for each task
        $tasksData = [];
        foreach ($allTugas as $tugas) {
            $submission = $this->tugasModel->getSubmission($tugas['ID_Tugas'], $userId);
            $tasksData[] = [
                'tugas' => $tugas,
                'submission' => $submission
            ];
        }

        // Fetch student progress metrics
        $progress = $this->tugasModel->getStudentProgress($userId);

        require_once __DIR__ . '/../Views/upload_tugas.php';
    }

    // Handle File Upload POST
    public function upload() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /rpl/public/index.php?action=upload_tugas');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $tugasId = isset($_POST['id_tugas']) ? (int)$_POST['id_tugas'] : 0;

        if ($tugasId <= 0) {
            $_SESSION['upload_error'] = "ID Tugas tidak valid.";
            header('Location: /rpl/public/index.php?action=upload_tugas');
            exit;
        }

        if (!isset($_FILES['file_tugas']) || $_FILES['file_tugas']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['upload_error'] = "Gagal mengunggah file. Silakan pilih file kembali.";
            header('Location: /rpl/public/index.php?action=upload_tugas');
            exit;
        }

        $file = $_FILES['file_tugas'];
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileSize = $file['size'];

        // File validation: Size (max 10MB)
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($fileSize > $maxSize) {
            $_SESSION['upload_error'] = "Ukuran file terlalu besar. Maksimal ukuran file adalah 10MB.";
            header('Location: /rpl/public/index.php?action=upload_tugas');
            exit;
        }

        // File validation: Extension (.zip, .rar, .pdf)
        $allowedExtensions = ['zip', 'rar', 'pdf'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            $_SESSION['upload_error'] = "Format file tidak didukung. Harap unggah file dengan format .zip, .rar, atau .pdf.";
            header('Location: /rpl/public/index.php?action=upload_tugas');
            exit;
        }

        // Create upload directory if not exists
        $uploadDir = __DIR__ . '/../../public/assets/uploads/tugas/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Unique file name to prevent collision
        $newFileName = 'tugas_' . $tugasId . '_' . $userId . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmp, $destPath)) {
            // Save to database
            $success = $this->tugasModel->submitTugas($tugasId, $userId, $newFileName);
            if ($success) {
                $_SESSION['upload_success'] = "Tugas berhasil diunggah.";
            } else {
                $_SESSION['upload_error'] = "Gagal menyimpan status pengumpulan ke database.";
            }
        } else {
            $_SESSION['upload_error'] = "Gagal memindahkan file ke direktori tujuan di server.";
        }

        header('Location: /rpl/public/index.php?action=upload_tugas');
        exit;
    }

    // Handle Submission Cancellation (Delete/Cancel submit)
    public function cancel() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $tugasId = isset($_GET['id_tugas']) ? (int)$_GET['id_tugas'] : 0;

        if ($tugasId <= 0) {
            $_SESSION['upload_error'] = "ID Tugas tidak valid untuk pembatalan.";
            header('Location: /rpl/public/index.php?action=upload_tugas');
            exit;
        }

        $submission = $this->tugasModel->getSubmission($tugasId, $userId);
        if ($submission) {
            // If already graded (has status Selesai), it shouldn't be cancelled
            if ($submission['Status_Tugas'] === 'Selesai') {
                $_SESSION['upload_error'] = "Tugas yang sudah dinilai tidak dapat dibatalkan.";
                header('Location: /rpl/public/index.php?action=upload_tugas');
                exit;
            }

            // Delete physical file
            $filePath = __DIR__ . '/../../public/assets/uploads/tugas/' . $submission['File_Tugas'];
            if (!empty($submission['File_Tugas']) && file_exists($filePath)) {
                unlink($filePath);
            }

            // Remove/update submission record in database
            $db = (new Database())->getConnection();
            
            // Delete record from Tabel_Nilai first if any (cascaded or manually)
            $queryNilai = "DELETE FROM Tabel_Nilai WHERE ID_Pengumpulan = :id";
            $stmtNilai = $db->prepare($queryNilai);
            $stmtNilai->bindParam(':id', $submission['ID_Pengumpulan']);
            $stmtNilai->execute();

            $query = "DELETE FROM Tabel_Pengumpulan WHERE ID_Pengumpulan = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $submission['ID_Pengumpulan']);
            
            if ($stmt->execute()) {
                $_SESSION['upload_success'] = "Pengumpulan tugas berhasil dibatalkan.";
            } else {
                $_SESSION['upload_error'] = "Gagal membatalkan pengumpulan dari database.";
            }
        } else {
            $_SESSION['upload_error'] = "Data pengumpulan tidak ditemukan.";
        }

        header('Location: /rpl/public/index.php?action=upload_tugas');
        exit;
    }
}
