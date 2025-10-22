<?php include_once 'Views/template/header-admin.php'; ?>

<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" type="button" id="nuevo_registro">
        <i class="fas fa-plus me-1"></i> Nuevo Rol
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" style="width: 100%;" id="tblRoles">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
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
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal"></h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frmRegistro" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    
                    <div class="form-group mb-3">
                        <label for="nombre">Nombre del Rol <span class="text-danger">*</span></label>
                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Ej: SUPERVISOR">
                    </div>

                    <hr>
                    <label><i class="fas fa-key"></i> <strong>Permisos</strong> <span class="text-danger">*</span></label>
                    <p class="text-muted small">Selecciona los módulos a los que este rol tendrá acceso</p>
                    
                    <div class="row mt-3">
                        <?php foreach ($data['permisos'] as $permiso) { ?>
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input listaCheck" type="checkbox" name="permisos[]" value="<?php echo $permiso['nombre']; ?>" id="permiso_<?php echo $permiso['id']; ?>">
                                    <label class="form-check-label" for="permiso_<?php echo $permiso['id']; ?>">
                                        <?php echo ucfirst($permiso['nombre']); ?>
                                    </label>
                                </div>
                            </div>
                        <?php } ?>
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

<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/roles.js'; ?>"></script>

</body>
</html>