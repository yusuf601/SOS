<?php
// ==============================================================================
// EduLab UHO - Tugas Model
// ==============================================================================

require_once __DIR__ . '/../../config/database.php';

class TugasModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Get all tasks (Tugas)
    public function getAllTugas() {
        $query = "SELECT t.*, m.Judul_Modul FROM Tabel_Tugas t
                  JOIN Tabel_Modul m ON t.ID_Modul = m.ID_Modul
                  ORDER BY t.Deadline_Upload ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get tasks by Modul ID
    public function getTugasByModulId($modulId) {
        $query = "SELECT * FROM Tabel_Tugas WHERE ID_Modul = :modulId ORDER BY ID_Tugas ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':modulId', $modulId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get task by ID
    public function getTugasById($id) {
        $query = "SELECT t.*, m.Judul_Modul FROM Tabel_Tugas t
                  JOIN Tabel_Modul m ON t.ID_Modul = m.ID_Modul
                  WHERE t.ID_Tugas = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Get student submission for a task
    public function getSubmission($tugasId, $userId) {
        $query = "SELECT p.*, n.Nilai_Angka, n.Feedback, n.Status_Tugas, n.Alasan_Sanggah, n.Tanggapan_Sanggah
                  FROM Tabel_Pengumpulan p
                  LEFT JOIN Tabel_Nilai n ON p.ID_Pengumpulan = n.ID_Pengumpulan
                  WHERE p.ID_Tugas = :tugasId AND p.ID_User = :userId LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tugasId', $tugasId);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Submit task (create or update submission)
    public function submitTugas($tugasId, $userId, $fileTugas) {
        // Check if submission already exists
        $existing = $this->getSubmission($tugasId, $userId);
        $currentTime = date('Y-m-d H:i:s');

        if ($existing) {
            // Update existing submission
            $query = "UPDATE Tabel_Pengumpulan 
                      SET File_Tugas = :fileTugas, Waktu_Submit = :currentTime 
                      WHERE ID_Pengumpulan = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':fileTugas', $fileTugas);
            $stmt->bindParam(':currentTime', $currentTime);
            $stmt->bindParam(':id', $existing['ID_Pengumpulan']);
            return $stmt->execute();
        } else {
            // Insert new submission
            $query = "INSERT INTO Tabel_Pengumpulan (ID_Tugas, ID_User, File_Tugas, Waktu_Submit) 
                      VALUES (:tugasId, :userId, :fileTugas, :currentTime)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':tugasId', $tugasId);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':fileTugas', $fileTugas);
            $stmt->bindParam(':currentTime', $currentTime);
            return $stmt->execute();
        }
    }

    // Get grading info
    public function getGradeForSubmission($pengumpulanId) {
        $query = "SELECT * FROM Tabel_Nilai WHERE ID_Pengumpulan = :pengumpulanId LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':pengumpulanId', $pengumpulanId);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Calculate student progress metrics
    public function getStudentProgress($userId) {
        // 1. Get total active tasks
        $allTugas = $this->getAllTugas();
        $totalTasks = count($allTugas);

        // 2. Query student submissions and grades
        $query = "SELECT p.ID_Tugas, n.Nilai_Angka, n.Status_Tugas 
                  FROM Tabel_Pengumpulan p
                  LEFT JOIN Tabel_Nilai n ON p.ID_Pengumpulan = n.ID_Pengumpulan
                  WHERE p.ID_User = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $submissions = $stmt->fetchAll();

        $submittedCount = count($submissions);
        $gradedCount = 0;
        $pendingCount = 0;
        $totalScore = 0;

        foreach ($submissions as $sub) {
            if ($sub['Status_Tugas'] === 'Selesai') {
                $gradedCount++;
                $totalScore += (float)$sub['Nilai_Angka'];
            } elseif ($sub['Status_Tugas'] === 'Revisi' || $sub['Status_Tugas'] === 'Sanggah') {
                // Treated as graded or special
                $gradedCount++;
                $totalScore += (float)$sub['Nilai_Angka'];
            } else {
                $pendingCount++;
            }
        }

        $missingCount = max(0, $totalTasks - $submittedCount);
        $averageScore = $gradedCount > 0 ? round($totalScore / $gradedCount, 1) : 0;

        return [
            'total_tasks' => $totalTasks,
            'submitted' => $submittedCount,
            'graded' => $gradedCount,
            'pending' => $pendingCount,
            'missing' => $missingCount,
            'average_score' => $averageScore
        ];
    }
}
