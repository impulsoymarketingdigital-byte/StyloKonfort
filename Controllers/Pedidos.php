<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;

class Pedidos extends Controller
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
        $data['title'] = 'Gestión de Pedidos';
        $this->views->getView('admin/pedidos', "index", $data);
    }

    // PASO 1: RECEPCIÓN (Nuevos Pedidos)
    public function listarPedidos()
    {
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
                <button class="btn btn-info btn-sm" type="button" onclick="cambiarProceso(' . $data[$i]['id'] . ', 2)" title="Empacar (Pasar a En Proceso)">
                    <i class="fas fa-box-open"></i>
                </button>
                <button class="btn btn-dark btn-sm" type="button" onclick="cambiarProceso(' . $data[$i]['id'] . ', 4)" title="Anular/Cancelar Pedido">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>';
        }
        echo json_encode($data);
        die();
    }

    // PASO 2: EN PROCESO (Empacados y listos para enviar)
    public function listarProceso()
    {
        $data = $this->model->getPedidosEnProceso();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['estado'] = $this->obtenerBadgeEstado($data[$i]['proceso']);
            $data[$i]['accion'] = '
            <div class="d-flex gap-1">
                <button class="btn btn-success btn-sm" type="button" onclick="verPedido(' . $data[$i]['id'] . ')" title="Ver detalle">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-warning btn-sm" type="button" onclick="verEtiquetaEnvio(' . $data[$i]['id'] . ')" title="Imprimir etiqueta">
                    <i class="fas fa-tag"></i>
                </button>
                <button class="btn btn-primary btn-sm" type="button" onclick="cambiarProceso(' . $data[$i]['id'] . ', 3)" title="Marcar como Entregado">
                    <i class="fas fa-check-circle"></i>
                </button>
                <button class="btn btn-dark btn-sm" type="button" onclick="cambiarProceso(' . $data[$i]['id'] . ', 4)" title="Anular y Retornar Stock">
                    <i class="fas fa-undo"></i>
                </button>
            </div>';
        }
        echo json_encode($data);
        die();
    }

    // PASO 3: ENTREGADOS / FINALIZADOS
    public function listarFinalizados()
    {
        // Se pueden mostrar tanto los entregados (3) como los anulados (4) en el histórico si lo deseas
        $data = $this->model->getPedidos(3);
        // Opcional: También obtener anulados -> $dataAnulados = $this->model->getPedidos(4);
        // $data = array_merge($data, $dataAnulados);

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
            </div>';
        }
        echo json_encode($data);
        die();
    }

    private function obtenerBadgeEstado($proceso)
    {
        switch ($proceso) {
            case 1:
                return '<span class="badge bg-warning">Pendiente</span>';
            case 2:
                return '<span class="badge bg-info">En Proceso / Empacado</span>';
            case 3:
                return '<span class="badge bg-success">Entregado</span>';
            case 4:
                return '<span class="badge bg-dark">Cancelado</span>';
            default:
                return '<span class="badge bg-secondary">Desconocido</span>';
        }
    }

    // EL MOTOR DE ACTUALIZACIÓN (BLINDADO)
    public function update($datos)
    {
        // Limpiamos la entrada para evitar ataques
        $array = explode(',', strClean($datos));
        $idPedido = intval($array[0]);
        $proceso = intval($array[1]);

        if ($idPedido > 0) {
            // Obtenemos el estado actual del pedido en la base de datos
            $pedidoActual = $this->model->getPedido($idPedido);
            $estadoActual = intval($pedidoActual['proceso']);

            // LÓGICA 1: Si pasamos de Recepción (1) a En Proceso (2) -> DESCONTAMOS STOCK
            if ($estadoActual == 1 && $proceso == 2) {
                $productosInvalidos = $this->model->verificarStockPedido($idPedido);
                
                if (count($productosInvalidos) > 0) {
                    $mensaje = "No se puede empacar. Stock insuficiente en Bodega Principal:\n\n";
                    foreach ($productosInvalidos as $prod) {
                        $mensaje .= '• ' . $prod['producto'] . ' (' . $prod['atributos'] . '): ';
                        $mensaje .= 'Disp: ' . $prod['stock_disponible'] . ', Req: ' . $prod['cantidad_requerida'] . '\n';
                    }
                    echo json_encode(array('msg' => $mensaje, 'icono' => 'error'));
                    die();
                }
                // Si hay stock, lo descontamos de la estantería
                $this->descontarStock($idPedido);
            }

            // LÓGICA 2: Si el pedido se Finaliza (3) -> Solo actualizamos estado a COMPLETO
            if ($proceso == 3) {
                // El stock ya se debió haber descontado en el paso 2, solo cerramos la orden
                $this->model->actualizarEstadoCompleto($idPedido);
            }

            // LÓGICA 3: Si se Cancela (4) y ya estaba "En Proceso" (2) -> DEVOLVEMOS STOCK
            if ($proceso == 4 && $estadoActual == 2) {
                $this->restaurarStock($idPedido);
            }

            // Guardar el nuevo estado en la Base de Datos
            $data = $this->model->actualizarEstado($proceso, $idPedido);

            if ($data == 1) {
                $msgExito = 'Estado del pedido actualizado';
                if ($proceso == 2) $msgExito = 'Pedido en proceso. Inventario descontado.';
                if ($proceso == 4) $msgExito = 'Pedido Anulado correctamente.';
                
                echo json_encode(array('msg' => $msgExito, 'icono' => 'success'));
            } else {
                echo json_encode(array('msg' => 'Error al actualizar el estado', 'icono' => 'error'));
            }
        }
        die();
    }

    private function descontarStock($idPedido)
    {
        $detalles = $this->model->getDetallePedido($idPedido);
        foreach ($detalles as $detalle) {
            if (!empty($detalle['id_talla_color'])) {
                $atributo = $this->model->getStockDetalle($detalle['id_talla_color']);
                if ($atributo) {
                    $nuevoStock = $atributo['stock'] - $detalle['cantidad'];
                    $this->model->actualizarStockDetalle($nuevoStock, $detalle['id_talla_color']);
                }
            }
        }
    }

    // NUEVO: Función para devolver el stock si la compra fracasa
    private function restaurarStock($idPedido)
    {
        $detalles = $this->model->getDetallePedido($idPedido);
        foreach ($detalles as $detalle) {
            if (!empty($detalle['id_talla_color'])) {
                $atributo = $this->model->getStockDetalle($detalle['id_talla_color']);
                if ($atributo) {
                    $nuevoStock = $atributo['stock'] + $detalle['cantidad'];
                    $this->model->actualizarStockDetalle($nuevoStock, $detalle['id_talla_color']);
                }
            }
        }
    }

    public function verPedido($idPedido)
    {
        $idPedido = intval($idPedido);
        $pedido = $this->model->getPedido($idPedido);
        $productos = $this->model->getDetallePedido($idPedido);

        for ($i = 0; $i < count($productos); $i++) {
            $id_talla_color = $productos[$i]['id_talla_color'];
            $atributos = $this->model->getTallaColor($id_talla_color);

            if ($atributos) {
                $productos[$i]['atributos'] = ($atributos['talla'] ?? '') . ' - ' . ($atributos['color'] ?? '');
            } else {
                $productos[$i]['atributos'] = 'Estándar';
            }
        }

        $data = array(
            'pedido' => $pedido,
            'productos' => $productos,
            'moneda' => MONEDA
        );

        echo json_encode($data);
        die();
    }

    public function reporte($datos)
    {
        ob_start();
        $array = explode(',', strClean($datos));
        $tipo = $array[0];
        $idPedido = intval($array[1]);

        $data['title'] = 'Reporte Pedido';
        $data['empresa'] = $this->model->getEmpresa();
        $data['pedido'] = $this->model->getPedido($idPedido);
        $data['detalle'] = $this->model->getDetallePedido($idPedido);

        if (empty($data['pedido'])) {
            echo '<h3>Página no encontrada o pedido inválido</h3>';
            exit;
        }

        for ($i = 0; $i < count($data['detalle']); $i++) {
            $id_talla_color = $data['detalle'][$i]['id_talla_color'];
            $atributos = $this->model->getTallaColor($id_talla_color);
            if ($atributos) {
                $data['detalle'][$i]['talla'] = $atributos['talla'] ?? '';
                $data['detalle'][$i]['color'] = $atributos['color'] ?? '';
            }
        }

        $this->views->getView('admin/pedidos', $tipo, $data);
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);

        if ($tipo == 'ticked') { // Ojo: el desarrollador lo llamó 'ticked', se mantiene para compatibilidad
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
        $idPedido = intval($idPedido);

        $data['title'] = 'Etiqueta Envío';
        $data['empresa'] = $this->model->getEmpresa();
        $data['pedido'] = $this->model->getPedido($idPedido);
        
        if (empty($data['pedido'])) {
            echo '<h3>Página no encontrada o pedido inválido</h3>';
            exit;
        }
        
        $data['cliente'] = $this->model->getCliente($data['pedido']['id_cliente']);

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