<?php
class KetNoi {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $port;
    private $conn;

    public function __construct() {
        $this->host = getenv('DB_HOST');
        $this->user = getenv('DB_USER');
        $this->pass = getenv('DB_PASS');
        $this->dbname = getenv('DB_NAME');
        $this->port = getenv('DB_PORT') ?: 25060;
        $this->moKetNoi();
    }

    public function moKetNoi() {
        $this->conn = mysqli_init();
        if (!$this->conn) {
            die("mysqli_init failed");
        }

        mysqli_ssl_set($this->conn, NULL, NULL, NULL, NULL, NULL);

        $resolved = mysqli_real_connect(
            $this->conn, 
            $this->host, 
            $this->user, 
            $this->pass, 
            $this->dbname, 
            $this->port, 
            NULL, 
            MYSQLI_CLIENT_SSL
        );

        if (!$resolved) {
            die("Loi ket noi: " . mysqli_connect_error());
        }
        
        $this->conn->set_charset("utf8mb4");
    }

    public function truyVan($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    public function capNhat($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        return $stmt->execute();
    }

    public function getConn() {
        return $this->conn;
    }

    public function dongKetNoi() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}