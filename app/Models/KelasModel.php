<?php
// ==============================================================================
// EduLab UHO - Kelas Model
// ==============================================================================

require_once __DIR__ . '/../../config/database.php';

class KelasModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Get all classes
    public function getAllClasses() {
        $query = "SELECT * FROM Tabel_Kelas ORDER BY Nama_Kelas ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get class by ID
    public function getClassById($id) {
        $query = "SELECT * FROM Tabel_Kelas WHERE ID_Kelas = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Get classes plotted for a specific user (Dosen or Asisten)
    public function getClassesByUserId($userId) {
        $query = "SELECT k.* FROM Tabel_Kelas k
                  JOIN Tabel_Plotting_Asisten p ON k.ID_Kelas = p.ID_Kelas
                  WHERE p.ID_User = :userId
                  ORDER BY k.Nama_Kelas ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get all classes a student has joined
    public function getStudentClasses($userId) {
        try {
            $query = "SELECT k.*, kp.Nama_Kelompok, kp.ID_Kelompok FROM Tabel_Kelas k
                      JOIN Tabel_KRS krs ON k.ID_Kelas = krs.ID_Kelas
                      LEFT JOIN Tabel_Kelompok kp ON krs.ID_Kelompok = kp.ID_Kelompok
                      WHERE krs.ID_User = :userId
                      ORDER BY k.ID_Kelas DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Return empty array if Tabel_KRS doesn't exist yet (before migration)
            return [];
        }
    }

    // Get the first class a student has joined (for backward compatibility)
    public function getStudentClass($userId) {
        $classes = $this->getStudentClasses($userId);
        return !empty($classes) ? $classes[0] : null;
    }
    // Create a new class and assign the Dosen and chosen Asisten to it
    public function createClass($namaKelas, $userId, $asistenIds = [], $token = null, $jadwal = null) {
        try {
            $this->db->beginTransaction();

            // Insert new class
            $queryKelas = "INSERT INTO Tabel_Kelas (Nama_Kelas, Token_Kelas, Jadwal) VALUES (:namaKelas, :token, :jadwal)";
            $stmtKelas = $this->db->prepare($queryKelas);
            $stmtKelas->bindParam(':namaKelas', $namaKelas);
            $stmtKelas->bindParam(':token', $token);
            $stmtKelas->bindParam(':jadwal', $jadwal);
            $stmtKelas->execute();
            $newClassId = $this->db->lastInsertId();

            // Assign Dosen to the new class
            $queryPlotting = "INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES (:userId, :classId)";
            $stmtPlotting = $this->db->prepare($queryPlotting);
            $stmtPlotting->bindParam(':userId', $userId);
            $stmtPlotting->bindParam(':classId', $newClassId);
            $stmtPlotting->execute();

            // Assign chosen Asistens to the new class and promote them
            if (!empty($asistenIds)) {
                $queryAsisten = "INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES (:asistenId, :classId)";
                $stmtAsisten = $this->db->prepare($queryAsisten);
                
                $queryPromote = "UPDATE Tabel_User SET Role = 'Asisten' WHERE ID_User = :asistenId AND Role = 'Mahasiswa'";
                $stmtPromote = $this->db->prepare($queryPromote);

                foreach ($asistenIds as $asistenId) {
                    // 1. Assign to class
                    $stmtAsisten->bindParam(':asistenId', $asistenId);
                    $stmtAsisten->bindParam(':classId', $newClassId);
                    $stmtAsisten->execute();

                    // 2. Promote to Asisten
                    $stmtPromote->bindParam(':asistenId', $asistenId);
                    $stmtPromote->execute();
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
