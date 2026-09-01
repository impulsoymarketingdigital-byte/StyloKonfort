<?php
class PrincipalModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getProducto($id_producto)
    {
        $sql = "SELECT p.*, c.categoria FROM productos p INNER JOIN categorias c ON p.id_categoria = c.id WHERE p.id = ?";
        return $this->select($sql, [$id_producto]);
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
        // Las tablas no se pueden preparar directamente con ?, validamos la tabla por seguridad (aunque suele ser estática)
        $tablasPermitidas = ['productos', 'categorias'];
        if (!in_array($table, $tablasPermitidas)) {
            return false;
        }
        $sql = "SELECT * FROM $table WHERE slug = ?";
        return $this->select($sql, [$slug]);
    }

    public function getProductosCat($id_categoria, $desde, $porPagina)
    {
        // LIMIT requiere parámetros numéricos, se validan explícitamente.
        $desde = (int)$desde;
        $porPagina = (int)$porPagina;
        $sql = "SELECT * FROM productos WHERE id_categoria = ? AND estado = 1 ORDER BY id DESC LIMIT $desde, $porPagina";
        return $this->selectAll($sql, [$id_categoria]);
    }

    public function getTotalProductosCat($id_categoria)
    {
        $sql = "SELECT COUNT(*) AS total FROM productos WHERE id_categoria = ? AND estado = 1";
        return $this->select($sql, [$id_categoria]);
    }

    public function getProductos($desde, $hasta)
    {
        $desde = (int)$desde;
        $hasta = (int)$hasta;
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
        $sql = "SELECT * FROM productos WHERE id_categoria = ? AND estado = 1 AND id != ? ORDER BY RAND() LIMIT 20";
        return $this->selectAll($sql, [$id_categoria, $id_producto]);
    }

    public function getBusqueda($valor)
    {
        $valorStr = "%" . $valor . "%";
        $sql = "SELECT * FROM productos WHERE nombre LIKE ? OR descripcion LIKE ? LIMIT 6";
        return $this->selectAll($sql, [$valorStr, $valorStr]);
    }

    public function getTalla($id_producto)
    {
        $sql = "SELECT t.id, t.nombre, t.nombre_corto, 
            COALESCE(SUM(tc.stock), 0) as stock_disponible
            FROM tallas t
            LEFT JOIN tallas_colores tc ON t.id = tc.id_talla 
            AND tc.id_producto = ? 
            AND tc.id_almacen = 1
            GROUP BY t.id, t.nombre, t.nombre_corto
            HAVING stock_disponible > 0 OR t.id IN (
                SELECT DISTINCT id_talla FROM tallas_colores WHERE id_producto = ?
            )";
        return $this->selectAll($sql, [$id_producto, $id_producto]);
    }

    public function getColores($size, $id_producto)
    {
        $sql = "SELECT c.id, c.nombre, c.color, tc.stock 
            FROM tallas_colores tc 
            INNER JOIN colores c ON tc.id_color = c.id 
            WHERE tc.id_talla = ? 
            AND tc.id_producto = ? 
            AND tc.id_almacen = 1 
            GROUP BY c.id, c.nombre, c.color, tc.stock";
        return $this->selectAll($sql, [$size, $id_producto]);
    }

    public function getTotalStockProducto($id_producto)
    {
        $sql = "SELECT COALESCE(SUM(stock), 0) as total_stock 
            FROM tallas_colores 
            WHERE id_producto = ? 
            AND id_almacen = 1";
        return $this->select($sql, [$id_producto]);
    }

    public function getDatos($table)
    {
        $tablasPermitidas = ['tallas', 'colores', 'usuarios', 'categorias', 'clientes'];
        if (!in_array($table, $tablasPermitidas)) {
            return [];
        }
        $sql = "SELECT * FROM $table WHERE estado = 1";
        return $this->selectAll($sql);
    }

    public function consultaStock($item, $nombre, $id_producto)
    {
        // Validación estricta del nombre de la columna para evitar inyección
        $columnasPermitidas = ['id_color', 'id_talla'];
        if (!in_array($item, $columnasPermitidas)) {
            return false;
        }
        $sql = "SELECT tc.stock, p.precio_venta 
            FROM tallas_colores tc
            INNER JOIN productos p ON p.id = tc.id_producto
            WHERE tc.$item = ? 
            AND tc.id_producto = ?
            AND tc.id_almacen = 1";
        return $this->select($sql, [$nombre, $id_producto]);
    }

    public function getAtributos($size, $color, $id_producto)
    {
        $sql = "SELECT tc.id, tc.stock, p.precio_venta, t.nombre AS size, c.nombre, c.color, c.color_secundario 
        FROM tallas_colores tc
        INNER JOIN productos p ON tc.id_producto = p.id
        INNER JOIN tallas t ON tc.id_talla = t.id 
        INNER JOIN colores c ON tc.id_color = c.id 
        WHERE tc.id_talla = ? 
        AND tc.id_color = ? 
        AND tc.id_producto = ?
        AND tc.id_almacen = 1";
        return $this->select($sql, [$size, $color, $id_producto]);
    }

    public function getColorSize($table, $id)
    {
        $tablasPermitidas = ['colores', 'tallas'];
        if (!in_array($table, $tablasPermitidas)) {
            return false;
        }
        $sql = "SELECT * FROM $table WHERE id = ?";
        return $this->select($sql, [$id]);
    }

    public function getCalificacion($accion, $id)
    {
        $accionesPermitidas = ['SUM', 'COUNT'];
        if (!in_array(strtoupper($accion), $accionesPermitidas)) {
            return false;
        }
        $sql = "SELECT $accion(cantidad) AS total FROM calificaciones WHERE id_producto = ?";
        return $this->select($sql, [$id]);
    }

    public function getTotalFiltroProductos($categorias, $precioMin, $precioMax, $colores, $sizes, $marcas, $generos = '')
    {
        $params = [];
        $sql = "SELECT COUNT(DISTINCT p.id) AS total 
        FROM productos p 
        INNER JOIN tallas_colores pf ON p.id = pf.id_producto
        LEFT JOIN tallas t ON pf.id_talla = t.id
        LEFT JOIN colores c ON pf.id_color = c.id
        WHERE p.precio_venta >= ? AND p.precio_venta <= ? AND p.estado = 1 AND pf.id_almacen = 1";
        $params[] = $precioMin;
        $params[] = $precioMax;

        if (!empty($categorias)) {
            // Se asume que $categorias ya viene limpio o es un string seguro generado internamente, pero se previene inyección en el string.
            // Para mayor seguridad, idealmente se usaría FIND_IN_SET o PDO::PARAM_INT, pero para simplificar el código existente:
            $catArray = array_map('intval', explode(',', $categorias));
            $sql .= " AND p.id_categoria IN (" . implode(',', $catArray) . ")";
        }
        if (!empty($colores)) {
            $colArray = array_map('intval', explode(',', $colores));
            $sql .= " AND pf.id_color IN (" . implode(',', $colArray) . ")";
        }
        if (!empty($sizes)) {
            $sizesArray = array_map('intval', explode(',', $sizes));
            $sql .= " AND pf.id_talla IN (" . implode(',', $sizesArray) . ")";
        }
        if (!empty($marcas)) {
            $marcasArray = array_map('intval', explode(',', $marcas));
            $sql .= " AND p.id_marca IN (" . implode(',', $marcasArray) . ")";
        }
        if (!empty($generos)) {
            $generosArray = array_map('trim', explode(',', $generos));
            $placeholders = implode(',', array_fill(0, count($generosArray), '?'));
            $sql .= " AND p.genero IN ($placeholders)";
            $params = array_merge($params, $generosArray);
        }

        return $this->select($sql, $params);
    }

    public function getFiltroProductos($categorias, $precioMin, $precioMax, $colores, $sizes, $marcas, $generos = '', $desde, $hasta)
    {
        $params = [];
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
        LEFT JOIN colores c ON pf.id_color = c.id
        WHERE p.precio_venta >= ? AND p.precio_venta <= ? AND p.estado = 1 AND pf.id_almacen = 1";
        $params[] = $precioMin;
        $params[] = $precioMax;

        if (!empty($categorias)) {
            $catArray = array_map('intval', explode(',', $categorias));
            $sql .= " AND p.id_categoria IN (" . implode(',', $catArray) . ")";
        }
        if (!empty($colores)) {
            $colArray = array_map('intval', explode(',', $colores));
            $sql .= " AND pf.id_color IN (" . implode(',', $colArray) . ")";
        }
        if (!empty($sizes)) {
            $sizesArray = array_map('intval', explode(',', $sizes));
            $sql .= " AND pf.id_talla IN (" . implode(',', $sizesArray) . ")";
        }
        if (!empty($marcas)) {
            $marcasArray = array_map('intval', explode(',', $marcas));
            $sql .= " AND p.id_marca IN (" . implode(',', $marcasArray) . ")";
        }
        if (!empty($generos)) {
            $generosArray = array_map('trim', explode(',', $generos));
            $placeholders = implode(',', array_fill(0, count($generosArray), '?'));
            $sql .= " AND p.genero IN ($placeholders)";
            $params = array_merge($params, $generosArray);
        }

        $sql .= " GROUP BY p.id ORDER BY p.id DESC LIMIT " . (int)$desde . ", " . (int)$hasta;

        return $this->selectAll($sql, $params);
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

    public function getTipoCliente()
    {
        if (!empty($_SESSION['correoCliente'])) {
            $sql = "SELECT tipo_cliente FROM clientes WHERE correo = ? AND estado = 1";
            $cliente = $this->select($sql, [$_SESSION['correoCliente']]);
            return ($cliente) ? $cliente['tipo_cliente'] : 'final';
        }
        return 'final';
    }

    public function getCliente($correo)
    {
        $sql = "SELECT * FROM clientes WHERE correo = ? AND estado = 1";
        return $this->select($sql, [$correo]);
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }

    public function getFiltroProductoss($busqueda, $categorias, $desde, $hasta, $color, $sizes)
    {
        $params = [];
        $busquedaStr = "%" . $busqueda . "%";
        $sql = "SELECT p.*, pf.id AS id_detalle, pf.stock 
            FROM productos p 
            INNER JOIN tallas_colores pf ON p.id = pf.id_producto
            WHERE p.nombre LIKE ? AND p.precio_venta >= ? AND p.precio_venta <= ? AND p.estado = 1 AND pf.id_almacen = 1";
        $params[] = $busquedaStr;
        $params[] = $desde;
        $params[] = $hasta;

        if (!empty($categorias)) {
            $catArray = array_map('intval', explode(',', $categorias));
            $sql .= " AND p.id_categoria IN (" . implode(',', $catArray) . ")";
        }
        if (!empty($color)) {
            $sql .= " AND pf.id_color = ?";
            $params[] = $color;
        }
        if (!empty($sizes)) {
            $sizesArray = array_map('intval', explode(',', $sizes));
            $sql .= " AND pf.id_talla IN (" . implode(',', $sizesArray) . ")";
        }

        $sql .= " LIMIT 1";
        return $this->selectAll($sql, $params);
    }

    /**PERFIL */
    public function getDatosCliente($id)
    {
        $sql = "SELECT * FROM clientes WHERE id = ?";
        return $this->select($sql, [$id]);
    }

    public function getValidarCliente($campo, $valor, $accion, $id)
    {
        $camposPermitidos = ['telefono', 'correo'];
        if (!in_array($campo, $camposPermitidos)) {
            return false;
        }

        if ($accion == 'registrar' && $id == 0) {
            $sql = "SELECT id FROM clientes WHERE $campo = ?";
            return $this->select($sql, [$valor]);
        } else {
            $sql = "SELECT id FROM clientes WHERE $campo = ? AND id != ?";
            return $this->select($sql, [$valor, $id]);
        }
    }

    public function actualizarCliente($nombre, $apellido, $telefono, $correo, $direccion, $tipo_cliente, $perfil, $id)
    {
        $sql = "UPDATE clientes 
            SET nombre=?, apellido=?, telefono=?, correo=?, direccion=?, tipo_cliente=?, perfil=? 
            WHERE id=?";
        $array = array($nombre, $apellido, $telefono, $correo, $direccion, $tipo_cliente, $perfil, $id);
        return $this->save($sql, $array);
    }
}