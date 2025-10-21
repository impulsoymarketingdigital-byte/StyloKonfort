<?php include_once 'Views/template/header-admin.php'; ?>

<div class="card">
    <div class="card-body">

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
            <table class="table  table-striped table-hover align-middle" id="tblNuevoTraspaso" style="width: 100%;">
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