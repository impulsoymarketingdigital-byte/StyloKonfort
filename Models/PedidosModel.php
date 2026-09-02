<?php
class PedidosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    /** FIX: proceso interpolado directamente → prepared statement */
    public function getPedidos(int $proceso): array
    {
        $sql = "SELECT * FROM pedidos WHERE proceso = ? AND metodo = 'LLEVAR' ORDER BY fecha DESC";
        return $this->selectAll($sql, [$proceso]);
    }

    public function getPedidosEnProceso(): array
    {
        $sql = "SELECT * FROM pedidos WHERE proceso = 2 AND metodo = 'LLEVAR' ORDER BY fecha DESC";
        return $this->selectAll($sql);
    }

    public function actualizarEstado(int $proceso, int $idPedido): int
    {
        $sql = "UPDATE pedidos SET proceso = ? WHERE id = ?";
        return $this->save($sql, [$proceso, $idPedido]);
    }

    public function actualizarEstadoCompleto(int $idPedido): int
    {
        $sql = "UPDATE pedidos SET estado = 'COMPLETADO' WHERE id = ?";
        return $this->save($sql, [$idPedido]);
    }

    /** FIX: interpolación directa → prepared statement */
    public function getPedido(int $idPedido)
    {
        $sql = "SELECT * FROM pedidos WHERE id = ?";
        return $this->select($sql, [$idPedido]);
    }

    /** FIX: interpolación directa → prepared statement */
    public function getDetallePedido(int $idPedido): array
    {
        $sql = "SELECT * FROM detalle_pedidos WHERE id_pedido = ?";
        return $this->selectAll($sql, [$idPedido]);
    }

    /** FIX: interpolación directa → prepared statement */
    public function getTallaColor(int $id_talla_color)
    {
        $sql = "SELECT t.nombre AS talla, c.nombre AS color 
                FROM tallas_colores tc 
                INNER JOIN tallas t ON tc.id_talla = t.id 
                INNER JOIN colores c ON tc.id_color = c.id 
                WHERE tc.id = ?";
        return $this->select($sql, [$id_talla_color]);
    }

    /** FIX: interpolación directa → prepared statement */
    public function getStockDetalle(int $id_talla_color)
    {
        $sql = "SELECT stock FROM tallas_colores WHERE id = ?";
        return $this->select($sql, [$id_talla_color]);
    }

    public function actualizarStockDetalle(int $stock, int $id_talla_color): int
    {
        $sql = "UPDATE tallas_colores SET stock = ? WHERE id = ?";
        return $this->save($sql, [$stock, $id_talla_color]);
    }

    public function getConfiguracion()
    {
        $sql = "SELECT * FROM configuracion LIMIT 1";
        return $this->select($sql);
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion LIMIT 1";
        return $this->select($sql);
    }

    /** FIX: interpolación directa → prepared statement */
    public function getCliente(int $idCliente)
    {
        $sql = "SELECT * FROM clientes WHERE id = ?";
        return $this->select($sql, [$idCliente]);
    }

    /**
     * Verifica que todos los productos del pedido tienen stock suficiente.
     * Retorna array de productos con stock insuficiente (vacío si todo OK).
     */
    public function verificarStockPedido(int $idPedido): array
    {
        $detalles = $this->getDetallePedido($idPedido);
        $productosInvalidos = [];

        foreach ($detalles as $detalle) {
            $id_talla_color    = (int) $detalle['id_talla_color'];
            $cantidadRequerida = (int) $detalle['cantidad'];

            $atributo = $this->getStockDetalle($id_talla_color);

            if ($atributo) {
                $stockActual = (int) $atributo['stock'];
                if ($stockActual < $cantidadRequerida) {
                    $productoInfo = $this->getProductoInfo((int) $detalle['id_producto'], $id_talla_color);
                    $productosInvalidos[] = [
                        'producto'          => $productoInfo['nombre'],
                        'atributos'         => $productoInfo['atributos'],
                        'stock_disponible'  => $stockActual,
                        'cantidad_requerida'=> $cantidadRequerida,
                    ];
                }
            }
        }

        return $productosInvalidos;
    }

    /** FIX: interpolación directa → prepared statement */
    public function getProductoInfo(int $idProducto, int $id_talla_color): array
    {
        $sql = "SELECT p.nombre FROM productos p WHERE p.id = ?";
        $producto = $this->select($sql, [$idProducto]);

        $atributos   = $this->getTallaColor($id_talla_color);
        $atributosStr = ($atributos) ? $atributos['talla'] . ' - ' . $atributos['color'] : '';

        return [
            'nombre'   => $producto['nombre'] ?? 'Desconocido',
            'atributos'=> $atributosStr,
        ];
    }
}
?>