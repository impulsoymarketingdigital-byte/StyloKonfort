const btnAddDeseo = document.querySelectorAll(".btnAddDeseo");
const btnAddcarrito = document.querySelectorAll(".btnAddcarrito");
const btnDeseo = document.querySelector("#btnCantidadDeseo");
const btnDeseo1 = document.querySelector("#btnCantidadDeseo1");
const btnCarrito = document.querySelector("#btnCantidadCarrito");
const btnCarrito1 = document.querySelector("#btnCantidadCarrito1");
const verCarrito = document.querySelector('#verCarrito');
const contentCarrito = document.querySelector('#contentCarrito');
const contentListaDeseo = document.querySelector('#contentListaDeseo');
const errorBusqueda = document.querySelector('#errorBusqueda');
//ver carrito
const quickview = new bootstrap.Modal(document.getElementById('quick-view'))

let listaDeseo, listaCarrito;
document.addEventListener("DOMContentLoaded", function () {

    if (localStorage.getItem("listaDeseo") != null) {
        listaDeseo = JSON.parse(localStorage.getItem("listaDeseo"));
    } else {
        listaDeseo = [];
    }
    if (localStorage.getItem("listaCarrito") != null) {
        listaCarrito = JSON.parse(localStorage.getItem("listaCarrito"));
    } else {
        listaCarrito = [];
    }
    for (let i = 0; i < btnAddDeseo.length; i++) {
        btnAddDeseo[i].addEventListener("click", function () {
            let idProducto = btnAddDeseo[i].getAttribute("prod");
            agregarDeseo(idProducto, 1, 1);
        });
    }
    for (let i = 0; i < btnAddcarrito.length; i++) {
        btnAddcarrito[i].addEventListener("click", function () {
            let idProducto = btnAddcarrito[i].getAttribute("prod");
            agregarCarrito(idProducto, 1, 0, 0);
        });
    }
    cantidadDeseo();
    cantidadCarrito();

});
//agregar productos a la lista de deseos
function agregarDeseo(idProducto, size, color) {

    for (let i = 0; i < listaDeseo.length; i++) {
        if (listaDeseo[i]["idProducto"] == idProducto && listaDeseo[i]["size"] == size && listaDeseo[i]["color"] == color) {
            alertaPerzanalizada("EL PRODUCTO YA ESTA EN LISTA DE DESEO", "warning")
            return;
        }
    }
    listaDeseo.concat(localStorage.getItem("listaDeseo"));
    listaDeseo.push({
        idProducto: idProducto,
        cantidad: 1,
        size: size,
        color: color,
    });
    localStorage.setItem("listaDeseo", JSON.stringify(listaDeseo));
    alertaPerzanalizada("PRODUCTO AGREGADO A LA LISTA DE DESEOS", "success")
    cantidadDeseo();
}

function cantidadDeseo() {
    let listas = JSON.parse(localStorage.getItem("listaDeseo"));
    if (listas != null) {
        btnDeseo.textContent = listas.length;
        btnDeseo1.textContent = listas.length;
    } else {
        btnDeseo.textContent = 0;
        btnDeseo1.textContent = 0;
    }
}

//agregar productos al carrito
function agregarCarrito(idProducto, cantidad, size, color, accion = false) {
    for (let i = 0; i < listaCarrito.length; i++) {
        if (accion) {
            eliminarListaDeseo(idProducto, size, color, false);
        }
        if (listaCarrito[i]['idProducto'] == idProducto && listaCarrito[i]['size'] == size && listaCarrito[i]['color'] == color) {
            alertaPerzanalizada("EL PRODUCTO YA ESTA AGREGADO", "warning")
            return;
        }
    }
    listaCarrito.concat(localStorage.getItem("listaCarrito"));
    listaCarrito.push({
        idProducto: idProducto,
        cantidad: cantidad,
        size: size,
        color: color
    });
    localStorage.setItem("listaCarrito", JSON.stringify(listaCarrito));
    alertaPerzanalizada("PRODUCTO AGREGADO AL CARRITO", "success")
    cantidadCarrito();
}

function cantidadCarrito() {
    let listas = JSON.parse(localStorage.getItem("listaCarrito"));
    if (listas != null) {
        btnCarrito.textContent = listas.length;
        btnCarrito1.textContent = listas.length;
    } else {
        btnCarrito.textContent = 0;
        btnCarrito1.textContent = 0;
    }
}

