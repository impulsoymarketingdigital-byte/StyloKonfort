<?php include_once 'Views/template/header-admin.php'; ?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title text-center"><i class="fas fa-cash-register"></i> Procesar Pagos</h5>
        <hr>
        
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input class="form-control" type="text" id="buscarPedido" placeholder="Buscar por N° Pedido o Cliente" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="tblPedidosPendientes" style="width: 100%;">
                <thead>
                    <tr>
                        <th>N° Pedido</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Procesar Pago -->
<div id="modalProcesarPago" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-money-bill-wave"></i> Procesar Pago - Pedido #<span id="numPedidoPago"></span></h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-user"></i> Información del Cliente</h6>
                                <p><strong>Nombre:</strong> <span id="pagoCliente"></span></p>
                                <p><strong>Teléfono:</strong> <span id="pagoTelefono"></span></p>
                                <p><strong>Dirección:</strong> <span id="pagoDireccion"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-info-circle"></i> Información del Pedido</h6>
                                <p><strong>Fecha:</strong> <span id="pagoFecha"></span></p>
                                <p><strong>Vendedor:</strong> <span id="pagoVendedor"></span></p>
                                <p><strong>Método:</strong> <span id="pagoMetodo"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <h6><i class="fas fa-shopping-cart"></i> Productos</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Talla</th>
                                <th>Color</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="pagoProductos"></tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h4 class="text-end mb-3">
                                    <strong>TOTAL A PAGAR:</strong><br>
                                    <span class="text-primary" id="pagoTotal">COP 0.00</span>
                                </h4>
                                
                                <div class="mb-3">
                                    <label for="montoPagado" class="form-label"><strong>Monto Recibido:</strong></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">COP</span>
                                        <input type="number" class="form-control" id="montoPagado" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Cambio:</strong></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">COP</span>
                                        <input type="text" class="form-control bg-white" id="cambioDevolver" readonly value="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-lg" id="btnConfirmarPago">
                    <i class="fas fa-check"></i> Confirmar Pago
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalle -->
<div id="modalDetallePedido" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle Pedido #<span id="numPedidoDetalle"></span></h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <p><strong>Cliente:</strong> <span id="detCliente"></span></p>
                        <p><strong>Teléfono:</strong> <span id="detTelefono"></span></p>
                    </div>
                    <div class="col-6">
                        <p><strong>Fecha:</strong> <span id="detFecha"></span></p>
                        <p><strong>Vendedor:</strong> <span id="detVendedor"></span></p>
                    </div>
                </div>

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Talla</th>
                            <th>Color</th>
                            <th>Cant</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="detProductos"></tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="5" class="text-end">TOTAL:</td>
                            <td id="detTotal"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>
<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/procesar_pagos.js'; ?>"></script>