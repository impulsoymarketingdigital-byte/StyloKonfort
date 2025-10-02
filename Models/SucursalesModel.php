<?php
class SucursalesModel extends Query
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getSucursales()
    {
        $sql = "SELECT * FROM sucursales";
        return $this->selectAll($sql);
    }

    public function registrar($nombre, $codigo, $direccion, $telefono, $es_bodega)
    {
        $sql = "INSERT INTO sucursales (nombre, codigo, direccion, telefono, es_bodega) 
                VALUES (?, ?, ?, ?, ?)";
        $array = array($nombre, $codigo, $direccion, $telefono, $es_bodega);
        return $this->insertar($sql, $array);
    }

    public function getValidar($campo, $valor, $accion, $id)
    {
        if ($accion == 'registrar' && $id == 0) {
            $sql = "SELECT id FROM sucursales WHERE $campo = '$valor'";
        }else{
            $sql = "SELECT id FROM sucursales WHERE $campo = '$valor' AND id != $id";
        }
        return $this->select($sql);
    }

    public function eliminar($estado, $id)
    {
        $sql = "UPDATE sucursales SET estado = ? WHERE id = ?";
        $array = array($estado, $id);
        return $this->save($sql, $array);
    }

    public function getSucursal($id)
    {
        $sql = "SELECT * FROM sucursales WHERE id = $id";
        return $this->select($sql);
    }

    public function actualizar($nombre, $codigo, $direccion, $telefono, $es_bodega, $id)
    {
        $sql = "UPDATE sucursales 
                SET nombre = ?, codigo = ?, direccion = ?, telefono = ?, es_bodega = ? 
                WHERE id = ?";
        $array = array($nombre, $codigo, $direccion, $telefono, $es_bodega, $id);
        return $this->save($sql, $array);
    }
}

?>