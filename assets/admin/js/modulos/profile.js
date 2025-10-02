const nombre = document.querySelector('#nombrePerfil');
const apellido = document.querySelector('#apellidoPerfil');
const correo = document.querySelector('#correoPerfil');
const btnAccion = document.querySelector('#btnGuardarCambios');
const formularioPerfil = document.querySelector('#formularioPerfil');

const frmPass = document.querySelector('#frmPass');
const claveActual = document.querySelector('#claveActual');
const claveNueva = document.querySelector('#claveNueva');

document.addEventListener('DOMContentLoaded', function () {
    formularioPerfil.addEventListener('submit', function (e) {
        e.preventDefault();
        if (nombre.value == '') {
            alertas('EL NOMBRE ES REQUERIDO', 'warning');
        } else if (apellido.value == '') {
            alertas('EL APELLIDO ES REQUERIDO', 'warning');
        } else if (correo.value == '') {
            alertas('EL CORREO ES REQUERIDO', 'warning');
        } else {
            const url = base_url + 'usuarios/modificarDatos';
            //hacer una instancia del objeto XMLHttpRequest 
            const http = new XMLHttpRequest();
            //Abrir una Conexion - POST - GET
            http.open('POST', url, true);
            //Enviar Datos
            http.send(new FormData(this));
            //verificar estados
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertas(res.msg, res.type); 
                    if (res.type == 'success') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }                                      
                }
            }
        }
    })

    frmPass.addEventListener('submit', function (e) {
        e.preventDefault();
        if (claveActual.value == '') {
            alertas('LA CLAVE ACTUAL ES REQUERIDO', 'warning');
        } else if (claveNueva.value == '') {
            alertas('CLAVE NUEVA ES REQUERIDO', 'warning');
        } else {
            const url = base_url + 'usuarios/cambiarPass';
            //hacer una instancia del objeto XMLHttpRequest 
            const http = new XMLHttpRequest();
            //Abrir una Conexion - POST - GET
            http.open('POST', url, true);
            //Enviar Datos
            http.send(new FormData(this));
            //verificar estados
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    alertas(res.msg, res.type);
                    if (res.type == 'success') {
                        setTimeout(() => {
                            window.location = base_url + 'admin/salir';
                        }, 1500);
                    }                   
                }
            }
        }
    })
})