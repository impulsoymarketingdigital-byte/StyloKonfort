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
                p.id,
                p.nombre,
                p.descripcion,
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

    public function registrar($codigo, $nombre, $slug, $descripcion, $genero, $categoria, $marca)
    {
        $id_sucursal = $_SESSION['id_sucursal'];

        $sql = "INSERT INTO productos (codigo, nombre, slug, descripcion, genero, id_categoria, id_marca, id_sucursal) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $array = array($codigo, $nombre, $slug, $descripcion, $genero, $categoria, $marca, $id_sucursal);
        return $this->insertar($sql, $array);
    }

    public function modificar($codigo, $nombre, $slug, $descripcion, $genero, $categoria, $marca, $id)
    {
        $id_sucursal = $_SESSION['id_sucursal'];

        $sql = "UPDATE productos 
            SET codigo=?, nombre=?, slug=?, descripcion=?, genero=?, id_categoria=?, id_marca=?, id_sucursal=?
            WHERE id = ?";
        $array = array($codigo, $nombre, $slug, $descripcion, $genero, $categoria, $marca, $id_sucursal, $id);
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
        $id_sucursal = $_SESSION['id_sucursal'];

        $sql = "SELECT d.id, d.cantidad, d.precio, 
                   t.nombre AS talla, 
                   c.nombre, c.color 
            FROM tallas_colores d 
            INNER JOIN tallas t ON d.id_talla = t.id 
            INNER JOIN colores c ON d.id_color = c.id 
            WHERE d.id_producto = $id_producto 
              AND d.id_sucursal = $id_sucursal
            ORDER BY d.id DESC";

        return $this->selectAll($sql);
    }

    public function getTotalStock($id_producto)
    {
        $sql = "SELECT SUM(cantidad) AS total FROM tallas_colores WHERE id_producto = $id_producto";
        return $this->select($sql);
    }

    public function getVerificar($talla, $color, $id_producto, $id_sucursal)
    {
        $sql = "SELECT * FROM tallas_colores WHERE id_talla = $talla AND id_color = $color AND id_producto = $id_producto AND id_sucursal = $id_sucursal";
        return $this->select($sql);
    }

    public function registrarMantenimiento($talla, $color, $price, $id_producto, $id_sucursal)
    {
        $sql = "INSERT INTO tallas_colores (id_talla, id_color, precio, id_producto, id_sucursal) VALUES (?, ?, ?, ?, ?)";
        $array = array($talla, $color, $price, $id_producto, $id_sucursal);
        return $this->insertar($sql, $array);
    }
    public function actualizarMantenimiento($talla, $color, $price, $id_producto, $id_sucursal)
    {
        $sql = "UPDATE tallas_colores SET precio = ? WHERE id_talla = ? AND id_color = ? AND id_producto = ? AND id_sucursal = ?";
        $array = array($price, $talla, $color, $id_producto, $id_sucursal);
        return $this->save($sql, $array);
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
}

?>