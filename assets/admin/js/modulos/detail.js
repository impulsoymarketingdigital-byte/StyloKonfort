const comprar = document.querySelector('#comprar');
const btnAddCart = document.querySelector('#btnAddCart');
const cantidad = document.querySelector('#product-quanity');
const value = document.querySelector('#var-value');
const controles = document.querySelector('#controles');
const idProducto = document.querySelector('#idProducto');

const color = document.querySelector('#color');
const size = document.querySelector('#size');
const stockActual = document.querySelector('#stockActual');

document.addEventListener("DOMContentLoaded", function () {
    if (btnAddCart && comprar) {
        btnAddCart.addEventListener('click', function () {
            if (size && color) {
                if (color.value == '' || size.value == '') {
                    alertaPerzanalizada("SELECCIONA SIZE Y COLOR", "warning")
                }else{
                    agregarCarrito(idProducto.value, cantidad.value, size.value, color.value);
                }
                
            }else{
                agregarCarrito(idProducto.value, cantidad.value, 0, 0);
            }
            
        })
    
        comprar.addEventListener('click', function () {
            if (size && color) {
                if (color.value == '' || size.value == '') {
                    alertaPerzanalizada("SELECCIONA SIZE Y COLOR", "warning");
                    return;
                }else{
                    addProduct(size.value, color.value);
                } 
            } else {
                addProduct(0, 0);
            }
            
        })
    }
    
    if (size && color) {
        size.addEventListener('change', function (e) {
            if (e.target.value != '') {
                cambiarStock(e.target.value, color.value);
            }else{
                controles.classList.add('d-none');
                cantidad.value = 1;
                value.textContent = 1;
                btnAddCart.classList.add('d-none');
                comprar.classList.add('d-none');
                stockActual.textContent = 0;
            }
            
        })
    
        color.addEventListener('change', function (e) {
            if (e.target.value != '') {
                cambiarStock(size.value, e.target.value);
            }else{
                controles.classList.add('d-none');
                cantidad.value = 1;
                value.textContent = 1;
                btnAddCart.classList.add('d-none');
                comprar.classList.add('d-none');
                stockActual.textContent = 0;
            }
        })
    }    

    // $('.select').select2({
    //     theme: 'bootstrap-5'
    // });

});

function addProduct(size, color) {
    for (let i = 0; i < listaCarrito.length; i++) {
        if (listaCarrito[i]["idProducto"] == idProducto.value && listaCarrito[i]["size"] == size && listaCarrito[i]["color"] == color) {
            getListaCarrito();
            myModal.show();
            return;
        }
        
    }
    listaCarrito.concat(localStorage.getItem("listaCarrito"));
    listaCarrito.push({
        idProducto: idProducto.value,
        cantidad: cantidad.value,
        size: size,
        color: color
    });
    localStorage.setItem("listaCarrito", JSON.stringify(listaCarrito));
    getListaCarrito();
    myModal.show();
    cantidadCarrito();
}

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
            let html = '<option value="">Seleccionar</option>';
            res.colores.forEach(color1 => {
                html += `<option value="${color1.id}" ${ color == color1.id ? 'selected' : '' }>${color1.nombre}</option>`;
            });
            document.querySelector('#color').innerHTML = html;
            stockActual.textContent = res.atrib.cantidad;
            document.querySelector('#precio').textContent = res.moneda + ' ' + res.atrib.precio;
            if (parseInt(res.atrib.cantidad) > 0) {
                controles.classList.remove('d-none');
                btnAddCart.removeAttribute('disabled');
                cantidad.setAttribute('max', res.atrib.cantidad);
                cantidad.value = 1;
                value.textContent = 1;
                btnAddCart.classList.remove('d-none');
                comprar.classList.remove('d-none');
            }else{
                controles.classList.add('d-none');
                cantidad.value = 1;
                value.textContent = 1;
                btnAddCart.classList.add('d-none');
                comprar.classList.add('d-none');
            }
        }
    }
}

function deseoSizeColor(id_producto) {
    if (size.value != '' && color.value != '') {
        agregarDeseo(id_producto, size.value, color.value, false);
    }else{
        alertaPerzanalizada("SELECCIONA SIZE Y COLOR", "warning")
    }
}