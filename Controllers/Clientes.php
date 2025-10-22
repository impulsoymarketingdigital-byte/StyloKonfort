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
                $documento = strClean($_POST['documentoRegistro']);
                $tipo_cliente = strClean($_POST['tipoClienteRegistro']);

                $verificar = $this->model->getVerificar('clientes', $correo);
                if (empty($verificar)) {
                    // Verificar si el documento ya existe
                    $verificarDocumento = $this->model->getVerificarDocumento($documento);
                    if (empty($verificarDocumento)) {
                        $token = md5($correo);
                        $hash = password_hash($clave, PASSWORD_DEFAULT);
                        $data = $this->model->registroDirecto($nombre, $apellido, $correo, $hash, $token, $telefono, $direccion, $documento, $tipo_cliente);
                        if ($data > 0) {
                            $cliente = $this->model->editar($data);
                            $_SESSION['idCliente'] = $cliente['id'];
                            $_SESSION['correoCliente'] = $cliente['correo'];
                            $_SESSION['nombreCliente'] = $cliente['nombre'];
                            $_SESSION['apellidoCliente'] = $cliente['apellido'];
                            $_SESSION['dirrecionCliente'] = $cliente['direccion'];
                            $_SESSION['perfilCliente'] = $cliente['perfil'];
                            $mensaje = array('msg' => 'Registrado con éxito', 'icono' => 'success', 'token' => $token);
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
                //Server settings
                $mail->SMTPDebug = 0;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host = HOST_SMTP;                     //Set the SMTP server to send through
                $mail->SMTPAuth = true;                                   //Enable SMTP authentication
                $mail->Username = USER_SMTP;                     //SMTP username
                $mail->Password = PASS_SMTP;                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port = PUERTO_SMTP;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom(CORREO, TITLE);
                $mail->addAddress($correo);

                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'Mensaje desde la: ' . TITLE;
                $mail->Body = 'Para verificar tu correo en nuestra tienda <a href="' . BASE_URL . 'clientes/verificarCorreo/' . $token . '">CLIC AQUÍ</a>';
                $mail->AltBody = 'GRACIAS POR LA PREFERENCIA';

                $mail->send();
                $mensaje = array('msg' => 'CORREO ENVIADO, REVISA TU BANDEJA DE ENTRADA - SPAN', 'icono' => 'success');
            } catch (Exception $e) {
                $mensaje = array('msg' => 'ERROR AL ENVIAR CORREO: ' . $mail->ErrorInfo, 'icono' => 'error');
            }
        } else {
            $mensaje = array('msg' => 'ERROR FATAL: ', 'icono' => 'error');
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
                        $mensaje = array('msg' => 'OK', 'icono' => 'success');
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
                if ($producto['size'] > 0 && $producto['color'] > 0) {
                    $result = $this->model->getAtributos($producto['size'], $producto['color'], $producto['id']);
                    if (empty($result)) {
                        $mensaje = array('msg' => 'Producto sin stock disponible', 'icono' => 'error');
                        echo json_encode($mensaje);
                        die();
                    }
                    $precio = $result['precio'];
                } else {
                    $temp = $this->model->getProducto($producto['id']);
                    $precio = $temp['precio'];
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

                    if ($producto['size'] > 0 && $producto['color'] > 0) {
                        $result = $this->model->getAtributos($producto['size'], $producto['color'], $producto['id']);
                        $tallaColor = $this->model->getIdTallaColor($producto['size'], $producto['color'], $producto['id']);
                        $precio = $result['precio'];
                        $id_talla_color = $tallaColor['id'];

                        // Registrar detalle con id_talla_color
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
                        $precio = $temp['precio'];
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
                $mensajeWhatsApp .= "   • Precio: Bs " . number_format($producto['precio'], 2) . "\n";
                $mensajeWhatsApp .= "   • Subtotal: Bs " . number_format($total_producto, 2) . "\n";
            }

            $mensajeWhatsApp .= "\n━━━━━━━━━━━━━━━━━━━\n";
            $mensajeWhatsApp .= "*💰 TOTAL A PAGAR: Bs " . number_format($pedido['monto'], 2) . "*\n";
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
                <p><strong>Total:</strong> ' . number_format($pedido['monto'], 2) . ' Bs</p>
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
                    //recuperar anterior
                    $tmp = $this->model->editar($id);
                    if (file_exists('assets/images/clientes/' . $tmp['perfil'])) {
                        unlink('assets/images/clientes/' . $tmp['perfil']);
                        $destino = $tmp['perfil'];
                    } else {
                        $destino = (empty($foto['name'])) ? 'default.png' : $id . '.jpg';
                    }

                    $data = $this->model->actualizar(
                        $nombre,
                        $apellidos,
                        $telefono,
                        $correo,
                        $direccion,
                        $destino,
                        $id
                    );
                    if ($data > 0) {
                        if (!empty($foto['name'])) {
                            move_uploaded_file($foto['tmp_name'], 'assets/images/clientes/' . $destino);
                        }
                        $res = array('msg' => 'CLIENTE MODIFICADO', 'type' => 'success');
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
            $color = $estado == 1 ? 'success' : 'danger';
            $texto = $estado == 1 ? 'ACTIVO' : 'INACTIVO';
            $data[$i]['estado'] = "<div class='badge rounded-pill text-$color bg-light-$color p-2 text-uppercase px-3'>$texto</div>";

            $tipo = $data[$i]['tipo_cliente'];
            $colorTipo = $tipo == 'mayorista' ? 'primary' : 'info';
            $textoTipo = $tipo == 'mayorista' ? 'MAYORISTA' : 'FINAL';
            $data[$i]['tipo_cliente'] = "<span class='badge bg-$colorTipo'>$textoTipo</span>";

            if ($estado == 1) {
                $botonAccion = '<button class="btn btn-danger" type="button" onclick="eliminar(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></button>';
            } else {
                $botonAccion = '<button class="btn btn-success" type="button" onclick="restaurar(' . $data[$i]['id'] . ')"><i class="fas fa-undo"></i></button>';
            }

            $data[$i]['accion'] = '<div class="d-flex">
        <button class="btn btn-primary" type="button" onclick="editCat(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
        ' . $botonAccion . '
        </div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
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
