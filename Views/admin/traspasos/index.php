<?php include_once 'Views/template/header-admin.php'; ?>

<div class="card">
    <div class="card-body">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-traspasos-tab" data-bs-toggle="tab" data-bs-target="#nav-traspasos"
                    type="button" role="tab" aria-controls="nav-traspasos" aria-selected="true">Nuevo Traspaso</button>
                <button class="nav-link" id="nav-historial-tab" data-bs-toggle="tab" data-bs-target="#nav-historial"
                    type="button" role="tab" aria-controls="nav-historial" aria-selected="false">Historial</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active p-3" id="nav-traspasos" role="tabpanel"
                aria-labelledby="nav-traspasos-tab" tabindex="0">
                <h5 class="card-title text-center"><i class="fas fa-exchange-alt"></i> Nuevo Traspaso</h5>
                <hr>

                <!-- Seleccionar Almacén Origen -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Almacén Origen</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                            <select class="form-control" id="almacenOrigen">
                                <option value="">Seleccionar Almacén Origen</option>
                                <?php foreach ($data['almacenes'] as $almacen) { ?>
                                    <option value="<?php echo $almacen['id']; ?>"><?php echo $almacen['nombre']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <span class="text-danger fw-bold" id="errorAlmacenOrigen"></span>
                    </div>
                </div>

                <!-- Input para buscar producto -->
                <div class="input-group mb-2" id="containerNombre">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input class="form-control" type="text" id="buscarProductoNombre" placeholder="Buscar Producto"
                        autocomplete="off" disabled>
                </div>

                <span class="text-danger fw-bold mb-2" id="errorBusqueda"></span>

                <!-- Tabla productos -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle" id="tblNuevoTraspaso"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Atributo</th>
                                <th>Stock Origen</th>
                                <th>Cantidad a Traspasar</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="row justify-content-between">
                    <div class="col-md-6">
                        <label>Almacén Destino</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                            <select class="form-control" id="almacenDestino">
                                <option value="">Seleccionar Almacén Destino</option>
                                <?php foreach ($data['almacenes'] as $almacen) { ?>
                                    <option value="<?php echo $almacen['id']; ?>"><?php echo $almacen['nombre']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <span class="text-danger fw-bold" id="errorAlmacenDestino"></span>
                    </div>

                    <div class="col-md-6">
                        <label>Usuario</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input class="form-control" type="text" value="<?php echo $_SESSION['nombre_usuario']; ?>"
                                placeholder="Usuario" disabled>
                        </div>

                        <label>Total Productos</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-box"></i></span>
                            <input class="form-control" type="text" id="totalProductos" placeholder="0" disabled>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-primary" type="button" id="btnAccion">Registrar Traspaso</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade p-3" id="nav-historial" role="tabpanel" aria-labelledby="nav-historial-tab"
                tabindex="0">
                <div class="d-flex justify-content-center mb-3 gap-2">
                    <div class="form-group">
                        <label for="desde">Desde</label>
                        <input id="desde" class="form-control" type="date">
                    </div>
                    <div class="form-group">
                        <label for="hasta">Hasta</label>
                        <input id="hasta" class="form-control" type="date">
                    </div>
                    <div class="form-group">
                        <label for="filtroAlmacen">Almacén</label>
                        <select id="filtroAlmacen" class="form-control" style="min-width: 200px;">
                            <option value="">Todos los almacenes</option>
                            <?php foreach ($data['almacenes'] as $almacen) { ?>
                                <option value="<?php echo $almacen['nombre']; ?>"><?php echo $almacen['nombre']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>                   
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle nowrap" id="tblHistorial"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nº Traspaso</th>
                                <th>Fecha</th>
                                <th>Almacén Origen</th>
                                <th>Almacén Destino</th>
                                <th>Total Productos</th>
                                <th>Estado</th>
                                <th></th>
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

<!-- Modal Detalle Traspaso -->
<div id="modalDetalle" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detalle Traspaso #<span id="numTraspaso"></span></h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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

<!-- Modal Tamaño y Color -->
<div id="modalSizeColor" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar tamaño y color</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idProducto">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="size">Talla</label>
                            <select id="size" class="form-control select" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="color">Color</label>
                            <select id="color" class="form-control select" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="stockActual">Stock Origen</label>
                            <input id="stockActual" class="form-control" type="number" readonly>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="cantidadTraspaso">Cantidad a Traspasar</label>
                            <input id="cantidadTraspaso" class="form-control" type="number" min="1" value="1">
                        </div>
                    </div>
                </div>
               
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary d-none" type="button" id="btnAgregar">Agregar</button>
            </div>
        </div>
    </div>
</div>
<?php include_once 'Views/template/footer-admin.php'; ?>
<script>
    const nombreKey = 'posTraspaso';
</script>
<script src="<?php echo BASE_URL . 'assets/admin/js/jquery-ui.min.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/busqueda_traspasos.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/traspasos.js'; ?>"></script>

</body>

</html>