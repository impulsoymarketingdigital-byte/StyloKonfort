<?php include_once 'Views/template/header-admin.php'; ?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Datos de la empresa</h5>
        <hr>
        <form id="formulario" autocomplete="off">
            <input type="hidden" id="id" name="id" value="<?php echo $data['empresa']['id']; ?>">
            <div class="row mb-3">
                <div class="col-md-3 mb-3">
                    <label for="ruc">Documento <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-list"></i></span>
                        <input class="form-control" type="text" name="ruc" id="ruc" value="<?php echo $data['empresa']['ruc']; ?>" placeholder="Documento">
                    </div>
                </div>
                <div class="col-md-5 mb-3">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-list"></i></span>
                        <input class="form-control" type="text" name="nombre" id="nombre" value="<?php echo $data['empresa']['nombre']; ?>" placeholder="Nombre">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="telefono">Teléfono <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input class="form-control" type="number" name="telefono" id="telefono" value="<?php echo $data['empresa']['telefono']; ?>" placeholder="Telefono">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="whatsapp">WhatsApp <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input class="form-control" type="text" name="whatsapp" id="whatsapp" value="<?php echo $data['empresa']['whatsapp']; ?>" placeholder="WhatsApp">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="facebook">Facebook <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input class="form-control" type="url" name="facebook" id="facebook" value="<?php echo $data['empresa']['facebook']; ?>" placeholder="Facebook">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="twitter">Twitter <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input class="form-control" type="url" name="twitter" id="twitter" value="<?php echo $data['empresa']['twitter']; ?>" placeholder="Twitter">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="instagram">Instagram <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input class="form-control" type="url" name="instagram" id="instagram" value="<?php echo $data['empresa']['instagram']; ?>" placeholder="Instagram">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="correo">Correo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input class="form-control" type="text" name="correo" id="correo" value="<?php echo $data['empresa']['correo']; ?>" placeholder="Correo Electrónico">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="direccion">Dirreción <span class="text-danger">*</span></label>
                        <textarea id="direccion" class="form-control" name="direccion" rows="3" placeholder="Dirección"><?php echo $data['empresa']['direccion']; ?></textarea>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="ubicacion">Ubicación <span class="text-danger">*</span></label>
                        <textarea id="ubicacion" class="form-control" name="ubicacion" rows="3" placeholder="Ubicación"><?php echo $data['empresa']['ubicacion']; ?></textarea>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="mensaje">Mensaje <span class="text-danger">*</span></label>
                        <textarea id="mensaje" class="form-control" name="mensaje" rows="3" placeholder="Mensaje"><?php echo $data['empresa']['mensaje']; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <button class="btn btn-primary" type="submit" id="btnAccion">Modificar</button>
            </div>
        </form>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/empresa.js'; ?>"></script>

</body>

</html>