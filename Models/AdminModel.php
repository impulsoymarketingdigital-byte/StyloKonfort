<?php
class AdminModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function getUsuario($correo)
    {
        $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
        return $this->select($sql);
    }
    public function getTotales($estado)
    {
        $sql = "SELECT COUNT(*) AS total FROM pedidos WHERE proceso = $estado";
        return $this->select($sql);
    }
    public function getDatos($table)
    {
        $sql = "SELECT COUNT(*) AS total FROM $table WHERE estado = 1";
        return $this->select($sql);
    }

    public function nuevoProductos()
    {
        $sql = "SELECT p.*, c.categoria FROM productos p INNER JOIN categorias c ON p.id_categoria = c.id WHERE p.estado = 1 ORDER BY p.id DESC LIMIT 10";
        return $this->selectAll($sql);
    }

    public function productosMinimos()
    {
        $sql = "SELECT * FROM productos WHERE cantidad < 15 AND estado = 1 ORDER BY cantidad DESC LIMIT 5";
        return $this->selectAll($sql);
    }

    public function topProductos()
    {
        $sql = "SELECT * FROM productos ORDER BY ventas DESC LIMIT 5";
        return $this->selectAll($sql);
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }

    public function actualizar($ruc,$nombre,$telefono,$correo,$direccion,$whatsapp,$facebook, $twitter, $instagram, $ubicacion, $mensaje,$id)
    {
        $sql = "UPDATE configuracion SET ruc=?,nombre=?,telefono=?,correo=?,direccion=?,whatsapp=?,facebook=?, twitter=?, instagram=?, ubicacion=?, mensaje=? WHERE id=?";
        $array = array($ruc,$nombre,$telefono,$correo,$direccion,$whatsapp,$facebook, $twitter, $instagram, $ubicacion, $mensaje,$id);
        return $this->save($sql, $array);
    }
}
 
?>