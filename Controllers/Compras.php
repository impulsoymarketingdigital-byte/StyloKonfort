<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

class Compras extends Controller
{
    private $id_usuario;

    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $this->id_usuario = $_SESSION['id_usuario'];
    }

    public function index()
    {
        $data['title'] = 'Compras';
        $data['proveedores'] = $this->model->getProveedores();
        $data['almacenes'] = $this->model->getAlmacenes();
        $this->views->getView('admin/compras', 'index', $data);
    }

    public function listar_compras()
    {
        $data['title'] = 'Listar Compras';
        $this->views->getView('admin/compras', 'listar_compras', $data);
    }



    public function buscarPorNombre()
    {
        $array = array();
        $valor = $_GET['term'];
        $data = $this->model->buscarPorNombre($valor);
        foreach ($data as $row) {
            $result['id'] = $row['id'];
            $result['label'] = $row['nombre'];
            array_push($array, $result);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function buscarProveedor()
    {
        $array = array();
        $valor = $_GET['term'];
        $data = $this->model->buscarProveedor($valor);
        foreach ($data as $row) {
            $result['id'] = $row['id'];
            $result['label'] = $row['nombre'];
            $result['ruc'] = $row['ruc'];
            $result['telefono'] = $row['telefono'];
            $result['direccion'] = $row['direccion'];
            $result['email'] = $row['email'];
            array_push($array, $result);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function size($idProducto)
    {
        $data = $this->model->getSizes($idProducto);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrarCompra()
    {
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);
        $total = 0;
        $descuentoTotal = 0;

        if (!empty($datos['productos'])) {
            $fecha = date('Y-m-d H:i:s');
            $idProveedor = $datos['idProveedor'];
            $idAlmacen = $datos['idAlmacen'];
            $tipoComprobante = isset($datos['tipoComprobante']) ? $datos['tipoComprobante'] : 'FACTURA';

            if (empty($idProveedor)) {
                $res = array('msg' => 'EL PROVEEDOR ES REQUERIDO', 'type' => 'warning');
            } else {
                // Calcular total
                foreach ($datos['productos'] as $producto) {
                    $atributo = $this->model->getOrCreateAtributos(
                        $producto['size'],
                        $producto['color'],
                        $producto['idProducto'],
                        $idAlmacen
                    );

                    if (empty($atributo)) {
                        $res = array('msg' => 'ERROR: No se pudo crear el producto en este almacén', 'type' => 'error');
                        echo json_encode($res);
                        die();
                    }

                    $precioCompra = isset($producto['precioCompra']) ? $producto['precioCompra'] : $atributo['precio_compra'];
                    $descuento = isset($producto['descuento']) ? $producto['descuento'] : 0;
                    $subTotal = ($precioCompra * $producto['cantidad']) - $descuento;
                    $total += $subTotal;
                    $descuentoTotal += $descuento;
                }

                $numero_compra = $this->model->generarNumeroCompra();

                $compra = $this->model->registrarCompra(
                    $numero_compra,
                    $tipoComprobante,
                    $total,
                    $descuentoTotal,
                    $fecha,
                    $idProveedor,
                    $idAlmacen,
                    $_SESSION['id_usuario'],
                    null  // ⭐ YA NO SE ENVÍA id_caja
                );

                if ($compra > 0) {
                    foreach ($datos['productos'] as $producto) {
                        $result = $this->model->getProducto($producto['idProducto']);
                        $atributo = $this->model->getOrCreateAtributos(
                            $producto['size'],
                            $producto['color'],
                            $producto['idProducto'],
                            $idAlmacen
                        );

                        $precioCompra = isset($producto['precioCompra']) ? $producto['precioCompra'] : $atributo['precio_compra'];
                        $descuento = isset($producto['descuento']) ? $producto['descuento'] : 0;
                        $subTotal = ($precioCompra * $producto['cantidad']) - $descuento;

                        $this->model->registrarDetalleCompra(
                            $compra,
                            $result['id'],
                            $result['nombre'],
                            $precioCompra,
                            $producto['cantidad'],
                            $descuento,
                            $subTotal,
                            $atributo['id']
                        );

                        // Aumentar stock
                        $nuevoStock = $atributo['stock'] + $producto['cantidad'];
                        $this->model->actualizarStockDetalle($nuevoStock, $atributo['id']);

                        // Actualizar precio de compra
                        $this->model->actualizarPrecioCompra($precioCompra, $result['id']);
                    }

                    $res = array('msg' => 'COMPRA REGISTRADA', 'type' => 'success', 'idCompra' => $compra);
                } else {
                    $res = array('msg' => 'ERROR AL REGISTRAR COMPRA', 'type' => 'error');
                }
            }
        } else {
            $res = array('msg' => 'CARRITO VACIO', 'type' => 'warning');
        }

        echo json_encode($res);
        die();
    }

    public function reporte($datos)
    {
        ob_start();
        $array = explode(',', $datos);
        $tipo = $array[0];
        $idCompra = $array[1];

        $data['title'] = 'Reporte Compra';
        $data['empresa'] = $this->model->getEmpresa();
        $data['compra'] = $this->model->getCompra($idCompra);
        $data['detalle'] = $this->model->getDetalleCompra($idCompra);

        if (empty($data['compra'])) {
            echo 'Página no Encontrada';
            exit;
        }

        $this->views->getView('admin/compras', $tipo, $data);
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);

        if ($tipo == 'ticked') {
            $dompdf->setPaper(array(0, 0, 226.77, 500), 'portrait');
        } else {
            $dompdf->setPaper('A4', 'vertical');
        }

        $dompdf->render();
        $dompdf->stream('compra.pdf', array('Attachment' => false));
    }

    public function listar()
    {
        $data = $this->model->getCompras();

        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['estado'] == 'COMPLETADO') {
                $data[$i]['estado'] = '<span class="badge bg-success">Completado</span>';
            } else {
                $data[$i]['estado'] = '<span class="badge bg-secondary">Anulado</span>';
            }

            if ($data[$i]['estado'] == '<span class="badge bg-success">Completado</span>') {
                $data[$i]['acciones'] = '
                <div class="text-center">
                    <button class="btn btn-info btn-sm" onclick="verDetalle(' . $data[$i]['id'] . ')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="anularCompra(' . $data[$i]['id'] . ')">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="verReporte(' . $data[$i]['id'] . ')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>';
            } else {
                $data[$i]['acciones'] = '
                <div class="text-center">
                    <button class="btn btn-info btn-sm" onclick="verDetalle(' . $data[$i]['id'] . ')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="verReporte(' . $data[$i]['id'] . ')">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>';
            }
        }

        echo json_encode($data);
        die();
    }

    public function detalle($idCompra)
    {
        if (is_numeric($idCompra)) {
            $data['compra'] = $this->model->getCompra($idCompra);
            $data['detalle'] = $this->model->getDetalleCompra($idCompra);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function anular($idCompra)
    {
        if (isset($_GET) && is_numeric($idCompra)) {
            $data = $this->model->anular($idCompra);

            if ($data == 1) {
                $detalles = $this->model->getDetalleCompra($idCompra);

                foreach ($detalles as $detalle) {
                    $tallasColores = $this->model->getTallaColorPorId($detalle['id_talla_color']);

                    if (!empty($tallasColores)) {
                        $nuevoStock = $tallasColores['stock'] - $detalle['cantidad'];
                        if ($nuevoStock < 0)
                            $nuevoStock = 0;
                        $this->model->actualizarStockDetalle($nuevoStock, $detalle['id_talla_color']);
                    }
                }

                $res = array('msg' => 'COMPRA ANULADA', 'type' => 'success');
            } else {
                $res = array('msg' => 'ERROR AL ANULAR', 'type' => 'error');
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO', 'type' => 'error');
        }

        echo json_encode($res);
        die();
    }
}
?>