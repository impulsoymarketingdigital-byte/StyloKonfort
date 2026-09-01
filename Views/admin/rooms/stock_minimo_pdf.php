<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PRODUCTOS CON STOCK MÍNIMO</title>
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
            margin-bottom: 15px;
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
            border: 2px solid #FF6B6B;
            padding: 10px;
            border-radius: 5px;
            background: #ffe6e6;
        }
        .titulo-reporte {
            font-weight: bold;
            color: #FF6B6B;
            font-size: 16px;
            display: block;
            text-align: center;
            margin-bottom: 5px;
        }
        #tabla-productos {
            margin-top: 20px;
        }
        #tabla-productos thead th {
            background: #FF6B6B;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        #tabla-productos tbody td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .text-center {
            text-align: center;
        }
        .stock-bajo {
            color: #FF0000;
            font-weight: bold;
        }
        .mensaje {
            text-align: center;
            margin-top: 30px;
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
                    <span class="titulo-reporte">PRODUCTOS CON STOCK MÍNIMO</span>
                    <p>Fecha: <?php echo date('d/m/Y'); ?></p>
                    <p>Hora: <?php echo date('H:i:s'); ?></p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabla de productos -->
    <table id="tabla-productos">
        <thead>
            <tr>
                <th>N°</th>
                <th>ID</th>
                <th>PRODUCTO</th>
                <th class="text-center">STOCK ACTUAL</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($data['productos'])):
                foreach ($data['productos'] as $index => $producto):
                    $classStock = $producto['cantidad'] < 5 ? 'stock-bajo' : '';
            ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $producto['id']; ?></td>
                    <td><?php echo $producto['nombre']; ?></td>
                    <td class="text-center <?php echo $classStock; ?>">
                        <?php echo $producto['cantidad']; ?>
                    </td>
                </tr>
            <?php 
                endforeach;
            else:
            ?>
                <tr>
                    <td colspan="4" class="text-center">No hay productos con stock bajo</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Mensaje final -->
    <div class="mensaje">
        <p><strong>Generado por Mastec Digital</strong></p>
        <p><?php echo date('Y'); ?></p>
    </div>
</body>
</html>