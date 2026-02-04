<?php
class KetNoi {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $conn;

    public function __construct() {
        $this->host = getenv('DB_HOST') ?: "localhost";
        $this->user = getenv('DB_USER') ?: "root";
        $this->pass = getenv('DB_PASS') ?: "";
        $this->dbname = getenv('DB_NAME') ?: "test";
        $this->moKetNoi();
    }

    public function moKetNoi() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            die("Loi ket noi: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8mb4");
    }

    public function truyVan($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if ($params) {
            $types = str_repeat('s', count($params)); 
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $kq = $stmt->get_result();
        if ($kq === false) {
            die("Loi truy van: " . $this->conn->error);
        }
        return $kq;
    }

    public function capNhat($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $kq = $stmt->execute();
        if ($kq === false) {
            die("Loi cap nhat: " . $this->conn->error);
        }
        return $kq;
    }

    public function dongKetNoi() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>