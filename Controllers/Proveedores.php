<?php
class Proveedores extends Controller
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
        $data['title'] = 'proveedores';
        $this->views->getView('admin/proveedores', "index", $data);
    }

    public function listar()
    {
        $data = $this->model->getProveedores();
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
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        die();
    }

    public function registrar()
    {
        if (isset($_POST['nombre']) && isset($_POST['ruc'])) {
            $id = strClean($_POST['id']);
            $nombre = strClean($_POST['nombre']);
            $persona_contacto = strClean($_POST['persona_contacto']);
            $documento = strClean($_POST['documento']);
            $ruc = strClean($_POST['ruc']);
            $telefono = strClean($_POST['telefono']);
            $direccion = strClean($_POST['direccion']);
            $email = strClean($_POST['email']);

            if (empty($nombre)) {
                $respuesta = array('msg' => 'EL NOMBRE ES REQUERIDO', 'icono' => 'warning');
            } else if (empty($ruc)) {
                $respuesta = array('msg' => 'EL RUC ES REQUERIDO', 'icono' => 'warning');
            } else {
                if ($id == '') {
                    $verificarRuc = $this->model->getValidar('ruc', $ruc, 'registrar', 0);
                    if (empty($verificarRuc)) {
                        $data = $this->model->registrar(
                            $nombre,
                            $persona_contacto,
                            $documento,
                            $ruc,
                            $telefono,
                            $direccion,
                            $email
                        );
                        if ($data > 0) {
                            $respuesta = array('msg' => 'PROVEEDOR REGISTRADO', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'ERROR AL REGISTRAR', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'EL PROVEEDOR YA EXISTE', 'icono' => 'warning');
                    }
                } else {
                    $verificarRuc = $this->model->getValidar('ruc', $ruc, 'actualizar', $id);
                    if (empty($verificarRuc)) {
                        $data = $this->model->actualizar(
                            $nombre,
                            $persona_contacto,
                            $documento,
                            $ruc,
                            $telefono,
                            $direccion,
                            $email,
                            $id
                        );
                        if ($data > 0) {
                            $respuesta = array('msg' => 'PROVEEDOR MODIFICADO', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'ERROR AL MODIFICAR', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'EL PROVEEDOR YA EXISTE', 'icono' => 'warning');
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
                $respuesta = array('msg' => 'PROVEEDOR INACTIVO', 'icono' => 'success');
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
                $respuesta = array('msg' => 'PROVEEDOR RESTAURADO', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'ERROR AL ELIMINAR', 'icono' => 'error');
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
            $data = $this->model->getProveedor($id);
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        }
        die();
    }
}
?>
