<?php include_once 'views/templates/header.php'; ?>

<div class="card">
    <div class="card-body">

        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active mt-2" id="nav-clientes" role="tabpanel"
                aria-labelledby="nav-clientes-tab" tabindex="0">
                <h5 class="card-title text-center"><i class="fas fa-users"></i> Listado de Cajas</h5>
                <hr>
                <div class="d-flex justify-content-center mb-3">
                    <div class="form-group">
                        <label for="desde">Desde</label>
                        <input id="desde" class="form-control" type="date">
                    </div>
                    <div class="form-group">
                        <label for="hasta">Hasta</label>
                        <input id="hasta" class="form-control" type="date">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle" id="tbl" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>FECHA APERTURA</th>
                                <th>FECHA CIERRE</th>
                                <th>MONTO INICIAL</th>
                                <th>MONTO FINAL</th>
                                <th>USUARIO</th>
                                <th>APERTURA</th>
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

<div id="theModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">DETALLE DE VENTA</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row mb-2 p-1">

                    <div class="col-md-6 col-sm-6 mb-2">
                        <label>Cliente</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Proveedor" id="proveedorNombre"
                                readonly>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 mb-2">
                        <label>N° Venta</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="N° Factura" id="ventaNumero" readonly>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 mb-2">
                        <label>Usuario</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Usuario" id="usuarioNombre" readonly>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 mb-2">
                        <label>Total</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Total" id="ventaTotal" readonly>
                        </div>
                    </div>

                </div>
                <hr>
                <!-- Tabla de Productos -->
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDetalles">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/templates/footer.php'; ?>