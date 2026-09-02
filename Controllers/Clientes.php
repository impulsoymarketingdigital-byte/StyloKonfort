<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;

class Clientes extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ─── Vista de perfil del cliente ─────────────────────────────────────────

    public function index()
    {
        if (empty($_SESSION['idCliente'])) {
            header('Location: ' . BASE_URL);
            exit;
        }
        $data['perfil']     = 'si';
        $data['title']      = 'Tu Perfil';
        $data['testimonio'] = $this->model->getTestimonio((int) $_SESSION['idCliente']);
        $data['verificar']  = $this->model->getVerificar('clientes', $_SESSION['correoCliente']);
        $this->views->getView('principal', 'perfil', $data);
    }

    // ─── Registro ─────────────────────────────────────────────────────────────

    public function registro()
    {
        $data['perfil'] = 'si';
        $data['title']  = 'Registrarse';
        $this->views->getView('principal', 'registro', $data);
    }

    public function registroDirecto()
    {
        header('Content-Type: application/json; charset=utf-8');
        $campos = ['nombreRegistro', 'apellidoRegistro', 'correoRegistro', 'claveRegistro',
                   'telefonoRegistro', 'direccionRegistro', 'ciudadRegistro', 'departamentoRegistro',
                   'barrioRegistro', 'documentoRegistro', 'tipoClienteRegistro'];

        foreach ($campos as $campo) {
            if (empty($_POST[$campo])) {
                echo json_encode(['msg' => 'TODOS LOS CAMPOS SON REQUERIDOS', 'icono' => 'warning']);
                return;
            }
        }

        $nombre      = strClean($_POST['nombreRegistro']);
        $apellido    = strClean($_POST['apellidoRegistro']);
        $correo      = strtolower(trim($_POST['correoRegistro']));
        $clave       = $_POST['claveRegistro'];
        $telefono    = strClean($_POST['telefonoRegistro']);
        $direccion   = strClean($_POST['direccionRegistro']);
        $ciudad      = strClean($_POST['ciudadRegistro']);
        $departamento= strClean($_POST['departamentoRegistro']);
        $barrio      = strClean($_POST['barrioRegistro']);
        $documento   = strClean($_POST['documentoRegistro']);

        // SEGURIDAD: Los nuevos registros solo pueden ser 'final'
        // El tipo 'mayorista' lo aprueba un administrador
        $tipo_cliente = 'final';

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['msg' => 'CORREO ELECTRÓNICO INVÁLIDO', 'icono' => 'warning']);
            return;
        }

        // Verificar si el correo ya existe
        $existeCorreo = $this->model->getVerificar('clientes', $correo);
        if ($existeCorreo) {
            echo json_encode(['msg' => 'EL CORREO YA ESTÁ REGISTRADO', 'icono' => 'warning']);
            return;
        }

        // Verificar si el documento ya existe
        $existeDoc = $this->model->getVerificarDocumento($documento);
        if ($existeDoc) {
            echo json_encode(['msg' => 'EL DOCUMENTO YA ESTÁ REGISTRADO', 'icono' => 'warning']);
            return;
        }

        $claveHash = password_hash($clave, PASSWORD_BCRYPT);
        $token     = bin2hex(random_bytes(32));

        $id = $this->model->registroDirecto(
            $nombre, $apellido, $correo, $claveHash, $token,
            $telefono, $direccion, $ciudad, $departamento, $barrio,
            $documento, $tipo_cliente
        );

        if ($id > 0) {
            // Intentar enviar correo de verificación (no crítico si falla)
            $this->enviarCorreoVerificacion($correo, $nombre, $token, $id);
            echo json_encode(['msg' => 'REGISTRO EXITOSO. Revisa tu correo para verificar tu cuenta.', 'icono' => 'success', 'id' => $id]);
        } else {
            echo json_encode(['msg' => 'ERROR AL REGISTRAR. Intenta de nuevo.', 'icono' => 'error']);
        }
    }

    // ─── Login / Logout del cliente ───────────────────────────────────────────

    public function loginDirecto()
    {
        header('Content-Type: application/json; charset=utf-8');
        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $clave  = $_POST['clave'] ?? '';

        if (empty($correo) || empty($clave)) {
            echo json_encode(['msg' => 'TODOS LOS CAMPOS SON REQUERIDOS', 'icono' => 'warning']);
            return;
        }

        $cliente = $this->model->getVerificar('clientes', $correo);
        if (!$cliente) {
            echo json_encode(['msg' => 'EL CORREO NO EXISTE', 'icono' => 'warning']);
            return;
        }

        if (!password_verify($clave, $cliente['clave'])) {
            echo json_encode(['msg' => 'CONTRASEÑA INCORRECTA', 'icono' => 'warning']);
            return;
        }

        if (isset($cliente['estado']) && $cliente['estado'] == 0) {
            echo json_encode(['msg' => 'TU CUENTA ESTÁ DESACTIVADA', 'icono' => 'warning']);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['idCliente']      = $cliente['id'];
        $_SESSION['correoCliente']  = $cliente['correo'];
        $_SESSION['nombreCliente']  = $cliente['nombre'];
        $_SESSION['tipoCliente']    = $cliente['tipo_cliente'];

        echo json_encode(['msg' => 'BIENVENIDO ' . strtoupper($cliente['nombre']), 'icono' => 'success']);
    }

    public function salirCliente()
    {
        $_SESSION['idCliente']     = null;
        $_SESSION['correoCliente'] = null;
        $_SESSION['nombreCliente'] = null;
        $_SESSION['tipoCliente']   = null;
        header('Location: ' . BASE_URL);
        exit;
    }

    // ─── Verificación de email ────────────────────────────────────────────────

    public function verificar($token = null)
    {
        if (!$token) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $cliente = $this->model->getToken($token);
        if ($cliente) {
            $this->model->actualizarVerify((int) $cliente['id']);
            $data['title']   = 'Cuenta Verificada';
            $data['mensaje'] = 'Tu cuenta ha sido verificada exitosamente. ¡Ya puedes iniciar sesión!';
        } else {
            $data['title']   = 'Token Inválido';
            $data['mensaje'] = 'El enlace de verificación no es válido o ya fue utilizado.';
        }
        $this->views->getView('principal', 'verificacion', $data);
    }

    // ─── Pedidos del cliente ──────────────────────────────────────────────────

    public function registrarPedido()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['idCliente'])) {
            echo json_encode(['msg' => 'Debes iniciar sesión', 'icono' => 'warning']);
            return;
        }

        $body     = json_decode(file_get_contents('php://input'), true);
        $productos = $body['productos'] ?? [];

        if (empty($productos)) {
            echo json_encode(['msg' => 'EL CARRITO ESTÁ VACÍO', 'icono' => 'warning']);
            return;
        }

        $idCliente    = (int) $_SESSION['idCliente'];
        $clienteData  = $this->model->getVerificar('clientes', $_SESSION['correoCliente']);
        $nombre       = $clienteData['nombre']   ?? '';
        $apellido     = $clienteData['apellido'] ?? '';
        $correo       = $clienteData['correo']   ?? '';
        $direccion    = $clienteData['direccion']?? '';
        $ciudad       = $clienteData['ciudad']   ?? '';

        // Calcular monto total validando precios desde el servidor
        $total         = 0;
        $itemsValidos  = [];
        $tipo          = $this->model->getTipoCliente();

        foreach ($productos as $item) {
            $idProducto = (int) ($item['idProducto'] ?? 0);
            $cantidad   = (int) ($item['cantidad']   ?? 1);
            $size       = (int) ($item['size']       ?? 0);
            $color      = (int) ($item['color']      ?? 0);
            if ($idProducto <= 0 || $cantidad <= 0) continue;

            // Verificar stock
            $tallaColor = $this->model->getIdTallaColor($size, $color, $idProducto);
            if (!$tallaColor) continue;

            $atributos = $this->model->getAtributos($size, $color, $idProducto);
            if (!$atributos || $atributos['stock'] < $cantidad) continue;

            $producto = $this->model->getProducto($idProducto);
            if (!$producto) continue;

            $precio = (float) $producto['precio_venta'];
            $total += $precio * $cantidad;

            $itemsValidos[] = [
                'idProducto'    => $idProducto,
                'nombre'        => $producto['nombre'],
                'precio'        => $precio,
                'cantidad'      => $cantidad,
                'id_talla_color'=> $tallaColor['id'],
            ];
        }

        if (empty($itemsValidos)) {
            echo json_encode(['msg' => 'NO HAY PRODUCTOS VÁLIDOS', 'icono' => 'warning']);
            return;
        }

        $id_transaccion = $this->model->generarNumeroPedido();
        $fecha          = date('Y-m-d H:i:s');

        $idPedido = $this->model->registrarPedido(
            $id_transaccion, 'LLEVAR', $total, 'PENDIENTE', $fecha,
            $correo, $nombre, $apellido, $direccion, $ciudad, $idCliente
        );

        if ($idPedido > 0) {
            foreach ($itemsValidos as $item) {
                $this->model->registrarDetalle(
                    $item['nombre'], $item['precio'], $item['cantidad'],
                    $idPedido, $item['idProducto'], $item['id_talla_color']
                );
            }
            echo json_encode([
                'msg'     => 'PEDIDO REGISTRADO EXITOSAMENTE',
                'icono'   => 'success',
                'idPedido'=> $idPedido,
            ]);
        } else {
            echo json_encode(['msg' => 'ERROR AL REGISTRAR EL PEDIDO', 'icono' => 'error']);
        }
    }

    public function listarPendientes()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_SESSION['idCliente'])) {
            echo json_encode([]);
            return;
        }

        $pedidos = $this->model->getPedidos((int) $_SESSION['idCliente']);
        $empresa = $this->model->getEmpresa();
        $moneda  = $empresa['moneda'] ?? MONEDA;
        $data    = [];

        foreach ($pedidos as $pedido) {
            $data[] = [
                'id'             => $pedido['id'],
                'id_transaccion' => $pedido['id_transaccion'],
                'monto'          => $moneda . ' ' . number_format($pedido['monto'], 2),
                'metodo'         => $pedido['metodo'] ?? 'LLEVAR',
                'fecha'          => date('d/m/Y H:i', strtotime($pedido['fecha'])),
                'accion'         => '<button class="btn btn-info btn-sm" onclick="verPedido(' . $pedido['id'] . ')"><i class="fa fa-eye"></i> Ver</button>',
            ];
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    public function verPedido($idPedido = null)
    {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_SESSION['idCliente']) || !$idPedido) {
            echo json_encode(['error' => true]);
            return;
        }

        $pedido   = $this->model->getPedido((int) $idPedido);
        $detalles = $this->model->verPedidos((int) $idPedido);
        $empresa  = $this->model->getEmpresa();
        $moneda   = $empresa['moneda'] ?? MONEDA;

        // Enriquecer detalles con atributos legibles
        $productosDetalle = [];
        foreach ($detalles as $d) {
            $atrib = $this->model->getAtributosPorId((int) $d['id_talla_color']);
            $atribStr = $atrib ? $atrib['size'] . ' - ' . $atrib['nombre'] : '-';

            $productosDetalle[] = [
                'producto'  => $d['producto'],
                'precio'    => number_format((float) $d['precio'], 2),
                'cantidad'  => $d['cantidad'],
                'atributos' => $atribStr,
            ];
        }

        echo json_encode([
            'pedido'   => $pedido,
            'productos'=> $productosDetalle,
            'moneda'   => $moneda,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function listarProductos()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_SESSION['idCliente'])) {
            echo json_encode([]);
            return;
        }

        $prods   = $this->model->getProductos((int) $_SESSION['idCliente']);
        $empresa = $this->model->getEmpresa();
        $moneda  = $empresa['moneda'] ?? MONEDA;
        $data    = [];

        foreach ($prods as $p) {
            $calif = $this->model->comprobarCalificacion((int) $p['id_producto'], (int) $_SESSION['idCliente']);
            $estrellas = '';
            for ($i = 1; $i <= 5; $i++) {
                $active = ($calif && $calif['cantidad'] >= $i) ? 'text-warning' : 'text-muted';
                $estrellas .= '<i class="fa fa-star ' . $active . '" onclick="agregarCalificacion(' . $p['id_producto'] . ',' . $i . ')" style="cursor:pointer;font-size:1.2rem;"></i>';
            }
            $data[] = [
                'id_producto'  => $p['id_producto'],
                'producto'     => $p['producto'],
                'precio'       => $moneda . ' ' . number_format((float) $p['precio'], 2),
                'cantidad'     => $p['cantidad'],
                'calificacion' => $estrellas,
            ];
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    // ─── Calificaciones ───────────────────────────────────────────────────────

    public function agregarCalificacion()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_SESSION['idCliente'])) {
            echo json_encode(['msg' => 'Sin sesión', 'icono' => 'warning']);
            return;
        }

        $body       = json_decode(file_get_contents('php://input'), true);
        $id_producto = (int) ($body['id_producto'] ?? 0);
        $cantidad    = (int) ($body['cantidad']    ?? 0);
        $idCliente   = (int) $_SESSION['idCliente'];

        if ($id_producto <= 0 || $cantidad < 1 || $cantidad > 5) {
            echo json_encode(['msg' => 'Datos inválidos', 'icono' => 'warning']);
            return;
        }

        $existe = $this->model->comprobarCalificacion($id_producto, $idCliente);
        if ($existe) {
            $r = $this->model->cambiarCalificacion($cantidad, $id_producto, $idCliente);
        } else {
            $r = $this->model->agregarCalificacion($cantidad, $id_producto, $idCliente);
        }

        echo json_encode($r > 0
            ? ['msg' => 'CALIFICACIÓN GUARDADA', 'icono' => 'success']
            : ['msg' => 'ERROR AL CALIFICAR', 'icono' => 'error']
        );
    }

    // ─── Testimonio ───────────────────────────────────────────────────────────

    public function agregarMensaje()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_SESSION['idCliente'])) {
            echo json_encode(['msg' => 'Sin sesión', 'icono' => 'warning']);
            return;
        }

        $body     = json_decode(file_get_contents('php://input'), true);
        $mensaje  = strip_tags($body['mensaje'] ?? '');
        $idCliente= (int) $_SESSION['idCliente'];

        if (empty($mensaje)) {
            echo json_encode(['msg' => 'INGRESA UN COMENTARIO', 'icono' => 'warning']);
            return;
        }

        $existe = $this->model->getTestimonio($idCliente);
        if ($existe) {
            $r = $this->model->modificarMensaje($mensaje, $idCliente);
        } else {
            $r = $this->model->agregarMensaje($mensaje, $idCliente);
        }

        echo json_encode($r > 0
            ? ['msg' => 'TESTIMONIO GUARDADO', 'icono' => 'success']
            : ['msg' => 'ERROR AL GUARDAR', 'icono' => 'error']
        );
    }

    // ─── Tickets de pedido ────────────────────────────────────────────────────

    public function enviarTicket()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_SESSION['idCliente'])) {
            echo json_encode(['msg' => 'Sin sesión', 'icono' => 'warning']);
            return;
        }

        $idPedido = (int) ($_POST['idPedido'] ?? 0);
        if (!$idPedido) {
            echo json_encode(['msg' => 'Pedido inválido', 'icono' => 'error']);
            return;
        }

        $pedido   = $this->model->getPedido($idPedido);
        $empresa  = $this->model->getEmpresa();
        $detalles = $this->model->verPedidos($idPedido);

        if (!$pedido) {
            echo json_encode(['msg' => 'Pedido no encontrado', 'icono' => 'error']);
            return;
        }

        $resultado = $this->enviarEmailTicket($pedido, $empresa, $detalles);

        echo json_encode($resultado > 0
            ? ['msg' => 'TICKET ENVIADO AL CORREO', 'icono' => 'success']
            : ['msg' => 'Pedido registrado. No se pudo enviar el correo.', 'icono' => 'info']
        );
    }

    public function enviarTicketWhatsApp()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (empty($_SESSION['idCliente'])) {
            echo json_encode(['msg' => 'Sin sesión', 'icono' => 'warning']);
            return;
        }

        $idPedido = (int) ($_POST['idPedido'] ?? 0);
        if (!$idPedido) {
            echo json_encode(['msg' => 'Pedido inválido', 'icono' => 'error']);
            return;
        }

        $pedido  = $this->model->getPedido($idPedido);
        $empresa = $this->model->getEmpresa();

        if (!$pedido || empty($empresa['whatsapp'])) {
            echo json_encode(['msg' => 'Pedido registrado', 'icono' => 'success', 'whatsappLink' => null]);
            return;
        }

        $detalles = $this->model->verPedidos($idPedido);
        $lineas   = "✅ *Nuevo Pedido #" . $pedido['id_transaccion'] . "*\n";
        $lineas  .= "👤 " . $pedido['nombre'] . " " . $pedido['apellido'] . "\n";
        $lineas  .= "📦 Artículos:\n";
        foreach ($detalles as $d) {
            $lineas .= "  - " . $d['producto'] . " x" . $d['cantidad'] . " = " . MONEDA . number_format($d['precio'] * $d['cantidad'], 0) . "\n";
        }
        $lineas .= "💰 Total: " . MONEDA . number_format($pedido['monto'], 0) . "\n";
        $lineas .= "📅 " . date('d/m/Y H:i', strtotime($pedido['fecha']));

        $telefono = preg_replace('/[^0-9]/', '', $empresa['whatsapp']);
        if (strlen($telefono) === 10) {
            $telefono = '57' . $telefono;
        }

        $link = 'https://wa.me/' . $telefono . '?text=' . rawurlencode($lineas);

        echo json_encode([
            'msg'          => 'Pedido registrado',
            'icono'        => 'success',
            'whatsappLink' => $link,
        ]);
    }

    // ─── Suscripción (newsletter) ─────────────────────────────────────────────

    public function enviarSuscripcion()
    {
        header('Content-Type: application/json; charset=utf-8');
        $correo = strtolower(trim($_POST['correo'] ?? ''));

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['msg' => 'Correo inválido', 'icono' => 'warning']);
            return;
        }

        // Por ahora solo log — implementar lista de suscriptores cuando se necesite
        error_log('[StyloKonfort] Nueva suscripción: ' . $correo);
        echo json_encode(['msg' => '¡Gracias por suscribirte!', 'icono' => 'success']);
    }

    // ─── Métodos privados de email ────────────────────────────────────────────

    private function enviarCorreoVerificacion(string $correo, string $nombre, string $token, int $id): int
    {
        if (empty(USER_SMTP) || empty(PASS_SMTP) || empty(HOST_SMTP)) {
            error_log('[Clientes] SMTP no configurado — no se envió correo de verificación');
            return 0;
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = HOST_SMTP;
            $mail->SMTPAuth   = true;
            $mail->Username   = USER_SMTP;
            $mail->Password   = PASS_SMTP;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = PUERTO_SMTP;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(CORREO, TITLE);
            $mail->addAddress($correo, $nombre);
            $mail->isHTML(true);
            $mail->Subject = '✅ Verifica tu cuenta — ' . TITLE;

            $linkVerificacion = BASE_URL . 'clientes/verificar/' . $token;
            $mail->Body = "
                <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
                    <h2 style='color:#333;'>¡Hola, " . htmlspecialchars($nombre) . "!</h2>
                    <p>Gracias por registrarte en <strong>" . TITLE . "</strong>.</p>
                    <p>Haz clic en el botón para verificar tu cuenta:</p>
                    <div style='text-align:center;margin:30px 0;'>
                        <a href='" . $linkVerificacion . "' 
                           style='background:#333;color:#fff;padding:12px 28px;text-decoration:none;border-radius:4px;font-size:16px;'>
                            ✅ Verificar mi cuenta
                        </a>
                    </div>
                    <p style='color:#666;font-size:13px;'>Si no creaste esta cuenta, ignora este mensaje.</p>
                </div>";

            $mail->send();
            return 1;
        } catch (Exception $e) {
            error_log('[Clientes] Error envío correo verificación: ' . $e->getMessage());
            return 0;
        }
    }

    private function enviarEmailTicket(array $pedido, ?array $empresa, array $detalles): int
    {
        if (empty(USER_SMTP) || empty(PASS_SMTP) || empty(HOST_SMTP)) {
            return 0;
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = HOST_SMTP;
            $mail->SMTPAuth   = true;
            $mail->Username   = USER_SMTP;
            $mail->Password   = PASS_SMTP;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = PUERTO_SMTP;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(CORREO, TITLE);
            $mail->addAddress($pedido['email'] ?? $pedido['correo'] ?? '', $pedido['nombre'] ?? '');
            $mail->isHTML(true);
            $mail->Subject = '📦 Pedido #' . $pedido['id_transaccion'] . ' — ' . TITLE;

            $filas = '';
            $total = 0;
            foreach ($detalles as $d) {
                $sub   = (float) $d['precio'] * (int) $d['cantidad'];
                $total += $sub;
                $filas .= "<tr>
                    <td style='padding:8px;border:1px solid #eee;'>{$d['producto']}</td>
                    <td style='padding:8px;border:1px solid #eee;text-align:center;'>{$d['cantidad']}</td>
                    <td style='padding:8px;border:1px solid #eee;text-align:right;'>" . MONEDA . number_format($d['precio'], 2) . "</td>
                    <td style='padding:8px;border:1px solid #eee;text-align:right;'>" . MONEDA . number_format($sub, 2) . "</td>
                </tr>";
            }

            $mail->Body = "
                <div style='font-family:Arial,sans-serif;max-width:650px;margin:0 auto;'>
                    <h2 style='color:#333;'>Pedido confirmado ✅</h2>
                    <p>Hola <strong>{$pedido['nombre']}</strong>, tu pedido fue registrado exitosamente.</p>
                    <table style='width:100%;border-collapse:collapse;margin:20px 0;'>
                        <tr style='background:#333;color:#fff;'>
                            <th style='padding:10px;text-align:left;'>Producto</th>
                            <th style='padding:10px;text-align:center;'>Cant.</th>
                            <th style='padding:10px;text-align:right;'>Precio</th>
                            <th style='padding:10px;text-align:right;'>Subtotal</th>
                        </tr>
                        $filas
                        <tr>
                            <td colspan='3' style='padding:10px;text-align:right;font-weight:bold;'>TOTAL:</td>
                            <td style='padding:10px;text-align:right;font-weight:bold;'>" . MONEDA . number_format($total, 2) . "</td>
                        </tr>
                    </table>
                    <p><strong>Método:</strong> Contra Entrega</p>
                    <p><strong>Referencia:</strong> {$pedido['id_transaccion']}</p>
                    <p style='color:#666;font-size:13px;'>Nos pondremos en contacto contigo pronto.</p>
                </div>";

            $mail->send();
            return 1;
        } catch (Exception $e) {
            error_log('[Clientes] Error envío ticket: ' . $e->getMessage());
            return 0;
        }
    }
}
?>
