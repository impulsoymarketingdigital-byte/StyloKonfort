<?php
class SizesModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function getSizes($estado)
    {
        $sql = "SELECT * FROM tallas WHERE estado = $estado";
        return $this->selectAll($sql);
    }

    public function registrar($nombre, $nombre_corto)
    {
        $sql = "INSERT INTO tallas (nombre, nombre_corto) VALUES (?,?)";
        $array = array($nombre, $nombre_corto);
        return $this->insertar($sql, $array);
    }
    public function verificarSize($nombre)
    {
        $sql = "SELECT nombre FROM tallas WHERE nombre = '$nombre' AND estado = 1";
        return $this->select($sql);
    }

    public function eliminar($idSize)
    {
        $sql = "UPDATE tallas SET estado = ? WHERE id = ?";
        $array = array(0, $idSize);
        return $this->save($sql, $array);
    }

    public function getSize($idSize)
    {
        $sql = "SELECT * FROM tallas WHERE id = $idSize";
        return $this->select($sql);
    }

    public function modificar($nombre, $nombre_corto, $id)
    {
        $sql = "UPDATE tallas SET nombre=?, nombre_corto=? WHERE id = ?";
        $array = array($nombre, $nombre_corto, $id);
        return $this->save($sql, $array);
    }
}
 
?>