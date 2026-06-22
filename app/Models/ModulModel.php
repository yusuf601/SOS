<?php
// ==============================================================================
// EduLab UHO - Modul Model
// ==============================================================================

require_once __DIR__ . '/../../config/database.php';

class ModulModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Get all modules
    public function getAllModuls() {
        $query = "SELECT * FROM Tabel_Modul ORDER BY ID_Modul ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get modules by class ID
    public function getModulesByClass($classId) {
        $query = "SELECT * FROM Tabel_Modul WHERE ID_Kelas = :classId ORDER BY ID_Modul ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':classId', $classId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Create a new module
    public function createModul($idKelas, $judul, $fileMateri) {
        $query = "INSERT INTO Tabel_Modul (ID_Kelas, Judul_Modul, File_Materi) VALUES (:idKelas, :judul, :file)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':idKelas' => $idKelas,
            ':judul' => $judul,
            ':file' => $fileMateri
        ]);
    }

    // Get module by ID
    public function getModulById($id) {
        $query = "SELECT * FROM Tabel_Modul WHERE ID_Modul = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
