<?php
class ComprasModel extends Query
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

    public function buscarProveedor($valor)
    {
        $sql = "SELECT * FROM proveedores WHERE nombre LIKE '%" . $valor . "%' AND estado = 1 LIMIT 10";
        return $this->selectAll($sql);
    }

    public function getProducto($idProducto)
    {
        $sql = "SELECT * FROM productos WHERE id = $idProducto";
        return $this->select($sql);
    }

    public function getColores($size, $id_producto)
    {
        $sql = "SELECT c.id, c.nombre FROM tallas_colores d 
                INNER JOIN colores c ON d.id_color = c.id 
                WHERE d.id_talla = $size AND d.id_producto = $id_producto 
                GROUP BY d.id_color";
        return $this->selectAll($sql);
    }

    public function getSizes($idProducto)
    {
        $sql = "SELECT s.id, s.nombre FROM tallas_colores t 
                INNER JOIN tallas s ON t.id_talla = s.id 
                WHERE t.id_producto = $idProducto 
                GROUP BY t.id_talla";
        return $this->selectAll($sql);
    }

    public function getAtributos($size, $color, $id_producto, $id_almacen = 1)
    {
        $sql = "SELECT tc.id, tc.stock, p.precio_compra, t.nombre AS size, c.nombre, c.color 
                FROM tallas_colores tc
                INNER JOIN productos p ON tc.id_producto = p.id
                INNER JOIN tallas t ON tc.id_talla = t.id 
                INNER JOIN colores c ON tc.id_color = c.id 
                WHERE tc.id_talla = $size 
                AND tc.id_color = $color 
                AND tc.id_producto = $id_producto
                AND tc.id_almacen = $id_almacen";
        return $this->select($sql);
    }

    public function crearTallaColorEnAlmacen($id_talla, $id_color, $id_producto, $id_almacen)
    {
        $sql = "INSERT INTO tallas_colores (id_talla, id_color, id_producto, id_almacen, stock, created_at, updated_at) 
                VALUES (?, ?, ?, ?, 0, NOW(), NOW())";
        $datos = array($id_talla, $id_color, $id_producto, $id_almacen);
        return $this->insertar($sql, $datos);
    }

    public function getOrCreateAtributos($size, $color, $id_producto, $id_almacen = 1)
    {
        $atributo = $this->getAtributos($size, $color, $id_producto, $id_almacen);
        
        if (empty($atributo)) {
            $nuevo_id = $this->crearTallaColorEnAlmacen($size, $color, $id_producto, $id_almacen);
            
            if ($nuevo_id > 0) {
                $atributo = $this->getAtributos($size, $color, $id_producto, $id_almacen);
            }
        }
        
        return $atributo;
    }

    public function registrarCompra($numero_compra, $tipo_comprobante, $total, $descuento, $fecha, $id_proveedor, $id_almacen, $id_usuario)
    {
        $sql = "INSERT INTO compras (numero_compra, tipo_comprobante, total, descuento, fecha, id_proveedor, id_almacen, id_usuario) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $array = array($numero_compra, $tipo_comprobante, $total, $descuento, $fecha, $id_proveedor, $id_almacen, $id_usuario);
        return $this->insertar($sql, $array);
    }

    public function registrarDetalleCompra($id_compra, $id_producto, $nombre, $precio_compra, $cantidad, $descuento, $subtotal, $id_talla_color)
    {
        $sql = "INSERT INTO detalle_compras (id_compra, id_producto, producto, precio_compra, cantidad, descuento, subtotal, id_talla_color) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $datos = array($id_compra, $id_producto, $nombre, $precio_compra, $cantidad, $descuento, $subtotal, $id_talla_color);
        return $this->insertar($sql, $datos);
    }

    public function actualizarStockDetalle($stock, $id_talla_color)
    {
        $sql = "UPDATE tallas_colores SET stock = ?, updated_at = NOW() WHERE id = ?";
        $datos = array($stock, $id_talla_color);
        return $this->save($sql, $datos);
    }

    public function actualizarPrecioCompra($precio_compra, $id_producto)
    {
        $sql = "UPDATE productos SET precio_compra = ? WHERE id = ?";
        $datos = array($precio_compra, $id_producto);
        return $this->save($sql, $datos);
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }

    public function getCompras()
    {
        $sql = "SELECT c.*, p.nombre AS proveedor, a.nombre AS almacen 
                FROM compras c 
                INNER JOIN proveedores p ON c.id_proveedor = p.id 
                INNER JOIN almacenes a ON c.id_almacen = a.id 
                ORDER BY c.id DESC";
        return $this->selectAll($sql);
    }

    public function getCompra($idCompra)
    {
        $sql = "SELECT c.*, p.nombre AS proveedor, p.ruc, p.telefono, p.direccion, p.email,
                a.nombre AS almacen
                FROM compras c 
                INNER JOIN proveedores p ON c.id_proveedor = p.id 
                INNER JOIN almacenes a ON c.id_almacen = a.id 
                WHERE c.id = $idCompra";
        return $this->select($sql);
    }

    public function getDetalleCompra($idCompra)
    {
        $sql = "SELECT dc.*, 
                t.nombre AS talla, t.nombre_corto,
                c.nombre AS color_nombre, c.color AS color_hexa
                FROM detalle_compras dc
                LEFT JOIN tallas_colores tc ON dc.id_talla_color = tc.id
                LEFT JOIN tallas t ON tc.id_talla = t.id
                LEFT JOIN colores c ON tc.id_color = c.id
                WHERE dc.id_compra = $idCompra";
        return $this->selectAll($sql);
    }

    public function getTallaColorPorId($id)
    {
        $sql = "SELECT * FROM tallas_colores WHERE id = $id";
        return $this->select($sql);
    }

    public function anular($idCompra)
    {
        $sql = "UPDATE compras SET estado = ? WHERE id = ?";
        $array = array('ANULADO', $idCompra);
        return $this->save($sql, $array);
    }

    public function getProveedores()
    {
        $sql = "SELECT * FROM proveedores WHERE estado = 1";
        return $this->selectAll($sql);
    }

    public function getAlmacenes()
    {
        $sql = "SELECT * FROM almacenes WHERE estado = 1";
        return $this->selectAll($sql);
    }

    public function generarNumeroCompra()
    {
        $anio = date('y');
        
        $sql = "SELECT numero_compra FROM compras 
                WHERE numero_compra LIKE 'COMP-" . $anio . "-%' 
                ORDER BY id DESC 
                LIMIT 1";
        
        $result = $this->select($sql);
        
        if ($result && isset($result['numero_compra'])) {
            $partes = explode('-', $result['numero_compra']);
            $ultimo = (int) end($partes);
            $nuevo = $ultimo + 1;
        } else {
            $nuevo = 1;
        }
        
        return 'COMP-' . $anio . '-' . $nuevo;
    }
}
?>