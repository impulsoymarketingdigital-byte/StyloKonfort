<?php
class Sizes extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['id_usuario'])) {
            header('Location: '. BASE_URL . 'admin');
            exit;
        }
    }
    public function index()
    {
        $data['title'] = 'Sizes';
        $this->views->getView('admin/atributos', "sizes", $data);
    }
    public function listar()
    {
        $data = $this->model->getSizes(1);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex">
                <button class="btn btn-primary" type="button" onclick="editSize(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
                <button class="btn btn-danger" type="button" onclick="eliminarSize(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></button>
            </div>';
        }
        echo json_encode($data);
        die();
    }

    public function registrar()
    {
        if (isset($_POST['nombre'])) {
            $nombre = $_POST['nombre'];
            $nombre_corto = $_POST['nombre_corto'];
            $id = $_POST['id'];
            if (empty($_POST['nombre']) || empty($_POST['nombre_corto'])) {
                $respuesta = array('msg' => 'todo los campos son requeridos', 'icono' => 'warning');
            } else {                
                if (empty($id)) {
                    $result = $this->model->verificarSize($nombre);
                    if (empty($result)) {
                        $data = $this->model->registrar($nombre, $nombre_corto);
                        if ($data > 0) {
                            $respuesta = array('msg' => 'Size registrado', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'error al registrar', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'Size ya existe', 'icono' => 'warning');
                    }
                } else {
                    $data = $this->model->modificar($nombre, $nombre_corto, $id);
                    if ($data == 1) {
                        $respuesta = array('msg' => 'Size modificado', 'icono' => 'success');
                    } else {
                        $respuesta = array('msg' => 'error al modificar', 'icono' => 'error');
                    }
                }
            }
            echo json_encode($respuesta);
        }
        die();
    }
    //eliminar nombre_corto
    public function delete($idSize)
    {
        if (is_numeric($idSize)) {
            $data = $this->model->eliminar($idSize);
            if ($data == 1) {
                $respuesta = array('msg' => 'Size dado de baja', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'error al eliminar', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
    //editar nombre_corto
    public function edit($idSize)
    {
        if (is_numeric($idSize)) {
            $data = $this->model->getSize($idSize);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}