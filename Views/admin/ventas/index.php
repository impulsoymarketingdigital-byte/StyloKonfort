<?php include_once 'Views/template/header-admin.php'; ?>

<div class="card">
    <div class="card-body">

        <div class="tab-content" id="nav-tabContent">

            <h5 class="card-title text-center"><i class="fas fa-cash-register"></i> Nueva Venta</h5>
            <hr>

            <!-- input para buscar nombre -->
            <div class="input-group mb-2" id="containerNombre">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input class="form-control" type="text" id="buscarProductoNombre" placeholder="Buscar Producto"
                    autocomplete="off">
            </div>

            <span class="text-danger fw-bold mb-2" id="errorBusqueda"></span>

            <!-- table productos -->

            <div class="table-responsive">
                <table class="table  table-striped table-hover align-middle" id="tblNuevaVenta" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Atributo</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
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
                    <label>Buscar Cliente</label>
                    <div class="input-group mb-2">
                        <input type="hidden" id="idCliente" value="1">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input class="form-control" type="text" id="buscarCliente" placeholder="Buscar Cliente">
                    </div>

                    <span class="text-danger fw-bold mb-2" id="errorCliente"></span>

                    <label>Telefono</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input class="form-control" type="text" id="telefonoCliente" placeholder="Telefono" disabled>
                    </div>

                    <label>Dirección</label>
                    <ul class="list-group">
                        <li class="list-group-item" id="direccionCliente"><i class="fas fa-home"></i></li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <label>Tipo de Venta</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                        <select class="form-control" id="metodoPago">
                            <option value="VENTA DIRECTA">VENTA DIRECTA</option>
                            <option value="LLEVAR">LLEVAR</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label>Vendedor</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input class="form-control" type="text" value="<?php echo $_SESSION['nombre_usuario']; ?>"
                            placeholder="Vendedor" disabled>
                    </div>

                    <label>Total a Pagar</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                        <input class="form-control" type="text" id="totalPagar" placeholder="Total Pagar" disabled>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary" type="button" id="btnAccion">Completar</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

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
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="size">Size</label>
                            <select id="size" class="form-control select" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="color">Color</label>
                            <select id="color" class="form-control select" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="cantidad">Stock</label>
                            <input id="cantidad" class="form-control" type="number" readonly>
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
    const nombreKey = 'posVenta';
</script>
<script src="<?php echo BASE_URL . 'assets/admin/js/jquery-ui.min.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/jquery.datetimepicker.full.min.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/busqueda.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/ventas.js'; ?>"></script>

</body>

</html>