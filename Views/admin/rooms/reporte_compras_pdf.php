<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>REPORTE DE COMPRAS</title>
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
            border: 2px solid #70AD47;
            padding: 8px;
            border-radius: 5px;
            background: #f0f9f0;
        }
        .titulo-reporte {
            font-weight: bold;
            color: #70AD47;
            font-size: 16px;
            display: block;
            text-align: center;
            margin-bottom: 5px;
        }
        #container-producto thead th {
            background: #70AD47;
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
            background: #f0f9f0;
            border-top: 2px solid #70AD47;
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
                    <span class="titulo-reporte">REPORTE DE COMPRAS</span>
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

    <!-- Tabla de Compras -->
    <table id="container-producto">
        <thead>
            <tr>
                <th>N°</th>
                <th>N° COMPRA</th>
                <th>TIPO</th>
                <th>PROVEEDOR</th>
                <th>ALMACÉN</th>
                <th>PRODUCTO</th>
                <th>CANT.</th>
                <th>PRECIO</th>
                <th>DESC.</th>
                <th>SUBTOTAL</th>
                <th>FECHA</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalProductos = 0;
            $totalCantidad = 0;
            $totalCompras = 0;
            
            if (!empty($data['compras'])):
                foreach ($data['compras'] as $index => $compra):
                    $totalProductos++;
                    $totalCantidad += $compra['cantidad'];
                    $totalCompras += $compra['subtotal'];
            ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $compra['numero_compra']; ?></td>
                    <td><?php echo $compra['tipo_comprobante']; ?></td>
                    <td><?php echo $compra['proveedor']; ?></td>
                    <td><?php echo $compra['almacen']; ?></td>
                    <td><?php echo $compra['producto']; ?></td>
                    <td class="text-center"><?php echo $compra['cantidad']; ?></td>
                    <td class="text-right">COP. <?php echo number_format($compra['precio_compra'], 2); ?></td>
                    <td class="text-right">COP. <?php echo number_format($compra['descuento'], 2); ?></td>
                    <td class="text-right">COP. <?php echo number_format($compra['subtotal'], 2); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($compra['fecha'])); ?></td>
                </tr>
            <?php 
                endforeach;
            else:
            ?>
                <tr>
                    <td colspan="11" class="text-center">No hay compras en el período seleccionado</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10" class="text-right">TOTAL PRODUCTOS:</td>
                <td><?php echo $totalProductos; ?> items</td>
            </tr>
            <tr>
                <td colspan="10" class="text-right">TOTAL CANTIDAD:</td>
                <td><?php echo $totalCantidad; ?></td>
            </tr>
            <tr>
                <td colspan="10" class="text-right">TOTAL COMPRAS:</td>
                <td><strong>COP. <?php echo number_format($totalCompras, 2); ?></strong></td>
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