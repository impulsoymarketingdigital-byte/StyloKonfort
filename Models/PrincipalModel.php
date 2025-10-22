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

    public function getMarcas()
    {
        $sql = "SELECT * FROM marcas WHERE estado = 1 ORDER BY marca ASC";
        return $this->selectAll($sql);
    }

    public function getSlug($table, $slug)
    {
        $sql = "SELECT * FROM $table WHERE slug = '$slug'";
        return $this->select($sql);
    }
    public function getProductosCat($id_categoria, $desde, $porPagina)
    {
        $sql = "SELECT * FROM productos WHERE id_categoria = $id_categoria AND estado = 1 ORDER BY id DESC LIMIT $desde, $porPagina";
        return $this->selectAll($sql);
    }
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
    public function getPromocionesActivas()
    {
        $sql = "SELECT * FROM promociones 
            WHERE estado = 1 
            AND CURDATE() >= fecha_inicio 
            AND CURDATE() <= fecha_fin 
            ORDER BY id DESC";
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

    public function getAleatorios($id_categoria, $id_producto)
    {
        $sql = "SELECT * FROM productos WHERE id_categoria = $id_categoria AND estado = 1 AND id != $id_producto ORDER BY RAND() LIMIT 20";
        return $this->selectAll($sql);
    }
    public function getBusqueda($valor)
    {
        $sql = "SELECT * FROM productos WHERE nombre LIKE '%" . $valor . "%' OR descripcion LIKE '%" . $valor . "%' LIMIT 6";
        return $this->selectAll($sql);
    }

    public function getTalla($id_producto)
    {
        $sql = "SELECT t.id, t.nombre, t.nombre_corto, 
            COALESCE(SUM(tc.stock), 0) as stock_disponible
            FROM tallas t
            LEFT JOIN tallas_colores tc ON t.id = tc.id_talla 
            AND tc.id_producto = $id_producto 
            AND tc.id_almacen = 1
            GROUP BY t.id, t.nombre, t.nombre_corto
            HAVING stock_disponible > 0 OR t.id IN (
                SELECT DISTINCT id_talla FROM tallas_colores WHERE id_producto = $id_producto
            )";
        return $this->selectAll($sql);
    }

    public function getColores($size, $id_producto)
    {
        $sql = "SELECT c.id, c.nombre, c.color, tc.stock 
            FROM tallas_colores tc 
            INNER JOIN colores c ON tc.id_color = c.id 
            WHERE tc.id_talla = $size 
            AND tc.id_producto = $id_producto 
            AND tc.id_almacen = 1 
            GROUP BY c.id, c.nombre, c.color, tc.stock";
        return $this->selectAll($sql);
    }

    public function getTotalStockProducto($id_producto)
    {
        $sql = "SELECT COALESCE(SUM(stock), 0) as total_stock 
            FROM tallas_colores 
            WHERE id_producto = $id_producto 
            AND id_almacen = 1";
        return $this->select($sql);
    }
    public function getDatos($table)
    {
        $sql = "SELECT * FROM $table WHERE estado = 1";
        return $this->selectAll($sql);
    }

    public function consultaStock($item, $nombre, $id_producto)
    {
        $sql = "SELECT tc.stock, p.precio_venta 
            FROM tallas_colores tc
            INNER JOIN productos p ON p.id = tc.id_producto
            WHERE tc.$item = $nombre 
            AND tc.id_producto = $id_producto
            AND tc.id_almacen = 1";
        return $this->select($sql);
    }

    public function getAtributos($size, $color, $id_producto)
    {
        $sql = "SELECT tc.id, tc.stock, p.precio_venta, t.nombre AS size, c.nombre, c.color, c.color_secundario 
        FROM tallas_colores tc
        INNER JOIN productos p ON tc.id_producto = p.id
        INNER JOIN tallas t ON tc.id_talla = t.id 
        INNER JOIN colores c ON tc.id_color = c.id 
        WHERE tc.id_talla = $size 
        AND tc.id_color = $color 
        AND tc.id_producto = $id_producto
        AND tc.id_almacen = 1";
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


    public function getTotalFiltroProductos($categorias, $precioMin, $precioMax, $colores, $sizes, $marcas)
    {
        $sql = "SELECT COUNT(DISTINCT p.id) AS total 
    FROM productos p 
    INNER JOIN tallas_colores pf ON p.id = pf.id_producto
    LEFT JOIN tallas t ON pf.id_talla = t.id
    LEFT JOIN colores c ON pf.id_color = c.id";

        $sql .= " WHERE p.precio_venta >= $precioMin 
      AND p.precio_venta <= $precioMax 
      AND p.estado = 1 
      AND pf.id_almacen = 1";

        if (!empty($categorias)) {
            $sql .= " AND p.id_categoria IN ($categorias)";
        }
        if (!empty($colores)) {
            $sql .= " AND pf.id_color IN ($colores)";
        }
        if (!empty($sizes)) {
            $sql .= " AND pf.id_talla IN ($sizes)";
        }
        if (!empty($marcas)) {
            $sql .= " AND p.id_marca IN ($marcas)";
        }

        return $this->select($sql);
    }
    public function getFiltroProductos($categorias, $precioMin, $precioMax, $colores, $sizes, $marcas, $desde, $hasta)
    {
        $sql = "SELECT p.*, 
        MIN(pf.id) AS id_detalle, 
        MIN(pf.id_talla) AS id_talla, 
        MIN(pf.id_color) AS id_color, 
        SUM(pf.stock) AS stock,
        MIN(t.nombre) AS size, 
        MIN(c.nombre) AS colornombre, 
        MIN(c.color) AS color,
        MIN(c.color_secundario) AS color_secundario,
        (SELECT COALESCE(SUM(tc.stock), 0) 
         FROM tallas_colores tc 
         WHERE tc.id_producto = p.id AND tc.id_almacen = 1) as stock_total
    FROM productos p 
    INNER JOIN tallas_colores pf ON p.id = pf.id_producto
    LEFT JOIN tallas t ON pf.id_talla = t.id
    LEFT JOIN colores c ON pf.id_color = c.id";

        $sql .= " WHERE p.precio_venta >= $precioMin 
      AND p.precio_venta <= $precioMax 
      AND p.estado = 1 
      AND pf.id_almacen = 1";

        if (!empty($categorias)) {
            $sql .= " AND p.id_categoria IN ($categorias)";
        }
        if (!empty($colores)) {
            $sql .= " AND pf.id_color IN ($colores)";
        }
        if (!empty($sizes)) {
            $sql .= " AND pf.id_talla IN ($sizes)";
        }
        if (!empty($marcas)) {
            $sql .= " AND p.id_marca IN ($marcas)";
        }

        $sql .= " GROUP BY p.id";
        $sql .= " ORDER BY p.id DESC";
        $sql .= " LIMIT $desde, $hasta";

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
    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }
    public function getFiltroProductoss($busqueda, $categorias, $desde, $hasta, $color, $sizes)
    {
        $sql = "SELECT p.*, pf.id AS id_detalle, pf.stock 
            FROM productos p 
            INNER JOIN tallas_colores pf ON p.id = pf.id_producto";

        $sql .= " WHERE p.nombre LIKE '%" . $busqueda . "%' 
              AND p.precio_venta >= $desde 
              AND p.precio_venta <= $hasta 
              AND p.estado = 1 
              AND pf.id_almacen = 1";

        if (!empty($categorias)) {
            $sql .= " AND p.id_categoria IN ($categorias)";
        }
        if (!empty($color)) {
            $sql .= " AND pf.id_color = '" . $color . "'";
        }
        if (!empty($sizes)) {
            $sql .= " AND pf.id_talla IN ($sizes)";
        }

        $sql .= " LIMIT 1";

        return $this->selectAll($sql);
    }
}
