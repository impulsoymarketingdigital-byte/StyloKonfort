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

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#listaPedidos" type="button" role="tab" aria-controls="listaPedidos" aria-selected="true">
            <i class="fas fa-inbox text-warning"></i> Recepción
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="proceso-tab" data-bs-toggle="tab" data-bs-target="#listaProceso" type="button" role="tab" aria-controls="listaProceso" aria-selected="false">
            <i class="fas fa-box-open text-info"></i> En Proceso
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#pedidosFinalizados" type="button" role="tab" aria-controls="pedidosFinalizados" aria-selected="false">
            <i class="fas fa-check-circle text-success"></i> Entregados
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="anulados-tab" data-bs-toggle="tab" data-bs-target="#pedidosAnulados" type="button" role="tab" aria-controls="pedidosAnulados" aria-selected="false">
            <i class="fas fa-times-circle text-dark"></i> Anulados
        </button>
    </li>
</ul>

<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="listaPedidos" role="tabpanel" aria-labelledby="home-tab">
        <div class="card border-top border-warning border-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-inbox"></i> Nuevos Pedidos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle nowrap" style="width: 100%;" id="tblPendientes">
                        <thead class="table-light">
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="tab-pane fade" id="listaProceso" role="tabpanel" aria-labelledby="proceso-tab">
        <div class="card border-top border-info border-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box-open"></i> Pedidos Empacados</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle nowrap" style="width: 100%;" id="tblProceso">
                        <thead class="table-light">
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="tab-pane fade" id="pedidosFinalizados" role="tabpanel" aria-labelledby="profile-tab">
        <div class="card border-top border-success border-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-check-circle"></i> Historial Entregados</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle nowrap" style="width: 100%;" id="tblFinalizados">
                        <thead class="table-light">
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pedidosAnulados" role="tabpanel" aria-labelledby="anulados-tab">
        <div class="card border-top border-dark border-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-times-circle"></i> Pedidos Anulados / Devueltos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle nowrap" style="width: 100%;" id="tblAnulados">
                        <thead class="table-light">
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalPedidos" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-shopping-bag"></i> Detalle de la Orden</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle nowrap" id="tablePedidos" style="width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Variación (Talla-Color)</th>
                                <th>Precio Unitario</th>
                                <th>Cant.</th>
                                <th>SubTotal</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/admin/js/jquery.datetimepicker.full.min.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/pedidos.js'; ?>"></script>

</body>
</html>