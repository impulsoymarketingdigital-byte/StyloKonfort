<?php include_once 'Views/template/header-admin.php'; ?>

<button class="btn btn-inverse-primary mb-2" type="button" id="nuevo_registro">Nuevo</button>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle" style="width: 100%;" id="tblColores">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Color</th>
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
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="titleModal"></h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="frmRegistro" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group mb-2">
                        <label for="nombre">Nombre</label>
                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Ej: ROJO o VERDE/ROJO">
                    </div>
                    <div class="form-group mb-3">
                        <label for="color">Color Principal</label>
                        <input id="color" class="form-control" type="color" name="color" value="#000000">
                    </div>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="checkColorCombinado">
                        <label class="form-check-label" for="checkColorCombinado">
                            <strong>¿Es color combinado?</strong>
                        </label>
                    </div>
                    
                    <div class="form-group mb-2" id="divColorSecundario" style="display: none;">
                        <label for="color_secundario">Color Secundario</label>
                        <input id="color_secundario" class="form-control" type="color" name="color_secundario" value="#000000">
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

<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/colores.js'; ?>"></script>

</body>

</html>