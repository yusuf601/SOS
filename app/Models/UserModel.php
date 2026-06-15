<?php
// ==============================================================================
// EduLab UHO - User Model
// ==============================================================================

require_once __DIR__ . '/../../config/database.php';

class UserModel {
    private $db;
    private $table = "Tabel_User";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Get user by Username (NIM / NIDN)
    public function getUserByUsername($username) {
        $query = "SELECT * FROM " . $this->table . " WHERE Username = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Get user by ID
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE ID_User = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Verify password hash
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // Get all roles available for a user (Mahasiswa/Asisten check)
    public function getAvailableRoles($userId, $baseRole) {
        $roles = [$baseRole];

        if ($baseRole === 'Mahasiswa') {
            // Check if they are plotted as Asisten in Tabel_Plotting_Asisten
            $query = "SELECT COUNT(*) FROM Tabel_Plotting_Asisten WHERE ID_User = :userId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                $roles[] = 'Asisten';
            }
        } elseif ($baseRole === 'Asisten') {
            // Assistants are students, can switch to Mahasiswa role
            $roles[] = 'Mahasiswa';
        }

        return array_unique($roles);
    }
}
