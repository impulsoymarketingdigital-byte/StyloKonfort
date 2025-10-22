<?php include_once 'Views/template/header-admin.php'; ?>

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div></div>
            <div class="dropdown ms-auto">
                <a class="dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
                    <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="javascript:;"
                            onclick="window.open('<?php echo BASE_URL . 'reportes/reporte_ventas_pdf'; ?>?desde=' + document.getElementById('desde').value + '&hasta=' + document.getElementById('hasta').value, '_blank')">
                            <i class="fas fa-file-pdf text-danger"></i> Reporte PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:;"
                            onclick="window.open('<?php echo BASE_URL . 'reportes/reporte_ventas_excel'; ?>?desde=' + document.getElementById('desde').value + '&hasta=' + document.getElementById('hasta').value, '_blank')">
                            <i class="fas fa-file-excel text-success"></i> Reporte Excel
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <h5 class="card-title text-center"><i class="fas fa-chart-line"></i> Reporte de Ventas</h5>
        <hr>
        <div class="d-flex justify-content-center mb-3">
            <div class="form-group me-2">
                <label for="desde">Desde</label>
                <input id="desde" class="form-control" type="date">
            </div>
            <div class="form-group me-2">
                <label for="hasta">Hasta</label>
                <input id="hasta" class="form-control" type="date">
            </div>
            <div class="form-group me-2">
                <label for="metodo_pago">Método de Pago</label>
                <select id="metodo_pago" class="form-control">
                    <option value="">TODOS</option>
                    <option value="VENTA DIRECTA">VENTA DIRECTA</option>
                    <option value="LLEVAR">LLEVAR</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-striped" id="tblVentas" style="width: 100%;">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>N° VENTA</th>
                        <th>MÉTODO PAGO</th>
                        <th>CLIENTE</th>
                        <th>PRODUCTO</th>
                        <th>CANTIDAD</th>
                        <th>PRECIO UNIT.</th>
                        <th>SUBTOTAL</th>
                        <th>TOTAL PEDIDO</th>
                        <th>FECHA</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" class="text-end fw-bold">
                            <div>TOTAL PRODUCTOS:</div>
                            <div>TOTAL CANTIDAD:</div>
                            <div><strong>TOTAL VENTAS:</strong></div>
                        </td>
                        <td colspan="1" class="text-start fw-bold">
                            <div id="totalProductos">0 (items)</div>
                            <div id="totalCantidad">0</div>
                            <div><strong id="totalVentas">Bs. 0.00</strong></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/reporte_ventas.js'; ?>"></script>

</body>
</html>