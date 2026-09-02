<?php
class ClientesModel extends Query
{

    public function __construct()
    {
        parent::__construct();
    }

    public function registroDirecto(
        string $nombre,
        string $apellido,
        string $correo,
        string $clave,
        string $token,
        string $telefono,
        string $direccion,
        string $ciudad,
        string $departamento,
        string $barrio,
        string $documento,
        string $tipo_cliente
    ): int {
        $sql = "INSERT INTO clientes 
                (nombre, apellido, correo, clave, token, telefono, direccion, ciudad, departamento, barrio, documento, tipo_cliente) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
        return $this->insertar($sql, [
            $nombre, $apellido, $correo, $clave, $token,
            $telefono, $direccion, $ciudad, $departamento, $barrio,
            $documento, $tipo_cliente
        ]);
    }

    /** FIX: Eliminada interpolación directa */
    public function getVerificarDocumento(string $documento)
    {
        $sql = "SELECT * FROM clientes WHERE documento = ?";
        return $this->select($sql, [$documento]);
    }

    /** FIX: Eliminada interpolación directa */
    public function getToken(string $token)
    {
        $sql = "SELECT * FROM clientes WHERE token = ?";
        return $this->select($sql, [$token]);
    }

    public function actualizarVerify(int $id): int
    {
        $sql = "UPDATE clientes SET token=?, verify=? WHERE id=?";
        return $this->save($sql, [null, 1, $id]);
    }

    public function aprobarClienteMayorista(int $id): int
    {
        $sql = "UPDATE clientes SET verify = ? WHERE id = ?";
        return $this->save($sql, [1, $id]);
    }

    /**
     * FIX: Eliminada interpolación directa. Tabla validada por whitelist.
     */
    public function getVerificar(string $table, string $correo)
    {
        $tablasPermitidas = ['clientes', 'usuarios'];
        if (!in_array($table, $tablasPermitidas, true)) {
            return false;
        }
        $sql = "SELECT * FROM $table WHERE correo = ?";
        return $this->select($sql, [$correo]);
    }

    public function registrarPedido(
        string $id_transaccion,
        string $metodo,
        float  $monto,
        string $estado,
        string $fecha,
        string $email,
        string $nombre,
        string $apellido,
        string $direccion,
        string $ciudad,
        int    $id_cliente
    ): int {
        $sql = "INSERT INTO pedidos 
                (id_transaccion, metodo, monto, estado, fecha, email, nombre, apellido, direccion, ciudad, id_cliente) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        return $this->insertar($sql, [
            $id_transaccion, $metodo, $monto, $estado, $fecha,
            $email, $nombre, $apellido, $direccion, $ciudad, $id_cliente
        ]);
    }

    /** FIX: Eliminada interpolación directa */
    public function getProducto(int $id_producto)
    {
        $sql = "SELECT * FROM productos WHERE id = ?";
        return $this->select($sql, [$id_producto]);
    }

    public function registrarDetalle(
        string $producto,
        float  $precio,
        int    $cantidad,
        int    $id_pedido,
        int    $id_producto,
        int    $id_talla_color
    ): int {
        $sql = "INSERT INTO detalle_pedidos 
                (producto, precio, cantidad, id_pedido, id_producto, id_talla_color) 
                VALUES (?,?,?,?,?,?)";
        return $this->insertar($sql, [$producto, $precio, $cantidad, $id_pedido, $id_producto, $id_talla_color]);
    }

    /** FIX: Eliminada interpolación directa */
    public function getIdTallaColor(int $size, int $color, int $id_producto)
    {
        $sql = "SELECT id FROM tallas_colores WHERE id_talla = ? AND id_color = ? AND id_producto = ?";
        return $this->select($sql, [$size, $color, $id_producto]);
    }

    /** FIX: Eliminada interpolación directa */
    public function getPedidos(int $id_cliente): array
    {
        $sql = "SELECT * FROM pedidos WHERE id_cliente = ? ORDER BY fecha DESC";
        return $this->selectAll($sql, [$id_cliente]);
    }

