<?php
// filepath: g:\My Drive\Projects\joh\api\config\database.php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Database {
    private $host = "srv1212.hstgr.io";
    private $db_name = "u747325399_joh";
    private $username = "u747325399_joh"; // Change in production
    private $password = "lP4Ao2=#"; // Change in production
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            throw new Exception("Database connection failed: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>
