
const nombreRegistro = document.querySelector("#nombreRegistro");
const apellidoRegistro = document.querySelector("#apellidoRegistro");
const claveRegistro = document.querySelector("#claveRegistro");
const correoRegistro = document.querySelector("#correoRegistro");
const frmRegister = document.querySelector("#frmRegister");

document.addEventListener("DOMContentLoaded", function () {
    
    //registro
    frmRegister.addEventListener("submit", function (e) {
        e.preventDefault();
        if (nombreRegistro.value == "" ||
            apellidoRegistro.value == "" ||
            correoRegistro.value == "" ||
            claveRegistro.value == ""
        ) {
            alertaPerzanalizada("TODO LOS CAMPOS SON REQUERIDOS", "warning");
        } else {
            let formData = new FormData(this);
            const url = base_url + "clientes/registroDirecto";
            const http = new XMLHttpRequest();
            http.open("POST", url, true);
            http.send(formData);
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertaPerzanalizada(res.msg, res.icono);
                    if (res.icono == "success") {
                        setTimeout(() => {
                            enviarCorreo(correoRegistro.value, res.token);
                        }, 2000);
                    }
                }
            }
        }
    });
    
});

function enviarCorreo(correo, token) {
    let formData = new FormData();
    formData.append("token", token);
    formData.append("correo", correo);
    const url = base_url + "clientes/enviarCorreo";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(formData);
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            alertaPerzanalizada(res.msg, res.icono);
            if (res.icono == "success") {
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        }
    };
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

function abrirModalLogin() {
    myModal.hide();
    modalLogin.show();
}