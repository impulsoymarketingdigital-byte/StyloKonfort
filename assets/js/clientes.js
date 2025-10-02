const tableLista = document.querySelector("#tableListaProductos tbody");
const tblPendientes = document.querySelector('#tblPendientes');
let productosjson = [];
let tblCalificacion, mensaje;
const estadoEnviado = document.querySelector('#estadoEnviado');
const estadoProceso = document.querySelector('#estadoProceso');
const btnTestimonio = document.querySelector('#btnTestimonio');
const comentario = document.querySelector('#comentario');

const frmDatos = document.querySelector('#frmDatos');
const nomCliente = document.querySelector('#nomCliente');
const apeCliente = document.querySelector('#apeCliente');
const corCliente = document.querySelector('#corCliente');
const telCliente = document.querySelector('#telCliente');
const dirCliente = document.querySelector('#dirCliente');


const estadoCompletado = document.querySelector('#estadoCompletado');
document.addEventListener("DOMContentLoaded", function () {
    if (tableLista) {
        getListaProductos();
    }
    //cargar datos pendientes con DataTables
    $('#tblPendientes').DataTable({
        ajax: {
            url: base_url + 'clientes/listarPendientes',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'id_transaccion' },
            { data: 'monto' },
            { data: 'metodo' },
            { data: 'fecha' },
            { data: 'accion' }
        ],
        language,
        responsive: true,
        order: [[0, 'desc']],

    });

    tblCalificacion = $('#tblProductos').DataTable({
        ajax: {
            url: base_url + 'clientes/listarProductos',
            dataSrc: ''
        },
        columns: [
            { data: 'id_producto' },
            { data: 'producto' },
            { data: 'precio' },
            { data: 'cantidad' },
            { data: 'calificacion' }
        ],
        language,
    });

    ClassicEditor.create(document.querySelector("#comentario"), {
        toolbar: {
            items: [
                "heading",
                "|",
                "bold",
                "italic",
                "strikethrough",
                "underline",
                "|",
                "undo",
                "redo",
                "|",
                "alignment",
                "|",
                "link",
                "blockQuote",
                "insertTable",
                "mediaEmbed",
            ],
            shouldNotGroupWhenFull: true,
        },
    })
        .then((newEditor) => {
            mensaje = newEditor;
        })
        .catch((error) => {
            console.error(error);
        });

    btnTestimonio.addEventListener('click', function () {
        if (mensaje.getData() == '') {
            alertaPerzanalizada('INGRESA UN COMENTARIO', 'warning');
        } else {
            const url = base_url + 'clientes/agregarMensaje';
            const http = new XMLHttpRequest();
            http.open('POST', url, true);
            http.send(JSON.stringify({
                mensaje: mensaje.getData()
            }));
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const res = JSON.parse(this.responseText);
                    alertaPerzanalizada(res.msg, res.icono);
                }
            }
        }
    })

    frmDatos.addEventListener('submit', function (e) {
        e.preventDefault();
        if (nomCliente.value == '' || apeCliente.value == '' || telCliente.value == ''
            || corCliente.value == '' || dirCliente.value == '') {
            alertaPerzanalizada('TODO LOS CAMPOS CON * SON REQUERIDOS', 'warning');
        } else {
            const url = base_url + 'clientes/modificarDatos';
            const http = new XMLHttpRequest();
            http.open('POST', url, true);
            http.send(new FormData(this));
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertaPerzanalizada(res.msg, res.type);
                }
            }
        }
    })
});

