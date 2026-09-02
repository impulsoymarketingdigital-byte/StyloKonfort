<?php
class Roles extends Controller
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

    public function index()
    {
        $data['title'] = 'roles';
        $data['permisos'] = $this->model->getPermisos();
        $this->views->getView('admin/roles', "index", $data);
    }

    public function listar()
    {
        $data = $this->model->getRoles();
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
            <button class="btn btn-primary" type="button" onclick="editCat(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
            ' . $botonAccion . '
             </div>';
        }
        echo json_encode($data);
        die();
    }

    public function registrar()
    {
        if (isset($_POST['nombre'])) {
            $id = strClean($_POST['id']);
            $nombre = strClean($_POST['nombre']);
            $permisos = (!empty($_POST['permisos'])) ? $_POST['permisos'] : null;

            if (empty($nombre)) {
                $respuesta = array('msg' => 'EL NOMBRE ES REQUERIDO', 'icono' => 'warning');
            } else if ($permisos == null) {
                $respuesta = array('msg' => 'DEBES SELECCIONAR AL MENOS UN PERMISO', 'icono' => 'warning');
            } else {
                $listaPermisos = json_encode($permisos);

                if ($id == '') {
                    $verificar = $this->model->getValidar('nombre', $nombre, 'registrar', 0);
                    if (empty($verificar)) {
                        $data = $this->model->registrar($nombre, $listaPermisos);
                        if ($data > 0) {
                            $respuesta = array('msg' => 'ROL REGISTRADO', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'ERROR AL REGISTRAR', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'EL ROL YA EXISTE', 'icono' => 'warning');
                    }
                } else {
                    $verificar = $this->model->getValidar('nombre', $nombre, 'actualizar', $id);
                    if (empty($verificar)) {
                        $data = $this->model->actualizar($nombre, $listaPermisos, $id);
                        if ($data > 0) {
                            $respuesta = array('msg' => 'ROL MODIFICADO', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'ERROR AL MODIFICAR', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'EL ROL YA EXISTE', 'icono' => 'warning');
                    }
                }
            }
        } else {
            $respuesta = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }

    public function delete($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->eliminar(0, $id);
            if ($data == 1) {
                $respuesta = array('msg' => 'ROL INACTIVO', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'ERROR AL ELIMINAR', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }

    public function restaurar($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->eliminar(1, $id);
            if ($data == 1) {
                $respuesta = array('msg' => 'ROL RESTAURADO', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'ERROR AL RESTAURAR', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }

    public function edit($id)
    {
        if (is_numeric($id)) {
            $data['rol'] = $this->model->getRol($id);
            $permisos = [];
            if ($data['rol']['permisos'] != null) {
                $permisos = json_decode($data['rol']['permisos'], true);
            }
            $data['permisos'] = $permisos;
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
?>