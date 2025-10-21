<?php
class HomeModel extends Query{
 
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
        $sql = "SELECT t.mensaje, cl.nombre, cl.perfil FROM testimonial t INNER JOIN clientes cl ON t.id_cliente = cl.id ORDER BY RAND() LIMIT 12";
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

    public function getProductosEspeciales()
    {
        $sql = "SELECT d.producto, d.id_producto FROM pedidos p
        INNER JOIN detalle_pedidos d ON p.id = d.id_pedido
        GROUP BY d.producto, d.id_producto";
        return $this->selectAll($sql);
    }

    public function getProducto($idProducto)
    {
        $sql = "SELECT * FROM productos WHERE id = $idProducto";
        return $this->select($sql);
    }

    public function getProductosDestacados()
    {
        $sql = "SELECT SUM(cantidad) AS cantidad, id_producto
        FROM calificaciones GROUP BY id_producto";
        return $this->selectAll($sql);
    }

    public function getSliders()
    {
        $sql = "SELECT * FROM sliders";
        return $this->selectAll($sql);
    }
}
 
?>