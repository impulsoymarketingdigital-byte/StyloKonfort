<?php
class PrincipalModel extends Query
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getProducto($id_producto)
    {
        $sql = "SELECT p.*, c.categoria FROM productos p INNER JOIN categorias c ON p.id_categoria = c.id WHERE p.id = $id_producto";
        return $this->select($sql);
    }
    public function getCategorias()
    {
        $sql = "SELECT * FROM categorias WHERE estado = 1";
        return $this->selectAll($sql);
    }
    public function getSlug($table, $slug)
    {
        $sql = "SELECT * FROM $table WHERE slug = '$slug'";
        return $this->select($sql);
    }
    //productos relacionados con la categoria
    public function getProductosCat($id_categoria, $desde, $porPagina)
    {
        $sql = "SELECT * FROM productos WHERE id_categoria = $id_categoria AND estado = 1 ORDER BY id DESC LIMIT $desde, $porPagina";
        return $this->selectAll($sql);
    }
    //obtener total productos relacionados con la categoria
    public function getTotalProductosCat($id_categoria)
    {
        $sql = "SELECT COUNT(*) AS total FROM productos WHERE id_categoria = $id_categoria AND estado = 1";
        return $this->select($sql);
    }
    //paginacion
    public function getProductos($desde, $hasta)
    {
        $sql = "SELECT * FROM productos WHERE estado = 1 ORDER BY id DESC LIMIT $desde, $hasta";
        return $this->selectAll($sql);
    }

    public function getTotalProductos()
    {
        $sql = "SELECT COUNT(*) AS total FROM productos WHERE estado = 1";
        return $this->select($sql);
    }

    public function getNuevosProductos()
    {
        $sql = "SELECT * FROM productos WHERE estado = 1 ORDER BY id DESC LIMIT 10";
        return $this->selectAll($sql);
    }

    public function getTestimonial()
    {
        $sql = "SELECT t.mensaje, cl.nombre, cl.perfil FROM testimonial t INNER JOIN clientes cl ON t.id_cliente = cl.id ORDER BY RAND() LIMIT 12";
        return $this->selectAll($sql);
    }

    //productos relacionados aleatorios
    public function getAleatorios($id_categoria, $id_producto)
    {
        $sql = "SELECT * FROM productos WHERE id_categoria = $id_categoria AND estado = 1 AND id != $id_producto ORDER BY RAND() LIMIT 20";
        return $this->selectAll($sql);
    }
    //busqueda de productos
    public function getBusqueda($valor)
    {
        $sql = "SELECT * FROM productos WHERE nombre LIKE '%" . $valor . "%' OR descripcion LIKE '%" . $valor . "%' LIMIT 6";
        return $this->selectAll($sql);
    }

    public function getTalla($id_producto)
    {
        $sql = "SELECT t.id, t.nombre, t.nombre_corto FROM tallas_colores d INNER JOIN tallas t ON d.id_talla = t.id WHERE d.id_producto = $id_producto GROUP BY d.id_talla";
        return $this->selectAll($sql);
    }

    public function getColores($size, $id_producto)
    {
        $sql = "SELECT c.id, c.nombre,c.color FROM tallas_colores d INNER JOIN colores c ON d.id_color = c.id WHERE d.id_talla = $size AND d.id_producto = $id_producto GROUP BY d.id_color";
        return $this->selectAll($sql);
    }

    public function getDatos($table)
    {
        $sql = "SELECT * FROM $table WHERE estado = 1";
        return $this->selectAll($sql);
    }

    public function consultaStock($item, $nombre, $id_producto)
    {
        $sql = "SELECT cantidad, precio FROM tallas_colores WHERE $item = $nombre AND id_producto = $id_producto";
        return $this->select($sql);
    }

    public function getAtributos($size, $color, $id_producto)
    {
        $sql = "SELECT 
                d.stock, 
                p.precio_venta, 
                t.nombre_corto, 
                c.nombre, 
                c.color
            FROM tallas_colores d
            INNER JOIN productos p ON p.id = d.id_producto
            INNER JOIN tallas t ON d.id_talla = t.id
            INNER JOIN colores c ON d.id_color = c.id
            WHERE d.id_talla = $size 
              AND d.id_color = $color 
              AND d.id_producto = $id_producto";

        return $this->select($sql);
    }


    public function getColorSize($table, $id)
    {
        $sql = "SELECT * FROM $table WHERE id = $id";
        return $this->select($sql);
    }

    public function getCalificacion($accion, $id)
    {
        $sql = "SELECT $accion(cantidad) AS total FROM calificaciones WHERE id_producto = $id";
        return $this->select($sql);
    }

    public function getFiltroProductos($categorias, $precioMin, $precioMax, $color, $sizes, $desde, $hasta)
    {
        $sql = "SELECT p.*, pf.id AS id_detalle, pf.id_talla, id_color, t.nombre AS size, c.nombre AS colornombre, c.color
            FROM productos p 
            INNER JOIN tallas_colores pf ON p.id = pf.id_producto
            LEFT JOIN tallas t ON pf.id_talla = t.id
            LEFT JOIN colores c ON pf.id_color = c.id";

        $sql .= " WHERE p.precio_venta >= $precioMin AND p.precio_venta <= $precioMax AND p.estado = 1";

        if (!empty($categorias)) {
            $sql .= " AND p.id_categoria IN ($categorias)";
        }
        if (!empty($color)) {
            $sql .= " AND pf.id_color = '" . $color . "'";
        }
        if (!empty($sizes)) {
            $sql .= " AND pf.id_talla IN ($sizes)";
        }
        $sql .= " LIMIT $desde, $hasta";

        return $this->selectAll($sql);
    }

    public function getTotalFiltroProductos($categorias, $precioMin, $precioMax, $color, $sizes)
    {
        $sql = "SELECT COUNT(p.id) AS total FROM productos p 
            INNER JOIN tallas_colores pf ON p.id = pf.id_producto
            LEFT JOIN tallas t ON pf.id_talla = t.id
            LEFT JOIN colores c ON pf.id_color = c.id";

        $sql .= " WHERE p.precio_venta >= $precioMin AND p.precio_venta <= $precioMax AND p.estado = 1";

        if (!empty($categorias)) {
            $sql .= " AND p.id_categoria IN ($categorias)";
        }
        if (!empty($color)) {
            $sql .= " AND pf.id_color = '" . $color . "'";
        }
        if (!empty($sizes)) {
            $sql .= " AND pf.id_talla IN ($sizes)";
        }
        return $this->select($sql);
    }

    public function getFiltroProductoss($busqueda, $categorias, $desde, $hasta, $color, $sizes)
    {
        $sql = "SELECT p.*, pf.id AS id_detalle FROM productos p 
        INNER JOIN tallas_colores pf ON p.id = pf.id_producto";

        $sql .= " WHERE p.nombre LIKE '%" . $busqueda . "' AND p.precio_venta >= $desde AND p.precio_venta <= $hasta AND p.estado = 1";

        if (!empty($categorias)) {
            $sql .= " AND p.id_categoria IN ($categorias)";
        }
        if (!empty($color)) {
            $sql .= " AND pf.id_color = '" . $color . "'";
        }
        if (!empty($sizes)) {
            $sql .= " AND pf.id_talla IN ($sizes)";
        }
        $sql .= " LIMIT 1"; // Limitar a 12 resultados

        return $this->selectAll($sql);
    }
}
