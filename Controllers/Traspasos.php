<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

class Traspasos extends Controller
{
    private $id_usuario;
    
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $this->id_usuario = $_SESSION['id_usuario'];
    }

    public function index()
    {
        $data['title'] = 'Traspasos';
        $data['almacenes'] = $this->model->getAlmacenes();
        $this->views->getView('admin/traspasos', 'index', $data);
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

    public function size($idProducto)
    {
        $idAlmacen = isset($_GET['almacen']) ? $_GET['almacen'] : 1;
        $data = $this->model->getSizes($idProducto, $idAlmacen);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function registrarTraspaso()
    {
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);
        $totalProductos = 0;

        if (!empty($datos['productos'])) {
            $fecha = date('Y-m-d H:i:s');
            $idAlmacenOrigen = $datos['idAlmacenOrigen'];
            $idAlmacenDestino = $datos['idAlmacenDestino'];

            if (empty($idAlmacenOrigen)) {
                $res = array('msg' => 'EL ALMACÉN ORIGEN ES REQUERIDO', 'type' => 'warning');
            } elseif (empty($idAlmacenDestino)) {
                $res = array('msg' => 'EL ALMACÉN DESTINO ES REQUERIDO', 'type' => 'warning');
            } elseif ($idAlmacenOrigen == $idAlmacenDestino) {
                $res = array('msg' => 'EL ALMACÉN ORIGEN Y DESTINO NO PUEDEN SER IGUALES', 'type' => 'warning');
            } else {
                // Validar stock disponible
                foreach ($datos['productos'] as $producto) {
                    $atributoOrigen = $this->model->getAtributos(
                        $producto['size'], 
                        $producto['color'], 
                        $producto['idProducto'],
                        $idAlmacenOrigen
                    );
                    
                    if (empty($atributoOrigen)) {
                        $res = array('msg' => 'ERROR: Producto no encontrado en almacén origen', 'type' => 'error');
                        echo json_encode($res);
                        die();
                    }

                    if ($atributoOrigen['stock'] < $producto['cantidad']) {
                        $res = array('msg' => 'STOCK INSUFICIENTE EN ALMACÉN ORIGEN', 'type' => 'error');
                        echo json_encode($res);
                        die();
                    }

                    $totalProductos += $producto['cantidad'];
                }

                // Generar número de traspaso
                $numero_traspaso = $this->model->generarNumeroTraspaso();

                // Registrar traspaso
                $traspaso = $this->model->registrarTraspaso(
                    $numero_traspaso,
                    $totalProductos,
                    $fecha,
                    $idAlmacenOrigen,
                    $idAlmacenDestino,
                    $this->id_usuario
                );

                if ($traspaso > 0) {
                    // Registrar detalles y actualizar stock
                    foreach ($datos['productos'] as $producto) {
                        $result = $this->model->getProducto($producto['idProducto']);
                        
                        // Obtener atributo origen
                        $atributoOrigen = $this->model->getAtributos(
                            $producto['size'], 
                            $producto['color'], 
                            $producto['idProducto'],
                            $idAlmacenOrigen
                        );

                        // Obtener o crear atributo destino
                        $atributoDestino = $this->model->getOrCreateAtributos(
                            $producto['size'], 
                            $producto['color'], 
                            $producto['idProducto'],
                            $idAlmacenDestino
                        );

                        // Guardar detalle
                        $this->model->registrarDetalleTraspaso(
                            $traspaso,
                            $result['id'],
                            $result['nombre'],
                            $producto['cantidad'],
                            $atributoOrigen['id'],
                            $atributoDestino['id']
                        );

                        // RESTAR stock del almacén origen
                        $nuevoStockOrigen = $atributoOrigen['stock'] - $producto['cantidad'];
                        $this->model->actualizarStockDetalle($nuevoStockOrigen, $atributoOrigen['id']);

                        // SUMAR stock al almacén destino
                        $nuevoStockDestino = $atributoDestino['stock'] + $producto['cantidad'];
                        $this->model->actualizarStockDetalle($nuevoStockDestino, $atributoDestino['id']);
                    }

                    $res = array('msg' => 'TRASPASO REGISTRADO', 'type' => 'success', 'idTraspaso' => $traspaso);
                } else {
                    $res = array('msg' => 'ERROR AL REGISTRAR TRASPASO', 'type' => 'error');
                }
            }
        } else {
            $res = array('msg' => 'CARRITO VACIO', 'type' => 'warning');
        }

        echo json_encode($res);
        die();
    }

    public function listar()
    {
        $data = $this->model->getTraspasos();
        
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
                    <button class="btn btn-warning btn-sm" onclick="anularTraspaso(' . $data[$i]['id'] . ')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';
            } else {
                $data[$i]['acciones'] = '
                <div class="text-center">
                    <button class="btn btn-info btn-sm" onclick="verDetalle(' . $data[$i]['id'] . ')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>';
            }
        }

        echo json_encode($data);
        die();
    }

    public function detalle($idTraspaso)
    {
        if (is_numeric($idTraspaso)) {
            $data['traspaso'] = $this->model->getTraspaso($idTraspaso);
            $data['detalle'] = $this->model->getDetalleTraspaso($idTraspaso);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function anular($idTraspaso)
    {
        if (isset($_GET) && is_numeric($idTraspaso)) {
            $data = $this->model->anular($idTraspaso);
            
            if ($data == 1) {
                $detalles = $this->model->getDetalleTraspaso($idTraspaso);
                $traspaso = $this->model->getTraspaso($idTraspaso);
                
                foreach ($detalles as $detalle) {
                    // Regresar stock al origen
                    $tallaColorOrigen = $this->model->getTallaColorPorId($detalle['id_talla_color_origen']);
                    if (!empty($tallaColorOrigen)) {
                        $nuevoStockOrigen = $tallaColorOrigen['stock'] + $detalle['cantidad'];
                        $this->model->actualizarStockDetalle($nuevoStockOrigen, $detalle['id_talla_color_origen']);
                    }

                    // Restar stock del destino
                    $tallaColorDestino = $this->model->getTallaColorPorId($detalle['id_talla_color_destino']);
                    if (!empty($tallaColorDestino)) {
                        $nuevoStockDestino = $tallaColorDestino['stock'] - $detalle['cantidad'];
                        if ($nuevoStockDestino < 0) $nuevoStockDestino = 0;
                        $this->model->actualizarStockDetalle($nuevoStockDestino, $detalle['id_talla_color_destino']);
                    }
                }
                
                $res = array('msg' => 'TRASPASO ANULADO', 'type' => 'success');
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