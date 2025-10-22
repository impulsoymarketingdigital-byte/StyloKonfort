const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");

//CAMPOS
const id = document.querySelector("#id");
const nombre = document.querySelector("#nombre");
const persona_contacto = document.querySelector("#persona_contacto");
const documento = document.querySelector("#documento");
const ruc = document.querySelector("#ruc");
const telefono = document.querySelector("#telefono");
const direccion = document.querySelector("#direccion");
const email = document.querySelector("#email");

const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));
let tblProveedores;

document.addEventListener("DOMContentLoaded", function () {
  tblProveedores = $("#tblProveedores").DataTable({
    ajax: {
      url: base_url + "proveedores/listar",
      dataSrc: "",
    },
    columns: [
      { data: "ruc" },
      { data: "nombre" },
      { data: "persona_contacto" },
      { data: "telefono" },
      { data: "email" },
      { data: "estado" },
      { data: "accion" },
      { data: "created_at", visible: false },
    ],
    order: [[7, "desc"]],
    language,
    
  });

  //levantar modal
  nuevo.addEventListener("click", function () {
    document.querySelector("#id").value = "";
    titleModal.textContent = "NUEVO PROVEEDOR";
    btnAccion.textContent = "Registrar";
    frm.reset();
    myModal.show();
  });

  //submit proveedores
  frm.addEventListener("submit", function (e) {
    e.preventDefault();
    if (nombre.value === "" || ruc.value === "") {
      alertas("EL NOMBRE Y RUC SON OBLIGATORIOS", "warning");
      return;
    }
    let data = new FormData(this);
    const url = base_url + "proveedores/registrar";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        if (res.icono == "success") {
          myModal.hide();
          tblProveedores.ajax.reload();
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
      const url = base_url + "proveedores/delete/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblProveedores.ajax.reload();
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
      const url = base_url + "proveedores/restaurar/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblProveedores.ajax.reload();
          }
          alertas(res.msg.toUpperCase(), res.icono);
        }
      };
    }
  });
}

function editCat(id) {
  const url = base_url + "proveedores/edit/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      document.querySelector("#id").value = res.id;
      document.querySelector("#nombre").value = res.nombre;
      document.querySelector("#persona_contacto").value = res.persona_contacto;
      document.querySelector("#documento").value = res.documento;
      document.querySelector("#ruc").value = res.ruc;
      document.querySelector("#telefono").value = res.telefono;
      document.querySelector("#direccion").value = res.direccion;
      document.querySelector("#email").value = res.email;
      btnAccion.textContent = "Actualizar";
      titleModal.textContent = "MODIFICAR PROVEEDOR";
      myModal.show();
    }
  };
}