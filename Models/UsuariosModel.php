<?php
class UsuariosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUsuarios(): array
    {
        $sql = "SELECT u.*, a.nombre AS almacen, r.nombre AS rol
            FROM usuarios u
            LEFT JOIN almacenes a ON u.id_almacen = a.id
            LEFT JOIN roles r ON u.id_rol = r.id";
        return $this->selectAll($sql);
    }

    public function getDatos(string $table): array
    {
        $tablasPermitidas = ['almacenes', 'roles', 'sucursales'];
        if (!in_array($table, $tablasPermitidas, true)) {
            return [];
        }
        $sql = "SELECT * FROM $table WHERE estado = 1";
        return $this->selectAll($sql);
    }

    public function registrar(
        string $nombre,
        string $apellido,
        string $correo,
        string $clave,
        int    $id_almacen,
        int    $id_rol
    ): int {
        $sql = "INSERT INTO usuarios (nombres, apellidos, correo, clave, id_almacen, id_rol) VALUES (?,?,?,?,?,?)";
        return $this->insertar($sql, [$nombre, $apellido, $correo, $clave, $id_almacen, $id_rol]);
    }

    /** FIX: Eliminada interpolación directa */
    public function verificarCorreo(string $correo)
    {
        $sql = "SELECT correo FROM usuarios WHERE correo = ? AND estado = 1";
        return $this->select($sql, [$correo]);
    }

    /**
     * FIX: Whitelist estricto de campos + prepared statements.
     */
    public function getValidar(string $campo, string $valor, string $accion, int $id)
    {
        $camposPermitidos = ['correo'];
        if (!in_array($campo, $camposPermitidos, true)) {
            return false;
        }

        if ($accion === 'registrar' && $id === 0) {
            $sql = "SELECT id FROM usuarios WHERE $campo = ?";
            return $this->select($sql, [$valor]);
        }
        $sql = "SELECT id FROM usuarios WHERE $campo = ? AND id != ?";
        return $this->select($sql, [$valor, $id]);
    }

    public function modificarDatos(
        string $nombre,
        string $apellidos,
        string $correo,
        string $perfil,
        int    $id_almacen,
        int    $id
    ): int {
        $sql = "UPDATE usuarios SET nombres=?, apellidos=?, correo=?, perfil=?, id_almacen=? WHERE id=?";
        return $this->save($sql, [$nombre, $apellidos, $correo, $perfil, $id_almacen, $id]);
    }

    public function eliminar(int $idUser, int $estado = 0): int
    {
        $sql = "UPDATE usuarios SET estado = ? WHERE id = ?";
        return $this->save($sql, [$estado, $idUser]);
    }

    /** FIX: Eliminada interpolación directa */
    public function getUsuario(int $idUser)
    {
        $sql = "SELECT u.id, u.nombres, u.apellidos, u.correo, u.perfil, u.clave, u.id_almacen, u.id_rol,
                    a.nombre AS almacen, r.nombre AS rol
                FROM usuarios u
                LEFT JOIN almacenes a ON u.id_almacen = a.id
                LEFT JOIN roles r ON u.id_rol = r.id
                WHERE u.id = ?";
        return $this->select($sql, [$idUser]);
    }

    public function modificar(
        string $nombre,
        string $apellido,
        string $correo,
        int    $id_almacen,
        int    $id_rol,
        int    $id
    ): int {
        $sql = "UPDATE usuarios SET nombres=?, apellidos=?, correo=?, id_almacen=?, id_rol=? WHERE id = ?";
        return $this->save($sql, [$nombre, $apellido, $correo, $id_almacen, $id_rol, $id]);
    }

    public function modificarPass(string $clave, int $id): int
    {
        $sql = "UPDATE usuarios SET clave=? WHERE id = ?";
        return $this->save($sql, [$clave, $id]);
    }
}
?>