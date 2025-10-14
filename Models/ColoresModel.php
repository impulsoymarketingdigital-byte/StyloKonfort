<?php
class ColoresModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function getColores()
    {
        $sql = "SELECT * FROM colores";
        return $this->selectAll($sql);
    }
    public function registrar($nombre, $color, $color_secundario)
    {
        $sql = "INSERT INTO colores (nombre, color, color_secundario) VALUES (?,?,?)";
        $array = array($nombre, $color, $color_secundario);
        return $this->insertar($sql, $array);
    }
    public function verificarColor($nombre)
    {
        $sql = "SELECT nombre FROM colores WHERE nombre = '$nombre' AND estado = 1";
        return $this->select($sql);
    }
    public function eliminar($estado, $idColor)
    {
        $sql = "UPDATE colores SET estado = ? WHERE id = ?";
        $array = array($estado, $idColor);
        return $this->save($sql, $array);
    }
    public function getColor($idColor)
    {
        $sql = "SELECT * FROM colores WHERE id = $idColor";
        return $this->select($sql);
    }
    public function modificar($nombre, $color, $color_secundario, $id)
    {
        $sql = "UPDATE colores SET nombre=?, color=?, color_secundario=? WHERE id = ?";
        $array = array($nombre, $color, $color_secundario, $id);
        return $this->save($sql, $array);
    }
}
 
?>