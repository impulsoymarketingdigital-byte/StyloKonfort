const frm = document.querySelector("#formulario");
const email = document.querySelector("#email");
const clave = document.querySelector("#clave");

document.addEventListener("DOMContentLoaded", function() {
    frm.addEventListener("submit", function(e) {
        e.preventDefault();
        
        if (email.value == "" || clave.value == "") {
            alertas("Todos los campos son requeridos", "warning");
        } else {
            let data = new FormData(this);
            const url = base_url + "admin/validar";
            const http = new XMLHttpRequest();
            http.open("POST", url, true);
            http.send(data);
            
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    
                    if (res.icono == 'success') {
                        // Alerta con contador
                        let timerInterval;
                        Swal.fire({
                            title: '¡BIENVENIDO!',
                            html: res.msg + '<br>Serás redireccionado en <b></b> segundos.',
                            icon: 'success',
                            timer: 1000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                                const b = Swal.getHtmlContainer().querySelector('b');
                                timerInterval = setInterval(() => {
                                    b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                                }, 100);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                            }
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.timer) {
                                window.location = base_url + 'admin/home';
                            }
                        });
                    } else {
                        // Error normal
                        alertas(res.msg, res.icono);
                    }
                }
            }
        }
    });
});