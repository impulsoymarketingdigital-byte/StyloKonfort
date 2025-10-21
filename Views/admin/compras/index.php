<?php include_once 'Views/template/header-admin.php'; ?>

<div class="card">
    <div class="card-body">


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
            <table class="table  table-striped table-hover align-middle" id="tblNuevaCompra" style="width: 100%;">
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
                    <input class="form-control" type="text" id="telefonoProveedor" placeholder="Teléfono" disabled>
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