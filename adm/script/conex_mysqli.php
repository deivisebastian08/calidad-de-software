<?php
class MySQLcn {
    private $servidor = "localhost";
    private $usuario = "root";
    private $password = "";
    private $base_de_datos = "myweb";
    private $cn;
    private $result;

    public function __construct() {
        $this->cn = new mysqli($this->servidor, $this->usuario, $this->password, $this->base_de_datos);
        if ($this->cn->connect_error) {
            die("La conexión falló: " . $this->cn->connect_error);
        }
        $this->cn->set_charset("utf8");
    }

    public function query($sql) {
        $this->result = $this->cn->query($sql);
        if (!$this->result) {
            die("Error en la consulta: " . $this->cn->error);
        }
    }

    public function fetchAll() {
        $rows = [];
        if ($this->result) {
            while ($row = $this->result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function close() {
        if ($this->cn) {
            $this->cn->close();
        }
    }
}
?>
