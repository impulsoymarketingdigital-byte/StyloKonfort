<?php
class Usuarios extends Controller
{
    private $id_usuario;
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $this->id_usuario = $_SESSION['id_usuario'];
    }
    public function index()
    {
        $data['title'] = 'usuarios';
        $data['almacenes'] = $this->model->getDatos('almacenes');
        $data['roles'] = $this->model->getDatos('roles');

        $this->views->getView('admin/usuarios', "index", $data);
    }

    public function listar()
    {
        $data = $this->model->getUsuarios();
        for ($i = 0; $i < count($data); $i++) {
            $estado = $data[$i]['estado'];
            $color = $estado == 1 ? 'success' : 'danger';
            $texto = $estado == 1 ? 'ACTIVO' : 'INACTIVO';
            $data[$i]['estado'] = "<div class='badge rounded-pill text-$color bg-light-$color p-2 text-uppercase px-3'>$texto</div>";

            if ($estado == 1) {
                $botonAccion = '<button class="btn btn-danger" type="button" onclick="eliminar(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></button>';
            } else {
                $botonAccion = '<button class="btn btn-success" type="button" onclick="restaurar(' . $data[$i]['id'] . ')"><i class="fas fa-undo"></i></button>';
            }
            $data[$i]['accion'] = '<div class="d-flex">
            <button class="btn btn-primary" type="button" onclick="editUser(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
            ' . $botonAccion . '
        </div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        die();
    }

    public function registrar()
    {
        if (isset($_POST['nombre'])) {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $correo = $_POST['correo'];
            $id_almacen = $_POST['id_almacen'];
            $id_rol = $_POST['id_rol'];
            $clave = $_POST['clave'];
            $hash = password_hash($clave, PASSWORD_DEFAULT);

            if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['id_almacen']) || empty($_POST['id_rol'])) {
                $respuesta = array('msg' => 'todo los campos son requeridos', 'icono' => 'warning');
            } else {
                if (empty($id)) {
                    $result = $this->model->verificarCorreo($correo);
                    if (empty($result)) {
                        $data = $this->model->registrar($nombre, $apellido, $correo, $hash, $id_almacen, $id_rol);
                        if ($data > 0) {
                            $respuesta = array('msg' => 'usuario registrado', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'error al registrar', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'correo ya existe', 'icono' => 'warning');
                    }
                } else {
                    $data = $this->model->modificar($nombre, $apellido, $correo, $id_almacen, $id_rol, $id);
                    if ($data == 1) {
                        $respuesta = array('msg' => 'usuario modificado', 'icono' => 'success');
                    } else {
                        $respuesta = array('msg' => 'error al modificar', 'icono' => 'error');
                    }
                }
            }
            echo json_encode($respuesta);
        }
        die();
    }

    //eliminar user
    public function delete($idUser)
    {
        if (is_numeric($idUser)) {
            $data = $this->model->eliminar($idUser);
            if ($data == 1) {
                $respuesta = array('msg' => 'usuario dado de baja', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'error al eliminar', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }

    public function restaurar($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->eliminar(1, $id);
            if ($data == 1) {
                $respuesta = array('msg' => 'USUARIO RESTAURADO', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'ERROR AL ELIMINAR', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
    //editar user
    public function edit($idUser)
    {
        if (is_numeric($idUser)) {
            $data = $this->model->getUsuario($idUser);
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        }
        die();
    }
    public function profile()
    {
        $data['title'] = 'perfil de usuario';
        $data['usuario'] = $this->model->getUsuario($this->id_usuario);
        $this->views->getView('admin/usuarios', "perfil", $data);
    }

    public function modificarDatos()
    {
        $nombre = strClean($_POST['nombrePerfil']);
        $apellidos = strClean($_POST['apellidoPerfil']);
        $correo = strClean($_POST['correoPerfil']);

        $foto = $_FILES['fotoPerfil'];
        $name = $foto['name'];
        $tmp = $foto['tmp_name'];

        $verificarPerfil = $this->model->getUsuario($this->id_usuario);
        $destino = $verificarPerfil['perfil'];

        if (!empty($name)) {
            if (file_exists($destino)) {
                unlink($destino);
            }
            $perfil = date('YmdHis') . '.jpg';
            $destino = 'assets/images/perfil/' . $perfil;
        }

        if (empty($nombre)) {
            $res = array('msg' => 'EL NOMBRE ES REQUERIDO', 'type' => 'warning');
        } else if (empty($apellidos)) {
            $res = array('msg' => 'EL APELLIDO ES REQUERIDO', 'type' => 'warning');
        } else if (empty($correo)) {
            $res = array('msg' => 'EL CORREO ES REQUERIDO', 'type' => 'warning');
        } else {
            $verificarCorreo = $this->model->getValidar('correo', $correo, 'actualizar', $this->id_usuario);
            if (empty($verificarCorreo)) {
                $data = $this->model->modificarDatos($nombre, $apellidos, $correo, $destino, $this->id_usuario);
                if ($data == 1) {
                    if (!empty($name)) {
                        move_uploaded_file($tmp, $destino);
                    }
                    $res = array('msg' => 'DATOS MODIFICADO', 'type' => 'success');
                } else {
                    $res = array('msg' => 'ERROR AL MODIFICAR', 'type' => 'error');
                }
            } else {
                $res = array('msg' => 'EL CORREO DEBE SER UNICO', 'type' => 'warning');
            }
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function cambiarPass()
    {
        $claveActual = strClean($_POST['claveActual']);
        $claveNueva = strClean($_POST['claveNueva']);

        if (empty($claveActual)) {
            $res = array('msg' => 'CLAVE ACTUAL ES REQUERIDO', 'type' => 'warning');
        } else if (empty($claveNueva)) {
            $res = array('msg' => 'CLAVE NUEVA ES REQUERIDO', 'type' => 'warning');
        } else {
            $verificar = $this->model->getUsuario($this->id_usuario);
            if (password_verify($claveActual, $verificar['clave'])) {
                $hash = password_hash($claveNueva, PASSWORD_DEFAULT);
                $data = $this->model->modificarPass($hash, $this->id_usuario);
                if ($data == 1) {
                    $res = array('msg' => 'CONTRASEÑA MODIFICADA', 'type' => 'success');
                } else {
                    $res = array('msg' => 'ERROR AL MODIFICAR', 'type' => 'error');
                }
            } else {
                $res = array('msg' => 'CONTRASEÑA ACTUAL INCORRECTA', 'type' => 'error');
            }
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }
}
