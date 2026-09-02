<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

class Cajas extends Controller
{
    private $id_usuario;
    private $id_almacen;

    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL);
            exit;
        }
        $this->id_usuario = $_SESSION['id_usuario'];
        $this->id_almacen = $_SESSION['id_almacen']; // ✅ Obtener almacén de la sesión
    }

    public function index()
    {
        $data['script'] = 'cajas.js';
        $data['title'] = 'Movimientos de Caja';
        $this->views->getView('admin/cajas', 'index', $data);
    }

    public function listar_cajas()
    {
        $data['title'] = 'Historial de Cajas';
        $data['script'] = 'listar_cajas.js';
        $this->views->getView('admin/cajas', 'listar_cajas', $data);
    }

    public function listar()
    {
        $data = $this->model->getCajas($this->id_usuario);

        for ($i = 0; $i < count($data); $i++) {
            $tipo = $data[$i]['tipo'];
            $colorTipo = $tipo == 'INGRESO' ? 'success' : 'danger';
            $data[$i]['tipo'] = "<div class='badge rounded-pill text-$colorTipo bg-light-$colorTipo p-2 text-uppercase px-3'>$tipo</div>";
        }

        echo json_encode($data);
        die();
    }

    public function listarCajas()
    {
        $data = $this->model->listarCajas($this->id_usuario);

        for ($i = 0; $i < count($data); $i++) {
            $estado = $data[$i]['estado'];
            $color = $estado == 1 ? 'success' : 'danger';
            $texto = $estado == 1 ? 'ABIERTA' : 'CERRADA';
            $data[$i]['apertura'] = "<div class='badge rounded-pill text-$color bg-light-$color p-2 text-uppercase px-3'>$texto</div>";

            $data[$i]['monto_inicial'] = 'COP. ' . number_format($data[$i]['monto_inicial'], 2);
            $data[$i]['monto_final'] = $data[$i]['monto_final'] ? 'COP. ' . number_format($data[$i]['monto_final'], 2) : '-';
            $data[$i]['monto_fisico'] = $data[$i]['monto_fisico'] ? 'COP. ' . number_format($data[$i]['monto_fisico'], 2) : '-';
        }

        echo json_encode($data);
        die();
    }

    public function abrirCaja()
    {
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);

        if (empty($datos['monto'])) {
            $res = array('msg' => 'EL MONTO ES REQUERIDO', 'type' => 'warning');
        } else {
            $monto = strClean($datos['monto']);
            $verificar = $this->model->getCajaAbierta($this->id_usuario);

            if (empty($verificar)) {
                // ✅ Pasar id_almacen al modelo
                $id_caja = $this->model->abrirCaja($monto, $this->id_usuario, $this->id_almacen);

                if ($id_caja > 0) {
                    $this->model->insertarMovimientoAperturaCaja($id_caja, $monto, $this->id_usuario);
                    $res = array('msg' => 'CAJA ABIERTA', 'type' => 'success');
                } else {
                    $res = array('msg' => 'ERROR AL ABRIR LA CAJA', 'type' => 'error');
                }
            } else {
                $res = array('msg' => 'LA CAJA YA ESTA ABIERTA', 'type' => 'warning');
            }
        }

        echo json_encode($res);
        die();
    }

    public function guardarMovimiento()
    {
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);

        if (empty($datos['type']) || empty($datos['description']) || empty($datos['amount'])) {
            $res = array('msg' => 'TODOS LOS CAMPOS SON REQUERIDOS', 'type' => 'warning');
        } else {
            $type = strClean($datos['type']);
            $description = strClean($datos['description']);
            $amount = strClean($datos['amount']);

            $cajaAbierta = $this->model->getCajaAbierta($this->id_usuario);

            if (empty($cajaAbierta)) {
                $res = array('msg' => 'NO HAY CAJA ABIERTA', 'type' => 'warning');
            } else {
                $id_caja = $cajaAbierta['id'];

                if (!is_numeric($amount) || $amount <= 0) {
                    $res = array('msg' => 'EL MONTO DEBE SER UN NÚMERO VÁLIDO MAYOR A 0', 'type' => 'warning');
                } else {
                    if ($type === 'EGRESO') {
                        $saldoActual = $this->model->getSaldoActual($this->id_usuario, $id_caja);
                        if ($amount > $saldoActual['saldo']) {
                            $res = array('msg' => 'EL MONTO NO PUEDE SER MAYOR AL SALDO ACTUAL', 'type' => 'warning');
                            echo json_encode($res);
                            die();
                        }
                    }

                    $resultado = $this->model->guardarMovimiento($type, $description, $amount, $this->id_usuario, $id_caja);

                    if ($resultado > 0) {
                        $res = array('msg' => 'MOVIMIENTO REGISTRADO', 'type' => 'success');
                    } else {
                        $res = array('msg' => 'ERROR AL GUARDAR EL MOVIMIENTO', 'type' => 'error');
                    }
                }
            }
        }

        echo json_encode($res);
        die();
    }

    public function getSaldo()
    {
        $cajaAbierta = $this->model->getCajaAbierta($this->id_usuario);

        if (empty($cajaAbierta)) {
            $res = array('saldo' => 0, 'msg' => 'NO HAY CAJA ABIERTA', 'type' => 'warning');
        } else {
            $id_caja = $cajaAbierta['id'];
            $saldoActual = $this->model->getSaldoActual($this->id_usuario, $id_caja);
            $res = array('saldo' => number_format($saldoActual['saldo'], 2, '.', ''), 'type' => 'success');
        }

        echo json_encode($res);
        die();
    }

    public function getDatosCierre()
    {
        $cajaAbierta = $this->model->getCajaAbierta($this->id_usuario);

        if (empty($cajaAbierta)) {
            $res = array('msg' => 'NO HAY CAJA ABIERTA', 'type' => 'warning');
            echo json_encode($res);
            die();
        }

        $id_caja = $cajaAbierta['id'];

        $totalVentas = $this->model->getTotalVentas($this->id_usuario, $id_caja);
        $totalCompras = $this->model->getTotalCompras($this->id_usuario, $id_caja);

        $montoInicial = $cajaAbierta['monto_inicial'] ?? 0;
        $totalIngresos = $totalVentas['total'] ?? 0;
        $totalEgresos = $totalCompras['total'] ?? 0;
        $saldoFinal = $montoInicial + $totalIngresos - $totalEgresos;

        $data = array(
            'montoInicial' => number_format($montoInicial, 2, '.', ''),
            'totalVentas' => number_format($totalIngresos, 2, '.', ''),
            'totalCompras' => number_format($totalEgresos, 2, '.', ''),
            'saldoFinal' => number_format($saldoFinal, 2, '.', '')
        );

        echo json_encode($data);
        die();
    }

    public function cerrarCaja()
    {
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);

        if (empty($datos['physical_amount'])) {
            $res = array('msg' => 'EL MONTO FÍSICO ES REQUERIDO', 'type' => 'warning');
        } else {
            $monto_fisico = strClean($datos['physical_amount']);
            $monto_final = strClean($datos['final_amount']);
            $fecha_cierre = date('Y-m-d H:i:s');

            $result = $this->model->cerrarCaja($monto_final, $monto_fisico, $fecha_cierre, $this->id_usuario);

            if ($result == 1) {
                $res = array('msg' => 'CAJA CERRADA CORRECTAMENTE', 'type' => 'success');
            } else {
                $res = array('msg' => 'ERROR AL CERRAR LA CAJA', 'type' => 'error');
            }
        }

        echo json_encode($res);
        die();
    }
}
?>