<?php include_once 'Views/template/header-admin.php'; ?>

<style>
    /* Estilos para las bolitas de colores */
    .stock-color-circle {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid #ddd;
        vertical-align: middle;
        margin-right: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .stock-color-circle-split {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid #ddd;
        vertical-align: middle;
        margin-right: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .stock-color-name {
        vertical-align: middle;
        font-size: 13px;
    }
</style>

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-boxes"></i> Stock de Productos
            </h5>
            <div class="ms-auto">
                <a class="btn btn-danger" href="javascript:;"
                    onclick="window.open('<?php echo BASE_URL . 'productos/reporte_stock_pdf'; ?>?almacen=' + document.getElementById('almacen').value, '_blank')">
                    <i class="fas fa-file-pdf"></i> 
                </a>
            </div>
        </div>
        <hr>

        <div class="d-flex justify-content-center mb-3">
            <div class="form-group me-2">
                <label for="almacen">Almacén</label>
                <select id="almacen" name="almacen" class="form-select">
                    <option value="">TODOS</option>
                    <?php foreach ($data['almacenes'] as $almacen) { ?>
                        <option value="<?= $almacen['id'] ?>"><?= $almacen['nombre'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-striped" id="tblStock" style="width: 100%;">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>CÓDIGO</th>
                        <th>PRODUCTO</th>
                        <th>CATEGORÍA</th>
                        <th>MARCA</th>
                        <th>TALLA</th>
                        <th>COLOR</th>
                        <th>ALMACÉN</th>
                        <th>STOCK</th>
                        <th>P. COMPRA</th>
                        <th>P. VENTA</th>
                        <th>VALOR STOCK</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="text-end fw-bold">
                            <div>TOTAL PRODUCTOS:</div>
                            <div>TOTAL UNIDADES:</div>
                            <div><strong>VALOR TOTAL STOCK:</strong></div>
                        </td>
                        <td colspan="4" class="text-start fw-bold">
                            <div id="totalProductos">0 (items)</div>
                            <div id="totalStock">0</div>
                            <div><strong id="valorTotal">COP. 0.00</strong></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>
<script>

    let tblStock;

    document.addEventListener('DOMContentLoaded', function () {
        // Inicializar DataTable
        tblStock = $('#tblStock').DataTable({
            ajax: {
                url: base_url + 'productos/listar_stock',
                dataSrc: ''
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'codigo' },
                { data: 'producto' },
                { data: 'categoria' },
                { data: 'marca' },
                {
                    data: 'talla',
                    render: function (data) {
                        return `<span class="badge bg-info">${data}</span>`;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return generarColorHTML(row.codigo_color, row.color_secundario, row.color);
                    }
                },
                { data: 'almacen' },
                {
                    data: 'stock',
                    render: function (data) {
                        let clase = 'badge bg-success';
                        if (data <= 5) {
                            clase = 'badge bg-danger';
                        } else if (data <= 10) {
                            clase = 'badge bg-warning text-dark';
                        }
                        return `<span class="${clase}">${data}</span>`;
                    }
                },
                {
                    data: 'precio_compra',
                    render: function (data) {
                        return 'COP. ' + parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: 'precio_venta',
                    render: function (data) {
                        return 'COP. ' + parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: 'valor_stock',
                    render: function (data) {
                        return 'COP. ' + parseFloat(data).toFixed(2);
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            responsive: false,
            order: [[7, 'asc'], [2, 'asc']],
            drawCallback: function () {
                calcularTotales();
            }
        });

        // Filtrar por almacén
        $('#almacen').on('change', function () {
            const almacen = $(this).val();
            if (almacen === '') {
                tblStock.column(7).search('').draw();
            } else {
                const nombreAlmacen = $(this).find('option:selected').text();
                tblStock.column(7).search(nombreAlmacen).draw();
            }
            calcularTotales();
        });
    });

    function generarColorHTML(colorHexa, colorSecundario, nombreColor) {
        if (!colorHexa) {
            return '<span class="badge bg-secondary">Sin color</span>';
        }

        let colorHTML = '';
        if (colorSecundario && colorSecundario.trim() !== '' && colorSecundario !== 'NULL') {
            colorHTML = `<span class="stock-color-circle-split" style="background: linear-gradient(90deg, ${colorHexa} 50%, ${colorSecundario} 50%);" title="${nombreColor}"></span>`;
        } else {
            colorHTML = `<span class="stock-color-circle" style="background-color: ${colorHexa};" title="${nombreColor}"></span>`;
        }

        return `${colorHTML}<span class="stock-color-name">${nombreColor}</span>`;
    }

    function calcularTotales() {
        let totalProductos = 0;
        let totalStock = 0;
        let valorTotal = 0;

        tblStock.rows({ search: 'applied' }).every(function () {
            const data = this.data();
            totalProductos++;
            totalStock += parseInt(data.stock);
            valorTotal += parseFloat(data.valor_stock);
        });

        document.getElementById('totalProductos').textContent = totalProductos + ' (items)';
        document.getElementById('totalStock').textContent = totalStock;
        document.getElementById('valorTotal').textContent = 'COP. ' + valorTotal.toFixed(2);
    }
</script>
</body>

</html>