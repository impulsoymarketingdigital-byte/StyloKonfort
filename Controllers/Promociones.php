<?php
class Promociones extends Controller
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
        $data['title'] = 'Promociones';
        $this->views->getView('admin/promociones', "index", $data);
    }

    public function listar()
    {
        $data = $this->model->getPromociones();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['imagen'] = '<img class="img-thumbnail" src="' . BASE_URL . $data[$i]['imagen'] . '" alt="' . $data[$i]['titulo'] . '" width="50">';

            $estado = $data[$i]['estado'];
            $color = $estado == 1 ? 'success' : 'danger';
            $texto = $estado == 1 ? 'ACTIVO' : 'INACTIVO';
            $data[$i]['estado'] = "<div class='badge rounded-pill text-$color bg-light-$color p-2 text-uppercase px-3'>$texto</div>";

            $vigencia_estado = $data[$i]['vigencia_estado'];

            if ($vigencia_estado == 'Próximamente') {
                $vigencia = '<span class="badge bg-info">Próximamente</span>';
            } elseif ($vigencia_estado == 'Vigente') {
                $vigencia = '<span class="badge bg-success">Vigente</span>';
            } else {
                $vigencia = '<span class="badge bg-secondary">Vencida</span>';
            }

            $data[$i]['vigencia'] = $vigencia;
            $data[$i]['fecha_inicio'] = date('d/m/Y', strtotime($data[$i]['fecha_inicio']));
            $data[$i]['fecha_fin'] = date('d/m/Y', strtotime($data[$i]['fecha_fin']));

            if ($estado == 1) {
                $botonAccion = '<button class="btn btn-danger" type="button" onclick="eliminar(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></button>';
            } else {
                $botonAccion = '<button class="btn btn-success" type="button" onclick="restaurar(' . $data[$i]['id'] . ')"><i class="fas fa-undo"></i></button>';
            }

            $data[$i]['accion'] = '<div class="d-flex">
        <button class="btn btn-primary" type="button" onclick="edit(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
        ' . $botonAccion . '
         </div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        die();
    }

    public function registrar()
    {
        if (isset($_POST['titulo'])) {
            $titulo = strClean($_POST['titulo']);
            $descripcion = strClean($_POST['descripcion']);
            $link = strClean($_POST['link']);
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];
            $imagen = $_FILES['imagen'];
            $tmp_name = $imagen['tmp_name'];
            $id = strClean($_POST['id']);
            $ruta = 'assets/images/promociones/';
            $nombreImg = date('YmdHis');

            if (empty($titulo) || empty($fecha_inicio) || empty($fecha_fin)) {
                $respuesta = array('msg' => 'Título y fechas son requeridos', 'icono' => 'warning');
            } else {
                // Validar que fecha_fin sea mayor a fecha_inicio
                if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
                    $respuesta = array('msg' => 'La fecha fin debe ser mayor a la fecha inicio', 'icono' => 'warning');
                } else {
                    if (!empty($imagen['name'])) {
                        $destino = $ruta . $nombreImg . '.jpg';
                    } else if (!empty($_POST['imagen_actual']) && empty($imagen['name'])) {
                        $destino = $_POST['imagen_actual'];
                    } else {
                        $destino = $ruta . 'default.png';
                    }

                    if (empty($id)) {
                        $result = $this->model->verificarPromocion($titulo);
                        if (empty($result)) {
                            $data = $this->model->registrar($titulo, $descripcion, $destino, $link, $fecha_inicio, $fecha_fin);
                            if ($data > 0) {
                                if (!empty($imagen['name'])) {
                                    move_uploaded_file($tmp_name, $destino);
                                }
                                $respuesta = array('msg' => 'Promoción registrada', 'icono' => 'success');
                            } else {
                                $respuesta = array('msg' => 'Error al registrar', 'icono' => 'error');
                            }
                        } else {
                            $respuesta = array('msg' => 'La promoción ya existe', 'icono' => 'warning');
                        }
                    } else {
                        $data = $this->model->modificar($titulo, $descripcion, $destino, $link, $fecha_inicio, $fecha_fin, $id);
                        if ($data == 1) {
                            if (!empty($imagen['name'])) {
                                move_uploaded_file($tmp_name, $destino);
                            }
                            $respuesta = array('msg' => 'Promoción modificada', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'Error al modificar', 'icono' => 'error');
                        }
                    }
                }
            }
            echo json_encode($respuesta);
        }
        die();
    }

    public function delete($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->eliminar(0, $id);
            if ($data == 1) {
                $respuesta = array('msg' => 'PROMOCIÓN INACTIVA', 'icono' => 'success');
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
                $respuesta = array('msg' => 'PROMOCIÓN RESTAURADA', 'icono' => 'success');
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
            $data = $this->model->getPromocion($id);
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        }
        die();
    }
}
?>
