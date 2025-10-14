<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Ventas extends Controller
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
        $data['title'] = 'Ventas';
        $this->views->getView('admin/ventas', 'index', $data);
    }

    //buscar Productos por nombre
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

    //buscar cliente
    public function buscarCliente()
    {
        $array = array();
        $valor = $_GET['term'];
        $data = $this->model->buscarCliente($valor);
        foreach ($data as $row) {
            $result['id'] = $row['id'];
            $result['label'] = $row['nombre'];
            $result['telefono'] = $row['telefono'];
            $result['direccion'] = $row['direccion'];
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
    public function registrarVenta()
    {
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);
        $total = 0;

        if (!empty($datos['productos'])) {
            $fecha = date('Y-m-d H:i:s');
            $idCliente = $datos['idCliente'];
            $metodo = isset($datos['metodo']) ? $datos['metodo'] : 'VENTA DIRECTA';

            if (empty($idCliente)) {
                $res = array('msg' => 'EL CLIENTE ES REQUERIDO', 'type' => 'warning');
            } else {
                // Calcular total
                foreach ($datos['productos'] as $producto) {
                    $atributo = $this->model->getAtributos($producto['size'], $producto['color'], $producto['idProducto']);
                    if (empty($atributo)) {
                        $res = array('msg' => 'ERROR: Producto sin stock disponible', 'type' => 'error');
                        echo json_encode($res);
                        die();
                    }
                    $subTotal = $atributo['precio_venta'] * $producto['cantidad'];
                    $total += $subTotal;
                }

                // Registrar pedido
                $id_transaccion = $metodo . '-' . uniqid();
                $estado = 'COMPLETADO';
                $venta = $this->model->registrarPedido($id_transaccion, $metodo, $total, $estado, $fecha, $idCliente);

                if ($venta > 0) {
                    // Registrar detalles y actualizar stock
                    foreach ($datos['productos'] as $producto) {
                        $result = $this->model->getProducto($producto['idProducto']);
                        $atributo = $this->model->getAtributos($producto['size'], $producto['color'], $producto['idProducto']);

                        // Guardar detalle
                        $this->model->registrarDetallePedido(
                            $venta,
                            $result['id'],
                            $result['nombre'],
                            $atributo['precio_venta'],
                            $producto['cantidad'],
                            $atributo['id']
                        );

                        // Actualizar stock en tallas_colores
                        $nuevoStock = $atributo['stock'] - $producto['cantidad'];
                        $this->model->actualizarStockDetalle($nuevoStock, $atributo['id']);
                    }

                    $res = array('msg' => 'VENTA GENERADA', 'type' => 'success', 'idVenta' => $venta);
                } else {
                    $res = array('msg' => 'ERROR AL GENERAR VENTA', 'type' => 'error');
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
        $idVenta = $array[1];

        $data['title'] = 'Reporte';
        $data['empresa'] = $this->model->getEmpresa();
        $data['venta'] = $this->model->getVenta($idVenta);
        $data['detalle'] = $this->model->getDetallePedido($idVenta);

        if (empty($data['venta'])) {
            echo 'Pagina no Encontrada';
            exit;
        }

        $this->views->getView('admin/ventas', $tipo, $data);
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
        $dompdf->stream('reporte.pdf', array('Attachment' => false));
    }

    public function listar()
    {
        $data = $this->model->getVentas();

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
                    <button class="btn btn-warning btn-sm" onclick="anularVenta(' . $data[$i]['id'] . ')">
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

    public function detalle($idPedido)
    {
        if (is_numeric($idPedido)) {
            $data['pedido'] = $this->model->getVenta($idPedido);
            $data['detalle'] = $this->model->getDetallePedido($idPedido);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function anular($idVenta)
    {
        if (isset($_GET) && is_numeric($idVenta)) {
            $data = $this->model->anular($idVenta);
            if ($data == 1) {
                $detalles = $this->model->getDetallePedido($idVenta);

                foreach ($detalles as $detalle) {
                    $tallasColores = $this->model->getTallaColorPorId($detalle['id_talla_color']);

                    if (!empty($tallasColores)) {
                        $nuevoStock = $tallasColores['stock'] + $detalle['cantidad'];
                        $this->model->actualizarStockDetalle($nuevoStock, $detalle['id_talla_color']);
                    }
                }

                $res = array('msg' => 'VENTA ANULADA', 'type' => 'success');
            } else {
                $res = array('msg' => 'ERROR AL ANULAR', 'type' => 'error');
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO', 'type' => 'error');
        }
        echo json_encode($res);
        die();
    }

    public function verificarStock($idProducto)
    {
        $data = $this->model->getProducto($idProducto);
        echo json_encode($data);
        die();
    }

    public function reportesPDF($accion)
    {
        ob_start();
        $data['title'] = 'Reportes';
        $fecha = date('Y-m-d');
        $desde = $fecha . ' 00:00:00';
        $hasta = $fecha . ' 23:59:59';
        if ($accion == 'dia') {
            $data['ventas'] = $this->model->getReporte($desde, $hasta, $this->id_usuario);
        } else if ($accion == 'semana') {
            $desde1 = date("Y-m-d H:i:s", strtotime($desde . '-7 day'));
            $data['ventas'] = $this->model->getReporte($desde1, $hasta, $this->id_usuario);
        } else {
            $desde1 = date("Y-m-d H:i:s", strtotime($desde . '-30 day'));
            $data['ventas'] = $this->model->getReporte($desde1, $hasta, $this->id_usuario);
        }
        $this->views->getView('admin/ventas', 'reporte', $data);
        $html = ob_get_clean();
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isJavascriptEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'vertical');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('reporte.pdf', array('Attachment' => false));
    }

    public function reportesExcel($accion)
    {
        $data['title'] = 'Reportes';
        $fecha = date('Y-m-d');
        $desde = $fecha . ' 00:00:00';
        $hasta = $fecha . ' 23:59:59';
        if ($accion == 'dia') {
            $data['ventas'] = $this->model->getReporte($desde, $hasta, $this->id_usuario);
        } else if ($accion == 'semana') {
            $desde1 = date("Y-m-d H:i:s", strtotime($desde . '-7 day'));
            $data['ventas'] = $this->model->getReporte($desde1, $hasta, $this->id_usuario);
        } else {
            $desde1 = date("Y-m-d H:i:s", strtotime($desde . '-30 day'));
            $data['ventas'] = $this->model->getReporte($desde1, $hasta, $this->id_usuario);
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()
            ->setCreator($_SESSION['nombre_usuario'])
            ->setTitle("Detalle ventas");

        $spreadsheet->setActiveSheetIndex(0);

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->getColumnDimension('A')->setWidth(50);
        $hojaActiva->getColumnDimension('B')->setWidth(20);
        $hojaActiva->getColumnDimension('C')->setWidth(10);
        $hojaActiva->getColumnDimension('D')->setWidth(30);

        $spreadsheet->getActiveSheet()->getStyle('A1:D1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('008cff');

        $spreadsheet->getActiveSheet()->getStyle('A1:D1')
            ->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

        $spreadsheet->getActiveSheet()->getStyle('A:D')->getAlignment()->setHorizontal('left');

        $hojaActiva->setCellValue('A1', 'Detalle ventas');
        $hojaActiva->setCellValue('B1', 'Fecha');
        $hojaActiva->setCellValue('C1', 'Total');
        $hojaActiva->setCellValue('D1', 'Cliente');

        $fila = 2;
        foreach ($data['ventas'] as $venta) {

            $spreadsheet->getActiveSheet()->getStyle('A' . $fila . ':D' . $fila)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('C8C8C8');

            $spreadsheet->getActiveSheet()->getStyle('A' . $fila)
                ->getFont()->getColor()->setARGB(Color::COLOR_BLUE);

            $hojaActiva->setCellValue('A' . $fila, 'Productos');
            $hojaActiva->setCellValue('B' . $fila, $venta['fecha']);
            $hojaActiva->setCellValue('C' . $fila, $venta['total']);
            $hojaActiva->setCellValue('D' . $fila, $venta['nombre']);
            $fila++;
            $productos = json_decode($venta['productos'], true);
            foreach ($productos as $producto) {
                $hojaActiva->setCellValue('A' . $fila, $producto['nombre']);
                $hojaActiva->setCellValue('B' . $fila, '---');
                $hojaActiva->setCellValue('C' . $fila, '---');
                $hojaActiva->setCellValue('D' . $fila, '---');
                $fila++;
            }
        }

        //Generar archivo Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="ventas.xlsx"');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
