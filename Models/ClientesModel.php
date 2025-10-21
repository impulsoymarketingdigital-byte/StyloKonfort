<?php
class ClientesModel extends Query
{

    public function __construct()
    {
        parent::__construct();
    }
    public function registroDirecto($nombre, $apellido, $correo, $clave, $token)
    {
        $sql = "INSERT INTO clientes (nombre, apellido, correo, clave, token) VALUES (?,?,?,?,?)";
        $datos = array($nombre, $apellido, $correo, $clave, $token);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }
    public function getToken($token)
    {
        $sql = "SELECT * FROM clientes WHERE token = '$token'";
        return $this->select($sql);
    }
    public function actualizarVerify($id)
    {
        $sql = "UPDATE clientes SET token=?, verify=? WHERE id=?";
        $datos = array(null, 1, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }
    public function getVerificar($table, $correo)
    {
        $sql = "SELECT * FROM $table WHERE correo = '$correo'";
        return $this->select($sql);
    }

    public function registrarPedido(
        $id_transaccion,
        $metodo,
        $monto,
        $estado,
        $fecha,
        $email,
        $nombre,
        $apellido,
        $direccion,
        $ciudad,
        $id_cliente
    ) {
        $sql = "INSERT INTO pedidos (id_transaccion, metodo, monto, estado, fecha, email,
        nombre, apellido, direccion, ciudad, id_cliente) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $datos = array(
            $id_transaccion,
            $metodo,
            $monto,
            $estado,
            $fecha,
            $email,
            $nombre,
            $apellido,
            $direccion,
            $ciudad,
            $id_cliente
        );
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }
    public function getProducto($id_producto)
    {
        $sql = "SELECT * FROM productos WHERE id = $id_producto";
        return $this->select($sql);
    }
    public function registrarDetalle($producto, $precio, $cantidad, $id_pedido, $id_producto, $id_talla_color)
    {
        $sql = "INSERT INTO detalle_pedidos (producto, precio, cantidad, id_pedido, id_producto, id_talla_color) VALUES (?,?,?,?,?,?)";
        $datos = array($producto, $precio, $cantidad, $id_pedido, $id_producto, $id_talla_color);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }
    public function getIdTallaColor($size, $color, $id_producto)
    {
        $sql = "SELECT id FROM tallas_colores WHERE id_talla = $size AND id_color = $color AND id_producto = $id_producto";
        return $this->select($sql);
    }

    public function getPedidos($id_cliente)
    {
        $sql = "SELECT * FROM pedidos WHERE id_cliente = $id_cliente";
        return $this->selectAll($sql);
    }
    public function getPedido($idPedido)
    {
        $sql = "SELECT * FROM pedidos WHERE id = $idPedido";
        return $this->select($sql);
    }
    public function verPedidos($idPedido)
    {
        $sql = "SELECT d.* FROM pedidos p INNER JOIN detalle_pedidos d ON p.id = d.id_pedido WHERE p.id = $idPedido";
        return $this->selectAll($sql);
    }

    public function getProductos($id_cliente)
    {
        $sql = "SELECT d.producto, d.precio, SUM(d.cantidad) AS cantidad, d.id_producto
        FROM pedidos p
        INNER JOIN detalle_pedidos d ON p.id = d.id_pedido
        WHERE p.id_cliente = $id_cliente
        GROUP BY d.producto, d.precio, d.id_producto";
        return $this->selectAll($sql);
    }

    public function comprobarCalificacion($id_producto, $id_cliente)
    {
        $sql = "SELECT * FROM calificaciones WHERE id_producto = $id_producto AND id_cliente = $id_cliente";
        return $this->select($sql);
    }

    public function agregarCalificacion($cantidad, $id_producto, $id_cliente)
    {
        $sql = "INSERT INTO calificaciones (cantidad, id_producto, id_cliente) VALUES (?,?,?)";
        $datos = array($cantidad, $id_producto, $id_cliente);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }

