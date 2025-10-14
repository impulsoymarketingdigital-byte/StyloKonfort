<?php require_once 'Models/HomeModel.php'; $datos = new HomeModel(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $data['title']; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            width: 74mm;
            margin: 0;
            padding: 3mm;
            background: #fff;
            font-size: 9pt;
        }
        
        .ticket-header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px dashed #333;
            padding-bottom: 8px;
        }
        
        .logo-container {
            margin-bottom: 5px;
        }
        
        .logo-container img {
            max-width: 50mm;
            height: auto;
        }
        
        .empresa-nombre {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
        }
        
        .empresa-info {
            font-size: 8pt;
            line-height: 1.4;
            color: #333;
        }
        
        .section-title {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 8px 0 5px;
            padding: 3px 0;
            border-top: 1px dashed #666;
            border-bottom: 1px dashed #666;
            text-align: center;
            letter-spacing: 0.3px;
        }
        
        .cliente-info {
            font-size: 8pt;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        
        .cliente-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .cliente-info td {
            padding: 1px 0;
        }
        
        .cliente-info td:first-child {
            font-weight: bold;
            width: 60px;
        }
        
        .productos-table {
            width: 100%;
            font-size: 7pt;
            margin: 5px 0;
            border-collapse: collapse;
        }
        
        .productos-table thead {
            border-bottom: 1px solid #333;
        }
        
        .productos-table th {
            padding: 3px 1px;
            text-align: left;
            font-weight: bold;
            font-size: 7pt;
        }
        
        .productos-table th:last-child,
        .productos-table td:last-child {
            text-align: right;
        }
        
        .productos-table tbody tr {
            border-bottom: 1px dotted #ccc;
        }
        
        .productos-table td {
            padding: 4px 1px;
            vertical-align: top;
            font-size: 7pt;
        }
        
        .producto-descripcion {
            font-size: 7pt;
            line-height: 1.3;
        }
        
        .totales {
            margin-top: 8px;
            border-top: 2px solid #333;
            padding-top: 5px;
        }
        
        .total-row {
            width: 100%;
            margin-bottom: 3px;
        }
        
        .total-row table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .total-row td {
            font-size: 8pt;
            padding: 2px 0;
        }
        
        .total-row td:last-child {
            text-align: right;
        }
        
        .total-final {
            font-size: 11pt;
            font-weight: bold;
            padding: 5px 0;
            border-top: 2px double #333;
            border-bottom: 2px double #333;
            margin-top: 5px;
        }
        
        .total-final table {
            width: 100%;
        }
        
        .total-final td {
            font-size: 11pt;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 2px dashed #333;
        }
        
        .mensaje {
            font-size: 7pt;
            line-height: 1.5;
            font-style: italic;
            margin-bottom: 8px;
            text-align: center;
        }
        
        .gracias {
            font-size: 9pt;
            font-weight: bold;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .fecha-hora {
            font-size: 7pt;
            color: #666;
            margin-top: 5px;
        }
        
        .ticket-id {
            font-size: 8pt;
            margin-top: 3px;
            font-weight: bold;
        }
        
        small {
            font-size: 6pt;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="ticket-header">
        <?php if (!empty($data['empresa']['logo'])): ?>
        <div class="logo-container">
            <img src="<?php echo BASE_URL . 'assets/images/logo.png'; ?>" alt="Logo">
        </div>
        <?php endif; ?>
        
        <div class="empresa-nombre"><?php echo strtoupper($data['empresa']['nombre']); ?></div>
        <div class="empresa-info">
            <?php if (!empty($data['empresa']['telefono'])): ?>
            Tel: <?php echo $data['empresa']['telefono']; ?><br>
            <?php endif; ?>
            <?php if (!empty($data['empresa']['direccion'])): ?>
            <?php echo $data['empresa']['direccion']; ?><br>
            <?php endif; ?>
            <?php if (!empty($data['empresa']['nit'])): ?>
            NIT: <?php echo $data['empresa']['nit']; ?>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($data['venta']['id'])): ?>
        <div class="ticket-id">PEDIDO #<?php echo str_pad($data['venta']['id'], 6, '0', STR_PAD_LEFT); ?></div>
        <?php endif; ?>
        
        <div class="fecha-hora">
            <?php echo date('d/m/Y - H:i:s', strtotime($data['venta']['fecha'] ?? 'now')); ?>
        </div>
    </div>

    <!-- DATOS CLIENTE -->
    <div class="section-title">DATOS DEL CLIENTE</div>
    <div class="cliente-info">
        <table>
            <tr>
                <td>Nombre:</td>
                <td><?php echo $data['venta']['nombre'] . ' ' . $data['venta']['apellido']; ?></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><?php echo $data['venta']['email']; ?></td>
            </tr>
            <?php if (!empty($data['venta']['direccion'])): ?>
            <tr>
                <td>Dirección:</td>
                <td><?php echo $data['venta']['direccion']; ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td>Método:</td>
                <td><?php echo strtoupper($data['venta']['metodo']); ?></td>
            </tr>
            <tr>
                <td>Estado:</td>
                <td><?php echo strtoupper($data['venta']['estado']); ?></td>
            </tr>
        </table>
    </div>

    <!-- PRODUCTOS -->
    <div class="section-title">DETALLE DE PRODUCTOS</div>
    <table class="productos-table">
        <thead>
            <tr>
                <th style="width: 10%;">Cant</th>
                <th style="width: 55%;">Descripción</th>
                <th style="width: 17%;">Precio</th>
                <th style="width: 18%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $productos = $data['detalle'];
            $subtotal = 0;
            foreach ($productos as $producto) { 
                $total_producto = $producto['cantidad'] * $producto['precio'];
                $subtotal += $total_producto;
                
                // Decodificar atributos si existen
                $atributos = null;
                if (!empty($producto['atributos'])) {
                    $atributos = json_decode($producto['atributos'], true);
                }
            ?>
                <tr>
                    <td><?php echo $producto['cantidad']; ?></td>
                    <td>
                        <div class="producto-descripcion">
                            <?php echo $producto['producto']; ?>
                            <?php if ($atributos): ?>
                            <br>
                            <small>
                                Talla: <?php echo $atributos['size']; ?> - Color: <?php echo $atributos['color']; ?>
                            </small>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo number_format($producto['precio'], 2); ?></td>
                    <td><?php echo number_format($total_producto, 2); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- TOTALES -->
    <div class="totales">
        <div class="total-row">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td><?php echo number_format($subtotal, 2); ?> Bs</td>
                </tr>
            </table>
        </div>
        
        <div class="total-row total-final">
            <table>
                <tr>
                    <td>TOTAL A PAGAR:</td>
                    <td><?php echo number_format($data['venta']['monto'], 2); ?> Bs</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="mensaje">
            <?php echo !empty($data['empresa']['mensaje']) ? $data['empresa']['mensaje'] : 'GRACIAS POR TU COMPRA'; ?>
        </div>
        <div class="gracias">¡VUELVA PRONTO!</div>
        <div style="font-size: 6pt; margin-top: 5px; color: #999;">
            Generado por Mastec Digital
        </div>
    </div>
</body>
</html>