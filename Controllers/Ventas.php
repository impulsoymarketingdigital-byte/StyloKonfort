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
            header('Location: '. BASE_URL . 'admin');
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
            $result['stock'] = $row['cantidad'];
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
        $array['productos'] = array();
        $total = 0;
        if (!empty($datos['productos'])) {
            $fecha = date('Y-m-d H:i:s');
            $idCliente = $datos['idCliente'];
            if (empty($idCliente)) {
                $res = array('msg' => 'EL CLIENTE ES REQUERIDO', 'type' => 'warning');
            } else {
                foreach ($datos['productos'] as $producto) {
                    $result = $this->model->getProducto($producto['idProducto']);
                    $atributo = $this->model->getAtributos($producto['size'], $producto['color'], $producto['idProducto']);
                    $data['precio'] = $atributo['precio'];
                    $data['id'] = $result['id'];
                    $data['nombre'] = $result['nombre'];
                    $data['color'] = $producto['color'];
                    $data['size'] = $producto['size'];
                    $data['cantidad'] = $producto['cantidad'];
                    $subTotal = $data['precio'] * $producto['cantidad'];
                    array_push($array['productos'], $data);
                    $total += $subTotal;
                }
                $datosProductos = json_encode($array['productos']);
                $venta = $this->model->registrarVenta($datosProductos, $total, $fecha, $idCliente, $this->id_usuario);
                if ($venta > 0) {
                    foreach ($datos['productos'] as $producto) {
                        $atributo = $this->model->getAtributos($producto['size'], $producto['color'], $producto['idProducto']);
                        $result = $this->model->getProducto($producto['idProducto']);
                        $totalStock = 0;
                        $total = 0;
                        if ($producto['size'] > 0 && $producto['color'] > 0) {
                            //actualizar stock
                            $stock = $atributo['cantidad'] - $producto['cantidad'];
                            $this->model->actualizarStockDetalle($stock, $producto['size'], $producto['color'], $producto['idProducto']);
                        }
                        if ($producto['idProducto'] == $result['id']) {
                            $total += $producto['cantidad'];
                        }
                        $totalStock = $result['cantidad'] - $total;
                        $ventas = $result['ventas'] + $total;
                        $this->model->actualizarStockProducto($totalStock, $ventas, $result['id']);
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
        if (empty($data['venta'])) {
            echo 'Pagina no Encontrada';
            exit;
        }
        $this->views->getView('admin/ventas', $tipo, $data);
        $html = ob_get_clean();
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isJavascriptEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);

        if ($tipo == 'ticked') {
            $dompdf->setPaper(array(0, 0, 130, 841), 'portrait');
        } else {
            $dompdf->setPaper('A4', 'vertical');
        }

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('reporte.pdf', array('Attachment' => false));
    }

    public function listar()
    {
        $data = $this->model->getVentas();
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['estado'] == 1) {
                $data[$i]['acciones'] = '<div>
                <a class="btn btn-warning" href="#" onclick="anularVenta(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></a>
                <a class="btn btn-danger" href="#" onclick="verReporte(' . $data[$i]['id'] . ')"><i class="fas fa-file-pdf"></i></a>
                </div>';
            } else {
                $data[$i]['acciones'] = '<div>
                <span class="badge bg-info">Anulado</span>
                <a class="btn btn-danger" href="#" onclick="verReporte(' . $data[$i]['id'] . ')"><i class="fas fa-file-pdf"></i></a>
                </div>';
            }
        }
        echo json_encode($data);
        die();
    }

    public function anular($idVenta)
    {
        if (isset($_GET) && is_numeric($idVenta)) {
            $data = $this->model->anular($idVenta);
            if ($data == 1) {
                $resultVenta = $this->model->getVenta($idVenta);
                $ventaProducto = json_decode($resultVenta['productos'], true);
                foreach ($ventaProducto as $producto) {
                    $result = $this->model->getProducto($producto['id']);
                    $nuevaCantidad = 0;

                    $nuevaCantidad = $result['cantidad'] + $producto['cantidad'];
                    $atributo = $this->model->getAtributos($producto['size'], $producto['color'], $producto['id']);
                    $stock = $atributo['cantidad'] + $producto['cantidad'];
                    $this->model->actualizarStockDetalle($stock, $producto['size'], $producto['color'], $producto['id']);

                    $total = $result['ventas'] - $producto['cantidad'];
                    $this->model->actualizarStockProducto($nuevaCantidad, $total, $producto['id']);
                }
                $res = array('msg' => 'VENTA ANULADO', 'type' => 'success');
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
