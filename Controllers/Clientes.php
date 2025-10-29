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
        session_start();
    }
    public function index()
    {
        if (empty($_SESSION['idCliente'])) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $data['perfil'] = 'si';
        $data['title'] = 'Tu Perfil';
        $data['testimonio'] = $this->model->getTestimonio($_SESSION['idCliente']);
        $data['verificar'] = $this->model->getVerificar('clientes', $_SESSION['correoCliente']);
        $this->views->getView('principal', "perfil", $data);
    }
    public function registro()
    {
        $data['perfil'] = 'si';
        $data['title'] = 'Registrarse';
        $this->views->getView('principal', "registro", $data);
    }

    public function registroDirecto()
    {
        if (isset($_POST['nombreRegistro']) && isset($_POST['claveRegistro'])) {
            if (
                empty($_POST['nombreRegistro']) ||
                empty($_POST['apellidoRegistro']) ||
                empty($_POST['correoRegistro']) ||
                empty($_POST['claveRegistro']) ||
                empty($_POST['telefonoRegistro']) ||
                empty($_POST['direccionRegistro']) ||
                empty($_POST['ciudadRegistro']) ||
                empty($_POST['departamentoRegistro']) ||
                empty($_POST['barrioRegistro']) ||
                empty($_POST['documentoRegistro']) ||
                empty($_POST['tipoClienteRegistro'])
            ) {
                $mensaje = array('msg' => 'TODOS LOS CAMPOS SON REQUERIDOS', 'icono' => 'warning');
            } else {
                $nombre = strClean($_POST['nombreRegistro']);
                $apellido = strClean($_POST['apellidoRegistro']);
                $correo = strClean($_POST['correoRegistro']);
                $clave = strClean($_POST['claveRegistro']);
                $telefono = strClean($_POST['telefonoRegistro']);
                $direccion = strClean($_POST['direccionRegistro']);
                $ciudad = strClean($_POST['ciudadRegistro']);
                $departamento = strClean($_POST['departamentoRegistro']);
                $barrio = strClean($_POST['barrioRegistro']);
                $documento = strClean($_POST['documentoRegistro']);
                $tipo_cliente = strClean($_POST['tipoClienteRegistro']);

                $verificar = $this->model->getVerificar('clientes', $correo);
                if (empty($verificar)) {
                    $verificarDocumento = $this->model->getVerificarDocumento($documento);
                    if (empty($verificarDocumento)) {
                        $token = md5($correo);
                        $hash = password_hash($clave, PASSWORD_DEFAULT);
                        $data = $this->model->registroDirecto($nombre, $apellido, $correo, $hash, $token, $telefono, $direccion, $ciudad, $departamento, $barrio, $documento, $tipo_cliente);
                        if ($data > 0) {
                            // Solo enviar token si es cliente FINAL
                            if ($tipo_cliente == 'final') {
                                $mensaje = array('msg' => 'Registrado con éxito', 'icono' => 'success', 'token' => $token, 'tipo' => 'final');
                            } else {
                                // Si es MAYORISTA, no enviar token
                                $mensaje = array('msg' => 'Solicitud recibida', 'icono' => 'success', 'tipo' => 'mayorista');
                            }
                        } else {
                            $mensaje = array('msg' => 'Error al registrarse', 'icono' => 'error');
                        }
                    } else {
                        $mensaje = array('msg' => 'EL DOCUMENTO YA ESTÁ REGISTRADO', 'icono' => 'warning');
                    }
                } else {
                    $mensaje = array('msg' => 'YA TIENES UNA CUENTA', 'icono' => 'warning');
                }
            }
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    public function enviarCorreo()
    {
        if (isset($_POST['correo']) && isset($_POST['token'])) {
            $correo = strClean($_POST['correo']);
            $token = strClean($_POST['token']);
            $mail = new PHPMailer(true);
            try {
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = HOST_SMTP;
                $mail->SMTPAuth = true;
                $mail->Username = USER_SMTP;
                $mail->Password = PASS_SMTP;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = PUERTO_SMTP;

                $mail->setFrom(CORREO, TITLE);
                $mail->addAddress($correo);

                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = '¡Bienvenido a ' . TITLE . '!';
                $mail->Body = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #057997 0%, #046680 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; }
                    .button { display: inline-block; padding: 15px 30px; background: #057997; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; }
                    .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 10px 10px; }
                    .link-box { background: #f8f9fa; padding: 15px; border-radius: 5px; word-break: break-all; margin: 10px 0; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>¡Bienvenido a ' . TITLE . '!</h1>
                    </div>
                    <div class="content">
                        <p>Hola,</p>
                        
                        <p>Gracias por registrarte en nuestra tienda. Para completar tu registro y activar tu cuenta, por favor verifica tu correo electrónico.</p>
                        
                        <div style="text-align: center;">
                            <a href="' . BASE_URL . 'clientes/verificarCorreo/' . $token . '" class="button">
                                VERIFICAR MI CUENTA
                            </a>
                        </div>
                        
                        <p><strong>Importante:</strong> Si no solicitaste esta cuenta, puedes ignorar este correo de manera segura.</p>
                        
                        <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
                        <div class="link-box">
                            ' . BASE_URL . 'clientes/verificarCorreo/' . $token . '
                        </div>
                        
                        <p>Gracias por confiar en nosotros</p>
                    </div>
                    <div class="footer">
                        <p>© ' . date('Y') . ' ' . TITLE . '. Todos los derechos reservados.</p>
                    </div>
                </div>
            </body>
            </html>';

                $mail->AltBody = 'Verifica tu correo en: ' . BASE_URL . 'clientes/verificarCorreo/' . $token;

                $mail->send();
                $mensaje = array('msg' => 'CORREO ENVIADO, REVISA TU BANDEJA DE ENTRADA - SPAM', 'icono' => 'success');
            } catch (Exception $e) {
                $mensaje = array('msg' => 'ERROR AL ENVIAR CORREO: ' . $mail->ErrorInfo, 'icono' => 'error');
            }
        } else {
            $mensaje = array('msg' => 'ERROR FATAL', 'icono' => 'error');
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function verificarCorreo($token)
    {
        $verificar = $this->model->getToken($token);
        if (!empty($verificar)) {
            $this->model->actualizarVerify($verificar['id']);
            header('Location: ' . BASE_URL . 'clientes');
        }
    }

    public function enviarCorreoPendiente()
    {
        if (isset($_POST['correo']) && isset($_POST['nombre'])) {
            $correo = strClean($_POST['correo']);
            $nombre = strClean($_POST['nombre']);
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = HOST_SMTP;
                $mail->SMTPAuth = true;
                $mail->Username = USER_SMTP;
                $mail->Password = PASS_SMTP;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = PUERTO_SMTP;

                //Recipients
                $mail->setFrom(CORREO, TITLE);
                $mail->addAddress($correo);

                //Content
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Solicitud de Registro Mayorista - ' . TITLE;
                $mail->Body = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #057997 0%, #046680 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; }
                    .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 10px 10px; }
                    .highlight { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>¡Solicitud Recibida!</h1>
                    </div>
                    <div class="content">
                        <p>Estimado/a <strong>' . $nombre . '</strong>,</p>
                        
                        <p>Gracias por tu interés en registrarte como <strong>Cliente Mayorista</strong> en ' . TITLE . '.</p>
                        
                        <div class="highlight">
                            <strong>📋 Estado de tu solicitud:</strong> PENDIENTE DE APROBACIÓN
                        </div>
                        
                        <p>Tu solicitud será revisada por nuestro equipo administrativo. Una vez aprobada, recibirás un correo con un enlace de verificación para activar tu cuenta.</p>
                        
                        <p><strong>¿Qué sigue?</strong></p>
                        <ul>
                            <li>Nuestro equipo revisará tu solicitud</li>
                            <li>Si es aprobada, recibirás un correo de confirmación</li>
                            <li>Deberás hacer clic en el enlace de verificación</li>
                            <li>¡Listo! Podrás acceder a precios mayoristas</li>
                        </ul>
                        
                        <p>Este proceso generalmente toma entre 24-48 horas hábiles.</p>
                        
                        <p>Gracias por tu paciencia y confianza en nosotros.</p>
                    </div>
                    <div class="footer">
                        <p>© ' . date('Y') . ' ' . TITLE . '. Todos los derechos reservados.</p>
                        <p>Si no solicitaste este registro, puedes ignorar este correo.</p>
                    </div>
                </div>
            </body>
            </html>';

                $mail->AltBody = 'Estimado/a ' . $nombre . ', tu solicitud de registro como cliente mayorista ha sido recibida y está pendiente de aprobación. Recibirás un correo cuando sea aprobada.';

                $mail->send();
                $mensaje = array('msg' => 'Correo de notificación enviado', 'icono' => 'success');
            } catch (Exception $e) {
                $mensaje = array('msg' => 'Error al enviar correo: ' . $mail->ErrorInfo, 'icono' => 'error');
            }
        } else {
            $mensaje = array('msg' => 'Error en los datos', 'icono' => 'error');
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }

    //login directo
    public function loginDirecto()
    {
        if (isset($_POST['correoLogin']) && isset($_POST['claveLogin'])) {
            if (empty($_POST['correoLogin']) || empty($_POST['claveLogin'])) {
                $mensaje = array('msg' => 'TODO LOS CAMPOS SON REQUERIDOS', 'icono' => 'warning');
            } else {
                $correo = $_POST['correoLogin'];
                $clave = $_POST['claveLogin'];
                $verificar = $this->model->getVerificar('clientes', $correo);
                if (!empty($verificar)) {
                    if ($verificar['clave'] != null && password_verify($clave, $verificar['clave'])) {
                        $_SESSION['idCliente'] = $verificar['id'];
                        $_SESSION['correoCliente'] = $verificar['correo'];
                        $_SESSION['nombreCliente'] = $verificar['nombre'];
                        $_SESSION['apellidoCliente'] = $verificar['apellido'];
                        $_SESSION['dirrecionCliente'] = $verificar['direccion'];
                        $_SESSION['perfilCliente'] = $verificar['perfil'];
                        $mensaje = array('msg' => 'USUARIO CORRECTO', 'icono' => 'success');
                    } else {
                        $mensaje = array('msg' => 'CONTRASEÑA INCORRECTA', 'icono' => 'error');
                    }
                } else {
                    $mensaje = array('msg' => 'EL CORREO NO EXISTE', 'icono' => 'warning');
                }
            }
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
    //registrar pedidos
    public function registrarPedido()
    {
        if (empty($_SESSION['idCliente'])) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $datos = file_get_contents('php://input');
        $json = json_decode($datos, true);
        $productos = $json['productos'];

        // NUEVO: Obtener tipo de cliente
        $tipoCliente = $this->model->getTipoCliente();

        if (is_array($productos) && count($productos) > 0) {
            $id_transaccion = 'LLEVAR-' . uniqid();
            $metodo = 'LLEVAR';
            $monto = 0;
            $estado = 'PENDIENTE';
            $fecha = date('Y-m-d H:i:s');
            $email = $_SESSION['correoCliente'];
            $nombre = $_SESSION['nombreCliente'];
            $apellido = $_SESSION['apellidoCliente'];
            $direccion = $_SESSION['dirrecionCliente'];
            $ciudad = null;
            $id_cliente = $_SESSION['idCliente'];

            // Calcular el monto total
            foreach ($productos as $producto) {
                $temp = $this->model->getProducto($producto['id']);

                // NUEVO: Precio según tipo de cliente
                $precio = ($tipoCliente == 'mayorista') ? $temp['precio_mayorista'] : $temp['precio_venta'];

                if ($producto['size'] > 0 && $producto['color'] > 0) {
                    $result = $this->model->getAtributos($producto['size'], $producto['color'], $producto['id']);
                    if (empty($result)) {
                        $mensaje = array('msg' => 'Producto sin stock disponible', 'icono' => 'error');
                        echo json_encode($mensaje);
                        die();
                    }
                }
                $monto += $precio * $producto['cantidad'];
            }

            // Registrar el pedido
            $data = $this->model->registrarPedido(
                $id_transaccion,
                $metodo,
                $monto,
                $estado,
                $fecha,
                $email,
                $nombre,
                $apellido,
                $direccion,
                $ciudad,
                $id_cliente
            );

            if ($data > 0) {
                foreach ($productos as $producto) {
                    $temp = $this->model->getProducto($producto['id']);

                    // NUEVO: Precio según tipo de cliente
                    $precio = ($tipoCliente == 'mayorista') ? $temp['precio_mayorista'] : $temp['precio_venta'];

                    if ($producto['size'] > 0 && $producto['color'] > 0) {
                        $result = $this->model->getAtributos($producto['size'], $producto['color'], $producto['id']);
                        $tallaColor = $this->model->getIdTallaColor($producto['size'], $producto['color'], $producto['id']);
                        $id_talla_color = $tallaColor['id'];

                        // Registrar detalle con precio correcto
                        $this->model->registrarDetalle(
                            $temp['nombre'],
                            $precio,
                            $producto['cantidad'],
                            $data,
                            $producto['id'],
                            $id_talla_color
                        );

                        // Actualizar stock
                        $nuevoStock = $result['stock'] - $producto['cantidad'];
                        $this->model->actualizarStockDetalle($nuevoStock, $id_talla_color);
                    } else {
                        // Producto sin talla/color
                        $this->model->registrarDetalle(
                            $temp['nombre'],
                            $precio,
                            $producto['cantidad'],
                            $data,
                            $producto['id'],
                            null
                        );
                    }
                }

                unset($_SESSION['productos']);
                $mensaje = array('msg' => 'Pedido registrado exitosamente', 'icono' => 'success', 'idPedido' => $data);
            } else {
                $mensaje = array('msg' => 'Error al registrar el pedido', 'icono' => 'error');
            }
        } else {
            $mensaje = array('msg' => 'Error fatal con los datos', 'icono' => 'error');
        }
        echo json_encode($mensaje);
        die();
    }

    public function enviarTicketWhatsApp()
    {
        if (isset($_POST['idPedido'])) {
            $idPedido = strClean($_POST['idPedido']);

            // Obtener datos del pedido
            $pedido = $this->model->getPedido($idPedido);

            if (empty($pedido)) {
                $mensaje = array('msg' => 'Pedido no encontrado', 'icono' => 'error');
                echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
                die();
            }

            // Obtener empresa (incluye WhatsApp)
            $empresa = $this->model->getEmpresa();
            $whatsappNumber = preg_replace('/[^0-9]/', '', $empresa['whatsapp']);

            // Obtener detalles del pedido
            $productos = $this->model->verPedidos($idPedido);

            // Construir mensaje para WhatsApp
            $mensajeWhatsApp = "*🛒 NUEVO PEDIDO #" . str_pad($idPedido, 6, '0', STR_PAD_LEFT) . "*\n";
            $mensajeWhatsApp .= "━━━━━━━━━━━━━━━━━━━\n\n";

            $mensajeWhatsApp .= "*👤 DATOS DEL CLIENTE:*\n";
            $mensajeWhatsApp .= "📌 Nombre: " . $pedido['nombre'] . " " . $pedido['apellido'] . "\n";
            $mensajeWhatsApp .= "📧 Email: " . $pedido['email'] . "\n";
            if (!empty($pedido['direccion'])) {
                $mensajeWhatsApp .= "📍 Dirección: " . $pedido['direccion'] . "\n";
            }
            $mensajeWhatsApp .= "💳 Método: " . strtoupper($pedido['metodo']) . "\n";
            $mensajeWhatsApp .= "📊 Estado: " . strtoupper($pedido['estado']) . "\n\n";

            $mensajeWhatsApp .= "*📦 DETALLE DE PRODUCTOS:*\n";
            $mensajeWhatsApp .= "━━━━━━━━━━━━━━━━━━━\n";

            $subtotal = 0;
            foreach ($productos as $producto) {
                $total_producto = $producto['cantidad'] * $producto['precio'];
                $subtotal += $total_producto;

                $mensajeWhatsApp .= "\n*" . $producto['producto'] . "*\n";

                // Decodificar atributos si existen
                if (!empty($producto['atributos'])) {
                    $atributos = json_decode($producto['atributos'], true);
                    if ($atributos) {
                        $mensajeWhatsApp .= "   • Talla: " . $atributos['size'] . "\n";
                        $mensajeWhatsApp .= "   • Color: " . $atributos['color'] . "\n";
                    }
                }

                $mensajeWhatsApp .= "   • Cantidad: " . $producto['cantidad'] . "\n";
                $mensajeWhatsApp .= "   • Precio: COP " . number_format($producto['precio'], 2) . "\n";
                $mensajeWhatsApp .= "   • Subtotal: COP " . number_format($total_producto, 2) . "\n";
            }

            $mensajeWhatsApp .= "\n━━━━━━━━━━━━━━━━━━━\n";
            $mensajeWhatsApp .= "*💰 TOTAL A PAGAR: COP " . number_format($pedido['monto'], 2) . "*\n";
            $mensajeWhatsApp .= "━━━━━━━━━━━━━━━━━━━\n\n";

            $mensajeWhatsApp .= "📅 Fecha: " . date('d/m/Y H:i:s', strtotime($pedido['fecha'])) . "\n\n";
            $mensajeWhatsApp .= "✅ _Pedido registrado exitosamente_\n";
            $mensajeWhatsApp .= "🏪 *" . $empresa['nombre'] . "*";

            // Codificar mensaje para URL
            $mensajeCodificado = urlencode($mensajeWhatsApp);

            // Generar link de WhatsApp
            $whatsappLink = "https://wa.me/{$whatsappNumber}?text={$mensajeCodificado}";

            $mensaje = array(
                'msg' => 'Redirigiendo a WhatsApp',
                'icono' => 'success',
                'whatsappLink' => $whatsappLink
            );
        } else {
            $mensaje = array('msg' => 'ERROR FATAL', 'icono' => 'error');
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function enviarTicket()
    {
        if (isset($_POST['idPedido'])) {
            $idPedido = strClean($_POST['idPedido']);

            // Obtener el pedido para sacar el correo
            $pedido = $this->model->getPedido($idPedido);

            if (empty($pedido)) {
                $mensaje = array('msg' => 'Pedido no encontrado', 'icono' => 'error');
                echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
                die();
            }

            $correo = $pedido['email'];

            // Generar el PDF del ticket
            ob_start();
            $data['title'] = 'Ticket de Compra';
            $data['empresa'] = $this->model->getEmpresa();
            $data['venta'] = $pedido;
            $data['detalle'] = $this->model->verPedidos($idPedido);

            // Usar el template específico para ecommerce
            $this->views->getView('admin/ventas', 'ticket_ecommerce', $data);
            $html = ob_get_clean();

            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->set('isRemoteEnabled', true);
            $dompdf->setOptions($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper(array(0, 0, 226.77, 500), 'portrait');
            $dompdf->render();

            // Guardar PDF en memoria
            $pdfOutput = $dompdf->output();

            // Enviar correo
            $mail = new PHPMailer(true);
            try {
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = HOST_SMTP;
                $mail->SMTPAuth = true;
                $mail->Username = USER_SMTP;
                $mail->Password = PASS_SMTP;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = PUERTO_SMTP;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom(CORREO, TITLE);
                $mail->addAddress($correo);

                // Adjuntar PDF
                $mail->addStringAttachment($pdfOutput, 'pedido-' . $idPedido . '.pdf');

                $mail->isHTML(true);
                $mail->Subject = 'Confirmación de Pedido #' . $idPedido . ' - ' . TITLE;
                $mail->Body = '
                <h2>¡Gracias por tu compra!</h2>
                <p>Hola <strong>' . $pedido['nombre'] . '</strong>,</p>
                <p>Tu pedido ha sido registrado exitosamente.</p>
                <p><strong>Número de Pedido:</strong> #' . str_pad($idPedido, 6, '0', STR_PAD_LEFT) . '</p>
                <p><strong>Total:</strong> ' . number_format($pedido['monto'], 2) . ' COP</p>
                <p>Adjunto encontrarás el detalle completo de tu pedido.</p>
                <br>
                <p>¡Vuelve pronto!</p>
                <p><em>' . TITLE . '</em></p>
            ';
                $mail->AltBody = 'Gracias por tu compra. Tu pedido #' . $idPedido . ' ha sido registrado.';

                $mail->send();
                $mensaje = array('msg' => 'Ticket enviado a tu correo', 'icono' => 'success');
            } catch (Exception $e) {
                $mensaje = array('msg' => 'ERROR AL ENVIAR CORREO: ' . $mail->ErrorInfo, 'icono' => 'error');
            }
        } else {
            $mensaje = array('msg' => 'ERROR FATAL', 'icono' => 'error');
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        die();
    }
    //listar productos pendientes
    public function listarPendientes()
    {
        if (empty($_SESSION['idCliente'])) {
            header('Location: ' . BASE_URL);
            exit;
        }
        $id_cliente = $_SESSION['idCliente'];
        $data = $this->model->getPedidos($id_cliente);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="text-center"><button class="btn btn-primary" type="button" onclick="verPedido(' . $data[$i]['id'] . ')">Ver estado</button></div>';
        }
        echo json_encode($data);
        die();
    }
    public function verPedido($idPedido)
    {
        if (!empty($_SESSION['idCliente']) || !empty($_SESSION['id_usuario'])) {
            $data['pedido'] = $this->model->getPedido($idPedido);
            $data['productos'] = $this->model->verPedidos($idPedido);

            for ($i = 0; $i < count($data['productos']); $i++) {
                // Obtener datos de talla_color
                $id_talla_color = $data['productos'][$i]['id_talla_color'];
                $atributos = $this->model->getAtributosPorId($id_talla_color);

                if (!empty($atributos)) {
                    $data['productos'][$i]['atributos'] = json_encode([
                        'size' => $atributos['size'],
                        'color' => $atributos['nombre'],
                        'hexa' => $atributos['color']
                    ]);
                } else {
                    $data['productos'][$i]['atributos'] = json_encode([
                        'size' => 'N/A',
                        'color' => 'N/A',
                        'hexa' => '#000000'
                    ]);
                }
            }

            $data['moneda'] = MONEDA;
            echo json_encode($data);
        }
        die();
    }
    //listar productos pendientes
    public function listarProductos()
    {
        $id_cliente = $_SESSION['idCliente'];
        $data = $this->model->getProductos($id_cliente);
        for ($i = 0; $i < count($data); $i++) {
            $comprobar = $this->model->comprobarCalificacion($data[$i]['id_producto'], $id_cliente);
            $total = (empty($comprobar)) ? 0 : $comprobar['cantidad'];
            $uno = ($total >= 1) ? 'text-warning' : 'text-muted';
            $dos = ($total >= 2) ? 'text-warning' : 'text-muted';
            $tres = ($total >= 3) ? 'text-warning' : 'text-muted';
            $cuatro = ($total >= 4) ? 'text-warning' : 'text-muted';
            $cinco = ($total == 5) ? 'text-warning' : 'text-muted';
            $data[$i]['calificacion'] = '<ul class="list-unstyled d-flex justify-content-between">
                <li class="calificacion">
                    <i class="fa fa-star ' . $uno . '" onclick="agregarCalificacion(' . $data[$i]['id_producto'] . ', 1)"></i>
                    <i class="fa fa-star ' . $dos . '" onclick="agregarCalificacion(' . $data[$i]['id_producto'] . ', 2)"></i>
                    <i class="fa fa-star ' . $tres . '" onclick="agregarCalificacion(' . $data[$i]['id_producto'] . ', 3)"></i>
                    <i class="fa fa-star ' . $cuatro . '" onclick="agregarCalificacion(' . $data[$i]['id_producto'] . ', 4)"></i>
                    <i class="fa fa-star ' . $cinco . '" onclick="agregarCalificacion(' . $data[$i]['id_producto'] . ', 5)"></i>
                </li>
            </ul>';
        }
        echo json_encode($data);
        die();
    }

    public function agregarCalificacion()
    {
        if (empty($_SESSION['idCliente'])) {
            header('Location: ' . BASE_URL);
            exit;
        }
        $id_cliente = $_SESSION['idCliente'];
        $datos = file_get_contents('php://input');
        $json = json_decode($datos, true);
        $comprobar = $this->model->comprobarCalificacion($json['id_producto'], $id_cliente);
        if (empty($comprobar)) {
            $data = $this->model->agregarCalificacion($json['cantidad'], $json['id_producto'], $id_cliente);
            if ($data > 0) {
                $mensaje = array('msg' => 'calificacion agregada', 'icono' => 'success');
            } else {
                $mensaje = array('msg' => 'error al calificar', 'icono' => 'error');
            }
        } else {
            $data = $this->model->cambiarCalificacion($json['cantidad'], $json['id_producto'], $id_cliente);
            if ($data == 1) {
                $mensaje = array('msg' => 'calificacion agregada', 'icono' => 'success');
            } else {
                $mensaje = array('msg' => 'error al calificar', 'icono' => 'error');
            }
        }


        echo json_encode($mensaje);
        die();
    }

    public function agregarMensaje()
    {
        if (empty($_SESSION['idCliente'])) {
            header('Location: ' . BASE_URL);
            exit;
        }
        $id_cliente = $_SESSION['idCliente'];
        $datos = file_get_contents('php://input');
        $json = json_decode($datos, true);
        $consulta = $this->model->getTestimonio($id_cliente);
        if (empty($consulta)) {
            $data = $this->model->agregarMensaje($json['mensaje'], $id_cliente);
            if ($data > 0) {
                $mensaje = array('msg' => 'publicado', 'icono' => 'success');
            } else {
                $mensaje = array('msg' => 'error al publicar', 'icono' => 'error');
            }
        } else {
            $data = $this->model->modificarMensaje($json['mensaje'], $id_cliente);
            if ($data == 1) {
                $mensaje = array('msg' => 'publicado', 'icono' => 'success');
            } else {
                $mensaje = array('msg' => 'error al publicar', 'icono' => 'error');
            }
        }
        echo json_encode($mensaje);
        die();
    }

    public function modificarDatos()
    {
        if (empty($_SESSION['idCliente'])) {
            header('Location: ' . BASE_URL);
            exit;
        }

        if (isset($_POST['nombre']) && isset($_POST['apellidos']) && isset($_POST['correo'])) {
            $nombre = strClean($_POST['nombre']);
            $apellidos = strClean($_POST['apellidos']);
            $telefono = strClean($_POST['telefono']);
            $correo = strClean($_POST['correo']);
            $direccion = strClean($_POST['direccion']);
            $foto = $_FILES['fotoCliente'];
            $id = $_SESSION['idCliente'];

            if (
                empty($nombre) || empty($apellidos) || empty($telefono)
                || empty($correo) || empty($direccion)
            ) {
                $res = array('msg' => 'TODO LOS CAMPOS CON * SON REQUERIDOS', 'type' => 'warning');
            } else {
                $verificarTelefono = $this->model->getValidar('telefono', $telefono, 'actualizar', $id);
                if (empty($verificarTelefono)) {
                    if ($correo != null) {
                        $verificarCorreo = $this->model->getValidar('correo', $correo, 'actualizar', $id);
                        if (!empty($verificarCorreo)) {
                            $res = array('msg' => 'EL CORREO DEBE SER UNICO', 'type' => 'warning');
                            echo json_encode($res);
                            die();
                        }
                    }

                    //recuperar datos anteriores
                    $tmp = $this->model->editar($id);

                    // Determinar el destino de la foto
                    if (!empty($tmp['perfil']) && $tmp['perfil'] != 'default.png' && file_exists('assets/images/clientes/' . $tmp['perfil'])) {
                        if (!empty($foto['name'])) {
                            unlink('assets/images/clientes/' . $tmp['perfil']);
                        }
                        $destino = $tmp['perfil'];
                    } else {
                        $destino = (!empty($foto['name'])) ? $id . '.jpg' : 'default.png';
                    }

                    // AQUÍ ESTÁ LA CORRECCIÓN: Agregar $tmp['tipo_cliente']
                    $data = $this->model->actualizar(
                        $nombre,
                        $apellidos,
                        $telefono,
                        $correo,
                        $direccion,
                        $tmp['tipo_cliente'], // ← PARÁMETRO AGREGADO
                        $destino,
                        $id
                    );

                    if ($data > 0) {
                        if (!empty($foto['name'])) {
                            move_uploaded_file($foto['tmp_name'], 'assets/images/clientes/' . $destino);
                        }

                        // Actualizar la sesión con el nuevo perfil
                        $_SESSION['perfilCliente'] = $destino;

                        $res = array('msg' => 'PERFIL ACTUALIZADO CORRECTAMENTE', 'type' => 'success');
                    } else {
                        $res = array('msg' => 'ERROR AL MODIFICAR', 'type' => 'error');
                    }
                } else {
                    $res = array('msg' => 'EL TELEFONO DEBE SER UNICO', 'type' => 'warning');
                }
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO', 'type' => 'error');
        }

        echo json_encode($res);
        die();
    }

    public function salir()
    {
        session_destroy();
        header('Location: ' . BASE_URL);
    }

    ###### ADMIN CLIENTES #####
    public function admin()
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $data['title'] = 'Clientes';
        $this->views->getView('admin/clientes', 'index', $data);
    }
    public function listar()
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $data = $this->model->getClientes();
        for ($i = 0; $i < count($data); $i++) {
            $estado = $data[$i]['estado'];
            $verify = $data[$i]['verify'];
            $tipo = $data[$i]['tipo_cliente'];

            $color = $estado == 1 ? 'success' : 'danger';
            $texto = $estado == 1 ? 'ACTIVO' : 'INACTIVO';
            $data[$i]['estado'] = "<div class='badge rounded-pill text-$color bg-light-$color p-2 text-uppercase px-3'>$texto</div>";

            $colorTipo = $tipo == 'mayorista' ? 'primary' : 'info';
            $textoTipo = $tipo == 'mayorista' ? 'MAYORISTA' : 'FINAL';

            if ($tipo == 'mayorista' && $verify == 0) {
                $data[$i]['tipo_cliente'] = "<span class='badge bg-warning'>PENDIENTE</span>";
            } else {
                $data[$i]['tipo_cliente'] = "<span class='badge bg-$colorTipo'>$textoTipo</span>";
            }

            if ($estado == 1) {
                $botonAccion = '<button class="btn btn-danger" type="button" onclick="eliminar(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></button>';
            } else {
                $botonAccion = '<button class="btn btn-success" type="button" onclick="restaurar(' . $data[$i]['id'] . ')"><i class="fas fa-undo"></i></button>';
            }

            $botonAprobar = '';
            if ($tipo == 'mayorista' && $verify == 0) {
                $botonAprobar = '<button class="btn btn-warning" type="button" onclick="aprobarMayorista(' . $data[$i]['id'] . ')" title="Aprobar Mayorista"><i class="fas fa-check"></i></button>';
            }

            $data[$i]['accion'] = '<div class="d-flex gap-1">
            <button class="btn btn-primary" type="button" onclick="editCat(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
            ' . $botonAprobar . '
            ' . $botonAccion . '
        </div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function aprobarMayorista($id)
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }

        // Obtener datos del cliente
        $cliente = $this->model->editar($id);

        if (!empty($cliente)) {
            // Verificar que sea mayorista y no esté verificado
            if ($cliente['tipo_cliente'] == 'mayorista' && $cliente['verify'] == 0) {

                // Actualizar verify a 1
                $actualizado = $this->model->aprobarClienteMayorista($id);

                // ✅ VERIFICAR QUE LA ACTUALIZACIÓN FUE EXITOSA
                if ($actualizado == 1) {

                    // Enviar correo de aprobación
                    $mail = new PHPMailer(true);
                    try {
                        //Server settings
                        $mail->SMTPDebug = 0;
                        $mail->isSMTP();
                        $mail->Host = HOST_SMTP;
                        $mail->SMTPAuth = true;
                        $mail->Username = USER_SMTP;
                        $mail->Password = PASS_SMTP;
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port = PUERTO_SMTP;

                        //Recipients
                        $mail->setFrom(CORREO, TITLE);
                        $mail->addAddress($cliente['correo']);

                        //Content
                        $mail->isHTML(true);
                        $mail->CharSet = 'UTF-8';
                        $mail->Subject = '¡Cuenta Mayorista Aprobada! - ' . TITLE;
                        $mail->Body = '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .header { background: linear-gradient(135deg, #057997 0%, #046680 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                            .content { background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; }
                            .button { display: inline-block; padding: 15px 30px; background: #057997; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; }
                            .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 10px 10px; }
                            .success-box { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="header">
                                <h1>✅ ¡Felicidades!</h1>
                                <p>Tu cuenta mayorista ha sido aprobada</p>
                            </div>
                            <div class="content">
                                <p>Estimado/a <strong>' . htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) . '</strong>,</p>
                                
                                <div class="success-box">
                                    <strong>🎉 ¡Excelentes noticias!</strong><br>
                                    Tu solicitud de registro como Cliente Mayorista ha sido <strong>APROBADA</strong>.
                                </div>
                                
                                <p><strong>Tu cuenta ya está activa y puedes realizar tus compras con precios especiales mayoristas.</strong></p>
                                
                                <div style="text-align: center;">
                                    <a href="' . BASE_URL . '" class="button">
                                        IR A LA TIENDA
                                    </a>
                                </div>
                                
                                <p><strong>Beneficios de tu cuenta mayorista:</strong></p>
                                <ul>
                                    <li>✓ Precios especiales mayoristas</li>
                                    <li>✓ Descuentos exclusivos</li>
                                    <li>✓ Atención personalizada</li>
                                    <li>✓ Pedidos al por mayor</li>
                                </ul>
                                
                                <p>Inicia sesión con tu correo y contraseña para comenzar a comprar.</p>
                                
                                <p>¡Gracias por confiar en nosotros!</p>
                            </div>
                            <div class="footer">
                                <p>© ' . date('Y') . ' ' . TITLE . '. Todos los derechos reservados.</p>
                            </div>
                        </div>
                    </body>
                    </html>';

                        $mail->AltBody = 'Tu cuenta mayorista ha sido aprobada. Ya puedes realizar tus compras en: ' . BASE_URL;

                        $mail->send();
                        $res = array('msg' => 'Cliente mayorista aprobado y correo enviado exitosamente', 'icono' => 'success');

                    } catch (Exception $e) {
                        $res = array('msg' => 'Cliente aprobado pero error al enviar correo: ' . $mail->ErrorInfo, 'icono' => 'warning');
                    }

                } else {
                    // ✅ SI LA ACTUALIZACIÓN FALLÓ
                    $res = array('msg' => 'Error al aprobar cliente mayorista en la base de datos', 'icono' => 'error');
                }

            } else {
                $res = array('msg' => 'El cliente ya está aprobado o no es mayorista', 'icono' => 'warning');
            }
        } else {
            $res = array('msg' => 'Cliente no encontrado', 'icono' => 'error');
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function registrar()
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        if (isset($_POST['nombre']) && isset($_POST['apellido'])) {
            $id = strClean($_POST['id']);
            $nombre = strClean($_POST['nombre']);
            $apellido = strClean($_POST['apellido']);
            $telefono = strClean($_POST['telefono']);
            $correo = (empty($_POST['correo'])) ? null : strClean($_POST['correo']);
            $direccion = strClean($_POST['direccion']);
            $ciudad = strClean($_POST['ciudad']);
            $departamento = strClean($_POST['departamento']);
            $barrio = strClean($_POST['barrio']);
            $tipo_cliente = strClean($_POST['tipo_cliente']);

            if (empty($nombre)) {
                $res = array('msg' => 'EL NOMBRE ES REQUERIDO', 'icono' => 'warning');
            } else if (empty($apellido)) {
                $res = array('msg' => 'EL APELLIDO ES REQUERIDO', 'icono' => 'warning');
            } else if (empty($tipo_cliente)) {
                $res = array('msg' => 'EL TIPO DE CLIENTE ES REQUERIDO', 'icono' => 'warning');
            } else {
                if ($id == '') {
                    $verificarTelefono = $this->model->getValidar('telefono', $telefono, 'registrar', 0);
                    if (empty($verificarTelefono)) {
                        if ($correo != null) {
                            $verificarCorreo = $this->model->getValidar('correo', $correo, 'registrar', 0);
                            if (!empty($verificarCorreo)) {
                                $res = array('msg' => 'EL CORREO DEBE SER UNICO', 'icono' => 'warning');
                                echo json_encode($res);
                                die();
                            }
                        }
                        $data = $this->model->registrar(
                            $nombre,
                            $apellido,
                            $telefono,
                            $correo,
                            $direccion,
                            $ciudad,
                            $departamento,
                            $barrio,
                            $tipo_cliente,
                            'ADMINISTRACION'
                        );
                        if ($data > 0) {
                            $res = array('msg' => 'CLIENTE REGISTRADO', 'icono' => 'success');
                        } else {
                            $res = array('msg' => 'ERROR AL REGISTRAR', 'icono' => 'error');
                        }
                    } else {
                        $res = array('msg' => 'EL TELEFONO DEBE SER UNICO', 'icono' => 'warning');
                    }
                } else {
                    $verificarTelefono = $this->model->getValidar('telefono', $telefono, 'actualizar', $id);
                    if (empty($verificarTelefono)) {
                        if ($correo != null) {
                            $verificarCorreo = $this->model->getValidar('correo', $correo, 'actualizar', $id);
                            if (!empty($verificarCorreo)) {
                                $res = array('msg' => 'EL CORREO DEBE SER UNICO', 'icono' => 'warning');
                                echo json_encode($res);
                                die();
                            }
                        }
                        $data = $this->model->actualizar(
                            $nombre,
                            $apellido,
                            $telefono,
                            $correo,
                            $direccion,
                            $ciudad,
                            $departamento,
                            $barrio,
                            $tipo_cliente,
                            'default.png',
                            $id
                        );
                        if ($data > 0) {
                            $res = array('msg' => 'CLIENTE MODIFICADO', 'icono' => 'success');
                        } else {
                            $res = array('msg' => 'ERROR AL MODIFICAR', 'icono' => 'error');
                        }
                    } else {
                        $res = array('msg' => 'EL TELEFONO DEBE SER UNICO', 'icono' => 'warning');
                    }
                }
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($res);
        die();
    }

    public function delete($idCliente)
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        if (is_numeric($idCliente)) {
            $data = $this->model->eliminar(0, $idCliente);
            if ($data > 0) {
                $res = array('msg' => 'CLIENTE INACTIVO', 'icono' => 'success');
            } else {
                $res = array('msg' => 'ERROR AL ELIMINAR', 'icono' => 'error');
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function restaurar($idCliente)
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        if (is_numeric($idCliente)) {
            $data = $this->model->eliminar(1, $idCliente);
            if ($data == 1) {
                $res = array('msg' => 'CLIENTE RESTAURADO', 'icono' => 'success');
            } else {
                $res = array('msg' => 'ERROR AL RESTAURAR', 'icono' => 'error');
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($res);
        die();
    }

    public function edit($idCliente)
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        if (is_numeric($idCliente)) {
            $data = $this->model->editar($idCliente);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
