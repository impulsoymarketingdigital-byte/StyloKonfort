<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>REPORTE DE VENTAS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        body {
            padding: 20px;
            font-size: 11px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        #datos-empresa td {
            vertical-align: top;
            padding: 5px;
        }
        .info-empresa {
            text-align: left;
        }
        .info-reporte {
            text-align: right;
        }
        .container-reporte {
            border: 2px solid #4472C4;
            padding: 8px;
            border-radius: 5px;
            background: #f0f5ff;
        }
        .titulo-reporte {
            font-weight: bold;
            color: #4472C4;
            font-size: 16px;
            display: block;
            text-align: center;
            margin-bottom: 5px;
        }
        #container-producto thead th {
            background: #4472C4;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 10px;
        }
        #container-producto tbody td {
            padding: 6px 5px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        #container-producto tfoot td {
            padding: 8px 5px;
            font-weight: bold;
            background: #f0f5ff;
            border-top: 2px solid #4472C4;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .mensaje {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #555;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <table id="datos-empresa">
        <tr>
            <td class="info-empresa">
                <strong style="font-size: 14px;"><?php echo $data['empresa']['nombre'] ?? 'MI EMPRESA'; ?></strong><br>
                <?php if (isset($data['empresa']['telefono'])): ?>
                    Teléfono: <?php echo $data['empresa']['telefono']; ?><br>
                <?php endif; ?>
                <?php if (isset($data['empresa']['direccion'])): ?>
                    Dirección: <?php echo $data['empresa']['direccion']; ?>
                <?php endif; ?>
            </td>
            <td class="info-reporte">
                <div class="container-reporte">
                    <span class="titulo-reporte">REPORTE DE VENTAS</span>
                    <?php if (isset($_GET['desde']) && isset($_GET['hasta'])): ?>
                        <p>Período: <?php echo date('d/m/Y', strtotime($_GET['desde'])); ?> al <?php echo date('d/m/Y', strtotime($_GET['hasta'])); ?></p>
                    <?php endif; ?>
                    <p>Fecha: <?php echo date('d/m/Y'); ?></p>
                    <p>Hora: <?php echo date('H:i:s'); ?></p>
                </div>
            </td>
        </tr>
    </table>

    <br>

    <!-- Tabla de Ventas -->
    <table id="container-producto">
        <thead>
            <tr>
                <th>N°</th>
                <th>N° VENTA</th>
                <th>MÉTODO</th>
                <th>CLIENTE</th>
                <th>PRODUCTO</th>
                <th>CANT.</th>
                <th>PRECIO</th>
                <th>SUBTOTAL</th>
                <th>FECHA</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalProductos = 0;
            $totalCantidad = 0;
            $totalVentas = 0;
            
            if (!empty($data['ventas'])):
                foreach ($data['ventas'] as $index => $venta):
                    $totalProductos++;
                    $totalCantidad += $venta['cantidad'];
                    $totalVentas += $venta['subtotal'];
            ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $venta['numero_venta']; ?></td>
                    <td><?php echo $venta['metodo']; ?></td>
                    <td><?php echo $venta['cliente']; ?></td>
                    <td><?php echo $venta['producto']; ?></td>
                    <td class="text-center"><?php echo $venta['cantidad']; ?></td>
                    <td class="text-right">Bs. <?php echo number_format($venta['precio_venta'], 2); ?></td>
                    <td class="text-right">Bs. <?php echo number_format($venta['subtotal'], 2); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?></td>
                </tr>
            <?php 
                endforeach;
            else:
            ?>
                <tr>
                    <td colspan="9" class="text-center">No hay ventas en el período seleccionado</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="text-right">TOTAL PRODUCTOS:</td>
                <td><?php echo $totalProductos; ?> items</td>
            </tr>
            <tr>
                <td colspan="8" class="text-right">TOTAL CANTIDAD:</td>
                <td><?php echo $totalCantidad; ?></td>
            </tr>
            <tr>
                <td colspan="8" class="text-right">TOTAL VENTAS:</td>
                <td><strong>Bs. <?php echo number_format($totalVentas, 2); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- Mensaje final -->
  <div class="mensaje">
        <p><strong>Generado por Mastec Digital</strong></p>
        <p> <?php echo date('Y'); ?></p>
    </div>
</body>
</html>