<?php
class PedidosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getPedidos($proceso)
    {
        $sql = "SELECT * FROM pedidos WHERE proceso = $proceso AND metodo = 'LLEVAR' ORDER BY fecha DESC";
        return $this->selectAll($sql);
    }
    
    public function getPedidosEnProceso()
    {
        // Obtener pedidos en proceso 2 (En Preparación)
        $sql = "SELECT * FROM pedidos WHERE proceso = 2 AND metodo = 'LLEVAR' ORDER BY fecha DESC";
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
    
    public function getStockDetalle($id_talla_color)
    {
        $sql = "SELECT stock FROM tallas_colores WHERE id = $id_talla_color";
        return $this->select($sql);
    }
    
    public function actualizarStockDetalle($stock, $id_talla_color)
    {
        $sql = "UPDATE tallas_colores SET stock = ? WHERE id = ?";
        $datos = array($stock, $id_talla_color);
        return $this->save($sql, $datos);
    }
    
    public function getConfiguracion()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }
    
    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }
    
    // Verificar si hay stock suficiente para todos los productos del pedido
    public function verificarStockPedido($idPedido)
    {
        $detalles = $this->getDetallePedido($idPedido);
        $productosInvalidos = array();
        
        foreach ($detalles as $detalle) {
            $id_talla_color = $detalle['id_talla_color'];
            $cantidadRequerida = $detalle['cantidad'];
            
            $atributo = $this->getStockDetalle($id_talla_color);
            
            if ($atributo) {
                $stockActual = $atributo['stock'];
                
                if ($stockActual < $cantidadRequerida) {
                    $productoInfo = $this->getProductoInfo($detalle['id_producto'], $id_talla_color);
                    $productosInvalidos[] = array(
                        'producto' => $productoInfo['nombre'],
                        'atributos' => $productoInfo['atributos'],
                        'stock_disponible' => $stockActual,
                        'cantidad_requerida' => $cantidadRequerida
                    );
                }
            }
        }
        
        return $productosInvalidos;
    }
    
    public function getProductoInfo($idProducto, $id_talla_color)
    {
        $sql = "SELECT p.nombre FROM productos p WHERE p.id = $idProducto";
        $producto = $this->select($sql);
        
        $atributos = $this->getTallaColor($id_talla_color);
        $atributosStr = ($atributos) ? $atributos['talla'] . ' - ' . $atributos['color'] : '';
        
        return array(
            'nombre' => $producto['nombre'],
            'atributos' => $atributosStr
        );
    }
}
?>