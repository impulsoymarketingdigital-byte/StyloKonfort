<?php include_once 'Views/template/header-admin.php'; ?>

<div class="d-flex justify-content-center mb-3 gap-3">
    <div class="form-group">
        <label for="desde">Desde</label>
        <input id="desde" class="form-control" type="date">
    </div>
    <div class="form-group">
        <label for="hasta">Hasta</label>
        <input id="hasta" class="form-control" type="date">
    </div>
</div>

<!-- Estados del proceso de pedidos:
1 = Recepción del pedido
2 = En Proceso
3 = Entregado (descuenta stock)
-->

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#listaPedidos" type="button" role="tab" aria-controls="listaPedidos" aria-selected="true">
            <i class="fas fa-inbox"></i> Recepción
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="proceso-tab" data-bs-toggle="tab" data-bs-target="#listaProceso" type="button" role="tab" aria-controls="listaProceso" aria-selected="false">
            <i class="fas fa-tasks"></i> En Proceso
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#pedidosFinalizados" type="button" role="tab" aria-controls="pedidosFinalizados" aria-selected="false">
            <i class="fas fa-check-circle"></i> Entregados
        </button>
    </li>
</ul>

<div class="tab-content" id="myTabContent">
    <!-- Tab 1: Recepción del pedido (proceso = 1) -->
    <div class="tab-pane fade show active" id="listaPedidos" role="tabpanel" aria-labelledby="home-tab">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-inbox"></i> Pedidos en Recepción</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle nowrap" style="width: 100%;" id="tblPendientes">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Fecha</th>
                                <th>Transacción</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Correo</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab 2: En Proceso (proceso = 2) -->
    <div class="tab-pane fade" id="listaProceso" role="tabpanel" aria-labelledby="proceso-tab">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-tasks"></i> Pedidos en Proceso</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle" style="width: 100%;" id="tblProceso">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Fecha</th>
                                <th>Transacción</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Correo</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab 3: Entregados (proceso = 3) -->
    <div class="tab-pane fade" id="pedidosFinalizados" role="tabpanel" aria-labelledby="profile-tab">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-check-circle"></i> Pedidos Entregados</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle nowrap" style="width: 100%;" id="tblFinalizados">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Fecha</th>
                                <th>Transacción</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Correo</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalle del pedido -->
<div id="modalPedidos" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-shopping-bag"></i> Detalle del Pedido</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle nowrap" id="tablePedidos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Atributo</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>SubTotal</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/admin/js/jquery.datetimepicker.full.min.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/pedidos.js'; ?>"></script>

</body>
</html>