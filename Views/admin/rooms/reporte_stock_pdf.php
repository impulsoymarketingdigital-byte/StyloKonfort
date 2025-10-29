<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>REPORTE DE STOCK</title>
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
            background: #e7f0ff;
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
            background: #e7f0ff;
            border-top: 2px solid #4472C4;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .stock-bajo {
            background-color: #ffebee;
            color: #c62828;
            font-weight: bold;
        }
        .stock-medio {
            background-color: #fff3e0;
            color: #e65100;
        }
        .stock-alto {
            color: #2e7d32;
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
                    <span class="titulo-reporte">REPORTE DE STOCK</span>
                    <p>Almacén: <?php echo strtoupper($data['almacen_nombre']); ?></p>
                    <p>Fecha: <?php echo date('d/m/Y'); ?></p>
                    <p>Hora: <?php echo date('H:i:s'); ?></p>
                </div>
            </td>
        </tr>
    </table>

    <br>

    <!-- Tabla de Stock -->
    <table id="container-producto">
        <thead>
            <tr>
                <th>N°</th>
                <th>CÓDIGO</th>
                <th>PRODUCTO</th>
                <th>CATEGORÍA</th>
                <th>MARCA</th>
                <th>TALLA</th>
                <th>COLOR</th>
                <th>ALMACÉN</th>
                <th>STOCK</th>
                <th>P. COMPRA</th>
                <th>P. VENTA</th>
                <th>VALOR</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalProductos = 0;
            $totalStock = 0;
            $valorTotal = 0;
            
            if (!empty($data['stock'])):
                foreach ($data['stock'] as $index => $item):
                    $totalProductos++;
                    $totalStock += $item['stock'];
                    $valorTotal += $item['valor_stock'];
                    
                    $stockClass = '';
                    if ($item['stock'] <= 5) {
                        $stockClass = 'stock-bajo';
                    } elseif ($item['stock'] <= 10) {
                        $stockClass = 'stock-medio';
                    } else {
                        $stockClass = 'stock-alto';
                    }
            ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $item['codigo']; ?></td>
                    <td><?php echo $item['producto']; ?></td>
                    <td><?php echo $item['categoria']; ?></td>
                    <td><?php echo $item['marca']; ?></td>
                    <td class="text-center"><?php echo $item['talla']; ?></td>
                    <td><?php echo $item['color']; ?></td>
                    <td><?php echo $item['almacen']; ?></td>
                    <td class="text-center <?php echo $stockClass; ?>"><?php echo $item['stock']; ?></td>
                    <td class="text-right">COP. <?php echo number_format($item['precio_compra'], 2); ?></td>
                    <td class="text-right">COP. <?php echo number_format($item['precio_venta'], 2); ?></td>
                    <td class="text-right">COP. <?php echo number_format($item['valor_stock'], 2); ?></td>
                </tr>
            <?php 
                endforeach;
            else:
            ?>
                <tr>
                    <td colspan="12" class="text-center">No hay productos en stock</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="11" class="text-right">TOTAL PRODUCTOS:</td>
                <td><?php echo $totalProductos; ?> items</td>
            </tr>
            <tr>
                <td colspan="11" class="text-right">TOTAL UNIDADES:</td>
                <td><?php echo $totalStock; ?></td>
            </tr>
            <tr>
                <td colspan="11" class="text-right">VALOR TOTAL STOCK:</td>
                <td><strong>COP. <?php echo number_format($valorTotal, 2); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- Mensaje final -->
    <div class="mensaje">
        <p><strong>Generado por Mastec Digital</strong></p>
        <p><?php echo date('Y'); ?></p>
    </div>
</body>
</html>