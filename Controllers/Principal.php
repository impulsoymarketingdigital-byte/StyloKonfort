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
        }
        $productos = $this->model->getNuevosProductos();
        for ($i = 0; $i < count($productos); $i++) {
            $calificacion = $this->model->getCalificacion('SUM', $productos[$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $productos[$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];

            $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;

            $productos[$i]['calificacion'] = round($total);
        }

        $data['nuevosProductos'] = $productos;
        //sizes
        $data['sizes'] = $this->model->getDatos('tallas');
        $data['colores'] = $this->model->getDatos('colores');
        $this->views->getView('principal', "shop", $data);
    }
    public function filtro()
    {
        $categorias = $_POST['categorias'];
        $color = $_POST['color'];
        $sizes = $_POST['sizes'];
        $precio = explode(';', $_POST['precios']);
        $precioMin = $precio[0];
        $precioMax = $precio[1];

        $pagina = (empty($_POST['page'])) ? 1 : $_POST['page'];
        $desde = ($pagina - 1) * PORPAGINA;
        $total = $this->model->getTotalFiltroProductos($categorias, $precioMin, $precioMax, $color, $sizes);
        $data['pagina'] = $pagina;
        $data['total'] = ceil($total['total'] / PORPAGINA);

        $data['productos'] = $this->model->getFiltroProductos($categorias, $precioMin, $precioMax, $color, $sizes, $desde, PORPAGINA);
        for ($i = 0; $i < count($data['productos']); $i++) {
            $calificacion = $this->model->getCalificacion('SUM', $data['productos'][$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $data['productos'][$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];

            $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;

            $data['productos'][$i]['calificacion'] = round($total);
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
        }

        $data['nuevosProductos'] = $productos;

        $data['sizes'] = $this->model->getTalla($data['producto']['id']);

        $id_categoria = $data['producto']['id_categoria'];
        $data['relacionados'] = $this->model->getAleatorios($id_categoria, $data['producto']['id']);

        ######### CALIFICACION ###########
        for ($i = 0; $i < count($data['relacionados']); $i++) {
            $calificacion = $this->model->getCalificacion('SUM', $data['relacionados'][$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $data['relacionados'][$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];

            $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;

            $data['relacionados'][$i]['calificacion'] = round($total);
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
    //vista contactos
    public function contactos()
    {
        $data['perfil'] = 'si';
        $data['title'] = 'Contactos';
        $this->views->getView('principal', "contact", $data);
    }
    //obtener producto a partir de la lista de carrito
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
                $precio = $result['precio'];
                if ($producto['size'] > 0 && $producto['color'] > 0) {
                    $detalle = $this->model->getAtributos($producto['size'], $producto['color'], $producto['idProducto']);
                    if (empty($detalle)) {
                        $color = $this->model->getColorSize('colores', $producto['color']);
                        $talla = $this->model->getColorSize('tallas', $producto['color']);
                        $atributo = $talla['nombre_corto'] . ' - <span class="badge" style="background: ' . $color['color'] . ';">' . $color['nombre'] . '</span>';
                        $atributoMP = $talla['nombre_corto'] . ' - ' . $color['nombre'];
                        $precio = $detalle['precio'];
                    } else {
                        $atributoMP = $detalle['nombre_corto'] . ' - ' . $detalle['nombre'];
                        $atributo = $detalle['nombre_corto'] . ' - <span class="badge" style="background: ' . $detalle['color'] . ';">' . $detalle['nombre'] . '</span>';
                        $data['stock'] = $detalle['cantidad'];
                        $precio = $detalle['precio'];
                    }
                } else {
                    $data['stock'] = 'Ilimitado';
                }
                $data['id'] = $result['id'];
                $data['nombre'] = $result['nombre'];
                $data['atributo'] = $atributo;
                $data['atributoMP'] = $atributoMP;
                $data['precio'] = $precio;
                $data['cantidad'] = $producto['cantidad'];
                $data['size'] = $producto['size'];
                $data['color'] = $producto['color'];
                $data['imagen'] = $result['imagen'];
                $subTotal = $precio * $producto['cantidad'];
                $data['subTotal'] = number_format($subTotal, 2);
                array_push($array['productos'], $data);
                $total += $subTotal;
            }
            $_SESSION['productos'] = $array['productos'];
        }
        $array['login'] = (empty($_SESSION['idCliente'])) ? 0 : 1;
        $array['total'] = number_format($total, 2);
        $array['totalPaypal'] = number_format($total, 2, '.', '');
        $array['moneda'] = MONEDA;
        $array['currency'] = CURRENCY;
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
        if ($size != null) {
            $id_producto = $_POST['id_producto'];
            $data['atrib'] = $this->model->consultaStock('id_talla', $size, $id_producto);
            $data['colores'] = $this->model->getColores($size, $id_producto);
        }
        if ($color != null) {
            $id_producto = $_POST['id_producto'];
            $data['atrib'] = $this->model->consultaStock('id_color', $color, $id_producto);
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
                $data['sizes'] = $this->model->getTalla($idProducto);
            }
            //CALIFICACION
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
