<?php include_once 'Views/template/header-admin.php'; ?>

<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" type="button" id="nuevo_registro">
        <i class="fas fa-plus me-1"></i> Nuevo
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" style="width: 100%;" id="tblProductos">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>P. Compra</th>
                        <th>P. Venta</th>
                        <th>P. Mayorista</th>
                        <th>Categoria</th>
                        <th>Marca</th>
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

<div id="nuevoModal" class="modal fade" tabindex="-1" aria-labelledby="titleModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal">Registrar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="frmRegistro" autocomplete="off" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="imagen_actual" name="imagen_actual">

                    <div class="row mb-2 p-2">

                        <div class="col-lg-6 col-sm-6 mb-2">
                            <label class="form-label">Codigo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-list"></i></span>
                                <input id="codigo" name="codigo" type="text" class="form-control"
                                    placeholder="Codigo del producto">
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-6 mb-2">
                            <label class="form-label">Producto <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-list"></i></span>
                                <input id="nombre" name="nombre" type="text" class="form-control"
                                    placeholder="Nombre del producto">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 mb-2">
                            <label class="form-label">Precio Compra <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-dollar-sign"></i></span>
                                <input id="precio_compra" name="precio_compra" type="number" step="0.01"
                                    class="form-control" placeholder="Precio Compra">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 mb-2">
                            <label class="form-label">Precio Venta <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-dollar-sign"></i></span>
                                <input id="precio_venta" name="precio_venta" type="number" step="0.01"
                                    class="form-control" placeholder="Precio Venta">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 mb-2">
                            <label class="form-label">Precio Mayorista <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-dollar-sign"></i></span>
                                <input id="precio_mayorista" name="precio_mayorista" type="number" step="0.01"
                                    class="form-control" placeholder="Precio Mayorista">
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-6 mb-2">
                            <label class="form-label">Genero <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-tag"></i></span>
                                <select id="genero" name="genero" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-6 mb-2">
                            <label class="form-label">Categoría <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-tag"></i></span>
                                <select id="categoria" name="categoria" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($data['categorias'] as $categoria) { ?>
                                        <option value="<?= $categoria['id'] ?>"><?= $categoria['categoria'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-6 mb-2">
                            <label class="form-label">Marca <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-industry"></i></span>
                                <select id="marca" name="marca" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($data['marcas'] as $marca) { ?>
                                        <option value="<?= $marca['id'] ?>"><?= $marca['marca'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12 col-sm-6 mb-2">
                            <label class="form-label">Descripción</label>
                            <textarea id="descripcion" name="descripcion" class="form-control" rows="3"
                                placeholder="Descripción del producto..."></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnAccion" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ESTRUCTURA HTML DEL MODAL -->
<div id="modalGaleria" class="modal fade" role="dialog" aria-labelledby="modal-galeria-title" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white">
                <h5 class="modal-title">
                    <i class="fas fa-images me-2"></i>Galería de Imágenes del Producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">

                <!-- Card de subida -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-cloud-upload-alt me-2"></i>Subir Imágenes</h6>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo BASE_URL . 'productos/galeriaImagenes'; ?>" class="dropzone"
                            id="frmImagenes">
                            <input type="hidden" id="idProducto" name="idProducto">
                            <div class="dz-message needsclick text-center">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5>Arrastra y suelta las imágenes aquí</h5>
                                <p class="text-muted">o haz clic para seleccionar archivos</p>
                                <small class="text-muted">Formatos permitidos: PNG, JPG, JPEG | Máximo 10
                                    archivos</small>
                            </div>
                        </form>
                        <div class="text-end mt-3">
                            <button class="btn btn-primary" type="button" id="btnProcesar">
                                <i class="fas fa-upload me-2"></i>Subir Imágenes
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Galería existente -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-image me-2"></i>Imágenes Actuales</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3" id="containerGaleria">
                            <!-- Las imágenes se cargarán aquí dinámicamente -->
                            <div class="col-12 text-center text-muted py-5" id="no-images-placeholder">
                                <i class="fas fa-image display-1 opacity-25"></i>
                                <p class="mt-3">No hay imágenes disponibles</p>
                                <small>Sube imágenes usando el área de arriba</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<div id="modalMantenimiento" class="modal fade" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tallas y Color</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form id="frmMantenimiento">
                    <div class="row">
                        <input type="hidden" id="id_producto" name="id_producto">

                        <div class="col-md-5">
                            <label>Codigo</label>
                            <div class="input-group mb-3">
                                <input id="codigo_producto" class="form-control" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <label>Producto</label>
                            <div class="input-group mb-3">
                                <input id="producto" class="form-control" type="text" readonly>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="">Talla <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <select id="talla" class="form-control select" name="talla">
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($data['tallas'] as $talla) { ?>
                                        <option value="<?php echo $talla['id']; ?>">
                                            <?php echo $talla['nombre'] . ' - ' . $talla['nombre_corto']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="">Color <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <select id="color" class="form-control select" name="color">
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($data['colores'] as $color) { ?>
                                        <option value="<?php echo $color['id']; ?>"><?php echo $color['nombre']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="">Almacen <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <select id="almacen" class="form-control select" name="almacen">
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($data['almacenes'] as $almacen) { ?>
                                        <option value="<?php echo $almacen['id']; ?>"><?php echo $almacen['nombre']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-end">
                                <button class="btn btn-outline-info" type="submit" id="btnAgregar"><i
                                        class="fas fa-plus-circle"></i> Agregar</button>
                            </div>
                        </div>
                    </div>

                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle" style="width: 100%;"
                        id="tblMantenimiento">
                        <thead>
                            <tr>
                                <th>Talla</th>
                                <th>Color</th>
                                <th>Cantidad</th>
                                <th>Almacen</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/admin/js/ckeditor.js'; ?>"></script>

<script src="<?php echo BASE_URL . 'assets/admin/plugins/select2/js/select2.min.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/productos.js'; ?>"></script>

</body>

</html>