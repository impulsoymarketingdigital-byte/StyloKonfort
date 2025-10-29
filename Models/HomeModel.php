<?php
class HomeModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getNuevosProductos()
    {
        $sql = "SELECT * FROM productos WHERE estado = 1 ORDER BY id DESC LIMIT 18";
        return $this->selectAll($sql);
    }

    public function getCalificacion($accion, $id)
    {
        $sql = "SELECT $accion(cantidad) AS total FROM calificaciones WHERE id_producto = $id";
        return $this->select($sql);
    }

    public function getTestimonial()
    {
        $sql = "SELECT t.mensaje, cl.nombre, cl.perfil FROM testimonial t 
                INNER JOIN clientes cl ON t.id_cliente = cl.id 
                ORDER BY RAND() LIMIT 12";
        return $this->selectAll($sql);
    }

    public function getPrimeraImagen($id_producto)
    {
        $directorio = 'assets/images/productos/' . $id_producto;

        if (file_exists($directorio)) {
            $imagenes = scandir($directorio);
            if (false !== $imagenes) {
                foreach ($imagenes as $file) {
                    if ('.' != $file && '..' != $file) {
                        return $directorio . '/' . $file;
                    }
                }
            }
        }
        return null;
    }

    public function getSizeColor($table, $id)
    {
        $sql = "SELECT * FROM $table WHERE id = $id";
        return $this->select($sql);
    }

    // ⭐ CORREGIDO: Solo productos que existen en la tabla productos
    public function getProductosEspeciales()
    {
        $sql = "SELECT d.producto, d.id_producto, p.id, p.nombre, p.slug, p.precio_venta
                FROM detalle_pedidos d
                INNER JOIN productos p ON d.id_producto = p.id
                WHERE p.estado = 1
                GROUP BY d.id_producto, d.producto, p.id, p.nombre, p.slug, p.precio_venta
                ORDER BY COUNT(d.id_producto) DESC
                LIMIT 18";
        return $this->selectAll($sql);
    }

    public function getProducto($idProducto)
    {
        $sql = "SELECT * FROM productos WHERE id = $idProducto AND estado = 1";
        return $this->select($sql);
    }

    // ⭐ CORREGIDO: Solo productos que existen en la tabla productos
    public function getProductosDestacados()
    {
        $sql = "SELECT SUM(c.cantidad) AS cantidad, c.id_producto, 
                       p.id, p.nombre, p.slug, p.precio_venta
                FROM calificaciones c
                INNER JOIN productos p ON c.id_producto = p.id
                WHERE p.estado = 1
                GROUP BY c.id_producto, p.id, p.nombre, p.slug, p.precio_venta
                ORDER BY cantidad DESC
                LIMIT 18";
        return $this->selectAll($sql);
    }

    public function getSliders()
    {
        $sql = "SELECT * FROM sliders";
        return $this->selectAll($sql);
    }
}
?>