<?php
class ProductosModel extends Query
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getProductos()
    {
        $sql = "SELECT 
                p.*,
                c.categoria,
                m.marca,
                p.estado,
                p.created_at 
            FROM productos p
            INNER JOIN categorias c ON p.id_categoria = c.id
            INNER JOIN marcas m ON p.id_marca = m.id";
        return $this->selectAll($sql);
    }

    public function getDatos($table)
    {
        $sql = "SELECT * FROM $table WHERE estado = 1";
        return $this->selectAll($sql);
    }

    public function eliminar($estado, $id)
    {
        $sql = "UPDATE productos SET estado = ? WHERE id = ?";
        $array = array($estado, $id);
        return $this->save($sql, $array);
    }

    public function getProducto($id)
    {
        $sql = "SELECT * FROM productos WHERE id = $id";
        return $this->select($sql);
    }
    public function registrar($codigo, $nombre, $slug, $descripcion, $genero, $precio_compra, $precio_venta, $precio_mayorista, $id_categoria, $id_marca)
    {
        $id_sucursal = $_SESSION['id_sucursal'];
        $sql = "INSERT INTO productos (codigo, nombre, slug, descripcion, genero, precio_compra, precio_venta, precio_mayorista, id_categoria, id_marca, id_sucursal) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $array = array($codigo, $nombre, $slug, $descripcion, $genero, $precio_compra, $precio_venta, $precio_mayorista, $id_categoria, $id_marca, $id_sucursal);
        return $this->insertar($sql, $array);
    }

    public function modificar($codigo, $nombre, $slug, $descripcion, $genero, $precio_compra, $precio_venta, $precio_mayorista, $id_categoria, $id_marca, $id)
    {
        $id_sucursal = $_SESSION['id_sucursal'];
        $sql = "UPDATE productos 
        SET codigo=?, nombre=?, slug=?, descripcion=?, genero=?, precio_compra=?, precio_venta=?, precio_mayorista=?, id_categoria=?, id_marca=?, id_sucursal=?
        WHERE id = ?";
        $array = array($codigo, $nombre, $slug, $descripcion, $genero, $precio_compra, $precio_venta, $precio_mayorista, $id_categoria, $id_marca, $id_sucursal, $id);
        return $this->save($sql, $array);
    }

    public function getValidar($campo, $valor, $accion, $id)
    {
        if ($accion == 'registrar' && $id == 0) {
            $sql = "SELECT id FROM productos WHERE $campo = '$valor'";
        } else {
            $sql = "SELECT id FROM productos WHERE $campo = '$valor' AND id != $id";
        }
        return $this->select($sql);
    }

    public function getAtributos($id_producto)
    {
        $sql = "SELECT d.id,
                   d.stock,
                   t.nombre AS talla,
                   c.nombre AS color,
                   c.color AS codigo_color,
                   a.nombre AS almacen,
                   s.nombre AS sucursal
            FROM tallas_colores d
            INNER JOIN tallas t ON d.id_talla = t.id 
            INNER JOIN colores c ON d.id_color = c.id 
            INNER JOIN almacenes a ON d.id_almacen = a.id
            INNER JOIN sucursales s ON a.id_sucursal = s.id
            WHERE d.id_producto = $id_producto 
            ORDER BY d.id DESC";

        return $this->selectAll($sql);
    }


    public function getTotalStock($id_producto)
    {
        $sql = "SELECT SUM(cantidad) AS total FROM tallas_colores WHERE id_producto = $id_producto";
        return $this->select($sql);
    }

    public function getVerificar($talla, $color, $id_producto, $id_almacen)
    {
        $sql = "SELECT * FROM tallas_colores WHERE id_talla = $talla AND id_color = $color AND id_producto = $id_producto AND id_almacen = $id_almacen";
        return $this->select($sql);
    }

    public function registrarMantenimiento($talla, $color, $id_almacen, $id_producto)
    {
        $sql = "INSERT INTO tallas_colores (id_talla, id_color, id_producto, id_almacen) 
            VALUES (?, ?, ?, ?)";
        $array = array($talla, $color, $id_producto, $id_almacen);
        return $this->insertar($sql, $array);
    }

    public function registrarInventario($id_producto, $id_talla, $id_color, $id_sucursal, $precio)
    {
        $cantidad = 0;
        $estado = 1;

        $sql = "INSERT INTO inventarios (id_producto, id_talla, id_color, id_sucursal, cantidad, precio_compra, precio_venta, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $array = array($id_producto, $id_talla, $id_color, $id_sucursal, $cantidad, $precio, $precio, $estado);
        return $this->insertar($sql, $array);
    }


    public function eliminarDetalle($id)
    {
        $sql = "DELETE FROM tallas_colores WHERE id = ?";
        $array = array($id);
        return $this->save($sql, $array);
    }

    public function getStock()
    {
        $sql = "SELECT 
                p.codigo,
                p.nombre as producto,
                c.categoria,
                m.marca,
                t.nombre as talla,
                col.nombre as color,
                col.color as codigo_color,
                col.color_secundario,
                a.nombre as almacen,
                tc.stock,
                p.precio_compra,
                p.precio_venta,
                p.precio_mayorista,
                (tc.stock * p.precio_compra) as valor_stock
            FROM tallas_colores tc
            INNER JOIN productos p ON tc.id_producto = p.id
            INNER JOIN tallas t ON tc.id_talla = t.id
            INNER JOIN colores col ON tc.id_color = col.id
            INNER JOIN almacenes a ON tc.id_almacen = a.id
            LEFT JOIN categorias c ON p.id_categoria = c.id
            LEFT JOIN marcas m ON p.id_marca = m.id
            WHERE p.estado = 1 AND a.estado = 1
            ORDER BY a.nombre, p.nombre, t.nombre, col.nombre";
        return $this->selectAll($sql);
    }

    public function getStockPdf($id_almacen = null)
    {
        $sql = "SELECT 
                p.codigo,
                p.nombre as producto,
                c.categoria,
                m.marca,
                t.nombre as talla,
                col.nombre as color,
                a.nombre as almacen,
                tc.stock,
                p.precio_compra,
                p.precio_venta,
                p.precio_mayorista,
                (tc.stock * p.precio_compra) as valor_stock
            FROM tallas_colores tc
            INNER JOIN productos p ON tc.id_producto = p.id
            INNER JOIN tallas t ON tc.id_talla = t.id
            INNER JOIN colores col ON tc.id_color = col.id
            INNER JOIN almacenes a ON tc.id_almacen = a.id
            LEFT JOIN categorias c ON p.id_categoria = c.id
            LEFT JOIN marcas m ON p.id_marca = m.id
            WHERE p.estado = 1 AND a.estado = 1";

        if ($id_almacen && $id_almacen != '') {
            $sql .= " AND tc.id_almacen = $id_almacen";
        }

        $sql .= " ORDER BY a.nombre, p.nombre, t.nombre, col.nombre";
        return $this->selectAll($sql);
    }

    public function getAlmacenNombre($id)
    {
        $sql = "SELECT nombre FROM almacenes WHERE id = $id";
        $result = $this->select($sql);
        return $result ? $result['nombre'] : 'TODOS';
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion LIMIT 1";
        return $this->select($sql);
    }

}

?>