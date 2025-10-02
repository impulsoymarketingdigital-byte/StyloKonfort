const inputBuscarNombre = document.querySelector('#buscarProductoNombre');
const nombre = document.querySelector('#nombre');
const containerNombre = document.querySelector('#containerNombre');

const errorBusqueda = document.querySelector('#errorBusqueda');

const btnAccion = document.querySelector('#btnAccion');
const totalPagar = document.querySelector('#totalPagar');
const size = document.querySelector('#size');
const color = document.querySelector('#color');
const idProducto = document.querySelector('#idProducto');
const btnAgregar = document.querySelector('#btnAgregar');
const cantidad = document.querySelector('#cantidad');

//para filtro por rango de fechas
const desde = document.querySelector('#desde');
const hasta = document.querySelector('#hasta');

let listaCarrito, tblHistorial;

document.addEventListener('DOMContentLoaded', function () {
    //comprobar productos en localStorage
    if (localStorage.getItem(nombreKey) != null) {
        listaCarrito = JSON.parse(localStorage.getItem(nombreKey));
    }

    //autocomplete productos
    $("#buscarProductoNombre").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + 'ventas/buscarPorNombre',
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function (data) {
                    response(data);
                    if (data.length > 0) {
                        errorBusqueda.textContent = '';
                    } else {
                        errorBusqueda.textContent = 'NO HAY PRODUCTO CON ESE NOMBRE';
                    }
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            if (ui.item.descarga) {
                agregarProducto(ui.item.id, 1, ui.item.stock, 0, 0, true);
                inputBuscarNombre.value = '';
                inputBuscarNombre.focus();
            } else {
                idProducto.value = ui.item.id;
                cargarSizeTalla(ui.item.id);
                $('#modalSizeColor').modal('show');
            }

            return false;
        }
    });

    size.addEventListener('change', function (e) {
        if (e.target.value != '') {
            cambiarStock(e.target.value, color.value);
        }
    })

    color.addEventListener('change', function (e) {
        if (e.target.value != '') {
            cambiarStock(size.value, e.target.value);
        }
    })

    btnAgregar.addEventListener('click', function () {
        if (size.value != '' && color.value != '') {
            if (parseInt(cantidad.value) > 0) {
                agregarProducto(idProducto.value, 1, cantidad.value, size.value, color.value, false);
                idProducto.value = '';
                inputBuscarNombre.value = '';
                $('#modalSizeColor').modal('hide');
            } else {
                alertas('STOCK NO DISPONIBLE', 'warning');
            }
        } else {
            alertas('SELECCIONA TALLA Y COLOR', 'warning');
        }
    })

    $.datetimepicker.setLocale('es');

    $('#desde').datetimepicker({
        format:'Y-m-d H:m:s'
    });

    $('#hasta').datetimepicker({
        format:'Y-m-d H:m:s'
    });

    //filtro rango de fechas
    desde.addEventListener('blur', function () {
        tblHistorial.draw();
    })
    hasta.addEventListener('blur', function () {
        tblHistorial.draw();
    })

    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var FilterStart = desde.value;
            var FilterEnd = hasta.value;
            var DataTableStart = data[1].trim();
            var DataTableEnd = data[1].trim();
            if (FilterStart == '' || FilterEnd == '') {
                return true;
            }
            if (DataTableStart >= FilterStart && DataTableEnd <= FilterEnd) {
                return true;
            } else {
                return false;
            }

        }
    );

})

function cambiarStock(size, color) {
    let data = new FormData();
    data.append("size", size);
    data.append("color", color);
    data.append("id_producto", idProducto.value);
    const url = base_url + "principal/cambiarStock";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            cantidad.value = res.atrib.cantidad;
            let html = '<option value="">Seleccionar</option>';
            res.colores.forEach(color1 => {
                html += `<option value="${color1.id}" ${color == color1.id ? 'selected' : ''}>${color1.nombre}</option>`;
            });
            document.querySelector('#color').innerHTML = html;
            if (parseInt(res.atrib.cantidad) > 0) {
                btnAgregar.classList.remove('d-none');
            } else {
                btnAgregar.classList.add('d-none');
            }
        }
    }
}

//agregar productos a localStorage
function agregarProducto(idProducto, cantidad, stockActual, size, color, descarga) {
    if (localStorage.getItem(nombreKey) == null) {
        listaCarrito = [];
    } else {
        let cantidadAgregado = 0;
        for (let i = 0; i < listaCarrito.length; i++) {
            if (listaCarrito[i]['id'] == idProducto) {
                cantidadAgregado = parseInt(listaCarrito[i]['cantidad']) + parseInt(cantidad);
            }
        }
        if (!descarga) {
            if (parseInt(cantidadAgregado) > parseInt(stockActual) || parseInt(stockActual) == 0) {
                alertas('STOCK NO DISPONIBLE', 'warning');
                return;
            }
        }
    }
    listaCarrito.push({
        idProducto: idProducto,
        cantidad: cantidad,
        size: size,
        color: color
    })
    localStorage.setItem(nombreKey, JSON.stringify(listaCarrito));
    alertas('PRODUCTO AGREGADO', 'success');
    mostrarProducto();
}

//agregar evento click para eliminar
function btnEliminarProducto() {
    let lista = document.querySelectorAll('.btnEliminar');
    for (let i = 0; i < lista.length; i++) {
        lista[i].addEventListener('click', function () {
            let idProducto = lista[i].getAttribute('data-id');
            let size = lista[i].getAttribute('size');
            let color = lista[i].getAttribute('color');
            eliminarProducto(idProducto, size, color);
        });
    }
}
//eliminar productos del table
function eliminarProducto(idProducto, size, color) {
    for (let i = 0; i < listaCarrito.length; i++) {
        if (listaCarrito[i]['idProducto'] == idProducto && listaCarrito[i]['size'] == size && listaCarrito[i]['color'] == color) {
            listaCarrito.splice(i, 1);
        }
    }
    localStorage.setItem(nombreKey, JSON.stringify(listaCarrito));
    alertas('PRODUCTO ELIMINADO', 'success');
    mostrarProducto();
}

//agregar eventa change para cambiar la cantidad
function agregarCantidad() {
    let lista = document.querySelectorAll('.inputCantidad');
    for (let i = 0; i < lista.length; i++) {
        lista[i].addEventListener('change', function () {
            let idProducto = lista[i].getAttribute('data-id');
            let size = lista[i].getAttribute('size');
            let color = lista[i].getAttribute('color');
            let cantidad = lista[i].value;
            cambiarCantidad(idProducto, cantidad, size, color);
        });
    }
}

function cambiarCantidad(idProducto, cantidad, size, color) {
    for (let i = 0; i < listaCarrito.length; i++) {
        if (listaCarrito[i]['idProducto'] == idProducto && listaCarrito[i]['size'] == size && listaCarrito[i]['color'] == color) {
            listaCarrito[i]['cantidad'] = cantidad;
        }
    }
    localStorage.setItem(nombreKey, JSON.stringify(listaCarrito));
    mostrarProducto();
}

function cargarSizeTalla(idProducto) {
    const url = base_url + 'ventas/size/' + idProducto;
    //hacer una instancia del objeto XMLHttpRequest 
    const http = new XMLHttpRequest();
    //Abrir una Conexion - POST - GET
    http.open('GET', url, true);
    //Enviar Datos
    http.send();
    //verificar estados
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let sizes = '<option value="">Seleccionar</option>';
            res.forEach(size => {
                sizes += `<option value="${size.id}">${size.nombre}</option>`;
            });
            size.innerHTML = sizes;
        }
    }
}