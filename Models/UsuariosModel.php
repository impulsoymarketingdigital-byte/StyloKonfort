<?php
class UsuariosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getUsuarios()
    {
        $sql = "SELECT u.*, s.nombre AS sucursal, r.nombre AS rol
            FROM usuarios u
            LEFT JOIN sucursales s ON u.id_sucursal = s.id
            LEFT JOIN roles r ON u.id_rol = r.id";
        return $this->selectAll($sql);
    }
    
    public function getDatos($table)
    {
        $sql = "SELECT * FROM $table WHERE estado = 1";
        return $this->selectAll($sql);
    }
    
    public function registrar($nombre, $apellido, $correo, $clave, $id_sucursal, $id_rol)
    {
        $sql = "INSERT INTO usuarios (nombres, apellidos, correo, clave, id_sucursal, id_rol) VALUES (?,?,?,?,?,?)";
        $array = array($nombre, $apellido, $correo, $clave, $id_sucursal, $id_rol);
        return $this->insertar($sql, $array);
    }
    
    public function verificarCorreo($correo)
    {
        $sql = "SELECT correo FROM usuarios WHERE correo = '$correo' AND estado = 1";
        return $this->select($sql);
    }

    public function getValidar($campo, $valor, $accion, $id)
    {
        if ($accion == 'registrar' && $id == 0) {
            $sql = "SELECT id FROM usuarios WHERE $campo = '$valor'";
        } else {
            $sql = "SELECT id FROM usuarios WHERE $campo = '$valor' AND id != $id";
        }
        return $this->select($sql);
    }

    public function modificarDatos($nombre, $apellidos, $correo, $perfil, $id_sucursal, $id)
    {
        $sql = "UPDATE usuarios SET nombres=?, apellidos=?, correo=?, perfil=?, id_sucursal=? WHERE id=?";
        $array = array($nombre, $apellidos, $correo, $perfil, $id_sucursal, $id);
        return $this->save($sql, $array);
    }

    public function eliminar($estado = 0, $idUser)
    {
        $sql = "UPDATE usuarios SET estado = ? WHERE id = ?";
        $array = array($estado, $idUser);
        return $this->save($sql, $array);
    }

    public function getUsuario($idUser)
    {
        $sql = "SELECT u.id, u.nombres, u.apellidos, u.correo, u.perfil, u.clave, u.id_sucursal, u.id_rol, s.nombre AS sucursal, r.nombre AS rol
            FROM usuarios u
            LEFT JOIN sucursales s ON u.id_sucursal = s.id
            LEFT JOIN roles r ON u.id_rol = r.id
            WHERE u.id = $idUser";
        return $this->select($sql);
    }

    public function modificar($nombre, $apellido, $correo, $id_sucursal, $id_rol, $id)
    {
        $sql = "UPDATE usuarios SET nombres=?, apellidos=?, correo=?, id_sucursal=?, id_rol=? WHERE id = ?";
        $array = array($nombre, $apellido, $correo, $id_sucursal, $id_rol, $id);
        return $this->save($sql, $array);
    }

    public function modificarPass($clave, $id)
    {
        $sql = "UPDATE usuarios SET clave=? WHERE id = ?";
        $array = array($clave, $id);
        return $this->save($sql, $array);
    }
}
?>