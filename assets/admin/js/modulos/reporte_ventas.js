let tblVentas;

document.addEventListener('DOMContentLoaded', function () {
    // Inicializar DataTable
    tblVentas = $('#tblVentas').DataTable({
        ajax: {
            url: base_url + 'reportes/listar_ventas',
            dataSrc: ''
        },
        columns: [
            { 
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'numero_venta' },
            { data: 'metodo' },
            { data: 'cliente' },
            { data: 'producto' },
            { data: 'cantidad' },
            { 
                data: 'precio_venta',
                render: function(data) {
                    return 'Bs. ' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'subtotal',
                render: function(data) {
                    return 'Bs. ' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'total_pedido',
                render: function(data) {
                    return 'Bs. ' + parseFloat(data).toFixed(2);
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
        order: [[9, 'desc']],
        drawCallback: function() {
            calcularTotales();
        }
    });

    // Filtrar por fechas
    $('#desde, #hasta').on('change', function() {
        filtrarPorFechas();
    });

    // Filtrar por método de pago
    $('#metodo_pago').on('change', function() {
        const metodo = $(this).val();
        tblVentas.column(2).search(metodo).draw();
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
                const fecha = data[9]; // Columna de fecha
                const fechaSinHora = fecha.split(' ')[0];
                
                return fechaSinHora >= desde && fechaSinHora <= hasta;
            }
        );
        tblVentas.draw();
        $.fn.dataTable.ext.search.pop();
        calcularTotales();
    }
}

function calcularTotales() {
    let totalProductos = 0;
    let totalCantidad = 0;
    let totalVentas = 0;

    tblVentas.rows({ search: 'applied' }).every(function() {
        const data = this.data();
        totalProductos++;
        totalCantidad += parseInt(data.cantidad);
        totalVentas += parseFloat(data.subtotal);
    });

    document.getElementById('totalProductos').textContent = totalProductos + ' (items)';
    document.getElementById('totalCantidad').textContent = totalCantidad;
    document.getElementById('totalVentas').textContent = 'Bs. ' + totalVentas.toFixed(2);
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