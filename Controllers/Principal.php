<?php
class Principal extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
    }
    
    //vista about
    public function about()
    {
        $data['perfil'] = 'si';
        $data['title'] = 'Nuestro Equipo';
        $data['testimonios'] = $this->model->getTestimonial();
        $this->views->getView('principal', "about", $data);
    }

    //vista shop (MEJORADA PARA MAYORISTAS)
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
        
        // 1. Identificamos si es mayorista
        $data['tipo_cliente'] = $this->model->getTipoCliente();
        $esMayorista = ($data['tipo_cliente'] == 'mayorista');

        for ($i = 0; $i < count($data['productos']); $i++) {
            // ⭐ MAGIA B2B: Si es mayorista, cambiamos el precio visual de la vitrina
            if ($esMayorista && isset($data['productos'][$i]['precio_mayorista'])) {
                $data['productos'][$i]['precio_venta'] = $data['productos'][$i]['precio_mayorista'];
            }

            $calificacion = $this->model->getCalificacion('SUM', $data['productos'][$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $data['productos'][$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $totalCal = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $data['productos'][$i]['calificacion'] = round($totalCal);

            $primeraImagen = $this->model->getPrimeraImagen($data['productos'][$i]['id']);
            $data['productos'][$i]['imagen'] = $primeraImagen;
        }

        $productosNuevos = $this->model->getNuevosProductos();
        for ($i = 0; $i < count($productosNuevos); $i++) {
            // ⭐ MAGIA B2B para sección "Nuevos Productos"
            if ($esMayorista && isset($productosNuevos[$i]['precio_mayorista'])) {
                $productosNuevos[$i]['precio_venta'] = $productosNuevos[$i]['precio_mayorista'];
            }

            $calificacion = $this->model->getCalificacion('SUM', $productosNuevos[$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $productosNuevos[$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $totalCal = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $productosNuevos[$i]['calificacion'] = round($totalCal);

            $primeraImagen = $this->model->getPrimeraImagen($productosNuevos[$i]['id']);
            $productosNuevos[$i]['imagen'] = $primeraImagen;
        }

        $data['nuevosProductos'] = $productosNuevos;
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
        $generos = isset($_POST['generos']) && !empty($_POST['generos']) ? $_POST['generos'] : '';

        $precio = explode(';', $_POST['precios']);
        $precioMin = $precio[0];
        $precioMax = $precio[1];
        $pagina = (empty($_POST['page'])) ? 1 : $_POST['page'];
        $desde = ($pagina - 1) * PORPAGINA;

        $data['tipo_cliente'] = $this->model->getTipoCliente(); 
        $esMayorista = ($data['tipo_cliente'] == 'mayorista');

        $total = $this->model->getTotalFiltroProductos($categorias, $precioMin, $precioMax, $colores, $sizes, $marcas, $generos);
        $data['pagina'] = $pagina;
        $data['total'] = ceil($total['total'] / PORPAGINA);
        $data['productos'] = $this->model->getFiltroProductos($categorias, $precioMin, $precioMax, $colores, $sizes, $marcas, $generos, $desde, PORPAGINA);

        for ($i = 0; $i < count($data['productos']); $i++) {
            // ⭐ MAGIA B2B en los filtros
            if ($esMayorista && isset($data['productos'][$i]['precio_mayorista'])) {
                $data['productos'][$i]['precio_venta'] = $data['productos'][$i]['precio_mayorista'];
            }

            $calificacion = $this->model->getCalificacion('SUM', $data['productos'][$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $data['productos'][$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $totalCal = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $data['productos'][$i]['calificacion'] = round($totalCal);
            
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

        $empresa = $this->model->getEmpresa();
        $data['whatsapp'] = !empty($empresa) ? $empresa['whatsapp'] : '573003665138';

        $data['tipo_cliente'] = $this->model->getTipoCliente();
        $esMayorista = ($data['tipo_cliente'] == 'mayorista');

        // ⭐ MAGIA B2B en el detalle del producto
        if ($esMayorista && isset($data['producto']['precio_mayorista'])) {
            $data['producto']['precio_venta'] = $data['producto']['precio_mayorista'];
        }

        //CALIFICACION PRODUCTO
        $calific = $this->model->getCalificacion('SUM', $data['producto']['id']);
        $cant = $this->model->getCalificacion('COUNT', $data['producto']['id']);
        $totalCant = ($cant['total'] == 0) ? 5 : $cant['total'];
        $tot = ($calific['total'] != null) ? $calific['total'] / $totalCant : $totalCant;
        $data['calificacion'] = round($tot);
        $data['reviews'] = $cant['total'];

        $productosNuevos = $this->model->getNuevosProductos();
        for ($i = 0; $i < count($productosNuevos); $i++) {
            if ($esMayorista && isset($productosNuevos[$i]['precio_mayorista'])) {
                $productosNuevos[$i]['precio_venta'] = $productosNuevos[$i]['precio_mayorista'];
            }

            $calificacion = $this->model->getCalificacion('SUM', $productosNuevos[$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $productosNuevos[$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $totalCal = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $productosNuevos[$i]['calificacion'] = round($totalCal);

            $primeraImagen = $this->model->getPrimeraImagen($productosNuevos[$i]['id']);
            $productosNuevos[$i]['imagen'] = $primeraImagen;
        }
        $data['nuevosProductos'] = $productosNuevos;

        $tallasBase = $this->model->getTalla($data['producto']['id']);
        $data['sizes'] = [];

        foreach ($tallasBase as $talla) {
            $stockTotal = 0;
            $colores = $this->model->getColores($talla['id'], $data['producto']['id']);

            foreach ($colores as $color) {
                if (isset($color['stock']) && $color['stock'] > 0) {
                    $stockTotal += $color['stock'];
                }
            }

            $talla['stock_disponible'] = $stockTotal;
            $data['sizes'][] = $talla;
        }

        $data['tiene_stock'] = false;
        foreach ($data['sizes'] as $size) {
            if ($size['stock_disponible'] > 0) {
                $data['tiene_stock'] = true;
                break;
            }
        }

        $id_categoria = $data['producto']['id_categoria'];
        $data['relacionados'] = $this->model->getAleatorios($id_categoria, $data['producto']['id']);

        for ($i = 0; $i < count($data['relacionados']); $i++) {
            if ($esMayorista && isset($data['relacionados'][$i]['precio_mayorista'])) {
                $data['relacionados'][$i]['precio_venta'] = $data['relacionados'][$i]['precio_mayorista'];
            }

            $calificacion = $this->model->getCalificacion('SUM', $data['relacionados'][$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $data['relacionados'][$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $totalCal = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $data['relacionados'][$i]['calificacion'] = round($totalCal);

            $primeraImagen = $this->model->getPrimeraImagen($data['relacionados'][$i]['id']);
            $data['relacionados'][$i]['imagen'] = $primeraImagen;
        }

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

    //vista categorias (MEJORADA PARA MAYORISTAS)
    public function categorias($datos)
    {
        $data['perfil'] = 'si';
        $page = 1;
        $array = explode(',', $datos);
        if (is_numeric($datos)) {
            $slug = $datos;
        } else {
            if (isset($array[0]) && !empty($array[0])) { $slug = $array[0]; }
            if (isset($array[1]) && !empty($array[1])) { $page = $array[1]; }
        }
        $pagina = (empty($page)) ? 1 : $page;
        $desde = ($pagina - 1) * PORPAGINA;

        $data['categoria'] = $this->model->getSlug('categorias', $slug);
        $data['pagina'] = $pagina;
        $total = $this->model->getTotalProductosCat($data['categoria']['id']);
        $data['total'] = ceil($total['total'] / PORPAGINA);

        $data['tipo_cliente'] = $this->model->getTipoCliente();
        $esMayorista = ($data['tipo_cliente'] == 'mayorista');

        $data['productos'] = $this->model->getProductosCat($data['categoria']['id'], $desde, PORPAGINA);
        
        for ($i = 0; $i < count($data['productos']); $i++) {
            if ($esMayorista && isset($data['productos'][$i]['precio_mayorista'])) {
                $data['productos'][$i]['precio_venta'] = $data['productos'][$i]['precio_mayorista'];
            }

            $calificacion = $this->model->getCalificacion('SUM', $data['productos'][$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $data['productos'][$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $totalCal = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $data['productos'][$i]['calificacion'] = round($totalCal);

            $primeraImagen = $this->model->getPrimeraImagen($data['productos'][$i]['id']);
            $data['productos'][$i]['imagen'] = $primeraImagen;
        }

        $data['title'] = 'Categorias';
        $data['slug'] = $slug;
        $data['sizes'] = $this->model->getDatos('tallas');
        $data['colores'] = $this->model->getDatos('colores');
        $data['marcas'] = $this->model->getMarcas();

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

        $tipoCliente = $this->model->getTipoCliente();

        if (!empty($json)) {
            foreach ($json as $producto) {
                $result = $this->model->getProducto($producto['idProducto']);
                $nombreTalla = '';
                $nombreColor = '';
                $colorHexa = '#000';
                $colorSecundario = null;
                $atributoMP = '';

                $precio = ($tipoCliente == 'mayorista') ? $result['precio_mayorista'] : $result['precio_venta'];
                $stock = 'Ilimitado';

                if (!empty($producto['size']) && !empty($producto['color'])) {
                    $detalle = $this->model->getAtributos($producto['size'], $producto['color'], $producto['idProducto']);

                    if (!empty($detalle)) {
                        $nombreTalla = $detalle['size'] ?? '';
                        $nombreColor = $detalle['nombre'] ?? '';
                        $colorHexa = $detalle['color'] ?? '#000';
                        $colorSecundario = $detalle['color_secundario'] ?? null;
                        $atributoMP = $nombreTalla . ' - ' . $nombreColor;
                        $stock = $detalle['stock'] ?? 'Ilimitado';
                    } else {
                        $color = $this->model->getColorSize('colores', $producto['color']);
                        $talla = $this->model->getColorSize('tallas', $producto['size']);
                        $nombreTalla = $talla['nombre'] ?? '';
                        $nombreColor = $color['nombre'] ?? '';
                        $colorHexa = $color['color'] ?? '#000';
                        $colorSecundario = $color['color_secundario'] ?? null;
                        $atributoMP = $nombreTalla . ' - ' . $nombreColor;
                    }
                }

                $cantidad = $producto['cantidad'] ?? 1;
                $subTotal = $precio * $cantidad;

                $data = [
                    'id' => $result['id'] ?? 0,
                    'nombre' => $result['nombre'] ?? '',
                    'nombreTalla' => $nombreTalla,
                    'nombreColor' => $nombreColor,
                    'colorHexa' => $colorHexa,
                    'colorSecundario' => $colorSecundario,
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
                $array['productos'][] = $data;
            }

            $_SESSION['productos'] = $array['productos'];
        }

        $array['login'] = empty($_SESSION['idCliente']) ? 0 : 1;
        $array['total'] = number_format($total, 2);
        $array['totalPaypal'] = number_format($total, 2, '.', '');
        $array['moneda'] = MONEDA ?? 'COP';
        $array['currency'] = CURRENCY ?? 'COP';

        echo json_encode($array, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getPromocionesActivas()
    {
        $data = $this->model->getPromocionesActivas();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
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

            $empresa = $this->model->getEmpresa();
            $data['whatsapp'] = !empty($empresa) ? $empresa['whatsapp'] : '573003665138';

            $data['tipo_cliente'] = $this->model->getTipoCliente(); 
            $esMayorista = ($data['tipo_cliente'] == 'mayorista');

            if (!empty($data['producto'])) {
                // ⭐ MAGIA B2B en el API interno
                if ($esMayorista && isset($data['producto']['precio_mayorista'])) {
                    $data['producto']['precio_venta'] = $data['producto']['precio_mayorista'];
                }

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
            $totalCal = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $data['calificacion'] = round($totalCal);
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

            $data['tipo_cliente'] = $this->model->getTipoCliente();

            $producto = $this->model->getProducto($idProducto);
            if ($data['tipo_cliente'] == 'mayorista') {
                $data['precio_aplicable'] = $producto['precio_mayorista'];
            } else {
                $data['precio_aplicable'] = $producto['precio_venta'];
            }

            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    /**PERFIL */
    public function getDatosCliente()
    {
        if (empty($_SESSION['idCliente'])) {
            $res = array('success' => false, 'msg' => 'No autorizado');
        } else {
            $id = $_SESSION['idCliente'];
            $data = $this->model->getDatosCliente($id);
            if (!empty($data)) {
                $res = array('success' => true, 'data' => $data);
            } else {
                $res = array('success' => false, 'msg' => 'No se encontraron datos');
            }
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function modificarDatos()
    {
        if (empty($_SESSION['idCliente'])) {
            $res = array('msg' => 'NO AUTORIZADO', 'type' => 'error');
        } else if (isset($_POST['nombre']) && isset($_POST['apellidos']) && isset($_POST['correo'])) {
            $nombre = strClean($_POST['nombre']);
            $apellidos = strClean($_POST['apellidos']);
            $telefono = strClean($_POST['telefono']);
            $correo = strClean($_POST['correo']);
            $direccion = strClean($_POST['direccion']);
            $foto = $_FILES['fotoCliente'];
            $id = $_SESSION['idCliente'];

            if (empty($nombre) || empty($apellidos) || empty($telefono) || empty($correo) || empty($direccion)) {
                $res = array('msg' => 'TODOS LOS CAMPOS CON * SON REQUERIDOS', 'type' => 'warning');
            } else {
                $verificarTelefono = $this->model->getValidarCliente('telefono', $telefono, 'actualizar', $id);
                if (empty($verificarTelefono)) {
                    if ($correo != null) {
                        $verificarCorreo = $this->model->getValidarCliente('correo', $correo, 'actualizar', $id);
                        if (!empty($verificarCorreo)) {
                            $res = array('msg' => 'EL CORREO DEBE SER ÚNICO', 'type' => 'warning');
                            echo json_encode($res);
                            die();
                        }
                    }

                    $tmp = $this->model->getDatosCliente($id);

                    if (!empty($tmp['perfil']) && $tmp['perfil'] != 'default.png' && file_exists('assets/images/clientes/' . $tmp['perfil'])) {
                        if (!empty($foto['name'])) {
                            unlink('assets/images/clientes/' . $tmp['perfil']);
                        }
                        $destino = $tmp['perfil'];
                    } else {
                        $destino = (!empty($foto['name'])) ? $id . '.jpg' : 'default.png';
                    }

                    $data = $this->model->actualizarCliente(
                        $nombre, $apellidos, $telefono, $correo, $direccion,
                        $tmp['tipo_cliente'], $destino, $id
                    );

                    if ($data > 0) {
                        if (!empty($foto['name'])) {
                            move_uploaded_file($foto['tmp_name'], 'assets/images/clientes/' . $destino);
                        }

                        $_SESSION['perfilCliente'] = $destino;
                        $_SESSION['nombreCliente'] = $nombre . ' ' . $apellidos;

                        $res = array('msg' => 'PERFIL ACTUALIZADO CORRECTAMENTE', 'type' => 'success');
                    } else {
                        $res = array('msg' => 'ERROR AL MODIFICAR', 'type' => 'error');
                    }
                } else {
                    $res = array('msg' => 'EL TELÉFONO DEBE SER ÚNICO', 'type' => 'warning');
                }
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO', 'type' => 'error');
        }

        echo json_encode($res);
        die();
    }
}