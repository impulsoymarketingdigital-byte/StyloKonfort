<?php
class AlmacenesModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAlmacenes()
    {
        $sql = "SELECT a.*, s.nombre as sucursal 
                FROM almacenes a 
                LEFT JOIN sucursales s ON a.id_sucursal = s.id";
        return $this->selectAll($sql);
    }

    public function registrar($nombre, $codigo, $direccion, $id_sucursal)
    {
        $sql = "INSERT INTO almacenes (nombre, codigo, direccion, id_sucursal) 
                VALUES (?, ?, ?, ?)";
        $array = array($nombre, $codigo, $direccion, $id_sucursal);
        return $this->insertar($sql, $array);
    }

    public function getValidar($campo, $valor, $accion, $id)
    {
        if ($accion == 'registrar' && $id == 0) {
            $sql = "SELECT id FROM almacenes WHERE $campo = '$valor'";
        } else {
            $sql = "SELECT id FROM almacenes WHERE $campo = '$valor' AND id != $id";
        }
        return $this->select($sql);
    }

    public function eliminar($estado, $id)
    {
        $sql = "UPDATE almacenes SET estado = ? WHERE id = ?";
        $array = array($estado, $id);
        return $this->save($sql, $array);
    }

    public function getAlmacen($id)
    {
        $sql = "SELECT * FROM almacenes WHERE id = $id";
        return $this->select($sql);
    }

    public function actualizar($nombre, $codigo, $direccion, $id_sucursal, $id)
    {
        $sql = "UPDATE almacenes 
                SET nombre = ?, codigo = ?, direccion = ?, id_sucursal = ?
                WHERE id = ?";
        $array = array($nombre, $codigo, $direccion, $id_sucursal, $id);
        return $this->save($sql, $array);
    }

    public function getSucursales()
    {
        $sql = "SELECT id, nombre FROM sucursales WHERE estado = 1";
        return $this->selectAll($sql);
    }
}
?>