    public function cambiarCalificacion($cantidad, $id_producto, $id_cliente)
    {
        $sql = "UPDATE calificaciones SET cantidad=? WHERE id_producto=? AND id_cliente=?";
        $datos = array($cantidad, $id_producto, $id_cliente);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = 1;
        } else {
            $res = 0;
        }
        return $res;
    }

    public function getAtributos($size, $color, $id_producto)
    {
        $sql = "SELECT d.stock, t.nombre AS size, c.nombre, c.color, p.precio_venta as precio
            FROM tallas_colores d 
            INNER JOIN tallas t ON d.id_talla = t.id 
            INNER JOIN colores c ON d.id_color = c.id 
            INNER JOIN productos p ON d.id_producto = p.id
            WHERE d.id_talla = $size AND d.id_color = $color AND d.id_producto = $id_producto";
        return $this->select($sql);
    }
    public function actualizarStockDetalle($stock, $id_talla_color)
    {
        $sql = "UPDATE tallas_colores SET stock=? WHERE id=?";
        $datos = array($stock, $id_talla_color);
        return $this->save($sql, $datos);
    }
    public function agregarMensaje($mensaje, $id_cliente)
    {
        $sql = "INSERT INTO testimonial (mensaje, id_cliente) VALUES (?,?)";
        $datos = array($mensaje, $id_cliente);
        $data = $this->insertar($sql, $datos);
        if ($data > 0) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }
    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }

    public function modificarMensaje($mensaje, $id_cliente)
    {
        $sql = "UPDATE testimonial SET mensaje=? WHERE id_cliente=?";
        $datos = array($mensaje, $id_cliente);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = $data;
        } else {
            $res = 0;
        }
        return $res;
    }


    public function getAtributosPorId($id_talla_color)
    {
        $sql = "SELECT t.nombre AS size, c.nombre, c.color
            FROM tallas_colores d 
            INNER JOIN tallas t ON d.id_talla = t.id 
            INNER JOIN colores c ON d.id_color = c.id 
            WHERE d.id = $id_talla_color";
        return $this->select($sql);
    }

    public function getTestimonio($id_cliente)
    {
        $sql = "SELECT * FROM testimonial WHERE id_cliente = $id_cliente";
        return $this->select($sql);
    }

    ##### ADMIN CLIENTES ######
    public function getClientes($estado)
    {
        $sql = "SELECT * FROM clientes WHERE estado = $estado";
        return $this->selectAll($sql);
    }
    public function registrar($nombre, $apellido, $telefono, $correo, $direccion, $accion)
    {
        $sql = "INSERT INTO clientes (nombre, apellido, telefono, correo, direccion, accion) VALUES (?,?,?,?,?,?)";
        $array = array($nombre, $apellido, $telefono, $correo, $direccion, $accion);
        return $this->insertar($sql, $array);
    }

    public function getValidar($campo, $valor, $accion, $id)
    {
        if ($accion == 'registrar' && $id == 0) {
            $sql = "SELECT id FROM clientes WHERE $campo = '$valor'";
        } else {
            $sql = "SELECT id FROM clientes WHERE $campo = '$valor' AND id != $id";
        }
        return $this->select($sql);
    }

    public function eliminar($estado, $idCliente)
    {
        $sql = "UPDATE clientes SET estado = ? WHERE id = ?";
        $array = array($estado, $idCliente);
        return $this->save($sql, $array);
    }
    public function editar($idCliente)
    {
        $sql = "SELECT * FROM clientes WHERE id = $idCliente";
        return $this->select($sql);
    }

    public function actualizar(
        $nombre,
        $apellido,
        $telefono,
        $correo,
        $direccion,
        $perfil,
        $id
    ) {
        $sql = "UPDATE clientes SET nombre=?, apellido=?, telefono=?, correo=?, direccion=?, perfil=? WHERE id=?";
        $array = array($nombre, $apellido, $telefono, $correo, $direccion, $perfil, $id);
        return $this->save($sql, $array);
    }

}

?>