<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;

class Pedidos extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
    }

    public function index()
    {
        $data['title'] = 'pedidos';
        $this->views->getView('admin/pedidos', "index", $data);
    }

    public function listarPedidos()
    {
        // Proceso 1: Recepción del pedido
        $data = $this->model->getPedidos(1);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['estado'] = $this->obtenerBadgeEstado($data[$i]['proceso']);
            $data[$i]['accion'] = '
            <div class="d-flex gap-1">
                <button class="btn btn-success btn-sm" type="button" onclick="verPedido(' . $data[$i]['id'] . ')" title="Ver detalle">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-danger btn-sm" type="button" onclick="verReportePedido(' . $data[$i]['id'] . ')" title="Imprimir ticket">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <button class="btn btn-warning btn-sm" type="button" onclick="verEtiquetaEnvio(' . $data[$i]['id'] . ')" title="Imprimir etiqueta">
                    <i class="fas fa-tag"></i>
                </button>
                <button class="btn btn-info btn-sm" type="button" onclick="cambiarProceso(' . $data[$i]['id'] . ', 2)" title="Pasar a En Proceso">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>';
        }
        echo json_encode($data);
        die();
    }

    public function listarProceso()
    {
        // Proceso 2: En Proceso
        $data = $this->model->getPedidosEnProceso();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['estado'] = $this->obtenerBadgeEstado($data[$i]['proceso']);

          $data[$i]['accion'] = '
            <div class="d-flex gap-1">
                <button class="btn btn-success btn-sm" type="button" onclick="verPedido(' . $data[$i]['id'] . ')" title="Ver detalle">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-danger btn-sm" type="button" onclick="verReportePedido(' . $data[$i]['id'] . ')" title="Imprimir ticket">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <button class="btn btn-warning btn-sm" type="button" onclick="verEtiquetaEnvio(' . $data[$i]['id'] . ')" title="Imprimir etiqueta">
                    <i class="fas fa-tag"></i>
                </button>
                <button class="btn btn-warning btn-sm" type="button" onclick="cambiarProceso(' . $data[$i]['id'] . ', 3)" title="Marcar como Entregado">
                    <i class="fas fa-check-circle"></i>
                </button>
            </div>';
        }
        echo json_encode($data);
        die();
    }

    public function listarFinalizados()
    {
        // Proceso 3: Entregados
        $data = $this->model->getPedidos(3);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['estado'] = $this->obtenerBadgeEstado($data[$i]['proceso']);
            $data[$i]['accion'] = '
            <div class="d-flex gap-1">
                <button class="btn btn-success btn-sm" type="button" onclick="verPedido(' . $data[$i]['id'] . ')" title="Ver detalle">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-danger btn-sm" type="button" onclick="verReportePedido(' . $data[$i]['id'] . ')" title="Imprimir ticket">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <button class="btn btn-warning btn-sm" type="button" onclick="verEtiquetaEnvio(' . $data[$i]['id'] . ')" title="Imprimir etiqueta">
                    <i class="fas fa-tag"></i>
                </button>
            </div>';
        }
        echo json_encode($data);
        die();
    }

    private function obtenerBadgeEstado($proceso)
    {
        switch ($proceso) {
            case 1:
                return '<span class="badge bg-warning">Recepción</span>';
            case 2:
                return '<span class="badge bg-info">En Proceso</span>';
            case 3:
                return '<span class="badge bg-success">Entregado</span>';
            default:
                return '<span class="badge bg-dark">Desconocido</span>';
        }
    }

    public function update($datos)
    {
        $array = explode(',', $datos);
        $idPedido = $array[0];
        $proceso = $array[1];

        if (is_numeric($idPedido)) {
            // Si el proceso es 3 (Entregado), verificar stock antes de descontar
            if ($proceso == 3) {
                // Verificar stock disponible
                $productosInvalidos = $this->model->verificarStockPedido($idPedido);

                if (count($productosInvalidos) > 0) {
                    // Hay productos sin stock suficiente
                    $mensaje = 'No se puede entregar el pedido. Los siguientes productos no tienen stock suficiente:\n\n';

                    foreach ($productosInvalidos as $prod) {
                        $mensaje .= '• ' . $prod['producto'] . ' (' . $prod['atributos'] . '): ';
                        $mensaje .= 'Stock disponible: ' . $prod['stock_disponible'] . ', ';
                        $mensaje .= 'Cantidad requerida: ' . $prod['cantidad_requerida'] . '\n';
                    }

                    $respuesta = array('msg' => $mensaje, 'icono' => 'error');
                    echo json_encode($respuesta);
                    die();
                }

                // Hay stock suficiente, descontar
                $this->descontarStock($idPedido);

                // Actualizar estado a COMPLETADO
                $this->model->actualizarEstadoCompleto($idPedido);
            }

            // Actualizar proceso
            $data = $this->model->actualizarEstado($proceso, $idPedido);

            if ($data == 1) {
                $mensajeExito = ($proceso == 3) ? 'Pedido entregado y stock actualizado correctamente' : 'Pedido actualizado correctamente';
                $respuesta = array('msg' => $mensajeExito, 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'Error al actualizar', 'icono' => 'error');
            }
            echo json_encode($respuesta);
        }
        die();
    }

    private function descontarStock($idPedido)
    {
        // Obtener detalles del pedido
        $detalles = $this->model->getDetallePedido($idPedido);

        foreach ($detalles as $detalle) {
            $id_talla_color = $detalle['id_talla_color'];
            $cantidad = $detalle['cantidad'];

            // Obtener stock actual
            $atributo = $this->model->getStockDetalle($id_talla_color);

            if ($atributo) {
                $stockActual = $atributo['stock'];
                $nuevoStock = $stockActual - $cantidad;

                // Actualizar stock
                $this->model->actualizarStockDetalle($nuevoStock, $id_talla_color);
            }
        }
    }

    public function verPedido($idPedido)
    {
        $pedido = $this->model->getPedido($idPedido);
        $productos = $this->model->getDetallePedido($idPedido);

        $configuracion = $this->model->getConfiguracion();
        $moneda = $configuracion['moneda'] ?? 'COP. ';

        for ($i = 0; $i < count($productos); $i++) {
            $id_talla_color = $productos[$i]['id_talla_color'];

            $atributos = $this->model->getTallaColor($id_talla_color);

            if ($atributos) {
                $talla = $atributos['talla'] ?? '';
                $color = $atributos['color'] ?? '';
                $productos[$i]['atributos'] = $talla . ' - ' . $color;
            } else {
                $productos[$i]['atributos'] = ' - ';
            }
        }

        $data = array(
            'pedido' => $pedido,
            'productos' => $productos,
            'moneda' => $moneda
        );

        echo json_encode($data);
        die();
    }

    public function reporte($datos)
    {
        ob_start();
        $array = explode(',', $datos);
        $tipo = $array[0];
        $idPedido = $array[1];

        $data['title'] = 'Reporte Pedido';
        $data['empresa'] = $this->model->getEmpresa();
        $data['pedido'] = $this->model->getPedido($idPedido);
        $data['detalle'] = $this->model->getDetallePedido($idPedido);

        // Agregar atributos a los productos
        for ($i = 0; $i < count($data['detalle']); $i++) {
            $id_talla_color = $data['detalle'][$i]['id_talla_color'];
            $atributos = $this->model->getTallaColor($id_talla_color);

            if ($atributos) {
                $data['detalle'][$i]['talla'] = $atributos['talla'] ?? '';
                $data['detalle'][$i]['color'] = $atributos['color'] ?? '';
            }
        }

        if (empty($data['pedido'])) {
            echo 'Página no encontrada';
            exit;
        }

        $this->views->getView('admin/pedidos', $tipo, $data);
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
        $dompdf->stream('pedido_' . $idPedido . '.pdf', array('Attachment' => false));
    }

    public function etiqueta($idPedido)
    {
        ob_start();

        $data['title'] = 'Etiqueta Envío';
        $data['empresa'] = $this->model->getEmpresa();
        $data['pedido'] = $this->model->getPedido($idPedido);
        $data['cliente'] = $this->model->getCliente($data['pedido']['id_cliente']);

        if (empty($data['pedido'])) {
            echo 'Página no encontrada';
            exit;
        }

        $this->views->getView('admin/pedidos', 'etiqueta', $data);
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper(array(0, 0, 226.77, 500), 'portrait');
        $dompdf->render();
        $dompdf->stream('etiqueta_' . $idPedido . '.pdf', array('Attachment' => false));
    }
}
?>