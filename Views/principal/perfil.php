<?php
include_once 'Views/template/header-principal.php'; ?>

<!-- Start Content -->
<div class="container py-5">
    <?php if ($data['verificar']['verify'] == 1) { ?>
        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Pago</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pendientes-tab" data-bs-toggle="tab" data-bs-target="#pendientes-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Pedidos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="productos-tab" data-bs-toggle="tab" data-bs-target="#productos-tab-pane" type="button" role="tab" aria-controls="productos-tab-pane" aria-selected="false">Productos</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card shadow-lg">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover align-middle" id="tableListaProductos">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Producto</th>
                                                <th>Atributos</th>
                                                <th>Stock</th>
                                                <th>Precio</th>
                                                <th>Cantidad</th>
                                                <th>SubTotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <h3 id="totalProducto"></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle float-end" href="#" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-user"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL . 'clientes/salir'; ?>"><i class="fa fa-times-circle"></i> Cerrar Sesión</a></li>
                            </ul>
                        </div>
                        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="metodos-tab" data-bs-toggle="tab" data-bs-target="#metodos-tab-pane" type="button" role="tab" aria-controls="metodos-tab-pane" aria-selected="true">Metodos de pago</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="perfil-tab" data-bs-toggle="tab" data-bs-target="#perfil-tab-pane" type="button" role="tab" aria-controls="perfil-tab-pane" aria-selected="false">Perfil</button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="metodos-tab-pane" role="tabpanel" aria-labelledby="metodos-tab" tabindex="0">
                                <div class="card shadow-lg">
                                    <div class="card-body text-center">
                                        <?php $perfil = (empty($_SESSION['perfilCliente']) || $_SESSION['perfilCliente'] == null) ? 'default.png' : $_SESSION['perfilCliente']; ?>
                                        <img class="img-thumbnail rounded-circle" src="<?php echo BASE_URL . 'assets/images/clientes/' . $perfil; ?>" alt="" width="100">
                                        <hr>
                                        <p><?php echo $_SESSION['nombreCliente']; ?></p>
                                        <p><i class="fa fa-envelope"></i> <?php echo $_SESSION['correoCliente']; ?></p>
                                        <div class="accordion" id="accordionExample">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingOne">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        <i class="fa fa-paypal mx-2"></i> Paypal
                                                    </button>
                                                </h2>
                                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div id="paypal-button-container"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                        <i class="fa fa-credit-card mx-2"></i> Mercado Pago
                                                    </button>
                                                </h2>
                                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div id="wallet_container"></div>
                                                        <?php
                                                        // if (!empty($_SESSION['productos'])) {
                                                        //     // Agrega credenciales
                                                        //     MercadoPago\SDK::setAccessToken(ACCESS_TOKEN);
                                                        //     $preference = new MercadoPago\Preference();

                                                        //     $datos = array();

                                                        //     foreach ($_SESSION['productos'] as $producto) {
                                                        //         $item = new MercadoPago\Item();
                                                        //         // Crea un ítem en la preferencia
                                                        //         $item->id = $producto['id'];
                                                        //         $item->title = $producto['nombre'] . ' - ' . $producto['atributoMP'];
                                                        //         $item->currency_id = "USD";
                                                        //         $item->quantity = $producto['cantidad'];
                                                        //         $item->unit_price = $producto['precio'];
                                                        //         array_push($datos, $item);
                                                        //     }

                                                        //     $preference->items = $datos;

                                                        //     $preference->back_urls = array(
                                                        //         "success" => BASE_URL . 'clientes/success',
                                                        //         "failure" => BASE_URL . 'clientes',
                                                        //         "pending" => BASE_URL . 'clientes'
                                                        //     );
                                                        //     $preference->auto_return = "approved";
                                                        //     $preference->binary_mode = true;

                                                        //     $preference->save();
                                                        // }
                                                        ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="perfil-tab-pane" role="tabpanel" aria-labelledby="perfil-tab" tabindex="0">
                                <div class="card">
                                    <form autocomplete="off" id="frmDatos">
                                        <div class="card-body">
                                            <div class="form-group mb-2">
                                                <label for="nomCliente"><i class="fa fa-list"></i> Nombres <span class="text-danger">*</span></label>
                                                <input id="nomCliente" class="form-control" type="text" name="nombre" value="<?php echo $data['verificar']['nombre']; ?>" placeholder="Nombres">
                                            </div>
                                            <div class="form-group mb-2">
                                                <label for="apeCliente"><i class="fa fa-list-alt"></i> Apellidos <span class="text-danger">*</span></label>
                                                <input id="apeCliente" class="form-control" type="text" name="apellidos" value="<?php echo $data['verificar']['apellido']; ?>" placeholder="Apellidos">
                                            </div>
                                            <div class="form-group mb-2">
                                                <label for="corCliente"><i class="fa fa-envelope"></i> Correo <span class="text-danger">*</span></label>
                                                <input id="corCliente" class="form-control" type="text" name="correo" value="<?php echo $data['verificar']['correo']; ?>" placeholder="Correo">
                                            </div>
                                            <div class="form-group mb-2">
                                                <label for="telCliente"><i class="fa fa-phone"></i> Telefono <span class="text-danger">*</span></label>
                                                <input id="telCliente" class="form-control" type="text" name="telefono" value="<?php echo $data['verificar']['telefono']; ?>" placeholder="Télefono">
                                            </div>
                                            <div class="form-group mb-2">
                                                <label for="dirCliente"><i class="fa fa-home"></i> Dirección <span class="text-danger">*</span></label>
                                                <textarea id="dirCliente" class="form-control" name="direccion" rows="3" placeholder="Dirección"><?php echo $data['verificar']['direccion']; ?></textarea>
                                            </div>
                                            <div class="form-group mb-2">
                                                <label for="fotoCliente"><i class="fa fa-image"></i> Foto</label>
                                                <input id="fotoCliente" class="form-control" type="file" name="fotoCliente">
                                            </div>
                                        </div>
                                        <div class="card-footer text-end">
                                            <button class="btn btn-primary" type="submit">Actualizar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pendientes-tab-pane" role="tabpanel" aria-labelledby="pendientes-tab" tabindex="0">
                <div class="col-12">
                    <div class="card shadow-lg">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover" id="tblPendientes" style="width: 100%;">
                                    <thead class="bg-dark text-white">
                                        <tr>
                                            <th>Id</th>
                                            <th>Transacción</th>
                                            <th>Monto</th>
                                            <th>Método</th>
                                            <th>Fecha</th>
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
            <div class="tab-pane fade" id="productos-tab-pane" role="tabpanel" aria-labelledby="productos-tab" tabindex="0">
                <div class="col-12">
                    <div class="card shadow-lg">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover" id="tblProductos" style="width: 100%;">
                                    <thead class="bg-dark text-white">
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
                        <div class="card-footer">
                            <div class="form-group mb-3">
                                <label for="comentario"><i class="fa fa-envelope"></i> Comentario</label>
                                <textarea id="comentario" class="form-control" name="comentario" rows="3" placeholder="Dejanos un comentario"><?php echo (empty($data['testimonio'])) ? '' : $data['testimonio']['mensaje']; ?></textarea>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-primary" type="button" id="btnTestimonio">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="alert alert-danger text-center" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
            </svg>
            <div class="h3">
                VERIFICA TU CORREO ELECTRONICO
            </div>
        </div>
    <?php } ?>
