const frmLogin = document.querySelector("#frmLogin");

const correoLogin = document.querySelector("#correoLogin");
const claveLogin = document.querySelector("#claveLogin");

const inputSearch = document.querySelector('#inputSearch');
const inputSearch1 = document.querySelector('#inputSearch1');

document.addEventListener("DOMContentLoaded", function () {
    // //login directo
    frmLogin.addEventListener("submit", function (e) {
        e.preventDefault();
        if (frmLogin.correoLogin.value == "" ||
            frmLogin.claveLogin.value == "") {
                document.querySelector('#errorLogin').textContent = "TODO LOS CAMPOS SON REQUERIDOS";
                document.querySelector('#errorLogin').classList.add('text-danger');
        } else {
            let formData = new FormData(this);
            const url = base_url + "clientes/loginDirecto";
            const http = new XMLHttpRequest();
            http.open("POST", url, true);
            http.send(formData);
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    document.querySelector('#errorLogin').textContent = res.msg;
                    if (res.icono == "success") {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                }
            }
        }
    });

    inputSearch.addEventListener('keyup', function (e) {
        buscarProducto('resultBusqueda', e.target.value);
    })

    inputSearch1.addEventListener('keyup', function (e) {
        buscarProducto('resultBusqueda1', e.target.value);
    })

    //SUSCRIBIRSE
    // suscribirse.addEventListener('click', function () {
    //     if (subscribeEmail.value == '') {
    //         alertaPerzanalizada('EL CORREO ES REQUERIDO', 'warning');
    //     } else {
    //         const url = base_url + "clientes/suscribirse/" + subscribeEmail.value;
    //         const http = new XMLHttpRequest();
    //         http.open("GET", url, true);
    //         http.send();
    //         http.onreadystatechange = function () {
    //             if (this.readyState == 4 && this.status == 200) {
    //                 const res = JSON.parse(this.responseText);
    //                 alertaPerzanalizada(res.msg, res.type);
    //                 if (res.type == 'success') {
    //                     enviarCorreoSuscripcion(subscribeEmail.value);
    //                 }
    //             }
    //         };
    //     }
    // })
});

function buscarProducto(resultBusqueda, valor) {
    const url = base_url + "principal/busqueda/" + valor;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let html = '';
            res.forEach(producto => {
                html += `<a href="${base_url + 'principal/detail/' + producto.slug}" class="list-group-item list-group-item-action">${producto.nombre}</a>`;
            });
            document.querySelector('#' + resultBusqueda).innerHTML = html;
        }
    }
}

function enviarCorreoSuscripcion(correo) {
    let formData = new FormData();
    formData.append("mensaje", 'GRACIAS POR SUSCRIBIRSE A NUESTROS PRODUCTOS, TE INFORMAREMOS SOBRE LAS NUEVAS ACTUALIZACIONES');
    formData.append("correo", correo);
    const url = base_url + "clientes/enviarSuscripcion";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(formData);
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            alertaPerzanalizada(res.msg, res.icono);
            if (res.icono == 'success') {
                subscribeEmail.value = '';
            }
        }
    };
}