<?php
class Admin extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }
    public function index()
    {
        if (!empty($_SESSION['nombre_usuario'])) {
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
                $respuesta = array('msg' => 'todo los campos son requeridos', 'icono' => 'warning');
            } else {
                $data = $this->model->getUsuario($_POST['email']);
                if (empty($data)) {
                    $respuesta = array('msg' => 'el correo no existe', 'icono' => 'warning');
                } else {
                    if (password_verify($_POST['clave'], $data['clave'])) {
                        $_SESSION['id_usuario'] = $data['id'];
                        $_SESSION['email'] = $data['correo'];
                        $_SESSION['nombre_usuario'] = $data['nombres'];
                        $_SESSION['perfil_usuario'] = $data['perfil'];
                        $_SESSION['id_sucursal'] = $data['id_sucursal'];
                        $respuesta = array('msg' => 'datos correcto', 'icono' => 'success');
                    } else {
                        $respuesta = array('msg' => 'contraseña incorrecta', 'icono' => 'warning');
                    }
                }
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function home()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
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
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $data = $this->model->productosMinimos();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function topProductos()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $data = $this->model->topProductos();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function salir()
    {
        session_destroy();
        header('Location: ' . BASE_URL);
    }

    //CONFIGURACION
    public function empresa()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $data['title'] = 'Datos de empresa';
        $data['empresa'] = $this->model->getEmpresa();
        $this->views->getView('admin/administracion', "empresa", $data);
    }

    public function modificar()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
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
                $res = array('msg' => 'Todo los campos con * son requeridos', 'type' => 'warning');
            } else {
                $data = $this->model->actualizar($ruc,$nombre,$telefono,$correo,$direccion,$whatsapp,$facebook, $twitter, $instagram, $ubicacion, $mensaje,$id
                );
                if ($data > 0) {
                    $res = array('msg' => 'DATOS MODIFICADO', 'type' => 'success');
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