    /** FIX: Eliminada interpolación directa */
    public function getPedido(int $idPedido)
    {
        $sql = "SELECT * FROM pedidos WHERE id = ?";
        return $this->select($sql, [$idPedido]);
    }

    /** FIX: Eliminada interpolación directa */
    public function verPedidos(int $idPedido): array
    {
        $sql = "SELECT d.* FROM pedidos p 
                INNER JOIN detalle_pedidos d ON p.id = d.id_pedido 
                WHERE p.id = ?";
        return $this->selectAll($sql, [$idPedido]);
    }

    /** FIX: Eliminada interpolación directa */
    public function getProductos(int $id_cliente): array
    {
        $sql = "SELECT d.producto, d.precio, SUM(d.cantidad) AS cantidad, d.id_producto
                FROM pedidos p
                INNER JOIN detalle_pedidos d ON p.id = d.id_pedido
                WHERE p.id_cliente = ?
                GROUP BY d.producto, d.precio, d.id_producto";
        return $this->selectAll($sql, [$id_cliente]);
    }

    /** FIX: Eliminada interpolación directa */
    public function comprobarCalificacion(int $id_producto, int $id_cliente)
    {
        $sql = "SELECT * FROM calificaciones WHERE id_producto = ? AND id_cliente = ?";
        return $this->select($sql, [$id_producto, $id_cliente]);
    }

    public function agregarCalificacion(int $cantidad, int $id_producto, int $id_cliente): int
    {
        $sql = "INSERT INTO calificaciones (cantidad, id_producto, id_cliente) VALUES (?,?,?)";
        return $this->insertar($sql, [$cantidad, $id_producto, $id_cliente]);
    }

    public function cambiarCalificacion(int $cantidad, int $id_producto, int $id_cliente): int
    {
        $sql = "UPDATE calificaciones SET cantidad=? WHERE id_producto=? AND id_cliente=?";
        return $this->save($sql, [$cantidad, $id_producto, $id_cliente]);
    }

    /** FIX: Eliminada interpolación directa */
    public function getAtributos(int $size, int $color, int $id_producto)
    {
        $sql = "SELECT d.stock, t.nombre AS size, c.nombre, c.color, p.precio_venta as precio
            FROM tallas_colores d 
            INNER JOIN tallas t ON d.id_talla = t.id 
            INNER JOIN colores c ON d.id_color = c.id 
            INNER JOIN productos p ON d.id_producto = p.id
            WHERE d.id_talla = ? AND d.id_color = ? AND d.id_producto = ?";
        return $this->select($sql, [$size, $color, $id_producto]);
    }

    public function actualizarStockDetalle(int $stock, int $id_talla_color): int
    {
        $sql = "UPDATE tallas_colores SET stock=? WHERE id=?";
        return $this->save($sql, [$stock, $id_talla_color]);
    }

    public function agregarMensaje(string $mensaje, int $id_cliente): int
    {
        $sql = "INSERT INTO testimonial (mensaje, id_cliente) VALUES (?,?)";
        return $this->insertar($sql, [$mensaje, $id_cliente]);
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion LIMIT 1";
        return $this->select($sql);
    }

    public function modificarMensaje(string $mensaje, int $id_cliente): int
    {
        $sql = "UPDATE testimonial SET mensaje=? WHERE id_cliente=?";
        return $this->save($sql, [$mensaje, $id_cliente]);
    }

    /** FIX: Eliminada interpolación directa */
    public function getAtributosPorId(int $id_talla_color)
    {
        $sql = "SELECT t.nombre AS size, c.nombre, c.color, c.color_secundario
            FROM tallas_colores d 
            INNER JOIN tallas t ON d.id_talla = t.id 
            INNER JOIN colores c ON d.id_color = c.id 
            WHERE d.id = ?";
        return $this->select($sql, [$id_talla_color]);
    }

