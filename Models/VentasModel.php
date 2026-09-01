<?php
class VentasModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function buscarPorNombre($valor)
    {
        $sql = "SELECT * FROM productos WHERE nombre LIKE '%" . $valor . "%' AND estado = 1 LIMIT 10";
        return $this->selectAll($sql);
    }
    public function buscarCliente($valor)
    {
        $sql = "SELECT * FROM clientes WHERE nombre LIKE '%" . $valor . "%' AND estado = 1 LIMIT 10";
        return $this->selectAll($sql);
    }
    public function getProducto($idProducto)
    {
        $sql = "SELECT * FROM productos WHERE id = $idProducto";
        return $this->select($sql);
    }
    public function getColores($size, $id_producto)
    {
        $sql = "SELECT c.id, c.nombre FROM tallas_colores d INNER JOIN colores c ON d.id_color = c.id WHERE d.id_talla = $size AND d.id_producto = $id_producto GROUP BY d.id_color";
        return $this->selectAll($sql);
    }
    public function getSizes($idProducto)
    {
        $sql = "SELECT s.id, s.nombre FROM tallas_colores t INNER JOIN tallas s ON t.id_talla = s.id WHERE t.id_producto = $idProducto GROUP BY t.id_talla";
        return $this->selectAll($sql);
    }
    public function getDetalle($idProducto)
    {
        $sql = "SELECT * FROM talla_colores WHERE id_producto = $idProducto";
        return $this->selectAll($sql);
    }
    public function registrarVenta($productos, $total, $fecha, $idCliente, $idusuario)
    {
        $sql = "INSERT INTO ventas (productos, total, fecha, id_cliente, id_usuario) VALUES (?,?,?,?,?)";
        $array = array($productos, $total, $fecha, $idCliente, $idusuario);
        return $this->insertar($sql, $array);
    }
    public function getAtributos($size, $color, $id_producto)
    {
        $sql = "SELECT tc.id, tc.stock, p.precio_venta, t.nombre AS size, c.nombre, c.color 
            FROM tallas_colores tc
            INNER JOIN productos p ON tc.id_producto = p.id
            INNER JOIN tallas t ON tc.id_talla = t.id 
            INNER JOIN colores c ON tc.id_color = c.id 
            WHERE tc.id_talla = $size 
            AND tc.id_color = $color 
            AND tc.id_producto = $id_producto
            AND tc.id_almacen = 1";
        return $this->select($sql);
    }

    public function registrarPedido($id_transaccion, $metodo, $monto, $estado, $fecha, $id_cliente, $cash_box_id, $id_usuario)
    {
        $sql = "INSERT INTO pedidos (id_transaccion, metodo, monto, estado, fecha, id_cliente, cash_box_id, id_usuario, proceso) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $datos = array($id_transaccion, $metodo, $monto, $estado, $fecha, $id_cliente, $cash_box_id, $id_usuario);
        return $this->insertar($sql, $datos);
    }

    public function getCajaAbierta($id_usuario, $id_almacen = null)
    {
        if ($id_almacen !== null) {
            $sql = "SELECT * FROM cajas WHERE id_usuario = $id_usuario AND id_almacen = $id_almacen AND estado = 1 ORDER BY id DESC LIMIT 1";
        } else {
            $sql = "SELECT * FROM cajas WHERE id_usuario = $id_usuario AND estado = 1 ORDER BY id DESC LIMIT 1";
        }
        return $this->select($sql);
    }

    public function registrarMovimiento($tipo, $tipo_movimiento, $descripcion, $monto, $id_caja, $id_usuario, $transaction_id, $transaction_type)
    {
        $sql = "INSERT INTO movimientos (tipo, tipo_movimiento, descripcion, monto, id_caja, id_usuario, transaction_id, transaction_type, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $array = array($tipo, $tipo_movimiento, $descripcion, $monto, $id_caja, $id_usuario, $transaction_id, $transaction_type);
        return $this->insertar($sql, $array);
    }

    public function registrarDetallePedido($id_pedido, $id_producto, $nombre, $precio, $cantidad, $id_talla_color)
    {
        $sql = "INSERT INTO detalle_pedidos (producto, precio, cantidad, id_pedido, id_producto, id_talla_color) 
            VALUES (?, ?, ?, ?, ?, ?)";
        $datos = array($nombre, $precio, $cantidad, $id_pedido, $id_producto, $id_talla_color);
        return $this->insertar($sql, $datos);
    }
    public function actualizarStockDetalle($stock, $id_talla_color)
    {
        $sql = "UPDATE tallas_colores SET stock = ? WHERE id = ?";
        $datos = array($stock, $id_talla_color);
        return $this->save($sql, $datos);
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }
    public function getVentas()
    {
        $sql = "SELECT v.*, CONCAT(c.nombre,' ',c.apellido) AS nombre 
                FROM pedidos v 
                INNER JOIN clientes c ON v.id_cliente = c.id 
                WHERE v.metodo IN ('VENTA DIRECTA', 'LLEVAR')
                AND v.estado IN ('COMPLETADO', 'ANULADO')
                ORDER BY v.fecha DESC";
        return $this->selectAll($sql);
    }
    public function getVenta($idVenta)
    {
        $sql = "SELECT p.*, c.nombre, c.apellido, c.telefono, c.direccion 
            FROM pedidos p 
            INNER JOIN clientes c ON p.id_cliente = c.id 
            WHERE p.id = $idVenta";
        return $this->select($sql);
    }

    public function getDetallePedido($idPedido)
    {
        $sql = "SELECT dp.*, 
            t.nombre AS talla, t.nombre_corto,
            c.nombre AS color_nombre, c.color AS color_hexa
            FROM detalle_pedidos dp
            LEFT JOIN tallas_colores tc ON dp.id_talla_color = tc.id
            LEFT JOIN tallas t ON tc.id_talla = t.id
            LEFT JOIN colores c ON tc.id_color = c.id
            WHERE dp.id_pedido = $idPedido";
        return $this->selectAll($sql);
    }
    public function getTallaColorPorId($id)
    {
        $sql = "SELECT * FROM tallas_colores WHERE id = $id";
        return $this->select($sql);
    }

    public function anular($idVenta)
    {
        $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
        $array = array('ANULADO', $idVenta);
        return $this->save($sql, $array);
    }

    public function getReporte($desde, $hasta, $id_usuario)
    {
        $sql = "SELECT v.*, CONCAT(c.nombre, ' ', c.apellido) AS nombre FROM ventas v INNER JOIN clientes c ON v.id_cliente = c.id WHERE v.fecha BETWEEN '$desde' AND '$hasta' AND v.id_usuario = $id_usuario";
        return $this->selectAll($sql);
    }

    public function getPedidosPendientes()
    {
        $sql = "SELECT p.*, 
                CONCAT(c.nombre, ' ', c.apellido) AS cliente_nombre,
                c.nombre,
                c.apellido,
                CONCAT(u.nombres, ' ', u.apellidos) AS vendedor
                FROM pedidos p 
                INNER JOIN clientes c ON p.id_cliente = c.id 
                LEFT JOIN usuarios u ON p.id_usuario = u.id
                WHERE p.metodo = 'VENTA DIRECTA'
                AND p.estado = 'PENDIENTE'
                ORDER BY p.fecha DESC";
        return $this->selectAll($sql);
    }

    public function actualizarEstadoPedido($idPedido, $estado)
    {
        $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
        $array = array($estado, $idPedido);
        return $this->save($sql, $array);
    }

    public function actualizarCajaPedido($idPedido, $cash_box_id)
    {
        $sql = "UPDATE pedidos SET cash_box_id = ? WHERE id = ?";
        $array = array($cash_box_id, $idPedido);
        return $this->save($sql, $array);
    }

    public function generarNumeroVenta($metodo)
    {
        $anio = date('y');

        $prefijo = ($metodo == 'VENTA DIRECTA') ? 'VT' : 'EC';

        $sql = "SELECT id_transaccion FROM pedidos 
            WHERE id_transaccion LIKE '" . $prefijo . "-" . $anio . "-%' 
            ORDER BY id DESC 
            LIMIT 1";

        $result = $this->select($sql);

        if ($result && isset($result['id_transaccion'])) {
            $partes = explode('-', $result['id_transaccion']);
            $ultimo = (int) end($partes);
            $nuevo = $ultimo + 1;
        } else {
            $nuevo = 1;
        }

        return $prefijo . '-' . $anio . '-' . $nuevo;
    }
}


?>