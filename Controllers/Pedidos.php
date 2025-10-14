<?php
class Pedidos extends Controller
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
        $data['title'] = 'pedidos';
        $this->views->getView('admin/pedidos', "index", $data);
    }
    
    public function listarPedidos()
    {
        $data = $this->model->getPedidos(1);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex">
            <button class="btn btn-success" type="button" onclick="verPedido(' . $data[$i]['id'] . ')"><i class="fas fa-eye"></i></button>
            <button class="btn btn-info" type="button" onclick="cambiarProceso(' . $data[$i]['id'] . ', 2)"><i class="fas fa-check-circle"></i></button>
        </div>';
        }
        echo json_encode($data);
        die();
    }
    
    public function listarProceso()
    {
        $data = $this->model->getPedidos(2);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex">
            <button class="btn btn-success" type="button" onclick="verPedido(' . $data[$i]['id'] . ')"><i class="fas fa-eye"></i></button>
            <button class="btn btn-info" type="button" onclick="cambiarProceso(' . $data[$i]['id'] . ', 3)"><i class="fas fa-check-circle"></i></button>
        </div>';
        }
        echo json_encode($data);
        die();
    }
    
    public function listarFinalizados()
    {
        $data = $this->model->getPedidos(3);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex">
            <button class="btn btn-success" type="button" onclick="verPedido(' . $data[$i]['id'] . ')"><i class="fas fa-eye"></i></button>
        </div>';
        }
        echo json_encode($data);
        die();
    }
    
    public function update($datos)
    {
        $array = explode(',', $datos);
        $idPedido = $array[0];
        $proceso = $array[1];
        
        if (is_numeric($idPedido)) {
            // Si el proceso es 3, también actualizar el estado a COMPLETADO
            if ($proceso == 3) {
                $dataEstado = $this->model->actualizarEstadoCompleto($idPedido);
            }
            
            $data = $this->model->actualizarEstado($proceso, $idPedido);
            if ($data == 1) {
                $respuesta = array('msg' => 'pedido actualizado', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'error al actualizar', 'icono' => 'error');
            }
            echo json_encode($respuesta);
        }
        die();
    }
    
    public function verPedido($idPedido)
    {
        $pedido = $this->model->getPedido($idPedido);
        $productos = $this->model->getDetallePedido($idPedido);
        
        $configuracion = $this->model->getConfiguracion();
        $moneda = $configuracion['moneda'] ?? 'Bs. ';
        
        for ($i = 0; $i < count($productos); $i++) {
            $id_talla_color = $productos[$i]['id_talla_color'];
            
            $atributos = $this->model->getTallaColor($id_talla_color);
            
            if ($atributos) {
                $talla = $atributos['talla'] ?? '';
                $color = $atributos['color'] ?? '';
                $productos[$i]['atributos'] = $talla . ' - ' . $color;
            } else {
                $productos[$i]['atributos'] = ' - ';
            }
        }
        
        $data = array(
            'pedido' => $pedido,
            'productos' => $productos,
            'moneda' => $moneda
        );
        
        echo json_encode($data);
        die();
    }
}
?>