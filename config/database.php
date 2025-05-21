<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "wissenwelle";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->database,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->conn;
        } catch(PDOException $e) {
            // Log the error and return false
            error_log("Database Connection Error: " . $e->getMessage());
            return false;
        }
    }
} 