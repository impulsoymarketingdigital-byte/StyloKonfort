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
        
        /* REMITENTE */
        .seccion-remitente {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px dashed #333;
            padding-bottom: 8px;
        }
        
        .logo-remitente {
            margin-bottom: 5px;
        }
        
        .logo-remitente img {
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
        
        .info-linea {
            font-size: 8pt;
            line-height: 1.4;
            color: #333;
        }
        
        .titulo-seccion {
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
        
        /* DESTINATARIO */
        .destinatario-nombre {
            font-size: 11pt;
            font-weight: bold;
            margin: 5px 0 8px 0;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .destinatario-info {
            font-size: 8pt;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        
        .destinatario-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .destinatario-info td {
            padding: 2px 0;
            vertical-align: top;
        }
        
        .destinatario-info td:first-child {
            font-weight: bold;
            width: 70px;
        }
        
        .direccion-principal {
            font-size: 9pt;
            font-weight: bold;
            margin: 8px 0;
            padding: 5px;
            text-align: center;
            border: 2px solid #333;
            line-height: 1.4;
        }
        
        /* CÓDIGO DE PEDIDO */
        .codigo-pedido {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            padding: 5px 0;
            margin-top: 8px;
            border-top: 2px double #333;
            border-bottom: 2px double #333;
            letter-spacing: 1px;
        }
        
        .id-transaccion {
            font-size: 8pt;
            color: #666;
            margin-top: 3px;
            text-align: center;
        }
        
        .fecha-hora {
            font-size: 7pt;
            color: #666;
            margin-top: 5px;
            text-align: center;
        }
        
        .tipo-cliente-badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 3px;
            margin-top: 3px;
        }
        
        .badge-mayorista {
            background: #ffd700;
            color: #000;
        }
        
        .badge-final {
            background: #e0e0e0;
            color: #333;
        }
        
        small {
            font-size: 6pt;
        }
    </style>
</head>
<body>
    <!-- REMITENTE (EMPRESA) -->
    <div class="seccion-remitente">
        <?php if (!empty($data['empresa']['logo'])): ?>
        <div class="logo-remitente">
            <img src="<?php echo BASE_URL . 'assets/images/logo/' . $data['empresa']['logo']; ?>" alt="Logo">
        </div>
        <?php endif; ?>
        
        <div class="empresa-nombre"><?php echo strtoupper($data['empresa']['nombre']); ?></div>
        
        <div class="info-linea">
            <?php if (!empty($data['empresa']['telefono'])): ?>
            Tel: <?php echo $data['empresa']['telefono']; ?><br>
            <?php endif; ?>
            <?php if (!empty($data['empresa']['direccion'])): ?>
            <?php echo $data['empresa']['direccion']; ?><br>
            <?php endif; ?>
            <?php if (!empty($data['empresa']['ciudad'])): ?>
            <?php echo $data['empresa']['ciudad']; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- DESTINATARIO (CLIENTE) -->
    <div class="titulo-seccion">DATOS DEL DESTINATARIO</div>
    
    <div class="destinatario-nombre">
        <?php echo strtoupper($data['pedido']['nombre'] . ' ' . $data['pedido']['apellido']); ?>
        
        <?php if (!empty($data['cliente']['tipo_cliente'])): ?>
        <br>
        <span class="tipo-cliente-badge <?php echo ($data['cliente']['tipo_cliente'] == 'mayorista') ? 'badge-mayorista' : 'badge-final'; ?>">
            <?php echo strtoupper($data['cliente']['tipo_cliente']); ?>
        </span>
        <?php endif; ?>
    </div>
    
    <div class="destinatario-info">
        <table>
            <?php if (!empty($data['cliente']['documento'])): ?>
            <tr>
                <td>Documento:</td>
                <td><?php echo $data['cliente']['documento']; ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if (!empty($data['cliente']['telefono'])): ?>
            <tr>
                <td>Teléfono:</td>
                <td><?php echo $data['cliente']['telefono']; ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if (!empty($data['pedido']['email'])): ?>
            <tr>
                <td>Email:</td>
                <td style="font-size: 7pt;"><?php echo $data['pedido']['email']; ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if (!empty($data['cliente']['departamento'])): ?>
            <tr>
                <td>Departamento:</td>
                <td><?php echo $data['cliente']['departamento']; ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if (!empty($data['pedido']['ciudad']) || !empty($data['cliente']['ciudad'])): ?>
            <tr>
                <td>Ciudad:</td>
                <td><?php echo !empty($data['pedido']['ciudad']) ? $data['pedido']['ciudad'] : $data['cliente']['ciudad']; ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if (!empty($data['cliente']['barrio'])): ?>
            <tr>
                <td>Barrio:</td>
                <td><?php echo $data['cliente']['barrio']; ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    
    <?php if (!empty($data['pedido']['direccion']) || !empty($data['cliente']['direccion'])): ?>
    <div class="direccion-principal">
        DIRECCIÓN DE ENTREGA<br>
        <?php echo !empty($data['pedido']['direccion']) ? $data['pedido']['direccion'] : $data['cliente']['direccion']; ?>
    </div>
    <?php endif; ?>
    
    <!-- CÓDIGO DE PEDIDO -->
    <div class="codigo-pedido">
        PEDIDO #<?php echo str_pad($data['pedido']['id'], 6, '0', STR_PAD_LEFT); ?>
    </div>
    
    <div class="id-transaccion">
        <?php echo $data['pedido']['id_transaccion']; ?>
    </div>
    
    <div class="fecha-hora">
        <?php echo date('d/m/Y - H:i:s', strtotime($data['pedido']['fecha'] ?? 'now')); ?>
    </div>
</body>
</html>