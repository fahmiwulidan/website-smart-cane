<?php
class Database {
    private $host = "127.0.0.1";
    private $user = "root";
    private $pass = "";
    private $db   = "iot_tongkat-pintar";
    private $port = 3306;

    public function connect() {
        $conn = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->db,
            $this->port
        );

        if ($conn->connect_error) {
            die("DB Error: " . $conn->connect_error);
        }

        return $conn;
    }
}
