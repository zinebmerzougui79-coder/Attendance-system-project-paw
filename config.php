<?php
class Database {
    private $host = "localhost";
    private $db_name = "pawproject";
    private $username = "root";
    private $password = "";
    // If MySQL is also on different port, add:
    // private $port = "3307"; // or whatever port MySQL uses
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // If MySQL on different port, use:
            // $this->conn = new PDO("mysql:host=" . $this->host . ";port=3307;dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "<div style='background: #fee; color: #c00; padding: 10px; border-radius: 5px; margin: 10px;'>
                    <strong>Database Error:</strong> " . $exception->getMessage() . "
                  </div>";
        }
        return $this->conn;
    }
}

$database = new Database();
$db = $database->getConnection();
?>