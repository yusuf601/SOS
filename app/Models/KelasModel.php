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

    // Get student's class info
    public function getStudentClass($userId) {
        $query = "SELECT k.*, kp.Nama_Kelompok, kp.ID_Kelompok FROM Tabel_Kelas k
                  JOIN Tabel_Kelompok kp ON k.ID_Kelas = kp.ID_Kelas
                  JOIN Tabel_User u ON kp.ID_Kelompok = u.ID_Kelompok
                  WHERE u.ID_User = :userId LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetch();
    }
    // Create a new class and assign the Dosen to it
    public function createClass($namaKelas, $userId) {
        try {
            $this->db->beginTransaction();

            // Insert new class
            $queryKelas = "INSERT INTO Tabel_Kelas (Nama_Kelas) VALUES (:namaKelas)";
            $stmtKelas = $this->db->prepare($queryKelas);
            $stmtKelas->bindParam(':namaKelas', $namaKelas);
            $stmtKelas->execute();
            $newClassId = $this->db->lastInsertId();

            // Assign Dosen to the new class
            $queryPlotting = "INSERT INTO Tabel_Plotting_Asisten (ID_User, ID_Kelas) VALUES (:userId, :classId)";
            $stmtPlotting = $this->db->prepare($queryPlotting);
            $stmtPlotting->bindParam(':userId', $userId);
            $stmtPlotting->bindParam(':classId', $newClassId);
            $stmtPlotting->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
