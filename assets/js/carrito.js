const btnAddDeseo = document.querySelectorAll(".btnAddDeseo");
const btnAddcarrito = document.querySelectorAll(".btnAddcarrito");
const btnDeseo = document.querySelector("#btnCantidadDeseo");
const btnCarrito = document.querySelector("#btnCantidadCarrito");
const verCarrito = document.querySelector("#verCarrito");
const contentCarrito = document.querySelector("#contentCarrito");
const contentListaDeseo = document.querySelector("#contentListaDeseo");
const errorBusqueda = document.querySelector("#errorBusqueda");

// Modal de vista rápida
const quickview = document.getElementById("quick-view") ? new bootstrap.Modal(document.getElementById("quick-view")) : null;

let listaDeseo = [];
let listaCarrito = [];

document.addEventListener("DOMContentLoaded", function () {
    // Inicialización segura de LocalStorage
    if (localStorage.getItem("listaDeseo") != null) {
        listaDeseo = JSON.parse(localStorage.getItem("listaDeseo"));
    }
    if (localStorage.getItem("listaCarrito") != null) {
        listaCarrito = JSON.parse(localStorage.getItem("listaCarrito"));
    }

    // Eventos para botones estáticos de agregar a deseo
    btnAddDeseo.forEach(btn => {
        btn.addEventListener("click", function () {
            let idProducto = this.getAttribute("prod");
            agregarDeseo(idProducto, 1, 1);
        });
    });

    // Eventos para botones estáticos de agregar a carrito
    btnAddcarrito.forEach(btn => {
        btn.addEventListener("click", function () {
            let idProducto = this.getAttribute("prod");
            agregarCarrito(idProducto, 1, 0, 0);
        });
    });

    cantidadDeseo();
    cantidadCarrito();
});

// --- LISTA DE DESEOS ---
function agregarDeseo(idProducto, size, color) {
    const existe = listaDeseo.some(item => item.idProducto == idProducto && item.size == size && item.color == color);
    
    if (existe) {
        alertaPerzanalizada("EL PRODUCTO YA ESTÁ EN TU LISTA DE DESEOS", "warning");
        return;
    }

    listaDeseo.push({
        idProducto: idProducto,
        cantidad: 1,
        size: size,
        color: color,
    });
    
    localStorage.setItem("listaDeseo", JSON.stringify(listaDeseo));
    alertaPerzanalizada("PRODUCTO AGREGADO A LA LISTA DE DESEOS", "success");
    cantidadDeseo();
}

function cantidadDeseo() {
    let listas = JSON.parse(localStorage.getItem("listaDeseo"));
    btnDeseo.textContent = listas ? listas.length : 0;
}

// --- CARRITO DE COMPRAS ---
function agregarCarrito(idProducto, cantidad, size, color, accion = false) {
    const url = `${base_url}principal/getStock/${size}/${color}/${idProducto}`;

    fetch(url)
        .then(response => response.json())
        .then(res => {
            if (!res || res.stock <= 0) {
                alertaPerzanalizada("ESTE PRODUCTO NO TIENE STOCK DISPONIBLE", "error");
                return;
            }

            const indice = listaCarrito.findIndex(item => item.idProducto == idProducto && item.size == size && item.color == color);
            
            if (accion) {
                eliminarListaDeseo(idProducto, size, color, false);
            }

            if (indice !== -1) {
                alertaPerzanalizada("EL PRODUCTO YA ESTÁ AGREGADO", "warning");
                return;
            }

            if (cantidad > res.stock) {
                alertaPerzanalizada(`SOLO HAY ${res.stock} UNIDADES DISPONIBLES`, "warning");
                return;
            }

            listaCarrito.push({
                idProducto: idProducto,
                cantidad: cantidad,
                size: size,
                color: color,
                tipo_cliente: res.tipo_cliente, 
                precio_aplicable: res.precio_aplicable, 
            });

            localStorage.setItem("listaCarrito", JSON.stringify(listaCarrito));
            alertaPerzanalizada("PRODUCTO AGREGADO AL CARRITO", "success");
            cantidadCarrito();
        })
        .catch(error => console.error('Error:', error));
}

