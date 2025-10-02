<?php
class VentasModel extends Query{
    public function __construct() {
        parent::__construct();
    }
    public function buscarPorNombre($valor)
    {
        $sql = "SELECT * FROM productos WHERE nombre LIKE '%".$valor."%' AND estado = 1 LIMIT 10";
        return $this->selectAll($sql);
    }
    public function buscarCliente($valor)
    {
        $sql = "SELECT * FROM clientes WHERE nombre LIKE '%".$valor."%' AND estado = 1 LIMIT 10";
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
        $sql = "SELECT d.cantidad, d.precio, t.nombre AS size, c.nombre, c.color FROM tallas_colores d INNER JOIN tallas t ON d.id_talla = t.id INNER JOIN colores c ON d.id_color = c.id WHERE d.id_talla = $size AND d.id_color = $color AND d.id_producto = $id_producto";
        return $this->select($sql);
    }
    public function actualizarStockProducto($cantidad, $ventas, $idProducto)
    {
        $sql = "UPDATE productos SET cantidad = ?, ventas=? WHERE id = ?";
        $array = array($cantidad, $ventas, $idProducto);
        return $this->save($sql, $array);
    }

    public function actualizarStockDetalle($stock, $size, $color, $id_producto)
    {
        $sql = "UPDATE tallas_colores SET cantidad=? WHERE id_talla=? AND id_color=? AND id_producto=?";
        $datos = array($stock, $size, $color, $id_producto);
        return $this->save($sql, $datos);
    }
    
    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }

    public function getVenta($idVenta)
    {
        $sql = "SELECT v.*, c.nombre, c.apellido, c.telefono, c.direccion FROM ventas v INNER JOIN clientes c ON v.id_cliente = c.id WHERE v.id = $idVenta";
        return $this->select($sql);
    }

    public function getVentas()
    {
        $sql = "SELECT v.*, CONCAT(c.nombre,' ',c.apellido) AS nombre FROM ventas v INNER JOIN clientes c ON v.id_cliente = c.id";
        return $this->selectAll($sql);
    }

    public function anular($idVenta)
    {
        $sql = "UPDATE ventas SET estado = ? WHERE id = ?";
        $array = array(0, $idVenta);
        return $this->save($sql, $array);
    }

    public function getReporte($desde, $hasta, $id_usuario)
    {
        $sql = "SELECT v.*, CONCAT(c.nombre, ' ', c.apellido) AS nombre FROM ventas v INNER JOIN clientes c ON v.id_cliente = c.id WHERE v.fecha BETWEEN '$desde' AND '$hasta' AND v.id_usuario = $id_usuario";
        return $this->selectAll($sql);
    }
}


?>