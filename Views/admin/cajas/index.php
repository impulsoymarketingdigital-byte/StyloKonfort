<?php include_once 'Views/template/header-admin.php'; ?>

<button class="btn btn-success btnIcon" type="button" id="btnNuevo">
    <i class="bx bx-plus-circle"></i> APERTURA CAJA
</button>
<button class="btn btn-danger btnIcon" type="button" id="btnCierre">
    <i class="bx bx-x-circle"></i> CIERRE CAJA
</button>
<button class="btn btn-secondary btnIcon" type="button" id="btnMovimiento">
    <i class="bx bx-transfer"></i> MOVIMIENTO CAJA
</button>

<div class="card mt-3">
    <div class="card-body">
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active mt-2" id="nav-clientes" role="tabpanel"
                aria-labelledby="nav-clientes-tab" tabindex="0">
                <h5 class="card-title text-center"><i class="fas fa-cash-register"></i> Movimientos de Caja</h5>
                <hr>
                <div class="table-responsive">
                    <table class="table align-middle" id="tbl" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>FECHA</th>
                                <th>USUARIO</th>
                                <th>TIPO</th>
                                <th>TRANSACCIÓN</th>
                                <th>DETALLE</th>
                                <th>INGRESO</th>
                                <th>EGRESO</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6"></th>
                                <th id="totalIngreso"></th>
                                <th id="totalEgreso"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL APERTURA DE CAJA -->
<div id="theModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">REGISTRAR APERTURA DE CAJA</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmRegistro" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" id="id" name="id">
                    <div class="row mb-2 p-2">
                        <div class="col-lg-12 col-sm-6 mb-2">
                            <label for="monto_inicial">Monto Inicial</label>
                            <div class="position-relative input-icon">
                                <input type="text" class="form-control text-end" id="monto_inicial" name="monto_inicial"
                                    placeholder="0.00" inputmode="decimal" maxlength="10"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '')">
                                <span class="position-absolute top-50 translate-middle-y">COP</span>
                            </div>
                            <span id="errorMontoInicial" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnAccion" class="btn btn-success">Abrir Caja</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL MOVIMIENTO DE CAJA -->
<div id="movementCashBox" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">MOVIMIENTO DE CAJA</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmMovimiento" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" id="id" name="id">
                    <div class="row p-2">
                        <div class="col-md-6 col-sm-6 mb-2">
                            <label>Tipo</label>
                            <div class="input-group">
                                <select name="type" class="form-select" required>
                                    <option value="" selected>Seleccionar</option>
                                    <option value="INGRESO">INGRESO</option>
                                    <option value="EGRESO">EGRESO</option>
                                </select>
                            </div>
                            <span class="text-danger" id="errorType"></span>
                        </div>
                        <div class="col-lg-6 col-sm-6 mb-2">
                            <label>Transacción</label>
                            <div class="input-group">
                                <input type="text" name="transaction" value="MOVIMIENTO DE CAJA"
                                    class="form-control" maxlength="30" readonly>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-6 mb-2">
                            <label>Descripción</label>
                            <div class="input-group">
                                <textarea class="form-control" name="description" rows="2"
                                    placeholder="Descripción del movimiento" required></textarea>
                            </div>
                            <span class="text-danger" id="errorDescription"></span>
                        </div>
                        <div class="col-md-6 col-sm-6 mb-2">
                            <label>Monto</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="amount" maxlength="10"
                                    inputmode="decimal" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                    placeholder="0.00" required>
                            </div>
                            <span class="text-danger" id="errorAmount"></span>
                        </div>
                        <div class="col-md-6 col-sm-6 mb-2">
                            <label>Saldo Actual</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="0.00" id="balance" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-secondary">
                            <span>Guardar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CIERRE DE CAJA SIMPLIFICADO -->
<div id="closeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CIERRE DE CAJA</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4">
                <form id="frmCierre" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" id="id_cierre" name="id_cierre">
                    
                    <!-- Saldo Inicial -->
                    <h5 class="card-total mb-3">
                        <i class="bx bx-wallet"></i> Monto Inicial: COP. <span id="montoInicial">0.00</span>
                    </h5>
                    
                    <!-- Resumen simplificado -->
                    <div class="row">
                        <!-- Ingresos -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="fw-bold text-success"><i class="bx bx-up-arrow-alt"></i> INGRESOS</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span><i class="bx bx-cart"></i> Total Ventas</span>
                                            <span class="fw-bold">COP. <span id="totalVentas">0.00</span></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Egresos -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="fw-bold text-danger"><i class="bx bx-down-arrow-alt"></i> EGRESOS</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span><i class="bx bxs-truck"></i> Total Compras</span>
                                            <span class="fw-bold">COP. <span id="totalCompras">0.00</span></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="card-balance mt-3 mb-3">
                        <i class="bx bx-wallet"></i> Saldo Final: COP. <span id="saldoFinal">0.00</span>
                    </h5>

                    <div class="row">
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label>Saldo Sistema</label>
                            <div class="position-relative input-icon">
                                <input type="text" id="saldoInput" class="form-control text-end" value="0.00"
                                    placeholder="0.00" readonly>
                                <span class="position-absolute top-50 translate-middle-y">COP</span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label>Físico en Caja</label>
                            <div class="position-relative input-icon">
                                <input type="text" id="fisicoInput" class="form-control text-end" placeholder="0.00"
                                    maxlength="10"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, ''); calcularDiferencia();">
                                <span class="position-absolute top-50 translate-middle-y">COP</span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label>Diferencia</label>
                            <div class="position-relative input-icon">
                                <input type="text" id="diferenciaInput" class="form-control text-end" placeholder="0.00"
                                    readonly>
                                <span class="position-absolute top-50 translate-middle-y">COP</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnCerrarCaja" class="btn btn-danger">Cerrar Caja</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/cajas.js'; ?>"></script>

</body>
</html>