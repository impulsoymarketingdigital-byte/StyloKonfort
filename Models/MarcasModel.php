<?php
class MarcasModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function getMarcas()
    {
        $sql = "SELECT * FROM marcas";
        return $this->selectAll($sql);
    }

    public function registrar($marca, $slug, $imagen)
    {
        $sql = "INSERT INTO marcas (marca, slug, imagen) VALUES (?,?,?)";
        $array = array($marca, $slug, $imagen);
        return $this->insertar($sql, $array);
    }
    public function verificarMarca($marca)
    {
        $sql = "SELECT marca FROM marcas WHERE marca = '$marca' AND estado = 1";
        return $this->select($sql);
    }

    public function eliminar($estado, $id)
    {
        $sql = "UPDATE marcas SET estado = ? WHERE id = ?";
        $array = array($estado, $id);
        return $this->save($sql, $array);
    }

    public function getCatoria($id)
    {
        $sql = "SELECT * FROM marcas WHERE id = $id";
        return $this->select($sql);
    }

    public function modificar($marca, $slug, $imagen, $id)
    {
        $sql = "UPDATE marcas SET marca=?, slug=?, imagen=? WHERE id = ?";
        $array = array($marca, $slug, $imagen, $id);
        return $this->save($sql, $array);
    }
}
 
?>