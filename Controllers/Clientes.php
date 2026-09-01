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
                
                // SEGURIDAD: Forzar la validación del tipo de cliente
                $tipo_solicitado = strClean($_POST['