<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Reportes extends Controller
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

    // ============= REPORTE DE VENTAS =============
    public function reporte_ventas()
    {
        $data['title'] = 'Reporte de Ventas';
        $data['usuarios'] = $this->model->getDatos('usuarios');
        $data['almacenes'] = $this->model->getDatos('almacenes');
        $this->views->getView('admin/reportes', 'reporte_ventas', $data);
    }

    public function listar_ventas()
    {
        $data = $this->model->getVentas();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function reporte_ventas_pdf()
    {
        $desde = $_GET['desde'] ?? null;
        $hasta = $_GET['hasta'] ?? null;
        $id_usuario = $_GET['id_usuario'] ?? null;
        $id_almacen = $_GET['id_almacen'] ?? null;

        ob_start();
        $data['title'] = 'REPORTE DE VENTAS';
        $data['empresa'] = $this->model->getEmpresa();
        $data['ventas'] = $this->model->getVentasPdf($desde, $hasta, $id_usuario, $id_almacen);
        $this->views->getView('admin/rooms', 'reporte_ventas_pdf', $data);
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Reporte_Ventas.pdf', array('Attachment' => false));
    }

    public function reporte_ventas_excel()
    {
        $desde = $_GET['desde'] ?? null;
        $hasta = $_GET['hasta'] ?? null;
        $id_usuario = $_GET['id_usuario'] ?? null;
        $id_almacen = $_GET['id_almacen'] ?? null;

        if (ob_get_level()) {
            ob_end_clean();
        }

        try {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator($_SESSION['nombre'] ?? 'Sistema')
                ->setTitle("Reporte de Ventas");

            $hojaActiva = $spreadsheet->getActiveSheet();
            $hojaActiva->setTitle('Ventas');

            // Encabezados
            $columnas = [
                'A' => 'N°',
                'B' => 'N° VENTA',
                'C' => 'MÉTODO PAGO',
                'D' => 'CLIENTE',
                'E' => 'PRODUCTO',
                'F' => 'CANTIDAD',
                'G' => 'PRECIO UNITARIO',
                'H' => 'SUBTOTAL',
                'I' => 'TOTAL PEDIDO',
                'J' => 'USUARIO',
                'K' => 'ALMACÉN',
                'L' => 'FECHA'
            ];

            // Estilo encabezado
            $hojaActiva->getStyle('A1:L1')->getFont()->setBold(true);
            $hojaActiva->getStyle('A1:L1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('4472C4');
            $hojaActiva->getStyle('A1:L1')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

            foreach ($columnas as $col => $titulo) {
                $hojaActiva->setCellValue($col . '1', $titulo);
                $hojaActiva->getColumnDimension($col)->setAutoSize(true);
            }

            // Obtener datos
            $ventas = $this->model->getVentasPdf($desde, $hasta, $id_usuario, $id_almacen);
            $fila = 2;
            $totalProductos = 0;
            $totalCantidad = 0;
            $totalVentas = 0;

            foreach ($ventas as $index => $venta) {
                $hojaActiva->setCellValue('A' . $fila, $index + 1);
                $hojaActiva->setCellValue('B' . $fila, $venta['numero_venta']);
                $hojaActiva->setCellValue('C' . $fila, $venta['metodo']);
                $hojaActiva->setCellValue('D' . $fila, $venta['cliente']);
                $hojaActiva->setCellValue('E' . $fila, $venta['producto']);
                $hojaActiva->setCellValue('F' . $fila, $venta['cantidad']);
                $hojaActiva->setCellValue('G' . $fila, $venta['precio_venta']);
                $hojaActiva->setCellValue('H' . $fila, $venta['subtotal']);
                $hojaActiva->setCellValue('I' . $fila, $venta['total_pedido']);
                $hojaActiva->setCellValue('J' . $fila, $venta['usuario']);
                $hojaActiva->setCellValue('K' . $fila, $venta['almacen']);
                $hojaActiva->setCellValue('L' . $fila, $venta['fecha']);

                $totalProductos++;
                $totalCantidad += $venta['cantidad'];
                $totalVentas += $venta['subtotal'];
                $fila++;
            }

            // Totales
            $hojaActiva->setCellValue('E' . $fila, 'TOTALES:');
            $hojaActiva->setCellValue('F' . $fila, $totalCantidad);
            $hojaActiva->setCellValue('H' . $fila, $totalVentas);
            $hojaActiva->getStyle('E' . $fila . ':H' . $fila)->getFont()->setBold(true);

            $fechaHora = date('Y-m-d_H-i-s');
            $nombreArchivo = "reporte_ventas_{$fechaHora}.xlsx";

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$nombreArchivo\"");
            header('Cache-Control: max-age=0');

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            echo "Error generando Excel: " . $e->getMessage();
            exit;
        }
    }

    // ============= REPORTE DE COMPRAS =============
    public function reporte_compras()
    {
        $data['title'] = 'Reporte de Compras';
        $data['script'] = 'reporte_compras.js';
        $data['proveedores'] = $this->model->getDatos('proveedores');

        $this->views->getView('admin/reportes', 'reporte_compras', $data);
    }

    public function listar_compras()
    {
        $data = $this->model->getCompras();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function reporte_compras_pdf()
    {
        $desde = $_GET['desde'] ?? null;
        $hasta = $_GET['hasta'] ?? null;

        ob_start();
        $data['title'] = 'REPORTE DE COMPRAS';
        $data['empresa'] = $this->model->getEmpresa();
        $data['compras'] = $this->model->getComprasPdf($desde, $hasta);
        $this->views->getView('admin/rooms', 'reporte_compras_pdf', $data);
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Reporte_Compras.pdf', array('Attachment' => false));
    }

    public function reporte_compras_excel()
    {
        $desde = $_GET['desde'] ?? null;
        $hasta = $_GET['hasta'] ?? null;

        if (ob_get_level()) {
            ob_end_clean();
        }

        try {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator($_SESSION['nombre'] ?? 'Sistema')
                ->setTitle("Reporte de Compras");

            $hojaActiva = $spreadsheet->getActiveSheet();
            $hojaActiva->setTitle('Compras');

            // Encabezados
            $columnas = [
                'A' => 'N°',
                'B' => 'N° COMPRA',
                'C' => 'TIPO COMPROBANTE',
                'D' => 'PROVEEDOR',
                'E' => 'ALMACÉN',
                'F' => 'PRODUCTO',
                'G' => 'CANTIDAD',
                'H' => 'PRECIO COMPRA',
                'I' => 'DESCUENTO',
                'J' => 'SUBTOTAL',
                'K' => 'FECHA',
                'L' => 'USUARIO'
            ];

            // Estilo encabezado
            $hojaActiva->getStyle('A1:L1')->getFont()->setBold(true);
            $hojaActiva->getStyle('A1:L1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('70AD47');
            $hojaActiva->getStyle('A1:L1')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

            foreach ($columnas as $col => $titulo) {
                $hojaActiva->setCellValue($col . '1', $titulo);
                $hojaActiva->getColumnDimension($col)->setAutoSize(true);
            }

            // Obtener datos
            $compras = $this->model->getComprasPdf($desde, $hasta);

            $fila = 2;
            $totalProductos = 0;
            $totalCantidad = 0;
            $totalCompras = 0;

            foreach ($compras as $index => $compra) {
                $hojaActiva->setCellValue('A' . $fila, $index + 1);
                $hojaActiva->setCellValue('B' . $fila, $compra['numero_compra']);
                $hojaActiva->setCellValue('C' . $fila, $compra['tipo_comprobante']);
                $hojaActiva->setCellValue('D' . $fila, $compra['proveedor']);
                $hojaActiva->setCellValue('E' . $fila, $compra['almacen']);
                $hojaActiva->setCellValue('F' . $fila, $compra['producto']);
                $hojaActiva->setCellValue('G' . $fila, $compra['cantidad']);
                $hojaActiva->setCellValue('H' . $fila, $compra['precio_compra']);
                $hojaActiva->setCellValue('I' . $fila, $compra['descuento']);
                $hojaActiva->setCellValue('J' . $fila, $compra['subtotal']);
                $hojaActiva->setCellValue('K' . $fila, $compra['fecha']);
                $hojaActiva->setCellValue('L' . $fila, $compra['usuario']);

                $totalProductos++;
                $totalCantidad += $compra['cantidad'];
                $totalCompras += $compra['subtotal'];

                $fila++;
            }

            // Totales
            $hojaActiva->setCellValue('F' . $fila, 'TOTALES:');
            $hojaActiva->setCellValue('G' . $fila, $totalCantidad);
            $hojaActiva->setCellValue('J' . $fila, $totalCompras);
            $hojaActiva->getStyle('F' . $fila . ':J' . $fila)->getFont()->setBold(true);

            $fechaHora = date('Y-m-d_H-i-s');
            $nombreArchivo = "reporte_compras_{$fechaHora}.xlsx";

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$nombreArchivo\"");
            header('Cache-Control: max-age=0');

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            echo "Error generando Excel: " . $e->getMessage();
            exit;
        }
    }
}
?>