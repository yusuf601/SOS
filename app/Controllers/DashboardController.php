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

    // Render My Classes View
    public function myClasses() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['active_role'] ?? 'Mahasiswa';

        $classesData = [];

        if ($role === 'Mahasiswa') {
            // Get student's class
            $classInfo = $this->kelasModel->getStudentClass($userId);
            if ($classInfo) {
                // Fetch dynamic stats for this class
                $db = (new Database())->getConnection();
                
                // Get lecturers and assistants mapped to this class
                $queryPlotting = "SELECT u.Nama_Lengkap, u.Role FROM Tabel_Plotting_Asisten p
                                  JOIN Tabel_User u ON p.ID_User = u.ID_User
                                  WHERE p.ID_Kelas = :classId";
                $stmt = $db->prepare($queryPlotting);
                $stmt->bindParam(':classId', $classInfo['ID_Kelas']);
                $stmt->execute();
                $plottedUsers = $stmt->fetchAll();

                $lecturer = "Tidak Ada Dosen";
                $assistant = "Tidak Ada Asisten";
                foreach ($plottedUsers as $pu) {
                    if ($pu['Role'] === 'Dosen') {
                        $lecturer = $pu['Nama_Lengkap'];
                    } elseif ($pu['Role'] === 'Asisten') {
                        $assistant = $pu['Nama_Lengkap'];
                    }
                }

                // Get count of modules in database
                $queryModuls = "SELECT COUNT(*) as total FROM Tabel_Modul";
                $stmtModuls = $db->prepare($queryModuls);
                $stmtModuls->execute();
                $totalModuls = $stmtModuls->fetch()['total'];

                // Get count of students in this class
                // First get all groups for this class
                $queryGroups = "SELECT ID_Kelompok FROM Tabel_Kelompok WHERE ID_Kelas = :classId";
                $stmtGroups = $db->prepare($queryGroups);
                $stmtGroups->bindParam(':classId', $classInfo['ID_Kelas']);
                $stmtGroups->execute();
                $groups = $stmtGroups->fetchAll(PDO::FETCH_COLUMN);

                $totalStudents = 0;
                if (!empty($groups)) {
                    $inQuery = implode(',', array_map('intval', $groups));
                    $queryStudents = "SELECT COUNT(*) as total FROM Tabel_User WHERE ID_Kelompok IN ($inQuery) AND Role = 'Mahasiswa'";
                    $stmtStudents = $db->prepare($queryStudents);
                    $stmtStudents->execute();
                    $totalStudents = $stmtStudents->fetch()['total'];
                }

                // Get progress metrics
                $progress = $this->tugasModel->getStudentProgress($userId);

                // Set dummy schedule based on classId
                $schedule = "Belum Diatur";
                if ($classInfo['ID_Kelas'] == 1) {
                    $schedule = "Senin 08:00 - 10:00 (Lab Komputer 2)";
                } elseif ($classInfo['ID_Kelas'] == 2) {
                    $schedule = "Rabu 10:00 - 12:00 (Lab Komputer 1)";
                } else {
                    $schedule = "Jumat 14:00 - 16:00 (Lab Jaringan)";
                }

                $classesData[] = [
                    'class_id' => $classInfo['ID_Kelas'],
                    'class_name' => $classInfo['Nama_Kelas'],
                    'group_name' => $classInfo['Nama_Kelompok'] ?? 'Belum Terdaftar',
                    'lecturer' => $lecturer,
                    'assistant' => $assistant,
                    'schedule' => $schedule,
                    'total_students' => $totalStudents,
                    'total_modules' => $totalModuls,
                    'submitted_tasks' => $progress['submitted'],
                    'total_tasks' => $progress['total_tasks']
                ];
            }
        } else {
            // For Dosen/Asisten, fetch all classes they are plotted to
            $db = (new Database())->getConnection();
            $queryClasses = "SELECT k.* FROM Tabel_Plotting_Asisten p
                             JOIN Tabel_Kelas k ON p.ID_Kelas = k.ID_Kelas
                             WHERE p.ID_User = :userId";
            $stmt = $db->prepare($queryClasses);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            $myPlottedClasses = $stmt->fetchAll();

            foreach ($myPlottedClasses as $cls) {
                // Get other plotted users
                $queryPlotting = "SELECT u.Nama_Lengkap, u.Role FROM Tabel_Plotting_Asisten p
                                  JOIN Tabel_User u ON p.ID_User = u.ID_User
                                  WHERE p.ID_Kelas = :classId";
                $stmtPlotting = $db->prepare($queryPlotting);
                $stmtPlotting->bindParam(':classId', $cls['ID_Kelas']);
                $stmtPlotting->execute();
                $plottedUsers = $stmtPlotting->fetchAll();

                $lecturer = "Tidak Ada Dosen";
                $assistant = "Tidak Ada Asisten";
                foreach ($plottedUsers as $pu) {
                    if ($pu['Role'] === 'Dosen') {
                        $lecturer = $pu['Nama_Lengkap'];
                    } elseif ($pu['Role'] === 'Asisten') {
                        $assistant = $pu['Nama_Lengkap'];
                    }
                }

                // Get count of modules
                $queryModuls = "SELECT COUNT(*) as total FROM Tabel_Modul";
                $stmtModuls = $db->prepare($queryModuls);
                $stmtModuls->execute();
                $totalModuls = $stmtModuls->fetch()['total'];

                // Get count of students
                $queryGroups = "SELECT ID_Kelompok FROM Tabel_Kelompok WHERE ID_Kelas = :classId";
                $stmtGroups = $db->prepare($queryGroups);
                $stmtGroups->bindParam(':classId', $cls['ID_Kelas']);
                $stmtGroups->execute();
                $groups = $stmtGroups->fetchAll(PDO::FETCH_COLUMN);

                $totalStudents = 0;
                if (!empty($groups)) {
                    $inQuery = implode(',', array_map('intval', $groups));
                    $queryStudents = "SELECT COUNT(*) as total FROM Tabel_User WHERE ID_Kelompok IN ($inQuery) AND Role = 'Mahasiswa'";
                    $stmtStudents = $db->prepare($queryStudents);
                    $stmtStudents->execute();
                    $totalStudents = $stmtStudents->fetch()['total'];
                }

                // Set schedule
                $schedule = "Belum Diatur";
                if ($cls['ID_Kelas'] == 1) {
                    $schedule = "Senin 08:00 - 10:00 (Lab Komputer 2)";
                } elseif ($cls['ID_Kelas'] == 2) {
                    $schedule = "Rabu 10:00 - 12:00 (Lab Komputer 1)";
                } else {
                    $schedule = "Jumat 14:00 - 16:00 (Lab Jaringan)";
                }

                $classesData[] = [
                    'class_id' => $cls['ID_Kelas'],
                    'class_name' => $cls['Nama_Kelas'],
                    'group_name' => 'Koordinator/Asisten',
                    'lecturer' => $lecturer,
                    'assistant' => $assistant,
                    'schedule' => $schedule,
                    'total_students' => $totalStudents,
                    'total_modules' => $totalModuls,
                    'submitted_tasks' => 0,
                    'total_tasks' => 0
                ];
            }
        }

        require_once __DIR__ . '/../Views/my_classes.php';
    }

    // Render Lecturer / Assistant Dashboard
    public function staffIndex() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['active_role'] ?? '', ['Dosen', 'Asisten'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['active_role'];

        // Fetch plotted classes
        $db = (new Database())->getConnection();
        $queryClasses = "SELECT k.* FROM Tabel_Plotting_Asisten p
                         JOIN Tabel_Kelas k ON p.ID_Kelas = k.ID_Kelas
                         WHERE p.ID_User = :userId";
        $stmt = $db->prepare($queryClasses);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $classes = $stmt->fetchAll();

        // Fetch submissions for plotted classes
        $submissions = [];
        if (!empty($classes)) {
            $querySubs = "SELECT p.*, u.Nama_Lengkap as Nama_Mahasiswa, u.Username as NIM_Mahasiswa, 
                                 t.Instruksi_Tugas, m.Judul_Modul, k.Nama_Kelas,
                                 n.Nilai_Angka, n.Feedback, n.Status_Tugas, n.ID_Nilai
                          FROM Tabel_Pengumpulan p
                          JOIN Tabel_User u ON p.ID_User = u.ID_User
                          JOIN Tabel_Kelompok kl ON u.ID_Kelompok = kl.ID_Kelompok
                          JOIN Tabel_Kelas k ON kl.ID_Kelas = k.ID_Kelas
                          JOIN Tabel_Plotting_Asisten pa ON k.ID_Kelas = pa.ID_Kelas
                          JOIN Tabel_Tugas t ON p.ID_Tugas = t.ID_Tugas
                          JOIN Tabel_Modul m ON t.ID_Modul = m.ID_Modul
                          LEFT JOIN Tabel_Nilai n ON p.ID_Pengumpulan = n.ID_Pengumpulan
                          WHERE pa.ID_User = :staffId
                          ORDER BY p.Waktu_Submit DESC";
            $stmtSubs = $db->prepare($querySubs);
            $stmtSubs->bindParam(':staffId', $userId);
            $stmtSubs->execute();
            $submissions = $stmtSubs->fetchAll();
        }

        // Calculate metrics
        $totalSubmissions = count($submissions);
        $gradedCount = 0;
        $pendingCount = 0;
        foreach ($submissions as $sub) {
            if (!empty($sub['Nilai_Angka'])) {
                $gradedCount++;
            } else {
                $pendingCount++;
            }
        }

        $stats = [
            'total_classes' => count($classes),
            'total_subs' => $totalSubmissions,
            'graded' => $gradedCount,
            'pending' => $pendingCount
        ];

        // 1. Pending Attendance Module
        $queryModules = "SELECT * FROM Tabel_Modul ORDER BY ID_Modul DESC";
        $stmtModules = $db->prepare($queryModules);
        $stmtModules->execute();
        $modulesList = $stmtModules->fetchAll();

        $pendingAttendanceModule = null;
        foreach ($modulesList as $m) {
            $queryMissingPresensi = "SELECT COUNT(*) as count FROM Tabel_User u
                                     JOIN Tabel_Kelompok kl ON u.ID_Kelompok = kl.ID_Kelompok
                                     JOIN Tabel_Plotting_Asisten pa ON kl.ID_Kelas = pa.ID_Kelas
                                     WHERE pa.ID_User = :userId AND u.Role = 'Mahasiswa'
                                       AND u.ID_User NOT IN (
                                           SELECT ID_User FROM Tabel_Presensi WHERE ID_Modul = :modulId
                                       )";
            $stmtMissing = $db->prepare($queryMissingPresensi);
            $stmtMissing->bindParam(':userId', $userId);
            $stmtMissing->bindValue(':modulId', $m['ID_Modul']);
            $stmtMissing->execute();
            $missingCount = $stmtMissing->fetch()['count'];
            if ($missingCount > 0) {
                $pendingAttendanceModule = $m;
                break;
            }
        }

        // 2. Pending Grades Info
        $queryPendingGrades = "SELECT m.ID_Modul, m.Judul_Modul, COUNT(p.ID_Pengumpulan) as count
                               FROM Tabel_Pengumpulan p
                               JOIN Tabel_Tugas t ON p.ID_Tugas = t.ID_Tugas
                               JOIN Tabel_Modul m ON t.ID_Modul = m.ID_Modul
                               JOIN Tabel_User u ON p.ID_User = u.ID_User
                               JOIN Tabel_Kelompok kl ON u.ID_Kelompok = kl.ID_Kelompok
                               JOIN Tabel_Plotting_Asisten pa ON kl.ID_Kelas = pa.ID_Kelas
                               LEFT JOIN Tabel_Nilai n ON p.ID_Pengumpulan = n.ID_Pengumpulan
                               WHERE pa.ID_User = :userId AND (n.Nilai_Angka IS NULL)
                               GROUP BY m.ID_Modul, m.Judul_Modul
                               ORDER BY count DESC LIMIT 1";
        $stmtPendingGrades = $db->prepare($queryPendingGrades);
        $stmtPendingGrades->bindParam(':userId', $userId);
        $stmtPendingGrades->execute();
        $pendingGradesInfo = $stmtPendingGrades->fetch();

        // 3. Pending Disputes Count
        $queryDisputesCount = "SELECT COUNT(*) as count FROM Tabel_Nilai n
                               JOIN Tabel_Pengumpulan p ON n.ID_Pengumpulan = p.ID_Pengumpulan
                               JOIN Tabel_User u ON p.ID_User = u.ID_User
                               JOIN Tabel_Kelompok kl ON u.ID_Kelompok = kl.ID_Kelompok
                               JOIN Tabel_Plotting_Asisten pa ON kl.ID_Kelas = pa.ID_Kelas
                               WHERE pa.ID_User = :userId AND n.Status_Tugas = 'Sanggah' AND (n.Tanggapan_Sanggah IS NULL OR n.Tanggapan_Sanggah = '')";
        $stmtDisputes = $db->prepare($queryDisputesCount);
        $stmtDisputes->bindParam(':userId', $userId);
        $stmtDisputes->execute();
        $disputesCount = $stmtDisputes->fetch()['count'];

        // 4. Grading Progress per Group
        $activeModulId = null;
        $activeModulTitle = "";
        if ($pendingGradesInfo) {
            $activeModulId = $pendingGradesInfo['ID_Modul'];
            $activeModulTitle = $pendingGradesInfo['Judul_Modul'];
        } else {
            $queryLatest = "SELECT ID_Modul, Judul_Modul FROM Tabel_Modul ORDER BY ID_Modul DESC LIMIT 1";
            $stmtLatest = $db->prepare($queryLatest);
            $stmtLatest->execute();
            $latestM = $stmtLatest->fetch();
            if ($latestM) {
                $activeModulId = $latestM['ID_Modul'];
                $activeModulTitle = $latestM['Judul_Modul'];
            }
        }

        $groupProgress = [];
        $averageScore = 0.0;

        if ($activeModulId) {
            $queryGroups = "SELECT k.ID_Kelompok, k.Nama_Kelompok, cl.Nama_Kelas, cl.ID_Kelas
                            FROM Tabel_Kelompok k
                            JOIN Tabel_Kelas cl ON k.ID_Kelas = cl.ID_Kelas
                            JOIN Tabel_Plotting_Asisten pa ON cl.ID_Kelas = pa.ID_Kelas
                            WHERE pa.ID_User = :userId";
            $stmtGroups = $db->prepare($queryGroups);
            $stmtGroups->bindParam(':userId', $userId);
            $stmtGroups->execute();
            $groupsList = $stmtGroups->fetchAll();

            foreach ($groupsList as $g) {
                $queryTotalStud = "SELECT COUNT(*) as count FROM Tabel_User WHERE ID_Kelompok = :groupId AND Role = 'Mahasiswa'";
                $stmtTotalStud = $db->prepare($queryTotalStud);
                $stmtTotalStud->bindValue(':groupId', $g['ID_Kelompok']);
                $stmtTotalStud->execute();
                $totalStud = $stmtTotalStud->fetch()['count'];

                $queryGradedStud = "SELECT COUNT(*) as count 
                                    FROM Tabel_Pengumpulan p
                                    JOIN Tabel_Tugas t ON p.ID_Tugas = t.ID_Tugas
                                    JOIN Tabel_User u ON p.ID_User = u.ID_User
                                    JOIN Tabel_Nilai n ON p.ID_Pengumpulan = n.ID_Pengumpulan
                                    WHERE u.ID_Kelompok = :groupId 
                                      AND t.ID_Modul = :modulId 
                                      AND n.Nilai_Angka IS NOT NULL";
                $stmtGradedStud = $db->prepare($queryGradedStud);
                $stmtGradedStud->bindValue(':groupId', $g['ID_Kelompok']);
                $stmtGradedStud->bindValue(':modulId', $activeModulId);
                $stmtGradedStud->execute();
                $gradedStud = $stmtGradedStud->fetch()['count'];

                $groupProgress[] = [
                    'group_name' => $g['Nama_Kelompok'],
                    'total_students' => $totalStud,
                    'graded_count' => $gradedStud
                ];
            }

            $queryAvg = "SELECT AVG(n.Nilai_Angka) as avg_score 
                         FROM Tabel_Nilai n
                         JOIN Tabel_Pengumpulan p ON n.ID_Pengumpulan = p.ID_Pengumpulan
                         JOIN Tabel_Tugas t ON p.ID_Tugas = t.ID_Tugas
                         JOIN Tabel_User u ON p.ID_User = u.ID_User
                         JOIN Tabel_Kelompok kl ON u.ID_Kelompok = kl.ID_Kelompok
                         JOIN Tabel_Plotting_Asisten pa ON kl.ID_Kelas = pa.ID_Kelas
                         WHERE pa.ID_User = :userId AND t.ID_Modul = :modulId AND n.Nilai_Angka IS NOT NULL";
                         
            $stmtAvg = $db->prepare($queryAvg);
            $stmtAvg->bindParam(':userId', $userId);
            $stmtAvg->bindValue(':modulId', $activeModulId);
            $stmtAvg->execute();
            $averageScore = round((float)($stmtAvg->fetch()['avg_score'] ?? 0.0), 1);
        }

        if ($role === 'Dosen') {
            require_once __DIR__ . '/../Views/dashboard_dosen.php';
        } else {
            require_once __DIR__ . '/../Views/dashboard_asisten.php';
        }
    }

    // Handle Grade Submission POST
    public function submitGrade() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['active_role'] ?? '', ['Dosen', 'Asisten'])) {
            header('Location: /rpl/public/index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $redirectAction = $_SESSION['active_role'] === 'Dosen' ? 'dashboard_dosen' : 'dashboard_asisten';
            header("Location: /rpl/public/index.php?action=$redirectAction");
            exit;
        }

        $asistenId = $_SESSION['user_id'];
        $pengumpulanId = isset($_POST['id_pengumpulan']) ? (int)$_POST['id_pengumpulan'] : 0;
        $nilai = isset($_POST['nilai_angka']) ? (float)$_POST['nilai_angka'] : 0.0;
        $feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';
        $status = isset($_POST['status_tugas']) ? $_POST['status_tugas'] : 'Selesai';

        $redirectAction = $_SESSION['active_role'] === 'Dosen' ? 'dashboard_dosen' : 'dashboard_asisten';

        if ($pengumpulanId <= 0) {
            $_SESSION['grade_error'] = "Data pengumpulan tidak valid.";
            header("Location: /rpl/public/index.php?action=$redirectAction");
            exit;
        }

        // Save grade
        $success = $this->tugasModel->saveGrade($pengumpulanId, $asistenId, $nilai, $feedback, $status);

        if ($success) {
            $_SESSION['grade_success'] = "Penilaian berhasil disimpan.";
        } else {
            $_SESSION['grade_error'] = "Gagal menyimpan penilaian ke database.";
        }

        header("Location: /rpl/public/index.php?action=$redirectAction");
        exit;
    }
}
