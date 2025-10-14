<?php include_once 'Views/template/header-admin.php'; ?>

<div class="card">
    <div class="card-body">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-compras-tab" data-bs-toggle="tab" data-bs-target="#nav-compras"
                    type="button" role="tab" aria-controls="nav-compras" aria-selected="true">Nueva Compra</button>
                <button class="nav-link" id="nav-historial-tab" data-bs-toggle="tab" data-bs-target="#nav-historial"
                    type="button" role="tab" aria-controls="nav-historial" aria-selected="false">Historial</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active p-3" id="nav-compras" role="tabpanel"
                aria-labelledby="nav-compras-tab" tabindex="0">
                <h5 class="card-title text-center"><i class="fas fa-shopping-cart"></i> Nueva Compra</h5>
                <hr>

                <!-- Input para buscar producto -->
                <div class="input-group mb-2" id="containerNombre">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input class="form-control" type="text" id="buscarProductoNombre" placeholder="Buscar Producto"
                        autocomplete="off">
                </div>

                <span class="text-danger fw-bold mb-2" id="errorBusqueda"></span>

                <!-- Tabla productos -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle" id="tblNuevaCompra"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Atributo</th>
                                <th>Precio Compra</th>
                                <th>Cantidad</th>
                                <th>Descuento</th>
                                <th>SubTotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="row justify-content-between">
                    <div class="col-md-4">
                        <label>Buscar Proveedor</label>
                        <div class="input-group mb-2">
                            <input type="hidden" id="idProveedor" value="">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input class="form-control" type="text" id="buscarProveedor" placeholder="Buscar Proveedor">
                        </div>

                        <span class="text-danger fw-bold mb-2" id="errorProveedor"></span>

                        <label>Documento</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input class="form-control" type="text" id="rucProveedor" placeholder="Documento" disabled>
                        </div>

                        <label>Teléfono</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input class="form-control" type="text" id="telefonoProveedor" placeholder="Teléfono"
                                disabled>
                        </div>

                        <label>Email</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input class="form-control" type="text" id="emailProveedor" placeholder="Email" disabled>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label>Tipo de Comprobante</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                            <select class="form-control" id="tipoComprobante">
                                <option value="FACTURA">FACTURA</option>
                                <option value="BOLETA">BOLETA</option>
                                <option value="NOTA">NOTA</option>
                                <option value="RECIBO">RECIBO</option>
                            </select>
                        </div>

                        <label>Almacén</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                            <select class="form-control" id="almacen">
                                <?php foreach ($data['almacenes'] as $almacen) { ?>
                                    <option value="<?php echo $almacen['id']; ?>"><?php echo $almacen['nombre']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label>Usuario</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input class="form-control" type="text" value="<?php echo $_SESSION['nombre_usuario']; ?>"
                                placeholder="Usuario" disabled>
                        </div>

                        <label>Total Descuento</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                            <input class="form-control" type="text" id="totalDescuento" placeholder="0.00" disabled>
                        </div>

                        <label>Total a Pagar</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                            <input class="form-control" type="text" id="totalPagar" placeholder="Total Pagar" disabled>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-primary" type="button" id="btnAccion">Registrar Compra</button>
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
                        <label for="filtroProveedor">Proveedor</label>
                        <select id="filtroProveedor" class="form-control" style="min-width: 200px;">
                            <option value="">Todos los proveedores</option>
                            <?php foreach ($data['proveedores'] as $proveedor) { ?>
                                <option value="<?php echo $proveedor['nombre']; ?>"><?php echo $proveedor['nombre']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group align-self-end">
                        <button class="btn btn-secondary" type="button" id="btnLimpiarFiltros">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle nowrap" id="tblHistorial"
                        style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nº Compra</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Proveedor</th>
                                <th>Almacén</th>
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

<!-- Modal Detalle Compra -->
<div id="modalDetalle" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detalle Compra #<span id="numCompra"></span></h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <p><strong>Proveedor:</strong> <span id="detProveedor"></span></p>
                        <p><strong>RUC:</strong> <span id="detRuc"></span></p>
                        <p><strong>Teléfono:</strong> <span id="detTelefono"></span></p>
                    </div>
                    <div class="col-6">
                        <p><strong>Fecha:</strong> <span id="detFecha"></span></p>
                        <p><strong>Comprobante:</strong> <span id="detTipoComprobante"></span></p>
                        <p><strong>Almacén:</strong> <span id="detAlmacen"></span></p>
                    </div>
                </div>

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Talla</th>
                            <th>Color</th>
                            <th>Cant</th>
                            <th>Precio</th>
                            <th>Desc.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="detProductos"></tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="6" class="text-end">TOTAL:</td>
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
                            <label for="stockActual">Stock Actual</label>
                            <input id="stockActual" class="form-control" type="number" readonly>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="precioCompra">Precio Compra</label>
                            <input id="precioCompra" class="form-control" type="number" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cantidadCompra">Cantidad</label>
                            <input id="cantidadCompra" class="form-control" type="number" min="1" value="1">
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
    const nombreKey = 'posCompra';
</script>
<script src="<?php echo BASE_URL . 'assets/admin/js/jquery-ui.min.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/jquery.datetimepicker.full.min.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/busqueda_compras.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/compras.js'; ?>"></script>

</body>

</html>