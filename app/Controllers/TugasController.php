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

        // Fetch task details to check deadline
        $tugas = $this->tugasModel->getTugasById($tugasId);
        if (!$tugas) {
            $_SESSION['upload_error'] = "Tugas tidak ditemukan.";
            header('Location: /rpl/public/index.php?action=upload_tugas');
            exit;
        }

        // Check if deadline has passed
        if (time() > strtotime($tugas['Deadline_Upload'])) {
            $_SESSION['upload_error'] = "Batas waktu pengumpulan tugas telah berakhir. Upload ditolak.";
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

    // Render Attendance View
    public function presensi() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['active_role'] ?? 'Mahasiswa';
        $db = (new Database())->getConnection();

        $classInfo = null;
        $allClasses = [];
        $studentsList = [];
        $moduls = [];
        $attendance = [];

        if ($role === 'Mahasiswa') {
            $classInfo = $this->kelasModel->getStudentClass($userId);
            
            // Get all modules and left join student's attendance records
            $query = "SELECT m.ID_Modul, m.Judul_Modul, p.ID_Presensi, p.Status_Kehadiran, p.Tanggal 
                      FROM Tabel_Modul m
                      LEFT JOIN Tabel_Presensi p ON m.ID_Modul = p.ID_Modul AND p.ID_User = :userId
                      ORDER BY m.ID_Modul ASC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            $attendance = $stmt->fetchAll();
        } else {
            // Dosen/Asisten can manage attendance
            $allClasses = $this->kelasModel->getClassesByUserId($userId);
            
            // Get modules
            $queryModuls = "SELECT * FROM Tabel_Modul ORDER BY ID_Modul ASC";
            $stmtModuls = $db->prepare($queryModuls);
            $stmtModuls->execute();
            $moduls = $stmtModuls->fetchAll();

            // Fetch students list for plotting/selected class
            $selectedClass = isset($_GET['class_id']) ? (int)$_GET['class_id'] : ($allClasses[0]['ID_Kelas'] ?? 0);
            $selectedModul = isset($_GET['modul_id']) ? (int)$_GET['modul_id'] : ($moduls[0]['ID_Modul'] ?? 0);

            $groupsData = [];
            if ($selectedClass > 0) {
                // Fetch groups in this class
                $queryGroups = "SELECT * FROM Tabel_Kelompok WHERE ID_Kelas = :classId ORDER BY Nama_Kelompok ASC";
                $stmtGroups = $db->prepare($queryGroups);
                $stmtGroups->bindParam(':classId', $selectedClass);
                $stmtGroups->execute();
                $groups = $stmtGroups->fetchAll();

                foreach ($groups as $g) {
                    $groupId = $g['ID_Kelompok'];

                    // Fetch students in this group with their attendance for this module
                    $queryStudents = "SELECT u.ID_User, u.Username as NIM, u.Nama_Lengkap,
                                             p.Status_Kehadiran, p.ID_Presensi
                                      FROM Tabel_User u
                                      LEFT JOIN Tabel_Presensi p ON u.ID_User = p.ID_User AND p.ID_Modul = :modulId
                                      WHERE u.ID_Kelompok = :groupId AND u.Role = 'Mahasiswa'
                                      ORDER BY u.Username ASC";
                    $stmtStudents = $db->prepare($queryStudents);
                    $stmtStudents->bindParam(':modulId', $selectedModul);
                    $stmtStudents->bindParam(':groupId', $groupId);
                    $stmtStudents->execute();
                    $students = $stmtStudents->fetchAll();

                    $groupsData[] = [
                        'group_id' => $groupId,
                        'group_name' => $g['Nama_Kelompok'],
                        'students' => $students
                    ];
                }
            }
        }
 
        require_once __DIR__ . '/../Views/presensi.php';
    }

    // Submit Attendance POST (Asisten/Dosen side)
    public function submitPresensi() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['active_role'] ?? '', ['Dosen', 'Asisten'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /rpl/public/index.php?action=presensi');
            exit;
        }

        $db = (new Database())->getConnection();
        $modulId = isset($_POST['modul_id']) ? (int)$_POST['modul_id'] : 0;
        $classId = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
        $attendanceData = isset($_POST['attendance']) ? $_POST['attendance'] : [];
        $today = date('Y-m-d');

        if ($modulId <= 0 || empty($attendanceData)) {
            $_SESSION['presensi_error'] = "Data presensi tidak lengkap.";
            header("Location: /rpl/public/index.php?action=presensi&class_id=$classId&modul_id=$modulId");
            exit;
        }

        foreach ($attendanceData as $mhsId => $status) {
            $queryCheck = "SELECT ID_Presensi FROM Tabel_Presensi WHERE ID_User = :userId AND ID_Modul = :modulId LIMIT 1";
            $stmtCheck = $db->prepare($queryCheck);
            $stmtCheck->bindParam(':userId', $mhsId);
            $stmtCheck->bindParam(':modulId', $modulId);
            $stmtCheck->execute();
            $existing = $stmtCheck->fetch();

            if ($existing) {
                $query = "UPDATE Tabel_Presensi SET Status_Kehadiran = :status, Tanggal = :today WHERE ID_Presensi = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':today', $today);
                $stmt->bindParam(':id', $existing['ID_Presensi']);
                $stmt->execute();
            } else {
                $query = "INSERT INTO Tabel_Presensi (ID_User, ID_Modul, Status_Kehadiran, Tanggal) 
                          VALUES (:userId, :modulId, :status, :today)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':userId', $mhsId);
                $stmt->bindParam(':modulId', $modulId);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':today', $today);
                $stmt->execute();
            }
        }

        $_SESSION['presensi_success'] = "Data presensi berhasil diperbarui.";
        header("Location: /rpl/public/index.php?action=presensi&class_id=$classId&modul_id=$modulId");
        exit;
    }

    // Render Grade Appeal / View Grade Page
    public function sanggahNilai() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['active_role'] ?? 'Mahasiswa';
        $db = (new Database())->getConnection();

        $classInfo = null;
        $gradesList = [];
        $progress = null;
        $attendanceRate = 100.0;

        if ($role === 'Mahasiswa') {
            $classInfo = $this->kelasModel->getStudentClass($userId);
            
            $query = "SELECT p.ID_Pengumpulan, p.File_Tugas, p.Waktu_Submit, 
                             t.Instruksi_Tugas, m.Judul_Modul,
                             n.Nilai_Angka, n.Feedback, n.Status_Tugas, n.Alasan_Sanggah, n.Tanggapan_Sanggah, n.ID_Nilai
                      FROM Tabel_Pengumpulan p
                      JOIN Tabel_Tugas t ON p.ID_Tugas = t.ID_Tugas
                      JOIN Tabel_Modul m ON t.ID_Modul = m.ID_Modul
                      JOIN Tabel_Nilai n ON p.ID_Pengumpulan = n.ID_Pengumpulan
                      WHERE p.ID_User = :userId
                      ORDER BY m.ID_Modul ASC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            $gradesList = $stmt->fetchAll();

            // Fetch progress and attendance rate
            $progress = $this->tugasModel->getStudentProgress($userId);
            require_once __DIR__ . '/../Models/UserModel.php';
            $attendanceRate = (new UserModel())->getAttendanceRate($userId);
        } else {
            $query = "SELECT p.ID_Pengumpulan, p.File_Tugas, p.Waktu_Submit,
                             u.Nama_Lengkap as Nama_Mahasiswa, u.Username as NIM,
                             t.Instruksi_Tugas, m.Judul_Modul, k.Nama_Kelas,
                             n.Nilai_Angka, n.Feedback, n.Status_Tugas, n.Alasan_Sanggah, n.Tanggapan_Sanggah, n.ID_Nilai
                      FROM Tabel_Pengumpulan p
                      JOIN Tabel_User u ON p.ID_User = u.ID_User
                      JOIN Tabel_Kelompok kl ON u.ID_Kelompok = kl.ID_Kelompok
                      JOIN Tabel_Kelas k ON kl.ID_Kelas = k.ID_Kelas
                      JOIN Tabel_Plotting_Asisten pa ON k.ID_Kelas = pa.ID_Kelas
                      JOIN Tabel_Tugas t ON p.ID_Tugas = t.ID_Tugas
                      JOIN Tabel_Modul m ON t.ID_Modul = m.ID_Modul
                      JOIN Tabel_Nilai n ON p.ID_Pengumpulan = n.ID_Pengumpulan
                      WHERE pa.ID_User = :staffId AND n.Status_Tugas = 'Sanggah'
                      ORDER BY p.Waktu_Submit DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':staffId', $userId);
            $stmt->execute();
            $gradesList = $stmt->fetchAll();
        }

        require_once __DIR__ . '/../Views/sanggah_nilai.php';
    }

    // Render Sanggah Nilai Form Page (Student Side)
    public function sanggahForm() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['active_role'] ?? 'Mahasiswa';
        $db = (new Database())->getConnection();

        if ($role !== 'Mahasiswa') {
            header('Location: /rpl/public/index.php?action=sanggah_nilai');
            exit;
        }

        // Fetch student class info
        $classInfo = $this->kelasModel->getStudentClass($userId);

        // Fetch student grades list (only graded ones)
        $query = "SELECT p.ID_Pengumpulan, m.Judul_Modul, m.ID_Modul, n.Nilai_Angka, n.ID_Nilai, n.Alasan_Sanggah, n.Status_Tugas
                  FROM Tabel_Pengumpulan p
                  JOIN Tabel_Tugas t ON p.ID_Tugas = t.ID_Tugas
                  JOIN Tabel_Modul m ON t.ID_Modul = m.ID_Modul
                  JOIN Tabel_Nilai n ON p.ID_Pengumpulan = n.ID_Pengumpulan
                  WHERE p.ID_User = :userId
                  ORDER BY m.ID_Modul ASC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $gradesList = $stmt->fetchAll();

        // Fetch appeal history (where Alasan_Sanggah is not null)
        $historyQuery = "SELECT p.ID_Pengumpulan, m.Judul_Modul, k.Nama_Kelas, n.Nilai_Angka, n.ID_Nilai, n.Alasan_Sanggah, n.Status_Tugas, n.Tanggapan_Sanggah, p.Waktu_Submit
                         FROM Tabel_Pengumpulan p
                         JOIN Tabel_Tugas t ON p.ID_Tugas = t.ID_Tugas
                         JOIN Tabel_Modul m ON t.ID_Modul = m.ID_Modul
                         JOIN Tabel_Nilai n ON p.ID_Pengumpulan = n.ID_Pengumpulan
                         JOIN Tabel_User u ON p.ID_User = u.ID_User
                         JOIN Tabel_Kelompok kl ON u.ID_Kelompok = kl.ID_Kelompok
                         JOIN Tabel_Kelas k ON kl.ID_Kelas = k.ID_Kelas
                         WHERE p.ID_User = :userId AND n.Alasan_Sanggah IS NOT NULL
                         ORDER BY n.ID_Nilai DESC";
        $stmtHistory = $db->prepare($historyQuery);
        $stmtHistory->bindParam(':userId', $userId);
        $stmtHistory->execute();
        $appealsHistory = $stmtHistory->fetchAll();

        require_once __DIR__ . '/../Views/sanggah_form.php';
    }

    // Submit Appeal POST (Student side)
    public function submitSanggah() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['active_role'] ?? '') !== 'Mahasiswa') {
            header('Location: /rpl/public/index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /rpl/public/index.php?action=sanggah_form');
            exit;
        }

        $db = (new Database())->getConnection();
        $idNilai = isset($_POST['id_nilai']) ? (int)$_POST['id_nilai'] : 0;
        $alasan = isset($_POST['alasan_sanggah']) ? trim($_POST['alasan_sanggah']) : '';

        if ($idNilai <= 0 || empty($alasan)) {
            $_SESSION['sanggah_error'] = "Alasan sanggahan tidak boleh kosong.";
            header('Location: /rpl/public/index.php?action=sanggah_form');
            exit;
        }

        $query = "UPDATE Tabel_Nilai 
                  SET Alasan_Sanggah = :alasan, Status_Tugas = 'Sanggah' 
                  WHERE ID_Nilai = :idNilai";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':alasan', $alasan);
        $stmt->bindParam(':idNilai', $idNilai);

        if ($stmt->execute()) {
            $_SESSION['sanggah_success'] = "Sanggahan nilai berhasil dikirim ke asisten.";
        } else {
            $_SESSION['sanggah_error'] = "Gagal mengirim sanggahan nilai.";
        }

        header('Location: /rpl/public/index.php?action=sanggah_form');
        exit;
    }

    // Respond Appeal POST (Assistant/Dosen side)
    public function respondSanggah() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['active_role'] ?? '', ['Dosen', 'Asisten'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /rpl/public/index.php?action=sanggah_nilai');
            exit;
        }

        $db = (new Database())->getConnection();
        $idNilai = isset($_POST['id_nilai']) ? (int)$_POST['id_nilai'] : 0;
        $nilaiBaru = isset($_POST['nilai_baru']) ? (float)$_POST['nilai_baru'] : 0.0;
        $tanggapan = isset($_POST['tanggapan_sanggah']) ? trim($_POST['tanggapan_sanggah']) : '';
        $status = isset($_POST['status_tugas']) ? $_POST['status_tugas'] : 'Selesai';

        if ($idNilai <= 0) {
            $_SESSION['sanggah_error'] = "Data sanggahan tidak valid.";
            header('Location: /rpl/public/index.php?action=sanggah_nilai');
            exit;
        }

        $query = "UPDATE Tabel_Nilai 
                  SET Nilai_Angka = :nilai, Tanggapan_Sanggah = :tanggapan, Status_Tugas = :status 
                  WHERE ID_Nilai = :idNilai";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':nilai', $nilaiBaru);
        $stmt->bindParam(':tanggapan', $tanggapan);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':idNilai', $idNilai);

        if ($stmt->execute()) {
            $_SESSION['sanggah_success'] = "Tanggapan sanggahan nilai berhasil disimpan.";
        } else {
            $_SESSION['sanggah_error'] = "Gagal memproses tanggapan sanggahan.";
        }

        header('Location: /rpl/public/index.php?action=sanggah_nilai');
        exit;
    }

    // Render Graduation Status Page
    public function kelulusan() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['active_role'] ?? 'Mahasiswa';
        $db = (new Database())->getConnection();
        $classInfo = null;
        $finalGrades = [];
        $studentClassesData = [];
        $allClasses = [];

        if ($role === 'Mahasiswa') {
            $classInfo = $this->kelasModel->getStudentClass($userId);
            
            // Initial mockup classes data matching the mockup layout
            $studentClassesData = [
                [
                    'name' => 'Sistem Digital',
                    'presensi' => 85,
                    'tugas' => 86,
                    'nilai_akhir' => 89,
                    'status' => 'Lulus'
                ],
                [
                    'name' => 'Praktikum Basis Data',
                    'presensi' => 86,
                    'tugas' => 86,
                    'nilai_akhir' => 89,
                    'status' => 'Lulus'
                ],
                [
                    'name' => 'Praktikum Pemrograman Web',
                    'presensi' => 58,
                    'tugas' => 55,
                    'nilai_akhir' => 56,
                    'status' => 'Mengulang'
                ]
            ];

            if ($classInfo) {
                $classId = $classInfo['ID_Kelas'];

                $progress = $this->tugasModel->getStudentProgress($userId);
                require_once __DIR__ . '/../Models/UserModel.php';
                $attendanceRate = (new UserModel())->getAttendanceRate($userId);

                $avgScore = $progress ? $progress['average_score'] : 0.0;
                $actualPresensi = $attendanceRate !== null ? round($attendanceRate) : 0;
                $actualTugas = round($avgScore);
                $actualNilaiAkhir = round((0.3 * $actualPresensi) + (0.7 * $actualTugas));
                
                $statusKelulusan = ($actualNilaiAkhir >= 70 && $actualPresensi >= 75) ? 'Lulus' : 'Mengulang';

                $queryCheck = "SELECT ID_Nilai_Akhir FROM Tabel_Nilai_Akhir WHERE ID_User = :userId AND ID_Kelas = :classId LIMIT 1";
                $stmtCheck = $db->prepare($queryCheck);
                $stmtCheck->bindParam(':userId', $userId);
                $stmtCheck->bindParam(':classId', $classId);
                $stmtCheck->execute();
                $existing = $stmtCheck->fetch();

                if ($existing) {
                    $queryUpdate = "UPDATE Tabel_Nilai_Akhir 
                                    SET Nilai_Akhir = :avgScore, Status_Kelulusan = :status 
                                    WHERE ID_Nilai_Akhir = :id";
                    $stmtUpdate = $db->prepare($queryUpdate);
                    $stmtUpdate->bindParam(':avgScore', $actualNilaiAkhir);
                    $stmtUpdate->bindParam(':status', $statusKelulusan);
                    $stmtUpdate->bindParam(':id', $existing['ID_Nilai_Akhir']);
                    $stmtUpdate->execute();
                } else {
                    $queryInsert = "INSERT INTO Tabel_Nilai_Akhir (ID_User, ID_Kelas, Nilai_Akhir, Status_Kelulusan) 
                                    VALUES (:userId, :classId, :avgScore, :status)";
                    $stmtInsert = $db->prepare($queryInsert);
                    $stmtInsert->bindParam(':userId', $userId);
                    $stmtInsert->bindParam(':classId', $classId);
                    $stmtInsert->bindParam(':avgScore', $actualNilaiAkhir);
                    $stmtInsert->bindParam(':status', $statusKelulusan);
                    $stmtInsert->execute();
                }

                $queryFinal = "SELECT * FROM Tabel_Nilai_Akhir WHERE ID_User = :userId AND ID_Kelas = :classId LIMIT 1";
                $stmtFinal = $db->prepare($queryFinal);
                $stmtFinal->bindParam(':userId', $userId);
                $stmtFinal->bindParam(':classId', $classId);
                $stmtFinal->execute();
                $finalGrades = $stmtFinal->fetch();

                 // Merge actual dynamic class information into mockup array
                $actualClassName = $classInfo['Nama_Kelas'];
                $found = false;
                foreach ($studentClassesData as &$cData) {
                    if (stripos($actualClassName, $cData['name']) !== false || stripos($cData['name'], $actualClassName) !== false) {
                        $cData['name'] = $actualClassName;
                        $cData['presensi'] = $actualPresensi;
                        $cData['tugas'] = $actualTugas;
                        $cData['nilai_akhir'] = $actualNilaiAkhir;
                        $cData['status'] = $statusKelulusan;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    array_unshift($studentClassesData, [
                        'name' => $actualClassName,
                        'presensi' => $actualPresensi,
                        'tugas' => $actualTugas,
                        'nilai_akhir' => $actualNilaiAkhir,
                        'status' => $statusKelulusan
                    ]);
                }
            }
        } elseif ($role === 'Dosen') {
            $allClasses = $this->kelasModel->getAllClasses();
        } else {
            // Staff view: Fetch grading queue and final grades
            $queryQueue = "SELECT p.ID_Pengumpulan, p.File_Tugas, p.Waktu_Submit, 
                                  u.ID_User, u.Nama_Lengkap as Nama_Mahasiswa, u.Username as NIM_Mahasiswa,
                                  kp.Nama_Kelompok, m.Judul_Modul, m.ID_Modul,
                                  n.Nilai_Angka, n.Feedback, n.Status_Tugas, n.ID_Nilai
                           FROM Tabel_Pengumpulan p
                           JOIN Tabel_User u ON p.ID_User = u.ID_User
                           JOIN Tabel_Kelompok kp ON u.ID_Kelompok = kp.ID_Kelompok
                           JOIN Tabel_Kelas k ON kp.ID_Kelas = k.ID_Kelas
                           JOIN Tabel_Plotting_Asisten pa ON k.ID_Kelas = pa.ID_Kelas
                           JOIN Tabel_Tugas t ON p.ID_Tugas = t.ID_Tugas
                           JOIN Tabel_Modul m ON t.ID_Modul = m.ID_Modul
                           LEFT JOIN Tabel_Nilai n ON p.ID_Pengumpulan = n.ID_Pengumpulan
                           WHERE pa.ID_User = :staffId
                           ORDER BY p.Waktu_Submit DESC";
            $stmtQueue = $db->prepare($queryQueue);
            $stmtQueue->bindParam(':staffId', $userId);
            $stmtQueue->execute();
            $submissionsQueue = $stmtQueue->fetchAll();

            $pendingCount = 0;
            foreach ($submissionsQueue as $sub) {
                if (empty($sub['Nilai_Angka']) || $sub['Nilai_Angka'] == 0.00) {
                    $pendingCount++;
                }
            }

            $selectedSubId = isset($_GET['pengumpulan_id']) ? (int)$_GET['pengumpulan_id'] : 0;
            $selectedSubmission = null;
            if ($selectedSubId > 0) {
                foreach ($submissionsQueue as $sub) {
                    if ((int)$sub['ID_Pengumpulan'] === $selectedSubId) {
                        $selectedSubmission = $sub;
                        break;
                    }
                }
            }
            if (!$selectedSubmission && !empty($submissionsQueue)) {
                $selectedSubmission = $submissionsQueue[0];
            }

            // Also fetch rekapitulasi final grades
            $queryFinal = "SELECT na.*, u.Nama_Lengkap as Nama_Mahasiswa, u.Username as NIM, k.Nama_Kelas
                           FROM Tabel_Nilai_Akhir na
                           JOIN Tabel_User u ON na.ID_User = u.ID_User
                           JOIN Tabel_Kelas k ON na.ID_Kelas = k.ID_Kelas
                           JOIN Tabel_Plotting_Asisten pa ON k.ID_Kelas = pa.ID_Kelas
                           WHERE pa.ID_User = :staffId
                           ORDER BY k.ID_Kelas ASC, u.Username ASC";
            $stmtFinal = $db->prepare($queryFinal);
            $stmtFinal->bindParam(':staffId', $userId);
            $stmtFinal->execute();
            $finalGrades = $stmtFinal->fetchAll();
        }

        require_once __DIR__ . '/../Views/kelulusan.php';
    }

    // Export Rekapitulasi Grades as Excel or CSV
    public function exportRekap() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['active_role'] ?? '') !== 'Dosen') {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $classId = isset($_GET['class_id']) ? $_GET['class_id'] : 'all';
        $semester = isset($_GET['semester']) ? $_GET['semester'] : 'Genap 2024/2025';
        $format = isset($_GET['format']) ? $_GET['format'] : 'excel';
        
        $db = (new Database())->getConnection();

        if ($classId === 'all') {
            $query = "SELECT na.*, u.Nama_Lengkap as Nama_Mahasiswa, u.Username as NIM, k.Nama_Kelas
                      FROM Tabel_Nilai_Akhir na
                      JOIN Tabel_User u ON na.ID_User = u.ID_User
                      JOIN Tabel_Kelas k ON na.ID_Kelas = k.ID_Kelas
                      ORDER BY k.Nama_Kelas ASC, u.Username ASC";
            $stmt = $db->prepare($query);
        } else {
            $query = "SELECT na.*, u.Nama_Lengkap as Nama_Mahasiswa, u.Username as NIM, k.Nama_Kelas
                      FROM Tabel_Nilai_Akhir na
                      JOIN Tabel_User u ON na.ID_User = u.ID_User
                      JOIN Tabel_Kelas k ON na.ID_Kelas = k.ID_Kelas
                      WHERE na.ID_Kelas = :classId
                      ORDER BY u.Username ASC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':classId', $classId, PDO::PARAM_INT);
        }
        $stmt->execute();
        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($format === 'csv') {
            $filename = 'rekap_nilai_' . ($classId === 'all' ? 'semua_kelas' : 'kelas_' . $classId) . '_' . date('YmdHis') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            fputcsv($output, ['NIM', 'Nama Mahasiswa', 'Kelas', 'Nilai Akhir', 'Status Kelulusan']);
            
            foreach ($grades as $row) {
                fputcsv($output, [
                    $row['NIM'],
                    $row['Nama_Mahasiswa'],
                    $row['Nama_Kelas'],
                    $row['Nilai_Akhir'],
                    $row['Status_Kelulusan']
                ]);
            }
            fclose($output);
            exit;
        } else {
            // Excel HTML Format
            $filename = 'rekap_nilai_' . ($classId === 'all' ? 'semua_kelas' : 'kelas_' . $classId) . '_' . date('YmdHis') . '.xls';
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>';
            echo '<body>';
            echo '<table border="1">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>NIM</th>';
            echo '<th>Nama Mahasiswa</th>';
            echo '<th>Kelas</th>';
            echo '<th>Nilai Akhir</th>';
            echo '<th>Status Kelulusan</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($grades as $row) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['NIM']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Nama_Mahasiswa']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Nama_Kelas']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Nilai_Akhir']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Status_Kelulusan']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
            exit;
        }
    }

    // Render Task Verification View
    public function verifikasiTugas() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['active_role'] ?? '', ['Dosen', 'Asisten'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['active_role'];
        $db = (new Database())->getConnection();

        $allClasses = $this->kelasModel->getClassesByUserId($userId);
        
        // Fetch all modules
        $queryModuls = "SELECT * FROM Tabel_Modul ORDER BY ID_Modul ASC";
        $stmtModuls = $db->prepare($queryModuls);
        $stmtModuls->execute();
        $moduls = $stmtModuls->fetchAll();

        // Selected filter values (default to first class and first module if none selected)
        $selectedClass = isset($_GET['class_id']) ? (int)$_GET['class_id'] : ($allClasses[0]['ID_Kelas'] ?? 0);
        $selectedModul = isset($_GET['modul_id']) ? (int)$_GET['modul_id'] : ($moduls[0]['ID_Modul'] ?? 0);

        // Fetch students and their submissions in the selected class and module
        $studentsData = [];
        if ($selectedClass > 0) {
            // First find the task associated with the selected module
            $queryTugas = "SELECT ID_Tugas FROM Tabel_Tugas WHERE ID_Modul = :modulId LIMIT 1";
            $stmtTugas = $db->prepare($queryTugas);
            $stmtTugas->bindParam(':modulId', $selectedModul);
            $stmtTugas->execute();
            $tugas = $stmtTugas->fetch();
            $tugasId = $tugas ? $tugas['ID_Tugas'] : 0;

            // Query all students belonging to the selected class (through their groups)
            // Left join with their submission for the found task (if any) and its grade
            $queryStudents = "SELECT u.ID_User, u.Username as NIM, u.Nama_Lengkap, kp.Nama_Kelompok,
                                     p.ID_Pengumpulan, p.File_Tugas, p.Waktu_Submit,
                                     n.Nilai_Angka, n.Feedback, n.Status_Tugas, n.ID_Nilai
                              FROM Tabel_User u
                              JOIN Tabel_Kelompok kp ON u.ID_Kelompok = kp.ID_Kelompok
                              LEFT JOIN Tabel_Pengumpulan p ON u.ID_User = p.ID_User AND p.ID_Tugas = :tugasId
                              LEFT JOIN Tabel_Nilai n ON p.ID_Pengumpulan = n.ID_Pengumpulan
                              WHERE kp.ID_Kelas = :classId AND u.Role = 'Mahasiswa'
                              ORDER BY kp.Nama_Kelompok ASC, u.Username ASC";
            $stmtStudents = $db->prepare($queryStudents);
            $stmtStudents->bindParam(':classId', $selectedClass);
            $stmtStudents->bindParam(':tugasId', $tugasId);
            $stmtStudents->execute();
            $studentsData = $stmtStudents->fetchAll();
        }

        require_once __DIR__ . '/../Views/verifikasi_tugas.php';
    }

    // Handle Verification Submission POST (Option A)
    public function submitVerification() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['active_role'] ?? '', ['Dosen', 'Asisten'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /rpl/public/index.php?action=verifikasi_tugas');
            exit;
        }

        $asistenId = $_SESSION['user_id'];
        $pengumpulanId = isset($_POST['id_pengumpulan']) ? (int)$_POST['id_pengumpulan'] : 0;
        $status = isset($_POST['status_tugas']) ? $_POST['status_tugas'] : 'Selesai';
        $feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';
        
        $redirectUrl = "/rpl/public/index.php?action=verifikasi_tugas";
        if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
            $redirectUrl = $_POST['redirect_url'];
        }

        if ($pengumpulanId <= 0) {
            $_SESSION['grade_error'] = "Data pengumpulan tidak valid.";
            header("Location: $redirectUrl");
            exit;
        }

        // Save verification status
        $success = $this->tugasModel->saveVerification($pengumpulanId, $asistenId, $status, $feedback);

        if ($success) {
            $_SESSION['grade_success'] = "Verifikasi tugas berhasil disimpan.";
        } else {
            $_SESSION['grade_error'] = "Gagal menyimpan verifikasi ke database.";
        }

        header("Location: $redirectUrl");
        exit;
    }

    // Stream student submission file inline (Option 1)
    public function viewFile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $file = isset($_GET['file']) ? $_GET['file'] : '';
        if (empty($file)) {
            die("File tidak ditemukan.");
        }

        // Clean filename to prevent path traversal vulnerability
        $file = basename($file);
        $filepath = __DIR__ . '/../../public/assets/uploads/tugas/' . $file;

        if (!file_exists($filepath)) {
            die("Berkas tugas tidak ditemukan di server.");
        }

        // Get content type based on extension
        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        $contentType = 'application/octet-stream';
        if ($ext === 'pdf') {
            $contentType = 'application/pdf';
        } elseif ($ext === 'jpg' || $ext === 'jpeg') {
            $contentType = 'image/jpeg';
        } elseif ($ext === 'png') {
            $contentType = 'image/png';
        } elseif ($ext === 'zip') {
            $contentType = 'application/zip';
        }

        header("Content-Type: $contentType");
        header("Content-Disposition: inline; filename=\"$file\"");
        header("Content-Length: " . filesize($filepath));
        readfile($filepath);
        exit;
    }
}
