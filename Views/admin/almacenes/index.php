<?php include_once 'Views/template/header-admin.php'; ?>

<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" type="button" id="nuevo_registro">
        <i class="fas fa-plus me-1"></i> Nuevo
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" style="width: 100%;" id="tblAlmacenes">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Sucursal</th>
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

<div id="nuevoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal"></h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frmRegistro" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group mb-2">
                        <label for="codigo">Código</label>
                        <input id="codigo" class="form-control" type="text" name="codigo" placeholder="Código">
                    </div>
                    <div class="form-group mb-2">
                        <label for="nombre">Nombre</label>
                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombre del almacén">
                    </div>
                    <div class="form-group mb-2">
                        <label for="direccion">Dirección</label>
                        <input id="direccion" class="form-control" type="text" name="direccion" placeholder="Dirección">
                    </div>
                    <div class="form-group mb-2">
                        <label for="id_sucursal">Sucursal</label>
                        <select id="id_sucursal" class="form-control" name="id_sucursal">
                            <option value="">Seleccionar Sucursal</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" id="btnAccion">Registrar</button>
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/almacenes.js'; ?>"></script>

</body>
</html>