//ver carrito
function getListaCarrito() {
    if (listaCarrito.length > 0) {
        const url = base_url + 'principal/listaProductos';
        const http = new XMLHttpRequest();
        http.open('POST', url, true);
        http.send(JSON.stringify(listaCarrito));
        http.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                const res = JSON.parse(this.responseText);
                let acciones = '';
                if (res.login == 1) {
                    acciones = `<a href="${base_url + 'clientes'}" class="btn btn-solid btn-sm ">checkout</a>`;
                } else {
                    acciones = `<a href="#" onclick="openAccount()" class="btn btn-solid btn-sm ">login</a>`;
                }
                let html = '';                
                res.productos.forEach(producto => {
                    let verify = producto.stock == 'Ilimitado' ? '' : `max="${producto.stock}"`;
                    html += `<li>
                    <div class="media">
                      <a href="#">
                        <img alt="megastore1" class="me-3" src="${base_url + producto.imagen}">
                      </a>
                      <div class="media-body">
                        <a href="#">
                          <h6>${producto.nombre}</h6>
                          <p>${producto.atributo}</p>
                        </a>
                        <h6>
                        ${res.moneda + ' ' + producto.precio}
                        </h6>
                        <div class="addit-box">
                            <div class="input-group">
                                <input class="form-control agregarCantidad" type="number" value="${producto.cantidad}" id="${producto.id}" size="${producto.size}" color="${producto.color}" value="${producto.cantidad}" min="1" ${verify}>
                                <button class="input-group-text btnDeletecart" prod="${producto.id}" size="${producto.size}" color="${producto.color}" type="button"><i class="fa fa-trash text-danger"></i></button>
                            </div>
                        </div>
                      </div>
                    </div>
                  </li>`;
                });
                contentCarrito.innerHTML = html;
                document.querySelector('#contentTotal').innerHTML = `<li>
                        <div class="total">
                        total<span>${res.moneda + ' ' + res.total}</span>
                        </div>
                    </li>
                    <li>
                        <div class="buttons">
                            ${acciones}
                        </div>
                    </li>`;
                btnEliminarCarrito();
                cambiarCantidad();
            }
        }
    } else {
        document.querySelector('#contentTotal').innerHTML = '';
        contentCarrito.innerHTML = `<li>Carrito vacio</li>`;
    }
}

//ver lista de deso
function getListaDeseo() {
    if (listaDeseo.length > 0) {
        const url = base_url + 'principal/listaProductos';
        const http = new XMLHttpRequest();
        http.open('POST', url, true);
        http.send(JSON.stringify(listaDeseo));
        http.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                const res = JSON.parse(this.responseText);
                let html = '';
                res.productos.forEach(producto => {
                    let verify = producto.stock == 'Ilimitado' ? '' : `max="${producto.stock}"`;
                    html += `<li>
                    <div class="media">
                      <a href="#">
                        <img alt="megastore1" class="me-3" src="${base_url + producto.imagen}">
                      </a>
                      <div class="media-body">
                        <a href="#">
                          <h6>${producto.nombre}</h6>
                          <p>${producto.atributo}</p>
                        </a>
                        <h6>
                        ${res.moneda + ' ' + producto.precio_venta} <span>${res.moneda + ' ' + producto.precio_venta}</span>
                        </h6>
                        <div class="addit-box">
                            <div class="input-group">
                                <button class="input-group-text btnAddCart" prod="${producto.id}" size="${producto.size}" color="${producto.color}" type="button"><i class="fa fa-shopping-cart text-primary"></i></button>
                                <button class="input-group-text btnEliminarDeseo" prod="${producto.id}" size="${producto.size}" color="${producto.color}" type="button"><i class="fa fa-trash text-danger"></i></button>
                            </div>
                        </div>
                      </div>
                    </div>
                  </li>`;
                });
                contentListaDeseo.innerHTML = html;
                document.querySelector('#contentTotalDeseo').innerHTML = `<li>
                        <div class="total">
                        total<span>${res.moneda + ' ' + res.total}</span>
                        </div>
                    </li>`;
                    btnEliminarDeseo();
                    btnAgregarProducto();
            }
        }
    } else {
        document.querySelector('#contentTotalDeseo').innerHTML = '';
        contentListaDeseo.innerHTML = `<li>Lista de deseo vacio</li>`;
    }
}

function btnEliminarDeseo() {
    let listaEliminar = document.querySelectorAll('.btnEliminarDeseo');
    for (let i = 0; i < listaEliminar.length; i++) {
        listaEliminar[i].addEventListener('click', function () {
            let idProducto = listaEliminar[i].getAttribute('prod');
            let size = listaEliminar[i].getAttribute('size');
            let color = listaEliminar[i].getAttribute('color');
            eliminarListaDeseo(idProducto, size, color);
        })
    }
}

function eliminarListaDeseo(idProducto, size, color, accion = true) {
    for (let i = 0; i < listaDeseo.length; i++) {
        if (listaDeseo[i]['idProducto'] == idProducto && listaDeseo[i]['size'] == size 
        && listaDeseo[i]['color'] == color) {
            listaDeseo.splice(i, 1);
        }
    }
    localStorage.setItem('listaDeseo', JSON.stringify(listaDeseo));
    getListaDeseo();
    cantidadDeseo();
    if (accion) {
        alertaPerzanalizada('PRODUCTO ELIMINADO DE TU LISTA','success')
    }    
}

