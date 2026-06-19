<?php
// ==============================================================================
// EduLab UHO - Database Connection Config (PDO)
// ==============================================================================

class Database {
    private $host = "localhost";
    private $db_name = "db_praktikum";
    private $username = "root";
    private $password = "";
    public $conn;

    // Get the database connection
    public function getConnection() {
        $this->conn = null;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4";
        } else {
            $options[1002] = "SET NAMES utf8mb4";
        }

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                $options
            );
        } catch (PDOException $exception) {
            error_log("Database Connection Error: " . $exception->getMessage());
            die("Koneksi database gagal. Silakan hubungi administrator.");
        }

        return $this->conn;
    }
}
