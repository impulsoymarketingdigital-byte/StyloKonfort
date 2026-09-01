<?php include_once 'Views/template/header-admin.php'; ?>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3">
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total clientes</p>
                        <h4 class="my-1 text-warning"><?php echo intval($data['clientes']['total']); ?></h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto">
                        <i class='fas fa-user-cog'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Total usuarios</p>
                        <h4 class="my-1 text-danger"><?php echo intval($data['usuarios']['total']); ?></h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto">
                        <i class='fas fa-users'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Total categorias</p>
                        <h4 class="my-1 text-success"><?php echo intval($data['categorias']['total']); ?></h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                        <i class='fas fa-check-circle'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-secondary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Total Colores</p>
                        <h4 class="my-1 text-secondary"><?php echo intval($data['colores']['total']); ?></h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-secondary text-white ms-auto">
                        <i class='bx bxs-group'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Pedidos Pendientes</p>
                        <h4 class="my-1 text-success"><?php echo intval($data['pendientes']['total']); ?></h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-success text-white ms-auto">
                        <i class='fas fa-exclamation-circle'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-dark">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Pedidos en Proceso</p>
                        <h4 class="my-1 text-dark"><?php echo intval($data['procesos']['total']); ?></h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-dark text-white ms-auto">
                        <i class='fas fa-spinner'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Pedidos Finalizados</p>
                        <h4 class="my-1 text-danger"><?php echo intval($data['finalizados']['total']); ?></h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-danger text-white ms-auto">
                        <i class='fas fa-check-circle'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0">Total productos</p>
                        <h4 class="my-1 text-primary"><?php echo intval($data['productos']['total']); ?></h4>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-primary text-white ms-auto">
                        <i class='bx bxs-group'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-lg-4">
        <div class="card radius-10 w-100">
            <div class="card-header bg-transparent">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0">Productos con stock mínimo</h6>
                    </div>
                    <div class="dropdown">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
                            <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:;" onclick="exportarStockMinimoPDF()"><i class="fas fa-file-pdf text-danger"></i> Exportar PDF</a></li>
                            <li><a class="dropdown-item" href="javascript:;" onclick="exportarStockMinimoExcel()"><i class="fas fa-file-excel text-success"></i> Exportar Excel</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container-1">
                    <canvas id="chart4"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card radius-10 w-100">
            <div class="card-header bg-transparent">
                <div class="d-flex align-items-center">
                    <div><h6 class="mb-0">Productos más vendidos</h6></div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container-1">
                    <canvas id="topProductos"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="card radius-10">
            <div class="card-header bg-transparent">
                <div class="d-flex align-items-center">
                    <div><h6 class="mb-0">Pedidos</h6></div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container-2 mt-4">
                    <canvas id="reportePedidos"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5>Nuevos productos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Precio Compra</th>
                                <th>Precio Venta</th>
                                <th>Categoria</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['nuevos'] as $producto) { ?>
                                <tr>
                                    <td><?php echo intval($producto['id']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><span class="badge bg-info"><?php echo htmlspecialchars($producto['precio_compra'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td><span class="badge bg-success"><?php echo htmlspecialchars($producto['precio_venta'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td><?php echo htmlspecialchars($producto['categoria'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script>
    // chart 2
    var ctx = document.getElementById("reportePedidos").getContext('2d');

    var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
    gradientStroke1.addColorStop(0, '#fc4a1a');
    gradientStroke1.addColorStop(1, '#f7b733');

    var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
    gradientStroke2.addColorStop(0, '#4776e6');
    gradientStroke2.addColorStop(1, '#8e54e9');

    var gradientStroke3 = ctx.createLinearGradient(0, 0, 0, 300);
    gradientStroke3.addColorStop(0, '#42e695');
    gradientStroke3.addColorStop(1, '#3bb2b8');

    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ["Estado Pendientes", "Estado Proceso", "Estado Finalizados"],
            datasets: [{
                backgroundColor: [gradientStroke1, gradientStroke2, gradientStroke3],
                hoverBackgroundColor: [gradientStroke1, gradientStroke2, gradientStroke3],
                data: [
                    <?php echo intval($data['pendientes']['total']); ?>,
                    <?php echo intval($data['procesos']['total']); ?>,
                    <?php echo intval($data['finalizados']['total']); ?>
                ],
                borderWidth: [1, 1, 1],
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutoutPercentage: 0,
            legend: { position: "bottom", display: true, labels: { boxWidth: 8 } },
            tooltips: { displayColors: false },
        }
    });
</script>

<script src="<?php echo BASE_URL; ?>assets/admin/js/index.js"></script>

</body>
</html>