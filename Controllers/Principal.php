<?php
class Principal extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }
    //vista about
    public function about()
    {
        $data['perfil'] = 'si';
        $data['title'] = 'Nuestro Equipo';
        $data['testimonios'] = $this->model->getTestimonial();
        $this->views->getView('principal', "about", $data);
    }

    //vista shop
    public function shop($page)
    {
        $pagina = (empty($page)) ? 1 : $page;
        $desde = ($pagina - 1) * PORPAGINA;
        $data['perfil'] = 'si';
        $data['title'] = 'Nuestro Productos';
        $data['productos'] = $this->model->getProductos($desde, PORPAGINA);
        $total = $this->model->getTotalProductos();
        $data['pagina'] = $pagina;
        $data['total'] = ceil($total['total'] / PORPAGINA);

        ######### CALIFICACION ###########
        for ($i = 0; $i < count($data['productos']); $i++) {
            $calificacion = $this->model->getCalificacion('SUM', $data['productos'][$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $data['productos'][$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $data['productos'][$i]['calificacion'] = round($total);

            // ⭐ AGREGAR LA PRIMERA IMAGEN DE LA GALERÍA
            $primeraImagen = $this->model->getPrimeraImagen($data['productos'][$i]['id']);
            $data['productos'][$i]['imagen'] = $primeraImagen;
        }

        $productos = $this->model->getNuevosProductos();
        for ($i = 0; $i < count($productos); $i++) {
            $calificacion = $this->model->getCalificacion('SUM', $productos[$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $productos[$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $productos[$i]['calificacion'] = round($total);

            // ⭐ AGREGAR LA PRIMERA IMAGEN DE LA GALERÍA
            $primeraImagen = $this->model->getPrimeraImagen($productos[$i]['id']);
            $productos[$i]['imagen'] = $primeraImagen;
        }

        $data['nuevosProductos'] = $productos;

        // Filtros
        $data['sizes'] = $this->model->getDatos('tallas');
        $data['colores'] = $this->model->getDatos('colores');
        $data['marcas'] = $this->model->getMarcas();

        $this->views->getView('principal', "shop", $data);
    }

    public function filtro()
    {
        $categorias = isset($_POST['categorias']) && !empty($_POST['categorias']) ? $_POST['categorias'] : '';
        $colores = isset($_POST['colores']) && !empty($_POST['colores']) ? $_POST['colores'] : '';
        $sizes = isset($_POST['sizes']) && !empty($_POST['sizes']) ? $_POST['sizes'] : '';
        $marcas = isset($_POST['marcas']) && !empty($_POST['marcas']) ? $_POST['marcas'] : '';
        $precio = explode(';', $_POST['precios']);
        $precioMin = $precio[0];
        $precioMax = $precio[1];
        $pagina = (empty($_POST['page'])) ? 1 : $_POST['page'];
        $desde = ($pagina - 1) * PORPAGINA;

        $total = $this->model->getTotalFiltroProductos($categorias, $precioMin, $precioMax, $colores, $sizes, $marcas);
        $data['pagina'] = $pagina;
        $data['total'] = ceil($total['total'] / PORPAGINA);
        $data['productos'] = $this->model->getFiltroProductos($categorias, $precioMin, $precioMax, $colores, $sizes, $marcas, $desde, PORPAGINA);

        for ($i = 0; $i < count($data['productos']); $i++) {
            $calificacion = $this->model->getCalificacion('SUM', $data['productos'][$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $data['productos'][$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $data['productos'][$i]['calificacion'] = round($total);
            $primeraImagen = $this->model->getPrimeraImagen($data['productos'][$i]['id']);
            $data['productos'][$i]['imagen'] = $primeraImagen;
        }

        $data['moneda'] = MONEDA;
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    //vista detail
    public function detail($slug)
    {
        $data['perfil'] = 'si';
        $data['producto'] = $this->model->getSlug('productos', $slug);
        if (empty($data['producto'])) {
            echo 'Pagina no encontrada';
            exit;
        }

        //CALIFICACION PRODUCTO
        $calific = $this->model->getCalificacion('SUM', $data['producto']['id']);
        $cant = $this->model->getCalificacion('COUNT', $data['producto']['id']);
        $totalCant = ($cant['total'] == 0) ? 5 : $cant['total'];
        $tot = ($calific['total'] != null) ? $calific['total'] / $totalCant : $totalCant;
        $data['calificacion'] = round($tot);
        $data['reviews'] = $cant['total'];

        $productos = $this->model->getNuevosProductos();
        for ($i = 0; $i < count($productos); $i++) {
            $calificacion = $this->model->getCalificacion('SUM', $productos[$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $productos[$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $productos[$i]['calificacion'] = round($total);

            // Agregar primera imagen
            $primeraImagen = $this->model->getPrimeraImagen($productos[$i]['id']);
            $productos[$i]['imagen'] = $primeraImagen;
        }
        $data['nuevosProductos'] = $productos;

        // ⭐ OBTENER TALLAS CON STOCK (CORREGIDO)
        $tallasBase = $this->model->getTalla($data['producto']['id']);
        $data['sizes'] = [];

        foreach ($tallasBase as $talla) {
            // Calcular stock total de esta talla (suma de todos los colores)
            $stockTotal = 0;

            // 🔧 CORRECCIÓN: El orden correcto es getColores($idTalla, $idProducto)
            $colores = $this->model->getColores($talla['id'], $data['producto']['id']);

            foreach ($colores as $color) {
                // Sumar el stock de cada color
                if (isset($color['stock']) && $color['stock'] > 0) {
                    $stockTotal += $color['stock'];
                }
            }

            $talla['stock_disponible'] = $stockTotal;
            $data['sizes'][] = $talla;
        }

        // Verificar si el producto tiene stock
        $data['tiene_stock'] = false;
        foreach ($data['sizes'] as $size) {
            if ($size['stock_disponible'] > 0) {
                $data['tiene_stock'] = true;
                break;
            }
        }

        $id_categoria = $data['producto']['id_categoria'];
        $data['relacionados'] = $this->model->getAleatorios($id_categoria, $data['producto']['id']);

        ######### CALIFICACION ###########
        for ($i = 0; $i < count($data['relacionados']); $i++) {
            $calificacion = $this->model->getCalificacion('SUM', $data['relacionados'][$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $data['relacionados'][$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $data['relacionados'][$i]['calificacion'] = round($total);

            // Agregar primera imagen
            $primeraImagen = $this->model->getPrimeraImagen($data['relacionados'][$i]['id']);
            $data['relacionados'][$i]['imagen'] = $primeraImagen;
        }

        //scanear galeria
        $result = array();
        $directorio = 'assets/images/productos/' . $data['producto']['id'];
        if (file_exists($directorio)) {
            $imagenes = scandir($directorio);
            if (false !== $imagenes) {
                foreach ($imagenes as $file) {
                    if ('.' != $file && '..' != $file) {
                        array_push($result, $file);
                    }
                }
            }
        }
        $data['imagenes'] = $result;
        $data['title'] = $data['producto']['nombre'];
        $this->views->getView('principal', "detail", $data);
    }


    public function getColores($datos)
    {
        $array = explode(',', $datos);
        $idProducto = $array[0];
        $idSize = $array[1];
        if (is_numeric($idProducto) && is_numeric($idSize)) {
            $data['colores'] = $this->model->getColores($idSize, $idProducto);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    //vista categorias
    public function categorias($datos)
    {
        $data['perfil'] = 'si';
        $page = 1;
        $array = explode(',', $datos);
        if (is_numeric($datos)) {
            $slug = $datos;
        } else {
            if (isset($array[0])) {
                if (!empty($array[0])) {
                    $slug = $array[0];
                }
            }
            if (isset($array[1])) {
                if (!empty($array[1])) {
                    $page = $array[1];
                }
            }
        }
        $pagina = (empty($page)) ? 1 : $page;
        $desde = ($pagina - 1) * PORPAGINA;

        $data['categoria'] = $this->model->getSlug('categorias', $slug);

        $data['pagina'] = $pagina;
        $total = $this->model->getTotalProductosCat($data['categoria']['id']);
        $data['total'] = ceil($total['total'] / PORPAGINA);

        $data['productos'] = $this->model->getProductosCat($data['categoria']['id'], $desde, PORPAGINA);
        for ($i = 0; $i < count($data['productos']); $i++) {
            $calificacion = $this->model->getCalificacion('SUM', $data['productos'][$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $data['productos'][$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];

            $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;

            $data['productos'][$i]['calificacion'] = round($total);
        }
        $data['title'] = 'Categorias';
        $data['slug'] = $slug;
        //sizes
        $data['sizes'] = $this->model->getDatos('tallas');
        $data['colores'] = $this->model->getDatos('colores');
        $this->views->getView('principal', "categorias", $data);
    }
    public function contactos()
    {
        $data['perfil'] = 'si';
        $data['title'] = 'Contactos';
        $this->views->getView('principal', "contact", $data);
    }
    public function listaProductos()
    {
        $datos = file_get_contents('php://input');
        $json = json_decode($datos, true);
        $array['productos'] = array();
        $total = 0.00;

        if (!empty($json)) {
            foreach ($json as $producto) {
                $result = $this->model->getProducto($producto['idProducto']);
                $atributo = '';
                $atributoMP = '';
                $precio = $result['precio_venta'] ?? 0;
                $stock = 'Ilimitado';

                // Verificar si tiene talla y color
                if (!empty($producto['size']) && !empty($producto['color'])) {
                    $detalle = $this->model->getAtributos($producto['size'], $producto['color'], $producto['idProducto']);

                    if (!empty($detalle)) {
                        $talla = $detalle['size'] ?? '';
                        $colorNombre = $detalle['nombre'] ?? '';
                        $colorHexa = $detalle['color'] ?? '#000';

                        $atributoMP = $talla . ' - ' . $colorNombre;
                        $atributo = 'T: ' . $talla . ' | <span class="badge" style="background: ' . $colorHexa . ';">C: ' . $colorNombre . '</span>';
                        $precio = $detalle['precio_venta'] ?? $precio;
                        $stock = $detalle['stock'] ?? 'Ilimitado';
                    } else {
                        $color = $this->model->getColorSize('colores', $producto['color']);
                        $talla = $this->model->getColorSize('tallas', $producto['size']);
                        $atributo = ($talla['nombre'] ?? '') . ' - <span class="badge" style="background: ' . ($color['color'] ?? '#000') . ';">' . ($color['nombre'] ?? '') . '</span>';
                        $atributoMP = ($talla['nombre'] ?? '') . ' - ' . ($color['nombre'] ?? '');
                    }
                }

                $cantidad = $producto['cantidad'] ?? 1;

                if (isset($producto['precio_compra'])) {
                    // ES UNA COMPRA
                    $precio_compra = floatval($producto['precio_compra']);
                    $descuento = isset($producto['descuento']) ? floatval($producto['descuento']) : 0;
                    $subTotal = ($precio_compra * $cantidad) - $descuento;

                    $data = [
                        'id' => $result['id'] ?? 0,
                        'nombre' => $result['nombre'] ?? '',
                        'atributo' => $atributo,
                        'atributoMP' => $atributoMP,
                        'precio' => $precio_compra,
                        'precio_compra' => $precio_compra,
                        'descuento' => $descuento,
                        'cantidad' => $cantidad,
                        'size' => $producto['size'] ?? 0,
                        'color' => $producto['color'] ?? 0,
                        'stock' => $stock,
                        'imagen' => $result['imagen'] ?? 'assets/images/productos/product.png',
                        'subTotal' => number_format($subTotal, 2)
                    ];

                    $total += $subTotal;
                } else {
                    // ES UNA VENTA
                    $subTotal = $precio * $cantidad;

                    $data = [
                        'id' => $result['id'] ?? 0,
                        'nombre' => $result['nombre'] ?? '',
                        'atributo' => $atributo,
                        'atributoMP' => $atributoMP,
                        'precio' => $precio,
                        'cantidad' => $cantidad,
                        'size' => $producto['size'] ?? 0,
                        'color' => $producto['color'] ?? 0,
                        'stock' => $stock,
                        'imagen' => $result['imagen'] ?? 'assets/images/productos/product.png',
                        'subTotal' => number_format($subTotal, 2)
                    ];

                    $total += $subTotal;
                }

                $array['productos'][] = $data;
            }

            $_SESSION['productos'] = $array['productos'];
        }

        $array['login'] = empty($_SESSION['idCliente']) ? 0 : 1;
        $array['total'] = number_format($total, 2);
        $array['totalPaypal'] = number_format($total, 2, '.', '');
        $array['moneda'] = MONEDA ?? 'Bs';
        $array['currency'] = CURRENCY ?? 'USD';

        echo json_encode($array, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function busqueda($valor)
    {
        $data = [];
        if (!empty($valor)) {
            $data = $this->model->getBusqueda($valor);
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    ####### cambiarStock #######
    public function cambiarStock()
    {
        $size = (!empty($_POST['size'])) ? strClean($_POST['size']) : null;
        $color = (!empty($_POST['color'])) ? strClean($_POST['color']) : null;
        $id_producto = $_POST['id_producto'];

        $data = array();

        if ($size != null) {
            $data['colores'] = $this->model->getColores($size, $id_producto);

            if ($color != null && $color != '') {
                $data['atrib'] = $this->model->getAtributos($size, $color, $id_producto);
            }
        }

        $data['moneda'] = MONEDA;
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getProductos($idCat)
    {
        if (is_numeric($idCat)) {
            $data = $this->model->getProductosCategoria($idCat);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getProducto($idProducto)
    {
        if (is_numeric($idProducto)) {
            $data['moneda'] = MONEDA;
            $data['sizes'] = [];
            $data['producto'] = $this->model->getProducto($idProducto);

            if (!empty($data['producto'])) {
                $result = array();
                $directorio = 'assets/images/productos/' . $idProducto;
                if (file_exists($directorio)) {
                    $imagenes = scandir($directorio);
                    if (false !== $imagenes) {
                        foreach ($imagenes as $file) {
                            if ('.' != $file && '..' != $file) {
                                array_push($result, $file);
                            }
                        }
                    }
                }
                $data['imagenes'] = $result;

                if (!empty($result)) {
                    $data['producto']['imagen'] = 'assets/images/productos/' . $idProducto . '/' . $result[0];
                }

                $data['sizes'] = $this->model->getTalla($idProducto);

                $stockTotal = $this->model->getTotalStockProducto($idProducto);
                $data['tiene_stock'] = $stockTotal['total_stock'] > 0;
            }

            $calificacion = $this->model->getCalificacion('SUM', $idProducto);
            $cantidad = $this->model->getCalificacion('COUNT', $idProducto);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $data['calificacion'] = round($total);
            $data['totalCantidad'] = $cantidad['total'];

            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }


    public function getStock($datos)
    {
        $array = explode(',', $datos);
        $idSize = $array[0];
        $idColor = $array[1];
        $idProducto = $array[2];
        if (is_numeric($idSize) && is_numeric($idColor) && is_numeric($idProducto)) {
            $data = $this->model->getAtributos($idSize, $idColor, $idProducto);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
