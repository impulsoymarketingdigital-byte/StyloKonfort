<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Admin extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    // --- NUEVO: Guardia de Seguridad Centralizado ---
    // Esta función protege todas las rutas automáticamente
    private function verificarAcceso()
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
    }

    public function index()
    {
        // Si ya está logueado, lo manda directo al panel
        if (!empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin/home');
            exit;
        }
        $data['title'] = 'Acceso al sistema';
        $this->views->getView('admin', "login", $data);
    }

    public function validar()
    {
        if (isset($_POST['email']) && isset($_POST['clave'])) {
            if (empty($_POST['email']) || empty($_POST['clave'])) {
                $respuesta = array('msg' => 'TODOS LOS CAMPOS SON REQUERIDOS', 'icono' => 'warning');
            } else {
                $data = $this->model->getUsuario($_POST['email']);
                if (empty($data)) {
                    $respuesta = array('msg' => 'EL CORREO NO EXISTE', 'icono' => 'warning');
                } else {
                    if (password_verify($_POST['clave'], $data['clave'])) {
                        
                        // ✅ SEGURIDAD CRÍTICA: Prevenir el robo de sesión (Session Fixation)
                        session_regenerate_id(true);

                        // ✅ GUARDAR DATOS EN SESIÓN
                        $_SESSION['id_usuario'] = $data['id'];
                        $_SESSION['email'] = $data['correo'];
                        $_SESSION['nombre_usuario'] = $data['nombres'];
                        $_SESSION['perfil_usuario'] = $data['perfil'];
                        $_SESSION['id_almacen'] = $data['id_almacen'];
                        $_SESSION['id_rol'] = $data['id_rol'];
                        $_SESSION['nombre_rol'] = $data['nombre_rol'];

                        // ✅ GUARDAR PERMISOS COMO ARRAY
                        if (!empty($data['permisos'])) {
                            $_SESSION['permisos'] = json_decode($data['permisos'], true);
                        } else {
                            $_SESSION['permisos'] = [];
                        }

                        $respuesta = array('msg' => 'DATOS CORRECTOS', 'icono' => 'success');
                    } else {
                        $respuesta = array('msg' => 'CONTRASEÑA INCORRECTA', 'icono' => 'warning');
                    }
                }
            }
        } else {
            $respuesta = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function home()
    {
        $this->verificarAcceso();
        
        $data['title'] = 'Panel Administrativo';
        $data['pendientes'] = $this->model->getTotales(1);
        $data['procesos'] = $this->model->getTotales(2);
        $data['finalizados'] = $this->model->getTotales(3);
        $data['productos'] = $this->model->getDatos('productos');
        $data['usuarios'] = $this->model->getDatos('usuarios');
        $data['categorias'] = $this->model->getDatos('categorias');
        $data['clientes'] = $this->model->getDatos('clientes');
        $data['colores'] = $this->model->getDatos('colores');
        $data['nuevos'] = $this->model->nuevoProductos();
        
        $this->views->getView('admin/administracion', "index", $data);
    }

    public function productosMinimos()
    {
        $this->verificarAcceso();
        $data = $this->model->productosMinimos();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function exportarStockMinimoPDF()
    {
        $this->verificarAcceso();

        ob_start();
        $data['title'] = 'PRODUCTOS CON STOCK MÍNIMO';
        $data['empresa'] = $this->model->getEmpresa();
        $data['productos'] = $this->model->productosMinimos();
        $this->views->getView('admin/rooms', 'stock_minimo_pdf', $data);
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Productos_Stock_Minimo.pdf', array('Attachment' => false));
    }

    public function exportarStockMinimoExcel()
    {
        $this->verificarAcceso();

        if (ob_get_level()) {
            ob_end_clean();
        }

        try {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator($_SESSION['nombre_usuario'] ?? 'Sistema')
                ->setTitle("Productos con Stock Mínimo");

            $hojaActiva = $spreadsheet->getActiveSheet();
            $hojaActiva->setTitle('Stock Mínimo');

            // Encabezados
            $columnas = [
                'A' => 'N°',
                'B' => 'ID PRODUCTO',
                'C' => 'NOMBRE DEL PRODUCTO',
                'D' => 'STOCK ACTUAL'
            ];

            // Estilo encabezado
            $hojaActiva->getStyle('A1:D1')->getFont()->setBold(true);
            $hojaActiva->getStyle('A1:D1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF6B6B');
            $hojaActiva->getStyle('A1:D1')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

            foreach ($columnas as $col => $titulo) {
                $hojaActiva->setCellValue($col . '1', $titulo);
                $hojaActiva->getColumnDimension($col)->setAutoSize(true);
            }

            // Obtener datos
            $productos = $this->model->productosMinimos();

            $fila = 2;
            foreach ($productos as $index => $producto) {
                $hojaActiva->setCellValue('A' . $fila, $index + 1);
                $hojaActiva->setCellValue('B' . $fila, $producto['id']);
                $hojaActiva->setCellValue('C' . $fila, $producto['nombre']);
                $hojaActiva->setCellValue('D' . $fila, $producto['cantidad']);

                // Color rojo si stock es muy bajo (menor a 5)
                if ($producto['cantidad'] < 5) {
                    $hojaActiva->getStyle('D' . $fila)->getFont()->getColor()->setARGB('FF0000');
                    $hojaActiva->getStyle('D' . $fila)->getFont()->setBold(true);
                }
                $fila++;
            }

            $fechaHora = date('Y-m-d_H-i-s');
            $nombreArchivo = "productos_stock_minimo_{$fechaHora}.xlsx";

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

    public function topProductos()
    {
        $this->verificarAcceso();
        $data = $this->model->topProductos();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function salir()
    {
        // Limpieza profunda de sesión
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: ' . BASE_URL . 'admin');
        exit;
    }

    public function empresa()
    {
        $this->verificarAcceso();
        $data['title'] = 'Datos de empresa';
        $data['empresa'] = $this->model->getEmpresa();
        $this->views->getView('admin/administracion', "empresa", $data);
    }

    public function modificar()
    {
        $this->verificarAcceso();
        
        if (isset($_POST['ruc']) && isset($_POST['nombre'])) {
            $id = strClean($_POST['id']);
            $nombre = strClean($_POST['nombre']);
            $ruc = strClean($_POST['ruc']);
            $telefono = strClean($_POST['telefono']);
            $correo = (empty($_POST['correo'])) ? null : strClean($_POST['correo']);
            $direccion = strClean($_POST['direccion']);
            $whatsapp = strClean($_POST['whatsapp']);
            $facebook = strClean($_POST['facebook']);
            $twitter = strClean($_POST['twitter']);
            $instagram = strClean($_POST['instagram']);
            $ubicacion = strClean($_POST['ubicacion']);
            $mensaje = strClean($_POST['mensaje']);
            
            if (empty($nombre) || empty($ruc) || empty($telefono) || empty($direccion) || empty($id)) {
                $res = array('msg' => 'Todos los campos con * son requeridos', 'type' => 'warning');
            } else {
                $data = $this->model->actualizar(
                    $ruc, $nombre, $telefono, $correo, $direccion, $whatsapp,
                    $facebook, $twitter, $instagram, $ubicacion, $mensaje, $id
                );
                
                if ($data > 0) {
                    $res = array('msg' => 'DATOS MODIFICADOS', 'type' => 'success');
                } else {
                    $res = array('msg' => 'ERROR AL MODIFICAR', 'type' => 'error');
                }
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO', 'type' => 'error');
        }
        echo json_encode($res);
        die();
    }
}