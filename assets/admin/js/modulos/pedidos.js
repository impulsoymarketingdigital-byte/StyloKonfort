let tblPendientes, tblFinalizados, tblProceso;
const myModal = new bootstrap.Modal(document.getElementById("modalPedidos"));

document.addEventListener("DOMContentLoaded", function() {
    tblPendientes = $("#tblPendientes").DataTable({
        ajax: {
            url: base_url + "pedidos/listarPedidos",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "fecha" },
            { data: "id_transaccion" },
            { data: "monto" },
            { data: "estado" },
            { data: "email" },
            { data: "nombre" },
            { data: "apellido" },
            { data: "direccion" },
            { data: "accion" },
        ],
        responsive: false,
        language,
        dom,
        buttons,
    });
    
    tblProceso = $("#tblProceso").DataTable({
        ajax: {
            url: base_url + "pedidos/listarProceso",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "fecha" },
            { data: "id_transaccion" },
            { data: "monto" },
            { data: "estado" },
            { data: "email" },
            { data: "nombre" },
            { data: "apellido" },
            { data: "direccion" },
            { data: "accion" },
        ],
        responsive: false,
        language,
        dom,
        buttons,
    });
    
    tblFinalizados = $("#tblFinalizados").DataTable({
        ajax: {
            url: base_url + "pedidos/listarFinalizados",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "fecha" },
            { data: "id_transaccion" },
            { data: "monto" },
            { data: "estado" },
            { data: "email" },
            { data: "nombre" },
            { data: "apellido" },
            { data: "direccion" },
            { data: "accion" },
        ],
        responsive: false,
        language,
        dom,
        buttons,
    });
    
    $.datetimepicker.setLocale('es');
    
    // Filtro rango de fechas
    desde.addEventListener('blur', function () {
        tblPendientes.draw();
        tblProceso.draw();
        tblFinalizados.draw();
    })
    
    hasta.addEventListener('blur', function () {
        tblPendientes.draw();
        tblProceso.draw();
        tblFinalizados.draw();
    })
    
    // Agregar filtro personalizado solo UNA VEZ
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var FilterStart = desde.value;
            var FilterEnd = hasta.value;
            var DataTableDate = data[1].trim();
            
            if (FilterStart == '' || FilterEnd == '') {
                return true;
            }
            
            if (DataTableDate >= FilterStart && DataTableDate <= FilterEnd) {
                return true;
            } else {
                return false;
            }
        }
    );
});

function cambiarProceso(idPedido, proceso) {
    let mensaje = '';
    
    switch(proceso) {
        case 2:
            mensaje = '¿Pasar este pedido a "En Proceso"?';
            break;
        case 3:
            mensaje = '¿Marcar como "Entregado"? (Se descontará el stock)';
            break;
        default:
            mensaje = '¿Está seguro de cambiar el estado?';
    }
    
    Swal.fire({
        title: "¿Confirmar acción?",
        text: mensaje,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, cambiar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "pedidos/update/" + idPedido + "/" + proceso;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    if (res.icono == "success") {
                        tblPendientes.ajax.reload();
                        tblProceso.ajax.reload();
                        tblFinalizados.ajax.reload();
                    }
                    alertas(res.msg.toUpperCase(), res.icono);
                }
            };
        }
    });
}

function verPedido(idPedido) {
    const url = base_url + "pedidos/verPedido/" + idPedido;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = '';
            res.productos.forEach(row => {
                let subTotal = parseFloat(row.precio) * parseInt(row.cantidad);
                html += `<tr>
                    <td>${row.producto}</td>
                    <td>${row.atributos}</td>
                    <td><span class="badge bg-warning">${res.moneda + ' ' + row.precio}</span></td>
                    <td><span class="badge bg-primary">${row.cantidad}</span></td>
                    <td>${res.moneda + ' ' + subTotal.toFixed(2)}</td>
                </tr>`;
            });
            document.querySelector('#tablePedidos tbody').innerHTML = html;
            myModal.show();
        }
    }
}

function verReportePedido(idPedido) {
    const ruta = base_url + "pedidos/reporte/ticked/" + idPedido;
    window.open(ruta, "_blank");
}

function verEtiquetaEnvio(idPedido) {
    const ruta = base_url + "pedidos/etiqueta/" + idPedido;
    window.open(ruta, "_blank");
}