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
                            onclick="window.open('<?php echo BASE_URL . 'reportes/reporte_compras_pdf'; ?>?desde=' + document.getElementById('desde').value + '&hasta=' + document.getElementById('hasta').value, '_blank')">
                            <i class="fas fa-file-pdf text-danger"></i> Reporte PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:;"
                            onclick="window.open('<?php echo BASE_URL . 'reportes/reporte_compras_excel'; ?>?desde=' + document.getElementById('desde').value + '&hasta=' + document.getElementById('hasta').value, '_blank')">
                            <i class="fas fa-file-excel text-success"></i> Reporte Excel
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <h5 class="card-title text-center"><i class="fas fa-shopping-cart"></i> Reporte de Compras</h5>
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
                <label for="proveedor">Proveedor</label>
                <select id="proveedor" name="proveedor" class="form-select">
                    <option value="">TODOS</option>
                    <?php foreach ($data['proveedores'] as $proveedor) { ?>
                        <option value="<?= $proveedor['id'] ?>"><?= $proveedor['nombre'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-striped" id="tblCompras" style="width: 100%;">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>N° COMPRA</th>
                        <th>COMPROBANTE</th>
                        <th>PROVEEDOR</th>
                        <th>ALMACÉN</th>
                        <th>PRODUCTO</th>
                        <th>CANTIDAD</th>
                        <th>PRECIO</th>
                        <th>DESCUENTO</th>
                        <th>SUBTOTAL</th>
                        <th>FECHA</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10" class="text-end fw-bold">
                            <div>TOTAL PRODUCTOS:</div>
                            <div>TOTAL CANTIDAD:</div>
                            <div><strong>TOTAL COMPRAS:</strong></div>
                        </td>
                        <td colspan="1" class="text-start fw-bold">
                            <div id="totalProductos">0 (items)</div>
                            <div id="totalCantidad">0</div>
                            <div><strong id="totalCompras">Bs. 0.00</strong></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/admin/js/modulos/reporte_compras.js'; ?>"></script>

</body>

</html>