function getListaProductos() {
    let html = '';
    const url = base_url + 'principal/listaProductos';
    const http = new XMLHttpRequest();
    http.open('POST', url, true);
    http.send(JSON.stringify(listaCarrito));
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.totalPaypal > 0) {
                res.productos.forEach(producto => {
                    html += `<tr>
                            <td>
                                <img class="img-thumbnail rounded-circle" src="${producto.imagen}" alt="" width="100">
                            </td>
                            <td>${producto.nombre}</td>
                            <td>${producto.atributo}</td>
                            <td>${producto.stock}</td>
                            <td><span class="badge bg-warning">${res.moneda + ' ' + producto.precio}</span></td>
                            <td><span class="badge bg-primary">${producto.cantidad}</span></td>
                            <td>${producto.subTotal}</td>
                        </tr>`;
                    //agregrar producto para paypal
                    let json = {
                        "name": producto.nombre + ' - ' + producto.atributoMP,
                        /* Shows within upper-right dropdown during payment approval */
                        "unit_amount": {
                            "currency_code": res.currency,
                            "value": producto.precio
                        },
                        "quantity": producto.cantidad
                    }
                    productosjson.push(json);
                });
                tableLista.innerHTML = html;
                document.querySelector('#totalProducto').textContent = 'TOTAL A PAGAR: ' + res.moneda + ' ' + res.total;
                botonPaypal(res.totalPaypal, res.currency);
            } else {
                document.querySelector('#totalProducto').textContent = res.moneda + ' 0.00';
                tableLista.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center">CARRITO VACIO</td>
                </tr>
                `;
            }

        }
    }
}
//link boton
// https://developer.paypal.com/docs/checkout/

//https://developer.paypal.com/api/rest/reference/currency-codes/

function botonPaypal(total, moneda) {
    paypal.Buttons({
        // Sets up the transaction when a payment button is clicked
        createOrder: (data, actions) => {
            return actions.order.create({
                // application_context: {
                //     shipping_preference: "NO_SHIPPING"
                // },
                "purchase_units": [{
                    "amount": {
                        "currency_code": moneda,
                        "value": total,
                        "breakdown": {
                            "item_total": { /* Required when including the `items` array */
                                "currency_code": moneda,
                                "value": total
                            }
                        }
                    },
                    "items": productosjson
                }]
            });
        },
        // Finalize the transaction after payer approval
        onApprove: (data, actions) => {
            return actions.order.capture().then(function (orderData) {
                registrarPedido(orderData)
            });
        }
    }).render('#paypal-button-container');
}

function registrarPedido(datos) {
    const url = base_url + 'clientes/registrarPedido';
    const http = new XMLHttpRequest();
    http.open('POST', url, true);
    http.send(JSON.stringify({
        pedidos: datos,
        productos: listaCarrito
    }));
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            const res = JSON.parse(this.responseText);
            alertaPerzanalizada(res.msg, res.icono);
            if (res.icono == 'success') {
                localStorage.removeItem('listaCarrito');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        }
    }
}

function verPedido(idPedido) {
    estadoEnviado.classList.remove('border-success');
    estadoProceso.classList.remove('border-success');
    estadoCompletado.classList.remove('border-success');
    const mPedido = new bootstrap.Modal(document.getElementById('modalPedido'));
    const url = base_url + 'clientes/verPedido/' + idPedido;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            const res = JSON.parse(this.responseText);
            let html = '';
            if (res.pedido.proceso == 1) {
                estadoEnviado.classList.add('border-success');
            } else if (res.pedido.proceso == 2) {
                estadoProceso.classList.add('border-success');
            } else {
                estadoCompletado.classList.add('border-success');
            }
            res.productos.forEach(row => {
                let verify = row.atributos == 'Descargable' ? `<a href="${res.descarga.ruta}" class="btn btn-danger"><i class="fas fa-download"></i></a>` : '';
                let subTotal = parseFloat(row.precio) * parseInt(row.cantidad);
                html += `<tr>
                    <td>${row.producto}</td>
                    <td>${row.atributos}</td>
                    <td><span class="badge bg-warning">${res.moneda + ' ' + row.precio}</span></td>
                    <td><span class="badge bg-primary">${row.cantidad}</span></td>
                    <td>${subTotal.toFixed(2)}</td>
                    <td>${verify}</td>
                </tr>`;
            });
            document.querySelector('#tablePedidos tbody').innerHTML = html;
            mPedido.show();
        }
    }

}


function agregarCalificacion(id_producto, cantidad) {
    const url = base_url + 'clientes/agregarCalificacion';
    const http = new XMLHttpRequest();
    http.open('POST', url, true);
    http.send(JSON.stringify({
        id_producto: id_producto,
        cantidad: cantidad
    }));
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            const res = JSON.parse(this.responseText);
            alertaPerzanalizada(res.msg, res.icono);
            if (res.icono == 'success') {
                tblCalificacion.ajax.reload();
            }
        }
    }
}

// sb-j6jdb7896999@personal.example.com
// e8O2lR-I


//sb-y3jfn7901325@business.example.com
//Amqes3]/