</div>
<!-- End Content -->

<div id="modalPedido" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Estado del Pedido</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-lg-4 pb-5">
                        <div class="h-100 py-5 border border-3 border-success shadow" id="estadoEnviado">
                            <div class="h1 text-util text-center"><i class="fa fa-truck fa-lg"></i></div>
                            <h2 class="h5 mt-4 text-center">Pendiente</h2>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 pb-5">
                        <div class="h-100 py-5 border border-3 border-success shadow" id="estadoProceso">
                            <div class="h1 text-util text-center"><i class="fa fa-percent"></i></div>
                            <h2 class="h5 mt-4 text-center">Proceso</h2>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 pb-5">
                        <div class="h-100 py-5 border border-3 border-success shadow" id="estadoCompletado">
                            <div class="h1 text-util text-center"><i class="fa fa-check"></i></div>
                            <h2 class="h5 mt-4 text-center">Completado</h2>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-borderer table-striped table-hover align-middle" id="tablePedidos" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Atributo</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>SubTotal</th>
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
    </div>
</div>

<?php include_once 'Views/template/footer-principal.php'; ?>

<script type="text/javascript" src="<?php echo BASE_URL . 'assets/DataTables/datatables.min.js'; ?>"></script>

<script src="<?php echo BASE_URL; ?>assets/js/es-ES.js"></script>

<script src="<?php echo BASE_URL . 'assets/admin/js/ckeditor.js'; ?>"></script>

<script>
    <?php if (!empty($_GET['message'])) {
        if ($_GET['message'] == md5(TITLE)) { ?>
            localStorage.removeItem('listaCarrito');
            window.location = base_url + 'clientes';
    <?php }
    } ?>
    let editorDireccion;
    //Inicializar un Editor
    ClassicEditor
        .create(document.querySelector('#dirCliente'), {
            toolbar: {
                items: [
                    'selectAll', '|',
                    'bold', 'italic',
                    'alignment',
                    'link'
                ],
                shouldNotGroupWhenFull: true
            },
        })
        .then(editor => {
            editorDireccion = editor
        })
        .catch(error => {
            console.error(error);
        });
    <?php
    if (!empty($_SESSION['productos'])) { ?>
        ///mercado pago
        const mp = new MercadoPago('<?php echo PUBLIC_KEY; ?>');
        const bricksBuilder = mp.bricks();

        mp.bricks().create("wallet", "wallet_container", {
            initialization: {
                preferenceId: "<?php echo $data['preferenceid']; ?>",
            },
        });
    <?php } ?>
</script>

<script src="<?php echo BASE_URL . 'assets/js/clientes.js'; ?>"></script>

<!-- End Script -->
</body>

</html>