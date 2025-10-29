<?php
class CajasModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    // ✅ CORREGIDO: Listar movimientos SIN ANULADOS
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
            -- ✅ Movimientos manuales (siempre se muestran)
            (m.transaction_type IN ('cajas', 'income', 'expense'))
            OR
            -- ✅ Solo ventas COMPLETADAS (sin ANULADO ni PENDIENTE)
            (m.transaction_type = 'sales' AND EXISTS (
                SELECT 1 FROM pedidos p 
                WHERE p.id = m.transaction_id 
                AND p.estado = 'COMPLETADO'
            ))
            OR
            -- ✅ Solo compras COMPLETADAS (sin ANULADO)
            (m.transaction_type = 'compras' AND EXISTS (
                SELECT 1 FROM compras co 
                WHERE co.id = m.transaction_id 
                AND co.estado = 'COMPLETADO'
            ))
        )
        ORDER BY m.created_at DESC";

        return $this->selectAll($sql);
    }

    // Listar historial de cajas cerradas
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

    // Obtener caja abierta del usuario
    public function getCajaAbierta($id_usuario)
    {
        $sql = "SELECT * FROM cajas WHERE id_usuario = $id_usuario AND estado = 1";
        return $this->select($sql);
    }

    // Abrir caja
    public function abrirCaja($monto_inicial, $id_usuario)
    {
        $fecha = date('Y-m-d H:i:s');
        $sql = "INSERT INTO cajas (fecha_apertura, monto_inicial, estado, id_usuario, created_at, updated_at) 
                VALUES (?, ?, 1, ?, ?, ?)";
        $array = array($fecha, $monto_inicial, $id_usuario, $fecha, $fecha);
        return $this->insertar($sql, $array);
    }

    // Registrar movimiento de apertura
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

    // Guardar movimiento manual
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

    // ✅ CORREGIDO: Obtener saldo actual SIN ANULADOS
    public function getSaldoActual($id_usuario, $id_caja)
    {
        // Obtener monto inicial
        $sqlInicial = "SELECT monto_inicial FROM cajas WHERE id = $id_caja AND id_usuario = $id_usuario";
        $inicial = $this->select($sqlInicial);
        $montoInicial = $inicial['monto_inicial'] ?? 0;

        // ✅ Total ingresos (SOLO movimientos válidos)
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

        // ✅ Total egresos (SOLO movimientos válidos)
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

    // ✅ CORREGIDO: Total de ventas SIN ANULADOS
    public function getTotalVentas($id_usuario, $id_caja)
    {
        $sql = "SELECT COALESCE(SUM(monto), 0) AS total 
                FROM pedidos 
                WHERE id_caja = $id_caja 
                AND estado = 'COMPLETADO'";
        return $this->select($sql);
    }

    // ✅ CORREGIDO: Total de compras SIN ANULADOS
    public function getTotalCompras($id_usuario, $id_caja)
    {
        $sql = "SELECT COALESCE(SUM(total), 0) AS total 
                FROM compras 
                WHERE id_caja = $id_caja  
                AND estado = 'COMPLETADO'";
        return $this->select($sql);
    }

    // Cerrar caja
    public function cerrarCaja($monto_final, $monto_fisico, $fecha_cierre, $id_usuario)
    {
        $sql = "UPDATE cajas 
                SET monto_final = ?, monto_fisico = ?, fecha_cierre = ?, estado = 0, updated_at = ?
                WHERE estado = 1 AND id_usuario = ?";
        $array = array($monto_final, $monto_fisico, $fecha_cierre, $fecha_cierre, $id_usuario);
        return $this->save($sql, $array);
    }

    // Registrar movimiento (para ventas y compras)
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