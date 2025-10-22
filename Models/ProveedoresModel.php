<?php
class ProveedoresModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getProveedores()
    {
        $sql = "SELECT * FROM proveedores";
        return $this->selectAll($sql);
    }

    public function registrar($nombre, $persona_contacto, $documento, $ruc, $telefono, $direccion, $email)
    {
        $sql = "INSERT INTO proveedores (nombre, persona_contacto, documento, ruc, telefono, direccion, email) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $array = array($nombre, $persona_contacto, $documento, $ruc, $telefono, $direccion, $email);
        return $this->insertar($sql, $array);
    }

    public function getValidar($campo, $valor, $accion, $id)
    {
        if ($accion == 'registrar' && $id == 0) {
            $sql = "SELECT id FROM proveedores WHERE $campo = '$valor'";
        } else {
            $sql = "SELECT id FROM proveedores WHERE $campo = '$valor' AND id != $id";
        }
        return $this->select($sql);
    }

    public function eliminar($estado, $id)
    {
        $sql = "UPDATE proveedores SET estado = ? WHERE id = ?";
        $array = array($estado, $id);
        return $this->save($sql, $array);
    }

    public function getProveedor($id)
    {
        $sql = "SELECT * FROM proveedores WHERE id = $id";
        return $this->select($sql);
    }

    public function actualizar($nombre, $persona_contacto, $documento, $ruc, $telefono, $direccion, $email, $id)
    {
        $sql = "UPDATE proveedores 
                SET nombre = ?, persona_contacto = ?, documento = ?, ruc = ?, telefono = ?, direccion = ?, email = ?
                WHERE id = ?";
        $array = array($nombre, $persona_contacto, $documento, $ruc, $telefono, $direccion, $email, $id);
        return $this->save($sql, $array);
    }
}
?>