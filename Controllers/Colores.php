<?php
class Colores extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['id_usuario'])) {
            header('Location: '. BASE_URL . 'admin');
            exit;
        }
    }
    public function index()
    {
        $data['title'] = 'colores';
        $this->views->getView('admin/atributos', "colores", $data);
    }
    public function listar()
    {
        $data = $this->model->getColores(1);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['color'] = '<span class="badge" style="background: '.$data[$i]['color'].';">'.$data[$i]['color'].'</span>';
            $data[$i]['accion'] = '<div class="d-flex">
                <button class="btn btn-primary" type="button" onclick="editColor(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
                <button class="btn btn-danger" type="button" onclick="eliminarColor(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></button>
            </div>';
        }
        echo json_encode($data);
        die();
    }

    public function registrar()
    {
        if (isset($_POST['nombre'])) {
            $nombre = $_POST['nombre'];
            $color = $_POST['color'];
            $id = $_POST['id'];
            if (empty($_POST['nombre']) || empty($_POST['color'])) {
                $respuesta = array('msg' => 'todo los campos son requeridos', 'icono' => 'warning');
            } else {                
                if (empty($id)) {
                    $result = $this->model->verificarColor($nombre);
                    if (empty($result)) {
                        $data = $this->model->registrar($nombre, $color);
                        if ($data > 0) {
                            $respuesta = array('msg' => 'color registrado', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'error al registrar', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'color ya existe', 'icono' => 'warning');
                    }
                } else {
                    $data = $this->model->modificar($nombre, $color, $id);
                    if ($data == 1) {
                        $respuesta = array('msg' => 'color modificado', 'icono' => 'success');
                    } else {
                        $respuesta = array('msg' => 'error al modificar', 'icono' => 'error');
                    }
                }
            }
            echo json_encode($respuesta);
        }
        die();
    }
    //eliminar Color
    public function delete($idColor)
    {
        if (is_numeric($idColor)) {
            $data = $this->model->eliminar($idColor);
            if ($data == 1) {
                $respuesta = array('msg' => 'color dado de baja', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'error al eliminar', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
    //editar Color
    public function edit($idColor)
    {
        if (is_numeric($idColor)) {
            $data = $this->model->getColor($idColor);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}