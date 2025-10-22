<?php include_once 'Views/template/header-admin.php'; ?>

<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" type="button" id="nuevo_registro">
        <i class="fas fa-plus me-1"></i> Nuevo
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" style="width: 100%;" id="tblPromociones">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Imagen</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Vigencia</th>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal"></h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="frmRegistro" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="imagen_actual" name="imagen_actual">
                    
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label for="titulo">Título *</label>
                            <input id="titulo" class="form-control" type="text" name="titulo" placeholder="Título de la promoción">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" class="form-control" name="descripcion" rows="3" placeholder="Descripción de la promoción"></textarea>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="fecha_inicio">Fecha Inicio *</label>
                            <input id="fecha_inicio" class="form-control" type="date" name="fecha_inicio">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="fecha_fin">Fecha Fin *</label>
                            <input id="fecha_fin" class="form-control" type="date" name="fecha_fin">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label for="link">Link (Opcional)</label>
                            <input id="link" class="form-control" type="text" name="link" placeholder="URL de la promoción">
                        </div>

                        <div class="col-md-12">
                            <label for="imagen">Imagen (Opcional)</label>
                            <input id="imagen" class="form-control" type="file" name="imagen">
                            <div id="container-img" class="mt-2"></div>
                        </div>
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

<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/promociones.js'; ?>"></script>

</body>

</html>