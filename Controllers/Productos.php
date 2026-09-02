<?php
require 'vendor/autoload.php';

class Productos extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
    }
    public function index()
    {
        $data['title'] = 'productos';
        $data['categorias'] = $this->model->getDatos('categorias');
        $data['marcas'] = $this->model->getDatos('marcas');
        $data['colores'] = $this->model->getDatos('colores');
        $data['tallas'] = $this->model->getDatos('tallas');
        $data['almacenes'] = $this->model->getDatos('almacenes');
        $this->views->getView('admin/productos', "index", $data);
    }
    public function listar()
    {
        $data = $this->model->getProductos(1);
        for ($i = 0; $i < count($data); $i++) {
            $estado = $data[$i]['estado'];
            $color = $estado == 1 ? 'success' : 'danger';
            $texto = $estado == 1 ? 'ACTIVO' : 'INACTIVO';
            $data[$i]['estado'] = "<div class='badge rounded-pill text-$color bg-light-$color p-2 text-uppercase px-3'>$texto</div>";

            if ($estado == 1) {
                $botonAccion = '<button class="btn btn-danger" type="button" onclick="eliminarProducto(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></button>';
            } else {
                $botonAccion = '<button class="btn btn-success" type="button" onclick="restaurar(' . $data[$i]['id'] . ')"><i class="fas fa-undo"></i></button>';
            }

            $data[$i]['accion'] = '
            <div class="d-flex gap-1 flex-wrap">
                <button class="btn btn-primary" type="button" onclick="edit(' . $data[$i]['id'] . ')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-secondary" type="button" onclick="agregarImagenes(' . $data[$i]['id'] . ')">
                    <i class="fas fa-images"></i>
                </button>
                <button class="btn btn-info" type="button" onclick="mantenimiento(' . $data[$i]['id'] . ')">
                    <i class="fas fa-cogs"></i>
                </button>
                ' . $botonAccion . '
            </div>';

        }
        echo json_encode($data);
        die();
    }

    public function stock()
    {
        $data['title'] = 'Stock de Productos';
        $data['script'] = 'stock.js';
        $data['almacenes'] = $this->model->getDatos('almacenes');
        $this->views->getView('admin/productos', 'stock', $data);
    }

    public function listar_stock()
    {
        $data = $this->model->getStock();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }


    public function reporte_stock_pdf()
    {

        $id_almacen = $_GET['almacen'] ?? null;

        ob_start();
        $data['title'] = 'REPORTE DE STOCK';
        $data['empresa'] = $this->model->getEmpresa();
        $data['stock'] = $this->model->getStockPdf($id_almacen);
        $data['almacen_nombre'] = $id_almacen ? $this->model->getAlmacenNombre($id_almacen) : 'TODOS LOS ALMACENES';
        $this->views->getView('admin/rooms', 'reporte_stock_pdf', $data);
        $html = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Reporte_Stock.pdf', array('Attachment' => false));
    }


    public function registrar()
    {
        if (isset($_POST['nombre']) && isset($_POST['codigo'])) {
            $id = strClean($_POST['id']);
            $codigo = strClean($_POST['codigo']);
            $nombre = strClean($_POST['nombre']);
            $slug = strtolower(str_replace(' ', '-', $nombre));
            $descripcion = strClean($_POST['descripcion']);
            $genero = strClean($_POST['genero']);
            $precio_compra = strClean($_POST['precio_compra']);
            $precio_venta = strClean($_POST['precio_venta']);
            $precio_mayorista = strClean($_POST['precio_mayorista']);
            $categoria = strClean($_POST['categoria']);
            $marca = strClean($_POST['marca']);

            if (empty($nombre)) {
                $respuesta = array('msg' => 'EL NOMBRE ES REQUERIDO', 'icono' => 'warning');
            } else if (empty($codigo)) {
                $respuesta = array('msg' => 'EL CÓDIGO ES REQUERIDO', 'icono' => 'warning');
            } else if (empty($precio_compra)) {
                $respuesta = array('msg' => 'EL PRECIO COMPRA ES REQUERIDO', 'icono' => 'warning');
            } else if (empty($precio_venta)) {
                $respuesta = array('msg' => 'EL PRECIO VENTA ES REQUERIDO', 'icono' => 'warning');
            } else if (empty($precio_mayorista)) {
                $respuesta = array('msg' => 'EL PRECIO MAYORISTA ES REQUERIDO', 'icono' => 'warning');
            } else if (empty($categoria)) {
                $respuesta = array('msg' => 'LA CATEGORÍA ES REQUERIDA', 'icono' => 'warning');
            } else if (empty($marca)) {
                $respuesta = array('msg' => 'LA MARCA ES REQUERIDA', 'icono' => 'warning');
            } else {
                if ($id == '') {
                    $verificar = $this->model->getValidar('codigo', $codigo, 'registrar', 0);
                    if (empty($verificar)) {
                        $data = $this->model->registrar(
                            $codigo,
                            $nombre,
                            $slug,
                            $descripcion,
                            $genero,
                            $precio_compra,
                            $precio_venta,
                            $precio_mayorista,
                            $categoria,
                            $marca
                        );
                        if ($data > 0) {
                            $respuesta = array('msg' => 'PRODUCTO REGISTRADO', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'ERROR AL REGISTRAR', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'EL CÓDIGO YA EXISTE', 'icono' => 'warning');
                    }
                } else {
                    $verificar = $this->model->getValidar('codigo', $codigo, 'actualizar', $id);
                    if (empty($verificar)) {
                        $data = $this->model->modificar(
                            $codigo,
                            $nombre,
                            $slug,
                            $descripcion,
                            $genero,
                            $precio_compra,
                            $precio_venta,
                            $precio_mayorista,
                            $categoria,
                            $marca,
                            $id
                        );
                        if ($data > 0) {
                            $respuesta = array('msg' => 'PRODUCTO MODIFICADO', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'ERROR AL MODIFICAR', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'EL CÓDIGO YA EXISTE', 'icono' => 'warning');
                    }
                }
            }
        } else {
            $respuesta = array('msg' => 'DATOS INSUFICIENTES', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }

    //eliminar pro
    public function delete($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->eliminar(0, $id);
            if ($data == 1) {
                $respuesta = array('msg' => 'PRODUCTO INACTIVO', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'ERROR AL ELIMINAR', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }

    public function restaurar($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->eliminar(1, $id);
            if ($data == 1) {
                $respuesta = array('msg' => 'PRODUCTO RESTAURADO', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'ERROR AL ELIMINAR', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
    //editar pro
    public function edit($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->getProducto($id);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function galeriaImagenes()
    {
        $id = $_POST['idProducto'];
        $folder_name = 'assets/images/productos/' . $id . '/';
        if (!empty($_FILES)) {
            if (!file_exists($folder_name)) {
                mkdir($folder_name);
            }
            $temp_name = $_FILES['file']['tmp_name'];
            $ruta = $folder_name . date('YmdHis') . $_FILES['file']['name'];
            move_uploaded_file($temp_name, $ruta);
        }
    }

    public function verGaleria($id_producto)
    {
        $result = array();
        $directorio = 'assets/images/productos/' . $id_producto;
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
        echo json_encode($result);
        die();
    }

    public function eliminarImg()
    {
        $datos = file_get_contents('php://input');
        $json = json_decode($datos, true);
        $destino = 'assets/images/productos/' . $json['url'];
        if (unlink($destino)) {
            $res = array('msg' => 'IMAGEN ELIMINADO', 'icono' => 'success');
        } else {
            $res = array('msg' => 'ERROR AL ELIMINAR', 'icono' => 'error');
        }
        echo json_encode($res);
        die();
    }

    public function borra_dir($dir)
    {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                unlink($dir . '/' . $file);
            }
        }
        rmdir($dir);
    }

    public function getAtributos($id_producto)
    {
        $data['producto'] = $this->model->getProducto($id_producto);
        $data['detalle'] = $this->model->getAtributos($id_producto);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function mantenimiento()
    {
        $id_producto = $_POST['id_producto'];
        $talla = $_POST['talla'];
        $color = $_POST['color'];
        $almacen = $_POST['almacen'];

        $consulta = $this->model->getVerificar($talla, $color, $id_producto, $almacen);

        if (empty($consulta)) {
            $data = $this->model->registrarMantenimiento($talla, $color, $almacen, $id_producto);
            if ($data > 0) {
                $respuesta = array('msg' => 'ATRIBUTO AGREGADO', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'ERROR AL AGREGAR', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'EL ATRIBUTO YA EXISTE', 'icono' => 'warning');
        }

        echo json_encode($respuesta);
        die();
    }



    public function eliminarDetalle($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->eliminarDetalle($id);
            if ($data == 1) {
                $respuesta = array('msg' => 'detalle eliminado', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'error al eliminar', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
}