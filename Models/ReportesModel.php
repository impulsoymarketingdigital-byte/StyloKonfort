<?php
class ReportesModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    // ============= REPORTES DE VENTAS =============
    public function getVentas()
    {
        $sql = "SELECT 
                p.id_transaccion as numero_venta,
                p.metodo,
                COALESCE(CONCAT(c.nombre, ' ', c.apellido), 'CLIENTE GENERAL') as cliente,
                prod.nombre as producto,
                dp.cantidad,
                dp.precio as precio_venta,
                (dp.precio * dp.cantidad) as subtotal,
                p.monto as total_pedido,
                p.fecha,
                p.estado,
                COALESCE(u.nombres, 'SIN USUARIO') as usuario,
                COALESCE(alm.nombre, 'SIN ALMACÉN') as almacen,
                p.id_usuario,
                tc.id_almacen
            FROM pedidos p
            INNER JOIN detalle_pedidos dp ON p.id = dp.id_pedido
            LEFT JOIN clientes c ON p.id_cliente = c.id
            LEFT JOIN productos prod ON dp.id_producto = prod.id
            LEFT JOIN tallas_colores tc ON dp.id_talla_color = tc.id
            LEFT JOIN almacenes alm ON tc.id_almacen = alm.id
            LEFT JOIN usuarios u ON p.id_usuario = u.id
            WHERE p.estado = 'COMPLETADO'
            ORDER BY p.fecha DESC";
        return $this->selectAll($sql);
    }

    public function getVentasPdf($desde = null, $hasta = null, $id_usuario = null, $id_almacen = null)
    {
        $sql = "SELECT 
                p.id_transaccion as numero_venta,
                p.metodo,
                COALESCE(CONCAT(c.nombre, ' ', c.apellido), 'CLIENTE GENERAL') as cliente,
                prod.nombre as producto,
                dp.cantidad,
                dp.precio as precio_venta,
                (dp.precio * dp.cantidad) as subtotal,
                p.monto as total_pedido,
                p.fecha,
                COALESCE(u.nombres, 'SIN USUARIO') as usuario,
                COALESCE(alm.nombre, 'SIN ALMACÉN') as almacen
            FROM pedidos p
            INNER JOIN detalle_pedidos dp ON p.id = dp.id_pedido
            LEFT JOIN clientes c ON p.id_cliente = c.id
            LEFT JOIN productos prod ON dp.id_producto = prod.id
            LEFT JOIN tallas_colores tc ON dp.id_talla_color = tc.id
            LEFT JOIN almacenes alm ON tc.id_almacen = alm.id
            LEFT JOIN usuarios u ON p.id_usuario = u.id
            WHERE p.estado = 'COMPLETADO'";

        if ($desde && $hasta) {
            $sql .= " AND DATE(p.fecha) BETWEEN '$desde' AND '$hasta'";
        }

        if ($id_usuario) {
            $sql .= " AND p.id_usuario = $id_usuario";
        }

        if ($id_almacen) {
            $sql .= " AND tc.id_almacen = $id_almacen";
        }

        $sql .= " ORDER BY p.fecha DESC";
        return $this->selectAll($sql);
    }

    // ============= REPORTES DE COMPRAS =============
    public function getCompras()
    {
        $sql = "SELECT 
                    c.numero_compra,
                    c.tipo_comprobante,
                    prov.nombre as proveedor,
                    alm.nombre as almacen,
                    dc.producto,
                    dc.cantidad,
                    dc.precio_compra,
                    dc.descuento,
                    dc.subtotal,
                    c.total,
                    c.fecha,
                    c.estado,
                    u.nombres as usuario
                FROM compras c
                INNER JOIN detalle_compras dc ON c.id = dc.id_compra
                LEFT JOIN proveedores prov ON c.id_proveedor = prov.id
                LEFT JOIN almacenes alm ON c.id_almacen = alm.id
                LEFT JOIN usuarios u ON c.id_usuario = u.id
                WHERE c.estado = 'COMPLETADO'
                ORDER BY c.fecha DESC";
        return $this->selectAll($sql);
    }

    public function getComprasPdf($desde = null, $hasta = null)
    {
        $sql = "SELECT 
                    c.numero_compra,
                    c.tipo_comprobante,
                    prov.nombre as proveedor,
                    alm.nombre as almacen,
                    dc.producto,
                    dc.cantidad,
                    dc.precio_compra,
                    dc.descuento,
                    dc.subtotal,
                    c.total,
                    c.fecha,
                    u.nombres as usuario
                FROM compras c
                INNER JOIN detalle_compras dc ON c.id = dc.id_compra
                LEFT JOIN proveedores prov ON c.id_proveedor = prov.id
                LEFT JOIN almacenes alm ON c.id_almacen = alm.id
                LEFT JOIN usuarios u ON c.id_usuario = u.id
                WHERE c.estado = 'COMPLETADO'";

        if ($desde && $hasta) {
            $sql .= " AND DATE(c.fecha) BETWEEN '$desde' AND '$hasta'";
        }

        $sql .= " ORDER BY c.fecha DESC";
        return $this->selectAll($sql);
    }

    public function getDatos($table)
    {
        $sql = "SELECT * FROM $table WHERE estado = 1";
        return $this->selectAll($sql);
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion LIMIT 1";
        return $this->select($sql);
    }
}
?>