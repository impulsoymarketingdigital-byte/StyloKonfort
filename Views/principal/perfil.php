<?php
include_once 'Views/template/header-principal.php'; ?>

<!-- Start Content -->
<div class="container-fluid py-5">
    <?php if ($data['verificar']['verify'] == 1) { ?>
        <!-- Barra superior con info del usuario -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <?php $perfil = (empty($_SESSION['perfilCliente']) || $_SESSION['perfilCliente'] == null) ? 'default.png' : $_SESSION['perfilCliente']; ?>
                                <img class="rounded-circle me-3" src="<?php echo BASE_URL . 'assets/images/clientes/' . $perfil; ?>" alt="" width="60" height="60" style="object-fit: cover;">
                                <div>
                                    <h5 class="mb-0"><?php echo $_SESSION['nombreCliente'] . ' ' . $_SESSION['apellidoCliente']; ?></h5>
                                    <small class="text-muted"><i class="fa fa-envelope"></i> <?php echo $_SESSION['correoCliente']; ?></small>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-outline-primary me-2" type="button" data-bs-toggle="modal" data-bs-target="#modalPerfil">
                                    <i class="fa fa-user"></i> Mi Perfil
                                </button>
                                <a href="<?php echo BASE_URL . 'clientes/salir'; ?>" class="btn btn-outline-danger">
                                    <i class="fa fa-sign-out-alt"></i> Cerrar Sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs de navegación -->
        <ul class="nav nav-pills mb-4 justify-content-center" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-4" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab">
                    <i class="fa fa-shopping-cart"></i> Mi Carrito
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4" id="pendientes-tab" data-bs-toggle="tab" data-bs-target="#pendientes-tab-pane" type="button" role="tab">
                    <i class="fa fa-clipboard-list"></i> Mis Pedidos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4" id="productos-tab" data-bs-toggle="tab" data-bs-target="#productos-tab-pane" type="button" role="tab">
                    <i class="fa fa-star"></i> Calificaciones
                </button>
            </li>
        </ul>

        <!-- Contenido de las tabs -->
        <div class="tab-content" id="myTabContent">
            <!-- TAB CARRITO -->
            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <div class="card shadow-lg border-0">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0"><i class="fa fa-shopping-cart"></i> Carrito de Compras</h4>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="tableListaProductos">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" width="100">Imagen</th>
                                                <th>Producto</th>
                                                <th>Atributos</th>
                                                <th class="text-center">Stock</th>
                                                <th class="text-end">Precio</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-end">SubTotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h3 class="mb-0 text-primary" id="totalProducto"></h3>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button class="btn btn-success btn-lg px-5" type="button" id="btnFinalizarPedido">
                                            <i class="fa fa-check-circle"></i> Finalizar Pedido
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB PEDIDOS -->
            <div class="tab-pane fade" id="pendientes-tab-pane" role="tabpanel" aria-labelledby="pendientes-tab" tabindex="0">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <div class="card shadow-lg border-0">
                            <div class="card-header bg-info text-white">
                                <h4 class="mb-0"><i class="fa fa-clipboard-list"></i> Mis Pedidos</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tblPendientes" style="width: 100%;">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Id</th>
                                                <th>Transacción</th>
                                                <th>Monto</th>
                                                <th>Método</th>
                                                <th>Fecha</th>
                                                <th class="text-center">Acciones</th>
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
            </div>

            <!-- TAB CALIFICACIONES -->
            <div class="tab-pane fade" id="productos-tab-pane" role="tabpanel" aria-labelledby="productos-tab" tabindex="0">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <div class="card shadow-lg border-0">
                            <div class="card-header bg-warning text-dark">
                                <h4 class="mb-0"><i class="fa fa-star"></i> Calificaciones de Productos</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tblProductos" style="width: 100%;">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Producto</th>
                                                <th>Precio</th>
                                                <th>Cantidad</th>
                                                <th>Calificación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <h5 class="mb-3"><i class="fa fa-comments"></i> Déjanos tu Testimonio</h5>
                                <div class="form-group mb-3">
                                    <textarea id="comentario" class="form-control" name="comentario" rows="4" placeholder="Cuéntanos tu experiencia..."><?php echo (empty($data['testimonio'])) ? '' : $data['testimonio']['mensaje']; ?></textarea>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-primary px-4" type="button" id="btnTestimonio">
                                        <i class="fa fa-save"></i> Guardar Testimonio
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php } else { ?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert alert-danger text-center py-5 shadow" role="alert">
                    <i class="fa fa-exclamation-triangle fa-3x mb-3"></i>
                    <h3 class="mb-3">VERIFICA TU CORREO ELECTRÓNICO</h3>
                    <p>Por favor revisa tu bandeja de entrada y confirma tu cuenta para continuar.</p>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<!-- Modal Perfil -->
<div class="modal fade" id="modalPerfil" tabindex="-1" aria-labelledby="modalPerfilLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalPerfilLabel">
                    <i class="fa fa-user-circle"></i> Mi Perfil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form autocomplete="off" id="frmDatos">
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <?php $perfil = (empty($_SESSION['perfilCliente']) || $_SESSION['perfilCliente'] == null) ? 'default.png' : $_SESSION['perfilCliente']; ?>
                        <img class="img-thumbnail rounded-circle" src="<?php echo BASE_URL . 'assets/images/clientes/' . $perfil; ?>" alt="" width="150" height="150" style="object-fit: cover;">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nomCliente" class="form-label">
                                    <i class="fa fa-user"></i> Nombres <span class="text-danger">*</span>
                                </label>
                                <input id="nomCliente" class="form-control" type="text" name="nombre" value="<?php echo $data['verificar']['nombre']; ?>" placeholder="Nombres">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="apeCliente" class="form-label">
                                    <i class="fa fa-user"></i> Apellidos <span class="text-danger">*</span>
                                </label>
                                <input id="apeCliente" class="form-control" type="text" name="apellidos" value="<?php echo $data['verificar']['apellido']; ?>" placeholder="Apellidos">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="corCliente" class="form-label">
                                    <i class="fa fa-envelope"></i> Correo <span class="text-danger">*</span>
                                </label>
                                <input id="corCliente" class="form-control" type="email" name="correo" value="<?php echo $data['verificar']['correo']; ?>" placeholder="Correo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="telCliente" class="form-label">
                                    <i class="fa fa-phone"></i> Teléfono <span class="text-danger">*</span>
                                </label>
                                <input id="telCliente" class="form-control" type="text" name="telefono" value="<?php echo $data['verificar']['telefono']; ?>" placeholder="Teléfono">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="dirCliente" class="form-label">
                            <i class="fa fa-map-marker-alt"></i> Dirección <span class="text-danger">*</span>
                        </label>
                        <textarea id="dirCliente" class="form-control" name="direccion" rows="3" placeholder="Dirección completa"><?php echo $data['verificar']['direccion']; ?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="fotoCliente" class="form-label">
                            <i class="fa fa-camera"></i> Cambiar Foto de Perfil
                        </label>
                        <input id="fotoCliente" class="form-control" type="file" name="fotoCliente" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG. Tamaño máximo: 2MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Estado Pedido -->
<div id="modalPedido" class="modal fade" tabindex="-1" aria-labelledby="modalPedidoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalPedidoLabel">
                    <i class="fa fa-box"></i> Estado del Pedido
                </h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-3" id="estadoEnviado">
                            <div class="card-body text-center py-4">
                                <div class="text-secondary mb-3">
                                    <i class="fa fa-truck fa-3x"></i>
                                </div>
                                <h5 class="card-title">Pendiente</h5>
                                <p class="card-text text-muted">Tu pedido está en espera</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-3" id="estadoProceso">
                            <div class="card-body text-center py-4">
                                <div class="text-warning mb-3">
                                    <i class="fa fa-spinner fa-3x"></i>
                                </div>
                                <h5 class="card-title">En Proceso</h5>
                                <p class="card-text text-muted">Preparando tu pedido</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-3" id="estadoCompletado">
                            <div class="card-body text-center py-4">
                                <div class="text-success mb-3">
                                    <i class="fa fa-check-circle fa-3x"></i>
                                </div>
                                <h5 class="card-title">Completado</h5>
                                <p class="card-text text-muted">Pedido entregado</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tablePedidos" style="width: 100%;">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Atributo</th>
                                <th class="text-end">Precio</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">SubTotal</th>
                                <th class="text-center">Acciones</th>
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

<?php include_once 'Views/template/footer-principal.php'; ?>

<script type="text/javascript" src="<?php echo BASE_URL . 'assets/DataTables/datatables.min.js'; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/es-ES.js"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/ckeditor.js'; ?>"></script>

<script>
    let editorDireccion;
    // Inicializar Editor para dirección
    ClassicEditor
        .create(document.querySelector('#dirCliente'), {
            toolbar: {
                items: ['selectAll', '|', 'bold', 'italic', 'alignment', 'link'],
                shouldNotGroupWhenFull: true
            },
        })
        .then(editor => {
            editorDireccion = editor
        })
        .catch(error => {
            console.error(error);
        });
</script>

<script src="<?php echo BASE_URL . 'assets/js/clientes.js'; ?>"></script>

<style>
    .nav-pills .nav-link {
        border: none;
        border-radius: 50px;
        transition: all 0.3s ease;
    }
    .nav-pills .nav-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    #estadoEnviado.border-success,
    #estadoProceso.border-success,
    #estadoCompletado.border-success {
        border-color: #28a745 !important;
        background-color: #d4edda;
    }
</style>
</body>
</html>