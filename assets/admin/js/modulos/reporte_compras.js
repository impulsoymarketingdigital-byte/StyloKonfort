let tblCompras;

document.addEventListener('DOMContentLoaded', function () {
    // Inicializar DataTable
    tblCompras = $('#tblCompras').DataTable({
        ajax: {
            url: base_url + 'reportes/listar_compras',
            dataSrc: ''
        },
        columns: [
            { 
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'numero_compra' },
            { data: 'tipo_comprobante' },
            { data: 'proveedor' },
            { data: 'almacen' },
            { data: 'producto' },
            { data: 'cantidad' },
            { 
                data: 'precio_compra',
                render: function(data) {
                    return 'COP. ' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'descuento',
                render: function(data) {
                    return 'COP. ' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'subtotal',
                render: function(data) {
                    return 'COP. ' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'fecha',
                render: function(data) {
                    return formatearFecha(data);
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        responsive: false,
        order: [[10, 'desc']],
        drawCallback: function() {
            calcularTotales();
        }
    });

    // Filtrar por fechas
    $('#desde, #hasta').on('change', function() {
        filtrarPorFechas();
    });

    // Filtrar por proveedor
    $('#proveedor').on('change', function() {
        const proveedor = $(this).val();
        if (proveedor === '') {
            // Si selecciona "Seleccionar", mostrar todos
            tblCompras.column(3).search('').draw();
        } else {
            // Buscar por el nombre del proveedor en la columna 3
            const nombreProveedor = $(this).find('option:selected').text();
            tblCompras.column(3).search(nombreProveedor).draw();
        }
        calcularTotales();
    });

    // Establecer fecha actual
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('hasta').value = hoy;
    
    // Fecha de hace 30 días
    const hace30Dias = new Date();
    hace30Dias.setDate(hace30Dias.getDate() - 30);
    document.getElementById('desde').value = hace30Dias.toISOString().split('T')[0];
});

function filtrarPorFechas() {
    const desde = document.getElementById('desde').value;
    const hasta = document.getElementById('hasta').value;

    if (desde && hasta) {
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                const fecha = data[10]; // Columna de fecha
                const fechaSinHora = fecha.split(' ')[0];
                
                return fechaSinHora >= desde && fechaSinHora <= hasta;
            }
        );
        tblCompras.draw();
        $.fn.dataTable.ext.search.pop();
        calcularTotales();
    }
}

function calcularTotales() {
    let totalProductos = 0;
    let totalCantidad = 0;
    let totalCompras = 0;

    tblCompras.rows({ search: 'applied' }).every(function() {
        const data = this.data();
        totalProductos++;
        totalCantidad += parseInt(data.cantidad);
        totalCompras += parseFloat(data.subtotal);
    });

    document.getElementById('totalProductos').textContent = totalProductos + ' (items)';
    document.getElementById('totalCantidad').textContent = totalCantidad;
    document.getElementById('totalCompras').textContent = 'COP. ' + totalCompras.toFixed(2);
}

function formatearFecha(fecha) {
    const date = new Date(fecha);
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0');
    const año = date.getFullYear();
    const horas = String(date.getHours()).padStart(2, '0');
    const minutos = String(date.getMinutes()).padStart(2, '0');
    
    return `${dia}/${mes}/${año} ${horas}:${minutos}`;
}