function cantidadCarrito() {
    let listas = JSON.parse(localStorage.getItem("listaCarrito"));
    btnCarrito.textContent = listas ? listas.length : 0;
}

function getListaCarrito() {
    if (listaCarrito.length > 0) {
        const url = base_url + "principal/listaProductos";
        
        fetch(url, {
            method: 'POST',
            body: JSON.stringify(listaCarrito),
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(res => {
            let acciones = res.login == 1 
                ? `<a href="${base_url}clientes" class="btn btn-solid btn-sm">Comprar</a>` 
                : `<a href="javascript:;" onclick="openAccount()" class="btn btn-solid btn-sm">Iniciar Sesión</a>`;
            
            let html = "";
            res.productos.forEach((producto) => {
                let verify = producto.stock == "Ilimitado" ? "" : `max="${producto.stock}"`;
                let colorHTML = generarColorHTMLCarrito(producto.colorHexa, producto.colorSecundario, producto.nombreColor);

                let atributoHTML = `
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 5px;">
                        <span class="badge bg-info" style="font-size: 11px;">${producto.nombreTalla}</span>
                        ${colorHTML}
                    </div>`;

                html += `<li>
                    <div class="media">
                        <a href="javascript:;">
                            <img alt="Producto" class="me-3" src="${base_url + producto.imagen}">
                        </a>
                        <div class="media-body">
                            <a href="javascript:;">
                                <h6>${producto.nombre}</h6>
                                ${atributoHTML}
                            </a>
                            <h6>${res.moneda} ${producto.precio}</h6>
                            <div class="addit-box">
                                <div class="input-group">
                                    <input class="form-control agregarCantidad" type="number" id="${producto.id}" size="${producto.size}" color="${producto.color}" value="${producto.cantidad}" min="1" ${verify}>
                                    <button class="input-group-text btnDeletecart" prod="${producto.id}" size="${producto.size}" color="${producto.color}" type="button"><i class="fa fa-trash text-danger"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>`;
            });

            contentCarrito.innerHTML = html;
            document.querySelector("#contentTotal").innerHTML = `
                <li>
                    <div class="total">Total<span>${res.moneda} ${res.total}</span></div>
                </li>
                <li>
                    <div class="buttons">${acciones}</div>
                </li>`;
                
            btnEliminarCarrito();
            cambiarCantidad();
        })
        .catch(error => console.error('Error:', error));
    } else {
        if(document.querySelector("#contentTotal")) document.querySelector("#contentTotal").innerHTML = "";
        if(contentCarrito) contentCarrito.innerHTML = `<li>Carrito vacío</li>`;
    }
}

function generarColorHTMLCarrito(colorHexa, colorSecundario, nombreColor) {
    if (!colorHexa) return '<span class="badge bg-secondary" style="font-size: 10px;">Sin color</span>';

    let colorCircle = (colorSecundario && colorSecundario.trim() !== "" && colorSecundario !== "NULL")
        ? `<span style="display: inline-block; width: 18px; height: 18px; border-radius: 50%; border: 2px solid #ddd; background: linear-gradient(90deg, ${colorHexa} 50%, ${colorSecundario} 50%); vertical-align: middle; margin-right: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.2);" title="${nombreColor}"></span>`
        : `<span style="display: inline-block; width: 18px; height: 18px; border-radius: 50%; border: 2px solid #ddd; background-color: ${colorHexa}; vertical-align: middle; margin-right: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.2);" title="${nombreColor}"></span>`;

    return `${colorCircle}<span style="font-size: 11px; vertical-align: middle;">${nombreColor}</span>`;
}

function getListaDeseo() {
    if (listaDeseo.length > 0) {
        const url = base_url + "principal/listaProductos";
        
        fetch(url, {