<?php include_once 'Views/template/header-admin.php'; ?>

<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" type="button" id="nuevo_registro">
        <i class="fas fa-plus me-1"></i> Nuevo
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" style="width: 100%;" id="tblProveedores">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Teléfono</th>
                        <th>Email</th>
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
                                <label for="nombre">Nombre</label>
                                <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombre del proveedor">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="ruc">Documento</label>
                                <input id="ruc" class="form-control" type="text" name="ruc" placeholder="Documento">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="persona_contacto">Persona de Contacto</label>
                                <input id="persona_contacto" class="form-control" type="text" name="persona_contacto" placeholder="Persona de contacto">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="documento">Documento</label>
                                <input id="documento" class="form-control" type="text" name="documento" placeholder="Documento">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="telefono">Teléfono</label>
                                <input id="telefono" class="form-control" type="text" name="telefono" placeholder="Teléfono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="email">Email</label>
                                <input id="email" class="form-control" type="email" name="email" placeholder="Email">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label for="direccion">Dirección</label>
                        <textarea id="direccion" class="form-control" name="direccion" rows="2" placeholder="Dirección"></textarea>
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

<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/proveedores.js'; ?>"></script>

</body>
</html>