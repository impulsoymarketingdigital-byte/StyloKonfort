let tblPendientes, tblFinalizados, tblProceso, tblAnulados;
const myModal = new bootstrap.Modal(document.getElementById("modalPedidos"));

document.addEventListener("DOMContentLoaded", function() {
    // Tabla 1: Pendientes
    tblPendientes = $("#tblPendientes").DataTable({
        ajax: {
            url: base_url + "pedidos/listarPedidos",
            dataSrc: "",
        },
        columns: [
            { data: "id" }, { data: "fecha" }, { data: "id_transaccion" },
            { data: "monto" }, { data: "estado" }, { data: "email" },
            { data: "nombre" }, { data: "apellido" }, { data: "direccion" },
            { data: "accion" },
        ],
        responsive: true,
        language: language,
        dom: dom,
        buttons: buttons,
    });
    
    // Tabla 2: En Proceso
    tblProceso = $("#tblProceso").DataTable({
        ajax: {
            url: base_url + "pedidos/listarProceso",
            dataSrc: "",
        },
        columns: [
            { data: "id" }, { data: "fecha" }, { data: "id_transaccion" },
            { data: "monto" }, { data: "estado" }, { data: "email" },
            { data: "nombre" }, { data: "apellido" }, { data: "direccion" },
            { data: "accion" },
        ],
        responsive: true,
        language: language,
        dom: dom,
        buttons: buttons,
    });
    
    // Tabla 3: Finalizados
    tblFinalizados = $("#tblFinalizados").DataTable({
        ajax: {
            url: base_url + "pedidos/listarFinalizados",
            dataSrc: "",
        },
        columns: [
            { data: "id" }, { data: "fecha" }, { data: "id_transaccion" },
            { data: "monto" }, { data: "estado" }, { data: "email" },
            { data: "nombre" }, { data: "apellido" }, { data: "direccion" },
            { data: "accion" },
        ],
        responsive: true,
        language: language,
        dom: dom,
        buttons: buttons,
    });

    // NUEVA Tabla 4: Anulados
    tblAnulados = $("#tblAnulados").DataTable({
        ajax: {
            // Reutilizamos la función del backend pero le pasamos el estado 4 si es necesario, 
            // aunque el backend que te di mezcla 3 y 4. Vamos a crear una ruta rápida si no carga,
            // pero con el código actual cargará los finalizados. Lo ideal es que pida listarAnulados()
            url: base_url + "pedidos/listarFinalizados", 
            dataSrc: function (json) {
                // Filtramos por JS solo los anulados (Proceso 4) para no crear más rutas en PHP
                return json.filter(item => item.estado.includes('Cancelado'));
            }
        },
        columns: [
            { data: "id" }, { data: "fecha" }, { data: "id_transaccion" },
            { data: "monto" }, { data: "estado" }, { data: "email" },
            { data: "nombre" }, { data: "apellido" }, { data: "direccion" },
            { data: "accion" },
        ],
        responsive: true,
        language: language,
        dom: dom,
        buttons: buttons,
    });
    
    $.datetimepicker.setLocale('es');
    
    // Filtro rango de fechas
    document.getElementById('desde').addEventListener('blur', function () {
        tblPendientes.draw();
        tblProceso.draw();
        tblFinalizados.draw();
        tblAnulados.draw();
    });
    
    document.getElementById('hasta').addEventListener('blur', function () {
        tblPendientes.draw();
        tblProceso.draw();
        tblFinalizados.draw();
        tblAnulados.draw();
    });
    
    // Agregar filtro personalizado solo UNA VEZ
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var FilterStart = document.getElementById('desde').value;
            var FilterEnd = document.getElementById('hasta').value;
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
            mensaje = '¿Pasar a "En Proceso"? (Se descontará el stock virtualmente)';
            break;
        case 3:
            mensaje = '¿Marcar pedido como "Entregado y Finalizado"?';
            break;
        case 4:
            mensaje = '¿Anular pedido? (Los productos volverán al stock de la página web)';
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
        confirmButtonText: "Sí, proceder",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            // Usando fetch moderno en lugar de XMLHttpRequest
            const url = base_url + "pedidos/update/" + idPedido + "," + proceso;
            
            fetch(url)
                .then(response => response.json())
                .then(res => {
                    if (res.icono == "success") {
                        // Recargar todas las tablas para mantener los datos frescos
                        tblPendientes.ajax.reload();
                        tblProceso.ajax.reload();
                        tblFinalizados.ajax.reload();
                        tblAnulados.ajax.reload();
                    }
                    // Asumiendo que tu función alertas() viene de otro archivo global
                    alertas(res.msg.toUpperCase(), res.icono);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alertas("ERROR DE CONEXIÓN CON EL SERVIDOR", "error");
                });
        }
    });
}

function verPedido(idPedido) {
    const url = base_url + "pedidos/verPedido/" + idPedido;
    
    fetch(url)
        .then(response => response.json())
        .then(res => {
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
        })
        .catch(error => console.error('Error:', error));
}

function verReportePedido(idPedido) {
    const ruta = base_url + "pedidos/reporte/ticked," + idPedido;
    window.open(ruta, "_blank");
}

function verEtiquetaEnvio(idPedido) {
    const ruta = base_url + "pedidos/etiqueta/" + idPedido;
    window.open(ruta, "_blank");
}