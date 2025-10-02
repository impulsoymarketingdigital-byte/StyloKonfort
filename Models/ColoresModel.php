<?php
class ColoresModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function getColores($estado)
    {
        $sql = "SELECT * FROM colores WHERE estado = $estado";
        return $this->selectAll($sql);
    }

    public function registrar($nombre, $color)
    {
        $sql = "INSERT INTO colores (nombre, color) VALUES (?,?)";
        $array = array($nombre, $color);
        return $this->insertar($sql, $array);
    }
    public function verificarColor($nombre)
    {
        $sql = "SELECT nombre FROM colores WHERE nombre = '$nombre' AND estado = 1";
        return $this->select($sql);
    }

    public function eliminar($idColor)
    {
        $sql = "UPDATE colores SET estado = ? WHERE id = ?";
        $array = array(0, $idColor);
        return $this->save($sql, $array);
    }

    public function getColor($idColor)
    {
        $sql = "SELECT * FROM colores WHERE id = $idColor";
        return $this->select($sql);
    }

    public function modificar($nombre, $color, $id)
    {
        $sql = "UPDATE colores SET nombre=?, color=? WHERE id = ?";
        $array = array($nombre, $color, $id);
        return $this->save($sql, $array);
    }
}
 
?>