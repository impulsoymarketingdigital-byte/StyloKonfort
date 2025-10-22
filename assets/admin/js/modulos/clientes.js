const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");

//CAMPOS
const id = document.querySelector("#id");
const nombre = document.querySelector("#nombre");
const apellido = document.querySelector("#apellido");
const telefono = document.querySelector("#telefono");
const correo = document.querySelector("#correo");
const direccion = document.querySelector("#direccion");
const tipo_cliente = document.querySelector("#tipo_cliente");

const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));
let tblClientes;

document.addEventListener("DOMContentLoaded", function () {
  tblClientes = $("#tblClientes").DataTable({
    ajax: {
      url: base_url + "clientes/listar",
      dataSrc: "",
    },
    columns: [
      { data: "id" },
      { data: "nombre" },
      { data: "apellido" },
      { data: "telefono" },
      { data: "correo" },
      { data: "tipo_cliente" },
      { data: "estado" },
      { data: "accion" },
    ],
    order: [[0, "desc"]],
    language,
    
  });

  //levantar modal
  nuevo.addEventListener("click", function () {
    document.querySelector("#id").value = "";
    titleModal.textContent = "NUEVO CLIENTE";
    btnAccion.textContent = "Registrar";
    frm.reset();
    myModal.show();
  });

  //submit clientes
  frm.addEventListener("submit", function (e) {
    e.preventDefault();
    if (
      nombre.value === "" ||
      apellido.value === "" ||
      tipo_cliente.value === ""
    ) {
      alertas("TODOS LOS CAMPOS OBLIGATORIOS SON REQUERIDOS", "warning");
      return;
    }
    let data = new FormData(this);
    const url = base_url + "clientes/registrar";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        if (res.icono == "success") {
          myModal.hide();
          tblClientes.ajax.reload();
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
      const url = base_url + "clientes/delete/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblClientes.ajax.reload();
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
      const url = base_url + "clientes/restaurar/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblClientes.ajax.reload();
          }
          alertas(res.msg.toUpperCase(), res.icono);
        }
      };
    }
  });
}

function editCat(id) {
  const url = base_url + "clientes/edit/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      document.querySelector("#id").value = res.id;
      document.querySelector("#nombre").value = res.nombre;
      document.querySelector("#apellido").value = res.apellido;
      document.querySelector("#telefono").value = res.telefono;
      document.querySelector("#correo").value = res.correo || "";
      document.querySelector("#direccion").value = res.direccion;
      document.querySelector("#tipo_cliente").value = res.tipo_cliente;
      btnAccion.textContent = "Actualizar";
      titleModal.textContent = "MODIFICAR CLIENTE";
      myModal.show();
    }
  };
}