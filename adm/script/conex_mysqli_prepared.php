<?php
class MySQLcn {
    private $servidor = "localhost";
    private $usuario = "root";
    private $password = "";
    private $base_de_datos = "myweb";
    private $cn;

    public function __construct() {
        $this->cn = new mysqli($this->servidor, $this->usuario, $this->password, $this->base_de_datos);
        if ($this->cn->connect_error) {
            die("La conexión falló: " . $this->cn->connect_error);
        }
        $this->cn->set_charset("utf8");
    }

    public function prepare($sql) {
        return $this->cn->prepare($sql);
    }

    public function bind_param($stmt, $types, ...$params) {
        $stmt->bind_param($types, ...$params);
    }

    public function execute($stmt) {
        return $stmt->execute();
    }

    public function close_stmt($stmt) {
        $stmt->close();
    }

    public function close() {
        if ($this->cn) {
            $this->cn->close();
        }
    }
    
    // Métodos para consultas no preparadas (lectura)
    public function query($sql) {
        return $this->cn->query($sql);
    }

    public function fetchAll($result) {
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
}
?>
