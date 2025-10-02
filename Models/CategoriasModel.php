<?php
class CategoriasModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function getCategorias()
    {
        $sql = "SELECT * FROM categorias";
        return $this->selectAll($sql);
    }

    public function registrar($categoria, $slug, $imagen)
    {
        $sql = "INSERT INTO categorias (categoria, slug, imagen) VALUES (?,?,?)";
        $array = array($categoria, $slug, $imagen);
        return $this->insertar($sql, $array);
    }
    public function verificarCategoria($categoria)
    {
        $sql = "SELECT categoria FROM categorias WHERE categoria = '$categoria' AND estado = 1";
        return $this->select($sql);
    }

    public function eliminar($estado, $id)
    {
        $sql = "UPDATE categorias SET estado = ? WHERE id = ?";
        $array = array($estado, $id);
        return $this->save($sql, $array);
    }

    public function getCatoria($id)
    {
        $sql = "SELECT * FROM categorias WHERE id = $id";
        return $this->select($sql);
    }

    public function modificar($categoria, $slug, $imagen, $id)
    {
        $sql = "UPDATE categorias SET categoria=?, slug=?, imagen=? WHERE id = ?";
        $array = array($categoria, $slug, $imagen, $id);
        return $this->save($sql, $array);
    }
}
 
?>