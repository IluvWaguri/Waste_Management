<?php
class Database {
    private $host = "sql110.infinityfree.com";
    private $db_name = "if0_43172985_neighborhood_waste";
    private $username = "if0_43172985";
    private $password = "61sdmSk2W4NK71";
    private $conn = null;

    public function connect() {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                die("Database Connection Error: " . $e->getMessage());
            }
        }
        return $this->conn;
    }
}
?>