//agregar productos desde la lista de deseos
function btnAgregarProducto() {
    let listaAgregar = document.querySelectorAll('.btnAddCart');
    for (let i = 0; i < listaAgregar.length; i++) {
        listaAgregar[i].addEventListener('click', function () {
            let idProducto = listaAgregar[i].getAttribute('prod');
            let size = listaAgregar[i].getAttribute('size');
            let color = listaAgregar[i].getAttribute('color');
            agregarCarrito(idProducto, 1, size, color, true);
        })
    }
}

function btnEliminarCarrito() {
    let listaEliminar = document.querySelectorAll('.btnDeletecart');
    for (let i = 0; i < listaEliminar.length; i++) {
        listaEliminar[i].addEventListener('click', function () {
            let idProducto = listaEliminar[i].getAttribute('prod');
            let size = listaEliminar[i].getAttribute('size');
            let color = listaEliminar[i].getAttribute('color');
            eliminarListaCarrito(idProducto, size, color);
        })
    }
}

function eliminarListaCarrito(idProducto, size, color) {
    for (let i = 0; i < listaCarrito.length; i++) {
        if (listaCarrito[i]['idProducto'] == idProducto && listaCarrito[i]['size'] == size && listaCarrito[i]['color'] == color) {
            listaCarrito.splice(i, 1);
        }
    }
    localStorage.setItem('listaCarrito', JSON.stringify(listaCarrito));
    getListaCarrito();
    cantidadCarrito();
    alertaPerzanalizada("PRODUCTO ELIMINADO DEL CARRITO", "success")
}
//cambiar la cantidad
function cambiarCantidad() {
    let listaCantidad = document.querySelectorAll('.agregarCantidad');
    for (let i = 0; i < listaCantidad.length; i++) {
        listaCantidad[i].addEventListener('change', function () {
            let idProducto = listaCantidad[i].id;
            let size = listaCantidad[i].getAttribute('size');
            let color = listaCantidad[i].getAttribute('color');
            let cantidad = listaCantidad[i].value;
            incrementarCantidad(idProducto, cantidad, size, color);
        })
    }
}

function incrementarCantidad(idProducto, cantidad, size, color) {
    for (let i = 0; i < listaCarrito.length; i++) {
        if (listaCarrito[i]['idProducto'] == idProducto && listaCarrito[i]['size'] == size && listaCarrito[i]['color'] == color) {
            listaCarrito[i].cantidad = cantidad;
        }
    }
    localStorage.setItem('listaCarrito', JSON.stringify(listaCarrito));
    getListaCarrito();
}

//ver detalle
function verDetalle(idProducto) {
    const url = base_url + 'principal/getProducto/' + idProducto;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            document.querySelector('#idSize').value="";
            document.querySelector('#idColor').value="";
            //MOSTRAR CALIFICACION
            let uno = (res.calificacion >= 1) ? 'text-warning' : 'text-muted';
            let dos = (res.calificacion >= 2) ? 'text-warning' : 'text-muted';
            let tres = (res.calificacion >= 3) ? 'text-warning' : 'text-muted';
            let cuatro = (res.calificacion >= 4) ? 'text-warning' : 'text-muted';
            let cinco = (res.calificacion == 5) ? 'text-warning' : 'text-muted';            
            //CREAR SIZE
            let contentSize = 'No asignado';
            if (res.sizes.length > 0) {
                contentSize = '';
                res.sizes.forEach(size => {
                    contentSize += `<label class="btn btn-outline-primary">
                        <input type="radio" name="size" onclick="coloresDisponible(${res.producto.id}, ${size.id})"> ${size.nombre}
                    </label>`;
                });
            }
            document.querySelector('#content-quick').innerHTML = `<div class="col-lg-6 col-xs-12">
                <div class="quick-view-img">
                <img src="${base_url + res.producto.imagen}" alt="" class="img-fluid bg-img">
                </div>
            </div>
          <div class="col-lg-6 rtl-text">
            <div class="product-right">
              <div class="pro-group">
                <h2>${res.producto.nombre}</h2>
                <ul class="pro-price">
                  <li>${res.moneda + ' ' + res.producto.precio_venta}</li>
                </ul>
                <div class="revieu-box">
                  <ul>
                    <li><i class="${uno} fa fa-star"></i></li>
                    <li><i class="${dos} fa fa-star"></i></li>
                    <li><i class="${tres} fa fa-star"></i></li>
                    <li><i class="${cuatro} fa fa-star"></i></li>
                    <li><i class="${cinco} fa fa-star"></i></li>
                  </ul>
                  <a href="#"><span>(${res.totalCantidad} reviews)</span></a>
                </div>
              </div>
              <div class="pro-group">
                <h6 class="product-title">Descripcion</h6>
                <p>${res.producto.descripcion}</p>
              </div>
              <div class="pro-group pb-0">
                <h6 class="product-title">Tamaño </h6>
                <div class="size-box">
                    <div class="btn-group-toggle" data-bs-toggle="buttons">
                        ${contentSize}
                    </div>
                </div>
                <h6 class="product-title">color</h6>
                <div class="color-selector inline">
                    <div class="btn-group-toggle" data-bs-toggle="buttons" id="content-color">
                        Selecciona Size
                    </div>
                </div>
                <h6 class="product-title">Cantidad</h6>
                <div class="qty-box">
                  <div class="input-group">
                    <button class="qty-minus" onclick="modificarStockQuick(0, 'quantity')"></button>
                    <input class="qty-adj form-control" type="number" min="1" value="1" id="quantity">
                    <button class="qty-plus" onclick="modificarStockQuick(1, 'quantity')"></button>
                  </div>
                </div>
                <div class="product-buttons">
                  <a href="javascript:void(0)" onclick="addCart(${res.producto.id}, 'quantity')" class="btn cart-btn btn-normal tooltip-top" data-tippy-content="Add to cart">
                    <i class="fa fa-shopping-cart"></i>
                    añadir al carrito
                  </a>
                  <a href="javascript:void(0)" onclick="addDeseo(${res.producto.id})" class="btn cart-btn btn-normal tooltip-top" data-tippy-content="Add to cart">
                    <i class="fa fa-heart"></i>
                    lista de deseo
                  </a>
                </div>
              </div>
            </div>
          </div>`;
            quickview.show();
        }
    }
}

