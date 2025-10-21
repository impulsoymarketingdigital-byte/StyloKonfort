<?php include_once 'Views/template/header-admin.php'; ?>

<div class="card">
    <div class="card-body">

        <h5 class="card-title text-center"><i class="fas fa-users"></i> Listado de Traspasos</h5>
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
            <table class="table align-middle" id="tblHistorial" style="width: 100%;">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Nº Traspaso</th>
                        <th>Fecha</th>
                        <th>Almacén Origen</th>
                        <th>Almacén Destino</th>
                        <th>Total Productos</th>
                        <th>Estado</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
            </table>

        </div>
    </div>
</div>


<!-- Modal Detalle Traspaso -->
<div id="modalDetalle" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle Traspaso #<span id="numTraspaso"></span></h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <p><strong>Almacén Origen:</strong> <span id="detAlmacenOrigen"></span></p>
                        <p><strong>Almacén Destino:</strong> <span id="detAlmacenDestino"></span></p>
                    </div>
                    <div class="col-6">
                        <p><strong>Fecha:</strong> <span id="detFecha"></span></p>
                        <p><strong>Usuario:</strong> <span id="detUsuario"></span></p>
                    </div>
                </div>

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Talla</th>
                            <th>Color</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody id="detProductos"></tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">TOTAL PRODUCTOS:</td>
                            <td id="detTotal"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>
<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/listar_traspasos.js'; ?>"></script>