    /** FIX: Eliminada interpolación directa */
    public function getTestimonio(int $id_cliente)
    {
        $sql = "SELECT * FROM testimonial WHERE id_cliente = ?";
        return $this->select($sql, [$id_cliente]);
    }

    public function getTipoCliente(): string
    {
        if (!empty($_SESSION['correoCliente'])) {
            $sql = "SELECT tipo_cliente FROM clientes WHERE correo = ? AND estado = 1";
            $cliente = $this->select($sql, [$_SESSION['correoCliente']]);
            return ($cliente) ? $cliente['tipo_cliente'] : 'final';
        }
        return 'final';
    }

    public function generarNumeroPedido(): string
    {
        $anio   = date('y');
        $prefijo = 'EC';
        $patron  = $prefijo . '-' . $anio . '-%';

        $sql    = "SELECT id_transaccion FROM pedidos 
                   WHERE id_transaccion LIKE ? 
                   ORDER BY id DESC LIMIT 1";
        $result = $this->select($sql, [$patron]);

        if ($result && isset($result['id_transaccion'])) {
            $partes = explode('-', $result['id_transaccion']);
            $ultimo = (int) end($partes);
            $nuevo  = $ultimo + 1;
        } else {
            $nuevo = 1;
        }

        return $prefijo . '-' . $anio . '-' . str_pad($nuevo, 4, '0', STR_PAD_LEFT);
    }

    // ─── SECCIÓN ADMIN CLIENTES ───────────────────────────────────────────────

    public function getClientes(): array
    {
        $sql = "SELECT * FROM clientes ORDER BY id DESC";
        return $this->selectAll($sql);
    }

    public function registrar(
        string $nombre,
        string $apellido,
        string $telefono,
        string $correo,
        string $direccion,
        string $ciudad,
        string $departamento,
        string $barrio,
        string $tipo_cliente,
        string $accion
    ): int {
        $sql = "INSERT INTO clientes 
                (nombre, apellido, telefono, correo, direccion, ciudad, departamento, barrio, tipo_cliente, accion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->insertar($sql, [
            $nombre, $apellido, $telefono, $correo, $direccion,
            $ciudad, $departamento, $barrio, $tipo_cliente, $accion
        ]);
    }

    /**
     * FIX: getValidar ahora usa whitelist de campos permitidos y prepared statements.
     */
    public function getValidar(string $campo, string $valor, string $accion, int $id)
    {
        $camposPermitidos = ['correo', 'documento', 'telefono'];
        if (!in_array($campo, $camposPermitidos, true)) {
            return false;
        }

        if ($accion === 'registrar' && $id === 0) {
            $sql = "SELECT id FROM clientes WHERE $campo = ?";
            return $this->select($sql, [$valor]);
        }
        $sql = "SELECT id FROM clientes WHERE $campo = ? AND id != ?";
        return $this->select($sql, [$valor, $id]);
    }

    public function eliminar(int $estado, int $idCliente): int
    {
        $sql = "UPDATE clientes SET estado = ? WHERE id = ?";
        return $this->save($sql, [$estado, $idCliente]);
    }

    /** FIX: Eliminada interpolación directa */
    public function editar(int $idCliente)
    {
        $sql = "SELECT * FROM clientes WHERE id = ?";
        return $this->select($sql, [$idCliente]);
    }

    public function actualizar(
        string $nombre,
        string $apellido,
        string $telefono,
        string $correo,
        string $direccion,
        string $ciudad,
        string $departamento,
        string $barrio,
        string $tipo_cliente,
        string $perfil,
        int    $id
    ): int {
        $sql = "UPDATE clientes 
                SET nombre=?, apellido=?, telefono=?, correo=?, direccion=?, 
                    ciudad=?, departamento=?, barrio=?, tipo_cliente=?, perfil=? 
                WHERE id=?";
        return $this->save($sql, [
            $nombre, $apellido, $telefono, $correo, $direccion,
            $ciudad, $departamento, $barrio, $tipo_cliente, $perfil, $id
        ]);
    }
}
?>