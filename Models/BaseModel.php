<?php
class BaseModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }

    public function getCategorias()
    {
        $sql = "SELECT * FROM categorias WHERE estado = 1";
        return $this->selectAll($sql);
    }

    public function getProductosCategoria($idCategoria)
    {
        $sql = "SELECT * FROM productos WHERE id_categoria = $idCategoria AND estado = 1 ORDER BY id DESC LIMIT 12";
        return $this->selectAll($sql);
    }

    public function getCalificacion($accion, $id)
    {
        $sql = "SELECT $accion(cantidad) AS total FROM calificaciones WHERE id_producto = $id";
        return $this->select($sql);
    }

    public function getSliders()
    {
        $sql = "SELECT * FROM sliders";
        return $this->selectAll($sql);
    }

    public function getProductos($idCategoria)
    {
        $sql = "SELECT * FROM productos WHERE id_categoria = $idCategoria AND estado = 1 ORDER BY id";
        return $this->selectAll($sql);
    }
}
 
?>