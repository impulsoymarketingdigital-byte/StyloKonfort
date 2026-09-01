<?php
class AdminModel extends Query
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getUsuario($correo)
    {
        $sql = "SELECT u.*, r.nombre as nombre_rol, r.permisos 
            FROM usuarios u 
            LEFT JOIN roles r ON u.id_rol = r.id 
            WHERE u.correo = '$correo' AND u.estado = 1";
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

    public function productosMinimos($stockMinimo = 0)
    {
        $sql = "SELECT 
                p.id,
                p.nombre,
                SUM(tc.stock) as cantidad
            FROM productos p
            INNER JOIN tallas_colores tc ON p.id = tc.id_producto
            WHERE p.estado = 1
            GROUP BY p.id, p.nombre
            HAVING cantidad <= $stockMinimo
            ORDER BY cantidad ASC
            LIMIT 5";
        return $this->selectAll($sql);
    }


    public function topProductos()
    {
        $sql = "SELECT 
                p.id,
                p.nombre,
                COUNT(tc.id) as ventas
            FROM productos p
            LEFT JOIN tallas_colores tc ON p.id = tc.id_producto
            WHERE p.estado = 1
            GROUP BY p.id, p.nombre
            ORDER BY p.created_at DESC
            LIMIT 5";
        return $this->selectAll($sql);
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }

    public function actualizar($ruc, $nombre, $telefono, $correo, $direccion, $whatsapp, $facebook, $twitter, $instagram, $ubicacion, $mensaje, $id)
    {
        $sql = "UPDATE configuracion SET ruc=?,nombre=?,telefono=?,correo=?,direccion=?,whatsapp=?,facebook=?, twitter=?, instagram=?, ubicacion=?, mensaje=? WHERE id=?";
        $array = array($ruc, $nombre, $telefono, $correo, $direccion, $whatsapp, $facebook, $twitter, $instagram, $ubicacion, $mensaje, $id);
        return $this->save($sql, $array);
    }
}

?>