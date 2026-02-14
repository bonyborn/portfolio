<?php
// backend/config/database.php

class Database {
    private $host = "localhost";
    private $db_name = "personal_profile_db";
    private $username = "profile_app";
    private $password = "SecurePassword123!";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );

            // PDO settings
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch(PDOException $exception) {
            error_log("Database Connection Error: " . $exception->getMessage());
            // Optionally throw exception to let APIs handle it
            throw new Exception("Database connection failed.");
        }

        return $this->conn;
    }
}
?>
