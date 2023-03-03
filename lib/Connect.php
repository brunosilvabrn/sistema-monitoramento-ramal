<?php

require '../config.php';
class Connect {
    private $host = DB_HOST;
    private $db_name = DB_DATABASE;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $conn;
    
    public function conn() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Error ao connectar na base de dados: ' . $e->getMessage();
        }
        
        return $this->conn;
    }
}