function coloresDisponible(idProducto, idSize) {
    const url = base_url + 'principal/getColores/' + idProducto + '/' + idSize;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = 'No disponible';
            if (res.colores.length > 0) {
                html = '';
                res.colores.forEach(color => {
                    html += `<label class="btn text-white" style="background-color: ${color.color};">
                    <input type="radio" name="color" onclick="verificarStock(${idSize}, ${color.id}, ${idProducto}, 'quantity')"> ${color.nombre}
                </label>`;
                });
            }
            document.querySelector('#idSize').value = idSize;
            document.querySelector('#content-color').innerHTML = html;
        }
    }
}

function verificarStock(idSize, idColor, idProducto, quantity) {
    const url = base_url + 'principal/getStock/' + idSize + '/' + idColor + '/' + idProducto;
    const http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            document.querySelector('#idColor').value = idColor;
            document.querySelector('#' + quantity).value = 1;
            document.querySelector('#' + quantity).setAttribute('max', res.cantidad);
        }
    }
}

function modificarStockQuick(accion, quantity) {
    let stock = document.querySelector('#' + quantity).value;
    let maximo = document.querySelector('#' + quantity).getAttribute('max');
    if (accion == 1) {
        if (parseInt(maximo) > stock) {
            document.querySelector('#' + quantity).value = parseInt(stock) + 1;
        }            
    } else {
        if (parseInt(stock) > 1) {
            document.querySelector('#' + quantity).value = parseInt(stock) - 1;
        }        
    }
}

// function modificarStockCarrito(accion) {
//     let stock = document.querySelector('#quantity').value;
//     let maximo = document.querySelector('#quantity').getAttribute('max');
//     if (accion == 1) {
//         if (parseInt(maximo) > stock) {
//             document.querySelector('#quantity').value = parseInt(stock) + 1;
//         }            
//     } else {
//         if (parseInt(stock) > 1) {
//             document.querySelector('#quantity').value = parseInt(stock) - 1;
//         }        
//     }
// }

function addCart(idProducto, quant) {
    let idSize = document.querySelector('#idSize');
    let idColor = document.querySelector('#idColor');
    let quantity = document.querySelector('#' + quant);
    if (idSize.value == '' || idColor.value == '') {
        alertaPerzanalizada('SELECCIONA SIZE Y COLOR', 'warning');
    } else {
        agregarCarrito(idProducto, quantity.value, idSize.value, idColor.value);
        idSize.value = '';
        idColor.value = '';
        quantity.value = '1';
    }
}

function addDeseo(idProducto) {
    let idSize = document.querySelector('#idSize');
    let idColor = document.querySelector('#idColor');
    if (idSize.value == '' || idColor.value == '') {
        alertaPerzanalizada('SELECCIONA SIZE Y COLOR', 'warning');
    } else {
        agregarDeseo(idProducto, idSize.value, idColor.value);
    }
}