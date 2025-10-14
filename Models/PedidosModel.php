<?php
class PedidosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getPedidos($proceso)
    {
        $sql = "SELECT * FROM pedidos WHERE proceso = $proceso AND metodo = 'LLEVAR'";
        return $this->selectAll($sql);
    }
    
    public function actualizarEstado($proceso, $idPedido)
    {
        $sql = "UPDATE pedidos SET proceso=? WHERE id = ?";
        $array = array($proceso, $idPedido);
        return $this->save($sql, $array);
    }
    
    public function actualizarEstadoCompleto($idPedido)
    {
        $sql = "UPDATE pedidos SET estado='COMPLETADO' WHERE id = ?";
        $array = array($idPedido);
        return $this->save($sql, $array);
    }
    
    public function getPedido($idPedido)
    {
        $sql = "SELECT * FROM pedidos WHERE id = $idPedido";
        return $this->select($sql);
    }
    
    public function getDetallePedido($idPedido)
    {
        $sql = "SELECT * FROM detalle_pedidos WHERE id_pedido = $idPedido";
        return $this->selectAll($sql);
    }
    
    public function getTallaColor($id_talla_color)
    {
        $sql = "SELECT t.nombre as talla, c.nombre as color 
                FROM tallas_colores tc 
                INNER JOIN tallas t ON tc.id_talla = t.id 
                INNER JOIN colores c ON tc.id_color = c.id 
                WHERE tc.id = $id_talla_color";
        return $this->select($sql);
    }
    
    public function getConfiguracion()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }
}
?>