<?php
class AdminModel extends Query
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene el usuario administrador por correo (para login).
     * Usa prepared statement — sin interpolación de variables.
     */
    public function getUsuario(string $correo)
    {
        $sql = "SELECT u.*, r.nombre as nombre_rol, r.permisos 
            FROM usuarios u 
            LEFT JOIN roles r ON u.id_rol = r.id 
            WHERE u.correo = ? AND u.estado = 1";
        return $this->select($sql, [$correo]);
    }

    public function getTotales(int $estado)
    {
        $sql = "SELECT COUNT(*) AS total FROM pedidos WHERE proceso = ?";
        return $this->select($sql, [$estado]);
    }

    public function getDatos(string $table): array
    {
        // Validar tabla para evitar inyección en nombre de tabla
        $tablasPermitidas = ['productos', 'clientes', 'pedidos', 'usuarios'];
        if (!in_array($table, $tablasPermitidas, true)) {
            return ['total' => 0];
        }
        $sql = "SELECT COUNT(*) AS total FROM $table WHERE estado = 1";
        return $this->select($sql) ?: ['total' => 0];
    }

    public function nuevoProductos(): array
    {
        $sql = "SELECT p.*, c.categoria FROM productos p 
                INNER JOIN categorias c ON p.id_categoria = c.id 
                WHERE p.estado = 1 
                ORDER BY p.id DESC 
                LIMIT 10";
        return $this->selectAll($sql);
    }

    public function productosMinimos(int $stockMinimo = 0): array
    {
        $sql = "SELECT 
                p.id,
                p.nombre,
                SUM(tc.stock) as cantidad
            FROM productos p
            INNER JOIN tallas_colores tc ON p.id = tc.id_producto
            WHERE p.estado = 1
            GROUP BY p.id, p.nombre
            HAVING cantidad <= ?
            ORDER BY cantidad ASC
            LIMIT 5";
        return $this->selectAll($sql, [$stockMinimo]);
    }

    public function topProductos(): array
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
        $sql = "SELECT * FROM configuracion LIMIT 1";
        return $this->select($sql);
    }

    public function actualizar(
        string $ruc,
        string $nombre,
        string $telefono,
        string $correo,
        string $direccion,
        string $whatsapp,
        string $facebook,
        string $twitter,
        string $instagram,
        string $ubicacion,
        string $mensaje,
        int    $id
    ): int {
        $sql = "UPDATE configuracion 
                SET ruc=?,nombre=?,telefono=?,correo=?,direccion=?,whatsapp=?,
                    facebook=?,twitter=?,instagram=?,ubicacion=?,mensaje=? 
                WHERE id=?";
        return $this->save($sql, [
            $ruc, $nombre, $telefono, $correo, $direccion, $whatsapp,
            $facebook, $twitter, $instagram, $ubicacion, $mensaje, $id
        ]);
    }
}
?>