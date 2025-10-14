<?php
class Colores extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
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
        $data = $this->model->getColores();
        for ($i = 0; $i < count($data); $i++) {
            $estado = $data[$i]['estado'];
            $color_badge = $estado == 1 ? 'success' : 'danger';
            $texto = $estado == 1 ? 'ACTIVO' : 'INACTIVO';

            // Mostrar color combinado si existe
            if (!empty($data[$i]['color_secundario']) && $data[$i]['color_secundario'] !== null) {
                $data[$i]['color'] = '<span class="badge" style="background: linear-gradient(90deg, ' . $data[$i]['color'] . ' 50%, ' . $data[$i]['color_secundario'] . ' 50%); padding: 8px 15px;">' . $data[$i]['nombre'] . '</span>';
            } else {
                $data[$i]['color'] = '<span class="badge" style="background: ' . $data[$i]['color'] . '; padding: 8px 15px;">' . $data[$i]['nombre'] . '</span>';
            }

            // Estado del color
            $data[$i]['estado'] = "<div class='badge rounded-pill text-$color_badge bg-light-$color_badge p-2 text-uppercase px-3'>$texto</div>";

            // Botón de acción según estado
            if ($estado == 1) {
                $botonAccion = '<button class="btn btn-danger" type="button" onclick="eliminarColor(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></button>';
            } else {
                $botonAccion = '<button class="btn btn-success" type="button" onclick="restaurarColor(' . $data[$i]['id'] . ')"><i class="fas fa-undo"></i></button>';
            }

            $data[$i]['accion'] = '<div class="d-flex gap-1">
                <button class="btn btn-primary" type="button" onclick="editColor(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
                ' . $botonAccion . '
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
            $color_secundario = isset($_POST['color_secundario']) ? $_POST['color_secundario'] : null;
            $id = $_POST['id'];

            if (empty($_POST['nombre']) || empty($_POST['color'])) {
                $respuesta = array('msg' => 'todo los campos son requeridos', 'icono' => 'warning');
            } else {
                if (empty($id)) {
                    $result = $this->model->verificarColor($nombre);
                    if (empty($result)) {
                        $data = $this->model->registrar($nombre, $color, $color_secundario);
                        if ($data > 0) {
                            $respuesta = array('msg' => 'color registrado', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'error al registrar', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'color ya existe', 'icono' => 'warning');
                    }
                } else {
                    $data = $this->model->modificar($nombre, $color, $color_secundario, $id);
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
            $data = $this->model->eliminar(0, $idColor);
            if ($data == 1) {
                $respuesta = array('msg' => 'COLOR INACTIVO', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'ERROR AL ELIMINAR', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }

    // Restaurar (poner estado 1)
    public function restaurar($idColor)
    {
        if (is_numeric($idColor)) {
            $data = $this->model->eliminar(1, $idColor);
            if ($data == 1) {
                $respuesta = array('msg' => 'COLOR RESTAURADO', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'ERROR AL RESTAURAR', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
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