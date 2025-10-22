const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");

//CAMPOS
const id = document.querySelector("#id");
const nombre = document.querySelector("#nombre");
const codigo = document.querySelector("#codigo");
const direccion = document.querySelector("#direccion");
const telefono = document.querySelector("#telefono");

const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));
let tblSucursales;
document.addEventListener("DOMContentLoaded", function () {
  tblSucursales = $("#tblSucursales").DataTable({
    ajax: {
      url: base_url + "sucursales/listar",
      dataSrc: "",
    },
    columns: [
      { data: "codigo" },
      { data: "nombre" },
      { data: "direccion" },
      { data: "estado" },
      { data: "accion" },
      { data: "created_at", visible: false },
    ],
    order: [[4, "desc"]],

    language,
    
  });

  //levantar modal
  nuevo.addEventListener("click", function () {
    document.querySelector("#id").value = "";
    titleModal.textContent = "NUEVA SUCURSAL";
    btnAccion.textContent = "Registrar";
    frm.reset();
    myModal.show();
    //$('#nuevoModal').modal('show');
  });
  //submit categorias
  frm.addEventListener("submit", function (e) {
    e.preventDefault();
    if (
      nombre.value === "" ||
      codigo.value === "" ||
      direccion.value === "" ||
      telefono.value === ""
    ) {
      alertas("TODOS LOS CAMPOS SON OBLIGATORIOS", "warning");
      return;
    }
    let data = new FormData(this);
    const url = base_url + "sucursales/registrar";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        if (res.icono == "success") {
          myModal.hide();
          tblSucursales.ajax.reload();
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
      const url = base_url + "sucursales/delete/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblSucursales.ajax.reload();
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
      const url = base_url + "sucursales/restaurar/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblSucursales.ajax.reload();
          }
          alertas(res.msg.toUpperCase(), res.icono);
        }
      };
    }
  });
}

function editCat(id) {
  const url = base_url + "sucursales/edit/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      document.querySelector("#id").value = res.id;
      document.querySelector("#codigo").value = res.codigo;
      document.querySelector("#nombre").value = res.nombre;
      document.querySelector("#direccion").value = res.direccion;
      document.querySelector("#telefono").value = res.telefono;
      btnAccion.textContent = "Actualizar";
      titleModal.textContent = "MODIFICAR SUCURSAL";
      myModal.show();
      //$('#nuevoModal').modal('show');
    }
  };
}
