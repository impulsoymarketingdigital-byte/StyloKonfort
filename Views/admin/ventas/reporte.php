<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL . 'assets/admin/css/reporte.css'; ?>">
</head>

<body>
    <table id="datos-empresa">
        <tr>
            <td class="logo">
                <img src="<?php echo BASE_URL . 'assets/images/logo.png'; ?>" alt="">
            </td>
            <td class="info-compra">
                <div class="container-factura">
                    <span class="factura">Reporte</span>
                    <p>Fecha y Hora: <?php echo date('d-m-Y H:i:s'); ?></p>
                </div>
            </td>
        </tr>
    </table>
    <h5 class="title">Detalle de las ventas</h5>
    <table id="container-producto">
        <thead>
            <tr>
                <th>N°</th>
                <th>Productos</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Cliente</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            foreach ($data['ventas'] as $venta) {
                $productos = json_decode($venta['productos'], true);
                $lista = '';
                foreach ($productos as $producto) {
                    $lista .= '<li>'.$producto['nombre'].'</li>';
                }
                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $lista; ?></td>
                    <td><?php echo $venta['fecha']; ?></td>
                    <td><?php echo $venta['total']; ?></td>
                    <td><?php echo $venta['nombre']; ?></td>
                </tr>
            <?php $i++; } ?>
        </tbody>
    </table>
</body>

</html>