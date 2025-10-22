const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");

//CAMPOS
const id = document.querySelector("#id");
const nombre = document.querySelector("#nombre");
const codigo = document.querySelector("#codigo");
const direccion = document.querySelector("#direccion");
const id_sucursal = document.querySelector("#id_sucursal");

const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));
let tblAlmacenes;

document.addEventListener("DOMContentLoaded", function () {
  tblAlmacenes = $("#tblAlmacenes").DataTable({
    ajax: {
      url: base_url + "almacenes/listar",
      dataSrc: "",
    },
    columns: [
      { data: "codigo" },
      { data: "nombre" },
      { data: "direccion" },
      { data: "sucursal" },
      { data: "estado" },
      { data: "accion" },
      { data: "created_at", visible: false },
    ],
    order: [[6, "desc"]],
    language,
    
  });

  //levantar modal
  nuevo.addEventListener("click", function () {
    document.querySelector("#id").value = "";
    titleModal.textContent = "NUEVO ALMACEN";
    btnAccion.textContent = "Registrar";
    frm.reset();
    cargarSucursales();
    myModal.show();
  });

  //submit almacenes
  frm.addEventListener("submit", function (e) {
    e.preventDefault();
    if (
      nombre.value === "" ||
      codigo.value === "" ||
      id_sucursal.value === ""
    ) {
      alertas("TODOS LOS CAMPOS SON OBLIGATORIOS", "warning");
      return;
    }
    let data = new FormData(this);
    const url = base_url + "almacenes/registrar";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        if (res.icono == "success") {
          myModal.hide();
          tblAlmacenes.ajax.reload();
        }
        alertas(res.msg.toUpperCase(), res.icono);
      }
    };
  });
});

function cargarSucursales() {
  const url = base_url + "almacenes/sucursales";
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const sucursales = JSON.parse(this.responseText);
      let html = '<option value="">Seleccionar Sucursal</option>';
      sucursales.forEach((sucursal) => {
        html += `<option value="${sucursal.id}">${sucursal.nombre}</option>`;
      });
      id_sucursal.innerHTML = html;
    }
  };
}

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
      const url = base_url + "almacenes/delete/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblAlmacenes.ajax.reload();
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
      const url = base_url + "almacenes/restaurar/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblAlmacenes.ajax.reload();
          }
          alertas(res.msg.toUpperCase(), res.icono);
        }
      };
    }
  });
}

function editCat(id) {
  const url = base_url + "almacenes/edit/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      cargarSucursales();
      setTimeout(() => {
        document.querySelector("#id").value = res.id;
        document.querySelector("#codigo").value = res.codigo;
        document.querySelector("#nombre").value = res.nombre;
        document.querySelector("#direccion").value = res.direccion;
        document.querySelector("#id_sucursal").value = res.id_sucursal;
        btnAccion.textContent = "Actualizar";
        titleModal.textContent = "MODIFICAR ALMACEN";
        myModal.show();
      }, 200);
    }
  };
}