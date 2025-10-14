<?php
class TraspasosModel extends Query
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

    public function getProducto($idProducto)
    {
        $sql = "SELECT * FROM productos WHERE id = $idProducto";
        return $this->select($sql);
    }

    public function getColores($size, $id_producto, $id_almacen)
    {
        $sql = "SELECT c.id, c.nombre FROM tallas_colores d 
                INNER JOIN colores c ON d.id_color = c.id 
                WHERE d.id_talla = $size 
                AND d.id_producto = $id_producto 
                AND d.id_almacen = $id_almacen
                AND d.stock > 0
                GROUP BY d.id_color";
        return $this->selectAll($sql);
    }

    public function getSizes($idProducto, $id_almacen)
    {
        $sql = "SELECT s.id, s.nombre FROM tallas_colores t 
                INNER JOIN tallas s ON t.id_talla = s.id 
                WHERE t.id_producto = $idProducto 
                AND t.id_almacen = $id_almacen
                AND t.stock > 0
                GROUP BY t.id_talla";
        return $this->selectAll($sql);
    }

    public function getAtributos($size, $color, $id_producto, $id_almacen)
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

    public function getOrCreateAtributos($size, $color, $id_producto, $id_almacen)
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

    public function registrarTraspaso($numero_traspaso, $total_productos, $fecha, $id_almacen_origen, $id_almacen_destino, $id_usuario)
    {
        $sql = "INSERT INTO traspasos (numero_traspaso, total_productos, fecha, id_almacen_origen, id_almacen_destino, id_usuario) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $array = array($numero_traspaso, $total_productos, $fecha, $id_almacen_origen, $id_almacen_destino, $id_usuario);
        return $this->insertar($sql, $array);
    }

    public function registrarDetalleTraspaso($id_traspaso, $id_producto, $nombre, $cantidad, $id_talla_color_origen, $id_talla_color_destino)
    {
        $sql = "INSERT INTO detalle_traspasos (id_traspaso, id_producto, producto, cantidad, id_talla_color_origen, id_talla_color_destino) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $datos = array($id_traspaso, $id_producto, $nombre, $cantidad, $id_talla_color_origen, $id_talla_color_destino);
        return $this->insertar($sql, $datos);
    }

    public function actualizarStockDetalle($stock, $id_talla_color)
    {
        $sql = "UPDATE tallas_colores SET stock = ?, updated_at = NOW() WHERE id = ?";
        $datos = array($stock, $id_talla_color);
        return $this->save($sql, $datos);
    }

    public function getTraspasos()
    {
        $sql = "SELECT t.*, 
                ao.nombre AS almacen_origen, 
                ad.nombre AS almacen_destino,
                u.nombres AS usuario
                FROM traspasos t 
                INNER JOIN almacenes ao ON t.id_almacen_origen = ao.id 
                INNER JOIN almacenes ad ON t.id_almacen_destino = ad.id 
                LEFT JOIN usuarios u ON t.id_usuario = u.id
                ORDER BY t.id DESC";
        return $this->selectAll($sql);
    }

    public function getTraspaso($idTraspaso)
    {
        $sql = "SELECT t.*, 
                ao.nombre AS almacen_origen, 
                ad.nombre AS almacen_destino,
                u.nombres AS nombres
                FROM traspasos t 
                INNER JOIN almacenes ao ON t.id_almacen_origen = ao.id 
                INNER JOIN almacenes ad ON t.id_almacen_destino = ad.id 
                INNER JOIN usuarios u ON t.id_usuario = u.id
                WHERE t.id = $idTraspaso";
        return $this->select($sql);
    }

    public function getDetalleTraspaso($idTraspaso)
    {
        $sql = "SELECT dt.*, 
                t.nombre AS talla, t.nombre_corto,
                c.nombre AS color_nombre, c.color AS color_hexa
                FROM detalle_traspasos dt
                LEFT JOIN tallas_colores tc ON dt.id_talla_color_origen = tc.id
                LEFT JOIN tallas t ON tc.id_talla = t.id
                LEFT JOIN colores c ON tc.id_color = c.id
                WHERE dt.id_traspaso = $idTraspaso";
        return $this->selectAll($sql);
    }

    public function getTallaColorPorId($id)
    {
        $sql = "SELECT * FROM tallas_colores WHERE id = $id";
        return $this->select($sql);
    }

    public function anular($idTraspaso)
    {
        $sql = "UPDATE traspasos SET estado = ? WHERE id = ?";
        $array = array('ANULADO', $idTraspaso);
        return $this->save($sql, $array);
    }

    public function getAlmacenes()
    {
        $sql = "SELECT * FROM almacenes WHERE estado = 1";
        return $this->selectAll($sql);
    }

    public function generarNumeroTraspaso()
    {
        $anio = date('y');
        
        $sql = "SELECT numero_traspaso FROM traspasos 
                WHERE numero_traspaso LIKE 'TRSP-" . $anio . "-%' 
                ORDER BY id DESC 
                LIMIT 1";
        
        $result = $this->select($sql);
        
        if ($result && isset($result['numero_traspaso'])) {
            $partes = explode('-', $result['numero_traspaso']);
            $ultimo = (int) end($partes);
            $nuevo = $ultimo + 1;
        } else {
            $nuevo = 1;
        }
        
        return 'TRSP-' . $anio . '-' . str_pad($nuevo, 4, '0', STR_PAD_LEFT);
    }
}
?>