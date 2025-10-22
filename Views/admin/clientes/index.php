<?php include_once 'Views/template/header-admin.php'; ?>

<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" type="button" id="nuevo_registro">
        <i class="fas fa-plus me-1"></i> Nuevo
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" style="width: 100%;" id="tblClientes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Tipo</th>
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
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frmRegistro" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="nombre">Nombre <span class="text-danger">*</span></label>
                                <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombre">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="apellido">Apellido <span class="text-danger">*</span></label>
                                <input id="apellido" class="form-control" type="text" name="apellido" placeholder="Apellido">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="telefono">Teléfono <span class="text-danger"></span></label>
                                <input id="telefono" class="form-control" type="text" name="telefono" placeholder="Teléfono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="correo">Correo</label>
                                <input id="correo" class="form-control" type="email" name="correo" placeholder="Correo electrónico">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label for="tipo_cliente">Tipo de Cliente <span class="text-danger">*</span></label>
                                <select id="tipo_cliente" class="form-control" name="tipo_cliente">
                                    <option value="">Seleccionar tipo</option>
                                    <option value="final">Cliente Final</option>
                                    <option value="mayorista">Cliente Mayorista</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label for="direccion">Dirección <span class="text-danger"></span></label>
                        <textarea id="direccion" class="form-control" name="direccion" rows="3" placeholder="Dirección"></textarea>
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

<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/clientes.js'; ?>"></script>

</body>
</html>