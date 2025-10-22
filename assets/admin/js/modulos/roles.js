const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");

const id = document.querySelector("#id");
const nombre = document.querySelector("#nombre");
let listaCheck = document.querySelectorAll(".listaCheck");

const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));
let tblRoles;

document.addEventListener("DOMContentLoaded", function () {
  tblRoles = $("#tblRoles").DataTable({
    ajax: {
      url: base_url + "roles/listar",
      dataSrc: "",
    },
    columns: [
      { data: "id" },
      { data: "nombre" },
      { data: "estado" },
      { data: "accion" },
      { data: "created_at", visible: false },
    ],
    order: [[4, "desc"]],
    buttons,
  });

  //levantar modal
  nuevo.addEventListener("click", function () {
    document.querySelector("#id").value = "";
    titleModal.textContent = "NUEVO ROL";
    btnAccion.textContent = "Registrar";
    // Desmarcar todos los checkboxes
    listaCheck.forEach((check) => (check.checked = false));
    frm.reset();
    myModal.show();
  });

  //submit roles
  frm.addEventListener("submit", function (e) {
    e.preventDefault();

    if (nombre.value === "") {
      alertas("EL NOMBRE ES OBLIGATORIO", "warning");
      return;
    }

    // ✅ VALIDAR QUE AL MENOS UN PERMISO ESTÉ SELECCIONADO
    const checkboxes = document.querySelectorAll(".listaCheck:checked");
    if (checkboxes.length === 0) {
      alertas("DEBES SELECCIONAR AL MENOS UN PERMISO", "warning");
      return;
    }

    let data = new FormData(this);
    const url = base_url + "roles/registrar";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        if (res.icono == "success") {
          myModal.hide();
          tblRoles.ajax.reload();
        }
        alertas(res.msg.toUpperCase(), res.icono);
      }
    };
  });
});

function eliminar(id) {
  Swal.fire({
    title: "Aviso?",
    text: "Esta seguro de eliminar el registro!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Eliminar!",
  }).then((result) => {
    if (result.isConfirmed) {
      const url = base_url + "roles/delete/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblRoles.ajax.reload();
          }
          alertas(res.msg.toUpperCase(), res.icono);
        }
      };
    }
  });
}

function restaurar(id) {
  Swal.fire({
    title: "Aviso?",
    text: "Esta seguro de restaurar el registro!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Restaurar!",
  }).then((result) => {
    if (result.isConfirmed) {
      const url = base_url + "roles/restaurar/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblRoles.ajax.reload();
          }
          alertas(res.msg.toUpperCase(), res.icono);
        }
      };
    }
  });
}

function editCat(id) {
  const url = base_url + "roles/edit/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);

      document.querySelector("#id").value = res.rol.id;
      document.querySelector("#nombre").value = res.rol.nombre;

      // Desmarcar todos primero
      listaCheck.forEach((check) => (check.checked = false));

      // Marcar los permisos asignados
      let arreglo = res.permisos;
      listaCheck.forEach((check) => {
        if (arreglo.includes(check.value)) {
          check.checked = true;
        }
      });

      btnAccion.textContent = "Actualizar";
      titleModal.textContent = "MODIFICAR ROL";
      myModal.show();
    }
  };
}
