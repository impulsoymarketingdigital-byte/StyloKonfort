<?php
class Sucursales extends Controller
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
        $data['title'] = 'sucursales';
        $this->views->getView('admin/sucursales', "index", $data);
    }
    public function listar()
    {
        $data = $this->model->getSucursales();
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
        if (isset($_POST['nombre']) && isset($_POST['codigo'])) {
            $id = strClean($_POST['id']);
            $nombre = strClean($_POST['nombre']);
            $codigo = strClean($_POST['codigo']);
            $direccion = strClean($_POST['direccion']);
            $telefono = strClean($_POST['telefono']);
            $es_bodega = strClean($_POST['es_bodega']);
            if (empty($nombre)) {
                $respuesta = array('msg' => 'EL NOMBRE ES REQUERIDO', 'icono' => 'warning');
            } else if (empty($codigo)) {
                $respuesta = array('msg' => 'EL CODIGO ES REQUERIDO', 'icono' => 'warning');
            } else if (empty($telefono)) {
                $respuesta = array('msg' => 'EL TELEFONO ES REQUERIDO', 'icono' => 'warning');
            } else if (empty($direccion)) {
                $respuesta = array('msg' => 'LA DIRECCION ES REQUERIDO', 'icono' => 'warning');
            } else {
                if ($id == '') {
                    $verificarCodigo = $this->model->getValidar('codigo', $codigo, 'registrar', 0);
                    if (empty($verificarCodigo)) {
                        $data = $this->model->registrar(
                            $nombre,
                            $codigo,
                            $direccion,
                            $telefono,
                            $es_bodega,
                        );
                        if ($data > 0) {
                            $respuesta = array('msg' => 'SUCURSAL REGISTRADA', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'ERROR AL REGISTRAR', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'LA SUCURSAL YA EXISTE', 'icono' => 'warning');
                    }
                } else {
                    $verificarCodigo = $this->model->getValidar('codigo', $codigo, 'actualizar', $id);
                    if (empty($verificarCodigo)) {
                        $data = $this->model->actualizar(
                            $nombre,
                            $codigo,
                            $direccion,
                            $telefono,
                            $es_bodega,
                            $id
                        );
                        if ($data > 0) {
                            $respuesta = array('msg' => 'SUCURSAL MODIFICADA', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'ERROR AL MODIFICAR', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'LA SUCURSAL YA EXISTE', 'icono' => 'warning');
                    }
                }
            }
        } else {
            $respuesta = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
    //eliminar cat
    public function delete($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->eliminar(0, $id);
            if ($data == 1) {
                $respuesta = array('msg' => 'SUCURSAL INACTIVA', 'icono' => 'success');
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
                $respuesta = array('msg' => 'SUCURSAL RESTAURADA', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'ERROR AL ELIMINAR', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'ERROR DESCONOCIDO', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
    //editar cat
    public function edit($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->getSucursal($id);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
