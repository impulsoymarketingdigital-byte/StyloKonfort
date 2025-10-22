const nombreRegistro = document.querySelector("#nombreRegistro");
const apellidoRegistro = document.querySelector("#apellidoRegistro");
const claveRegistro = document.querySelector("#claveRegistro");
const correoRegistro = document.querySelector("#correoRegistro");
const telefonoRegistro = document.querySelector("#telefonoRegistro");
const direccionRegistro = document.querySelector("#direccionRegistro");
const documentoRegistro = document.querySelector("#documentoRegistro");
const tipoClienteRegistro = document.querySelector("#tipoClienteRegistro");
const frmRegister = document.querySelector("#frmRegister");

document.addEventListener("DOMContentLoaded", function () {
  //registro
  frmRegister.addEventListener("submit", function (e) {
    e.preventDefault();
    if (
      nombreRegistro.value == "" ||
      apellidoRegistro.value == "" ||
      correoRegistro.value == "" ||
      claveRegistro.value == "" ||
      telefonoRegistro.value == "" ||
      direccionRegistro.value == "" ||
      documentoRegistro.value == "" ||
      tipoClienteRegistro.value == ""
    ) {
      alertaPerzanalizada("TODOS LOS CAMPOS SON REQUERIDOS", "warning");
    } else {
      let formData = new FormData(this);
      const url = base_url + "clientes/registroDirecto";
      const http = new XMLHttpRequest();
      http.open("POST", url, true);
      http.send(formData);
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            enviarCorreo(correoRegistro.value, res.token);
            Swal.fire({
              icon: "success",
              title: "¡Registro Exitoso!",
              text: "Se ha registrado correctamente. Por favor, verifique su correo electrónico para activar su cuenta.",
              confirmButtonText: "Entendido",
              confirmButtonColor: "#667eea",
            });
          } else {
            alertaPerzanalizada(res.msg, res.icono);
          }
        }
      };
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
  formData.append(
    "mensaje",
    "GRACIAS POR SUSCRIBIRSE A NUESTROS PRODUCTOS, TE INFORMAREMOS SOBRE LAS NUEVAS ACTUALIZACIONES"
  );
  formData.append("correo", correo);
  const url = base_url + "clientes/enviarSuscripcion";
  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(formData);
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      alertaPerzanalizada(res.msg, res.icono);
      if (res.icono == "success") {
        subscribeEmail.value = "";
      }
    }
  };
}

function abrirModalLogin() {
  myModal.hide();
  modalLogin.show();
}
