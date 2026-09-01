<?php
class CajasModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCajas($id_usuario)
    {
        $sql = "SELECT 
            m.id,
            m.tipo,
            m.tipo_movimiento,
            m.descripcion,
            m.monto,
            m.created_at,
            CONCAT(u.nombres, ' ', u.apellidos) AS usuario,
            CASE 
                WHEN m.tipo = 'INGRESO' THEN m.monto 
                ELSE 0 
            END AS ingreso,
            CASE 
                WHEN m.tipo = 'EGRESO' THEN m.monto 
                ELSE 0 
            END AS egreso
        FROM movimientos m
        INNER JOIN usuarios u ON m.id_usuario = u.id
        INNER JOIN cajas c ON m.id_caja = c.id
        WHERE m.id_usuario = $id_usuario 
        AND c.estado = 1
        AND (
            (m.transaction_type IN ('cajas', 'income', 'expense'))
            OR
            (m.transaction_type = 'sales' AND EXISTS (
                SELECT 1 FROM pedidos p 
                WHERE p.id = m.transaction_id 
                AND p.estado = 'COMPLETADO'
            ))
            OR
            (m.transaction_type = 'compras' AND EXISTS (
                SELECT 1 FROM compras co 
                WHERE co.id = m.transaction_id 
                AND co.estado = 'COMPLETADO'
            ))
        )
        ORDER BY m.created_at DESC";

        return $this->selectAll($sql);
    }

    public function listarCajas($id_usuario)
    {
        $sql = "SELECT 
            c.id,
            c.fecha_apertura,
            c.fecha_cierre,
            c.monto_inicial,
            c.monto_final,
            c.monto_fisico,
            c.estado,
            CONCAT(u.nombres, ' ', u.apellidos) AS usuario
        FROM cajas c
        INNER JOIN usuarios u ON c.id_usuario = u.id
        WHERE c.id_usuario = $id_usuario
        ORDER BY c.created_at DESC";

        return $this->selectAll($sql);
    }

    public function getCajaAbierta($id_usuario)
    {
        $sql = "SELECT * FROM cajas WHERE id_usuario = $id_usuario AND estado = 1";
        return $this->select($sql);
    }

    public function abrirCaja($monto_inicial, $id_usuario, $id_almacen)
    {
        $fecha = date('Y-m-d H:i:s');
        $sql = "INSERT INTO cajas (fecha_apertura, monto_inicial, estado, id_usuario, id_almacen, created_at, updated_at) 
                VALUES (?, ?, 1, ?, ?, ?, ?)";
        $array = array($fecha, $monto_inicial, $id_usuario, $id_almacen, $fecha, $fecha);
        return $this->insertar($sql, $array);
    }

    public function insertarMovimientoAperturaCaja($id_caja, $monto, $id_usuario)
    {
        $tipo = 'INGRESO';
        $tipo_movimiento = 'APERTURA DE CAJA';
        $descripcion = 'Apertura de caja';
        $transaction_type = 'cajas';

        $sql = "INSERT INTO movimientos 
                (tipo, tipo_movimiento, descripcion, monto, id_caja, id_usuario, transaction_id, transaction_type) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $array = array($tipo, $tipo_movimiento, $descripcion, $monto, $id_caja, $id_usuario, $id_caja, $transaction_type);
        return $this->insertar($sql, $array);
    }

    public function guardarMovimiento($tipo, $descripcion, $monto, $id_usuario, $id_caja)
    {
        $tipo_movimiento = 'MOVIMIENTO DE CAJA';
        $transaction_type = ($tipo === 'INGRESO') ? 'income' : 'expense';

        $sql = "INSERT INTO movimientos 
                (tipo, tipo_movimiento, descripcion, monto, id_caja, id_usuario, transaction_id, transaction_type)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $array = array($tipo, $tipo_movimiento, $descripcion, $monto, $id_caja, $id_usuario, $id_caja, $transaction_type);
        return $this->insertar($sql, $array);
    }

    public function getSaldoActual($id_usuario, $id_caja)
    {
        $sqlInicial = "SELECT monto_inicial FROM cajas WHERE id = $id_caja AND id_usuario = $id_usuario";
        $inicial = $this->select($sqlInicial);
        $montoInicial = $inicial['monto_inicial'] ?? 0;

        $sqlIngresos = "SELECT COALESCE(SUM(m.monto), 0) AS total 
                        FROM movimientos m
                        WHERE m.id_caja = $id_caja 
                        AND m.id_usuario = $id_usuario 
                        AND m.tipo = 'INGRESO'
                        AND (
                            (m.transaction_type IN ('cajas', 'income'))
                            OR
                            (m.transaction_type = 'sales' AND EXISTS (
                                SELECT 1 FROM pedidos p 
                                WHERE p.id = m.transaction_id 
                                AND p.estado = 'COMPLETADO'
                            ))
                        )";
        $ingresos = $this->select($sqlIngresos);

        $sqlEgresos = "SELECT COALESCE(SUM(m.monto), 0) AS total 
                       FROM movimientos m
                       WHERE m.id_caja = $id_caja 
                       AND m.id_usuario = $id_usuario 
                       AND m.tipo = 'EGRESO'
                       AND (
                           (m.transaction_type IN ('cajas', 'expense'))
                           OR
                           (m.transaction_type = 'compras' AND EXISTS (
                               SELECT 1 FROM compras c 
                               WHERE c.id = m.transaction_id 
                               AND c.estado = 'COMPLETADO'
                           ))
                       )";
        $egresos = $this->select($sqlEgresos);

        $saldoActual = $montoInicial + ($ingresos['total'] ?? 0) - ($egresos['total'] ?? 0);

        return array('saldo' => $saldoActual);
    }

    public function getTotalVentas($id_usuario, $id_caja)
    {
        $sql = "SELECT COALESCE(SUM(monto), 0) AS total 
                FROM pedidos 
                WHERE id_caja = $id_caja 
                AND estado = 'COMPLETADO'";
        return $this->select($sql);
    }

    public function getTotalCompras($id_usuario, $id_caja)
    {
        $sql = "SELECT COALESCE(SUM(total), 0) AS total 
                FROM compras 
                WHERE id_caja = $id_caja  
                AND estado = 'COMPLETADO'";
        return $this->select($sql);
    }

    public function cerrarCaja($monto_final, $monto_fisico, $fecha_cierre, $id_usuario)
    {
        $sql = "UPDATE cajas 
                SET monto_final = ?, monto_fisico = ?, fecha_cierre = ?, estado = 0, updated_at = ?
                WHERE estado = 1 AND id_usuario = ?";
        $array = array($monto_final, $monto_fisico, $fecha_cierre, $fecha_cierre, $id_usuario);
        return $this->save($sql, $array);
    }

    public function registrarMovimiento($tipo, $tipo_movimiento, $descripcion, $monto, $id_caja, $id_usuario, $transaction_id, $transaction_type)
    {
        $sql = "INSERT INTO movimientos 
                (tipo, tipo_movimiento, descripcion, monto, id_caja, id_usuario, transaction_id, transaction_type) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $array = array($tipo, $tipo_movimiento, $descripcion, $monto, $id_caja, $id_usuario, $transaction_id, $transaction_type);
        return $this->insertar($sql, $array);
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }
}
?>