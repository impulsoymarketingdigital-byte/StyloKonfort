const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");
const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));

const containerGaleria = document.querySelector("#containerGaleria");

const cantidad = document.querySelector("#cantidad");

let tblProductos;

var firstTabEl = document.querySelector("#myTab li:last-child button");
var firstTab = new bootstrap.Tab(firstTabEl);

const modalGaleria = new bootstrap.Modal(
  document.getElementById("modalGaleria")
);

const modalMantenimiento = new bootstrap.Modal(
  document.getElementById("modalMantenimiento")
);

const btnProcesar = document.querySelector("#btnProcesar");
const tblMantenimiento = document.querySelector("#tblMantenimiento tbody");

const btnAgregar = document.querySelector("#btnAgregar");
const frmMantenimiento = document.querySelector("#frmMantenimiento");

document.addEventListener("DOMContentLoaded", function () {
  $(".select").select2({
    theme: "bootstrap-5",
    dropdownParent: $("#modalMantenimiento"),
  });

  tblProductos = $("#tblProductos").DataTable({
    ajax: {
      url: base_url + "productos/listar",
      dataSrc: "",
    },
    columns: [
      { data: "nombre" },
      { data: "categoria" },
      { data: "marca" },
      { data: "estado" },
      { data: "accion" },
      { data: "created_at", visible: false },
    ],
    responsive: true,
    language,
    dom,
    buttons,
    order: [[5, "desc"]],
  });

  //levantar modal
  nuevo.addEventListener("click", function () {
    document.querySelector("#id").value = "";
    titleModal.textContent = "NUEVO PRODUCTO";
    btnAccion.textContent = "Registrar";
    frm.reset();
    myModal.show();
    //$('#nuevoModal').modal('show');
  });

  //submit productos
  frm.addEventListener("submit", function (e) {
    e.preventDefault();
    if(frm.categoria.value === "" || frm.marca.value === ""){
        alertas("Seleccione categoría y marca", "warning");
        return;
    }
    let data = new FormData(this);
    const url = base_url + "productos/registrar";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        alertas(res.msg.toUpperCase(), res.icono);
        if (res.icono == "success") {
          frm.reset();
          myModal.hide();
          tblProductos.ajax.reload();
        }
      }
    };
  });

  //########### IMAGENES ##########
  let myDropzone = new Dropzone("#frmImagenes", {
    dictDefaultMessage: "Arrastar y Soltar Imagenes",
    acceptedFiles: ".png, .jpg, .jpeg",
    maxFiles: 10,
    addRemoveLinks: true,
    autoProcessQueue: false,
    parallelUploads: 10,
  });
  btnProcesar.addEventListener("click", function () {
    myDropzone.processQueue();
  });

  myDropzone.on("complete", function (file) {
    myDropzone.removeFile(file);
    alertas("IMAGENES SUBIDA", "success");
    setTimeout(() => {
      modalGaleria.hide();
    }, 1500);
  });

  frmMantenimiento.addEventListener("submit", function (e) {
    e.preventDefault();
    const id_producto = document.querySelector("#id_producto");
    const talla = document.querySelector("#talla");
    const color = document.querySelector("#color");
    const price = document.querySelector("#price");
    if (talla.value == "" || color.value == "" || price.value == "") {
      alertas("ATRIBUTOS VACIO", "warning");
    } else {
      let data = new FormData(this);
      const url = base_url + "productos/mantenimiento";
      const http = new XMLHttpRequest();
      http.open("POST", url, true);
      http.send(data);
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          alertas(res.msg.toUpperCase(), res.icono);
          if (res.icono == "success") {
            mantenimiento(id_producto.value);
            price.value = "";
          }
        }
      };
    }
  });
});

function eliminarProducto(id) {
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
      const url = base_url + "productos/delete/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblProductos.ajax.reload();
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
      const url = base_url + "productos/restaurar/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblProductos.ajax.reload();
          }
          alertas(res.msg.toUpperCase(), res.icono);
        }
      };
    }
  });
}

function edit(id) {
  const url = base_url + "productos/edit/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      document.querySelector("#id").value = res.id;
      document.querySelector("#codigo").value = res.codigo;
      document.querySelector("#nombre").value = res.nombre;
      document.querySelector("#genero").value = res.genero;
      document.querySelector("#precio").value = res.precio;
      document.querySelector("#categoria").value = res.id_categoria;
      document.querySelector("#marca").value = res.id_marca;
      document.querySelector("#descripcion").value = res.descripcion;
      btnAccion.textContent = "Actualizar";
      myModal.show();
    }
  };
}

function agregarImagenes(id) {
  const url = base_url + "productos/verGaleria/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      document.querySelector("#idProducto").value = id;
      let html = "";
      let destino = base_url + "assets/images/productos/" + id + "/";
      for (let i = 0; i < res.length; i++) {
        html += `<div class="col-md-3">
                    <img class="img-thumbnail" src="${destino + res[i]}">
                    <div class="d-grid">
                        <button class="btn btn-danger btnEliminarImagen" type="button" data-id="${id}" data-name="${
          id + "/" + res[i]
        }">Eliminar</button>
                    </div>     
                </div>`;
      }
      containerGaleria.innerHTML = html;
      eliminarImagen();
      modalGaleria.show();
    }
  };
}

function eliminarImagen() {
  let lista = document.querySelectorAll(".btnEliminarImagen");
  for (let i = 0; i < lista.length; i++) {
    lista[i].addEventListener("click", function () {
      let id = lista[i].getAttribute("data-id");
      let nombre = lista[i].getAttribute("data-name");
      eliminar(id, nombre);
    });
  }
}

function eliminar(id, nombre) {
  const url = base_url + "productos/eliminarImg";
  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(
    JSON.stringify({
      url: nombre,
    })
  );
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      alertas(res.msg, res.icono);
      if (res.icono == "success") {
        agregarImagenes(id);
      }
    }
  };
}

//##### MANTENIMIENTO #######
function mantenimiento(id) {
  const url = base_url + "productos/getAtributos/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      let html = "";
      res.detalle.forEach((atributo) => {
        html += `<tr>
                    <td>${atributo.talla}</td>
                    <td><span class="badge" style="background: ${atributo.color};">${atributo.nombre}</span></td>
                    <td>${atributo.cantidad}</td>
                    <td>${atributo.precio}</td>
                    <td>
                    <button class="btn btn-danger btn-sm" onclick="eliminarDetalle(${atributo.id})"><i class="fas fa-times-circle"></i></button>
                    </td>
                </tr>`;
      });
      document.querySelector("#id_producto").value = res.producto.id;
      document.querySelector("#producto").value =
        res.producto.nombre + " - " + res.producto.precio;
      document.querySelector("#codigo_producto").value = res.producto.codigo;

      tblMantenimiento.innerHTML = html;
      modalMantenimiento.show();
    }
  };
}

function eliminarDetalle(id) {
  const id_producto = document.querySelector("#id_producto");
  const url = base_url + "productos/eliminarDetalle/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      alertas(res.msg.toUpperCase(), res.icono);
      if (res.icono == "success") {
        mantenimiento(id_producto.value);
      }
    }
  };
}
