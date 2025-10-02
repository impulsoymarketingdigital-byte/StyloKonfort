<?php
class Sliders extends Controller
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
        $data['title'] = 'Sliders';
        $this->views->getView('admin/administracion', "sliders", $data);
    }
    public function listar()
    {
        $data = $this->model->getSliders(1);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['imagen'] = '<img class="img-thumbnail" src="' . $data[$i]['imagen'] . '" width="50">';
            $data[$i]['accion'] = '<button class="btn btn-primary" type="button" onclick="editSli(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>';
        }
        echo json_encode($data);
        die();
    }

    public function registrar()
    {
        if (isset($_POST['titulo'])) {
            $titulo = $_POST['titulo'];
            $subtitulo = $_POST['subtitulo'];
            $enlace = $_POST['enlace'];
            $imagen = $_FILES['imagen'];
            $tmp_name = $imagen['tmp_name'];
            $id = $_POST['id'];
            $ruta = 'assets/images/carrusel/';
            if (empty($id) || empty($titulo) || empty($subtitulo) || empty($enlace)) {
                $respuesta = array('msg' => 'Todo los campos son requeridos', 'icono' => 'warning');
            } else {
                $destino = $ruta . $id . '.jpg';
                if (!empty($_POST['imagen_actual'])) {
                    $destino = $_POST['imagen_actual'];
                }
                $result = $this->model->verificarTitulo($titulo, $id);
                if (empty($result)) {
                    $data = $this->model->modificar($titulo, $subtitulo, $enlace, $destino, $id);
                    if ($data == 1) {
                        if (!empty($imagen['name'])) {
                            if (file_exists($ruta . $id . '.jpg')) {
                                unlink($ruta . $id . '.jpg');
                            }
                            move_uploaded_file($tmp_name, $destino);
                        }
                        
                        $respuesta = array('msg' => 'slider modificado', 'icono' => 'success');
                    } else {
                        $respuesta = array('msg' => 'error al modificar', 'icono' => 'error');
                    }
                } else {
                    $respuesta = array('msg' => 'titulo ya existe', 'icono' => 'warning');
                }
            }
            echo json_encode($respuesta);
        }
        die();
    }
    //editar cat
    public function edit($idSli)
    {
        if (is_numeric($idSli)) {
            $data = $this->model->getSlider($idSli);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
