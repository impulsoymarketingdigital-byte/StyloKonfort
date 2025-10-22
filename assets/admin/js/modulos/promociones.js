const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const imagen = document.querySelector("#imagen");
const titulo = document.querySelector("#titulo");
const imagen_actual = document.querySelector("#imagen_actual");
const container_img = document.querySelector("#container-img");
const btnAccion = document.querySelector("#btnAccion");
const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));
let tblPromociones;

document.addEventListener("DOMContentLoaded", function () {
  imagen.addEventListener("change", function (e) {
    imagen_actual.value = "";
    if (
      e.target.files[0].type == "image/png" ||
      e.target.files[0].type == "image/jpg" ||
      e.target.files[0].type == "image/jpeg"
    ) {
      const url = e.target.files[0];
      const tmpUrl = URL.createObjectURL(url);
      container_img.innerHTML = `<img class="img-thumbnail" src="${tmpUrl}" width="200">`;
    } else {
      imagen.value = "";
      alertas("warning", "SOLO SE PERMITEN IMG DE TIPO PNG-JPG-JPEG");
    }
  });

  tblPromociones = $("#tblPromociones").DataTable({
    ajax: {
      url: base_url + "promociones/listar",
      dataSrc: "",
    },
    columns: [
      { data: "id" },
      { data: "titulo" },
      { data: "imagen" },
      { data: "fecha_inicio" },
      { data: "fecha_fin" },
      { data: "vigencia" },
      { data: "estado" },
      { data: "accion" },
    ],
    language,
    
  });

  // Levantar modal
  nuevo.addEventListener("click", function () {
    document.querySelector("#id").value = "";
    document.querySelector("#imagen_actual").value = "";
    document.querySelector("#imagen").value = "";
    document.querySelector("#container-img").innerHTML = "";
    titleModal.textContent = "NUEVA PROMOCIÓN";
    btnAccion.textContent = "Registrar";
    frm.reset();
    myModal.show();
  });

  // Submit promociones
  frm.addEventListener("submit", function (e) {
    e.preventDefault();
    if (titulo.value == "" || document.querySelector("#fecha_inicio").value == "" || document.querySelector("#fecha_fin").value == "") {
      alertas("TÍTULO Y FECHAS SON REQUERIDOS", "warning");
    } else {
      let data = new FormData(this);
      const url = base_url + "promociones/registrar";
      const http = new XMLHttpRequest();
      http.open("POST", url, true);
      http.send(data);
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            myModal.hide();
            tblPromociones.ajax.reload();
            document.querySelector("#imagen").value = "";
            document.querySelector("#container-img").innerHTML = "";
          }
          alertas(res.msg.toUpperCase(), res.icono);
        }
      };
    }
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
      const url = base_url + "promociones/delete/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblPromociones.ajax.reload();
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
      const url = base_url + "promociones/restaurar/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblPromociones.ajax.reload();
          }
          alertas(res.msg.toUpperCase(), res.icono);
        }
      };
    }
  });
}

function edit(id) {
  const url = base_url + "promociones/edit/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      document.querySelector("#id").value = res.id;
      document.querySelector("#titulo").value = res.titulo;
      document.querySelector("#descripcion").value = res.descripcion;
      document.querySelector("#link").value = res.link;
      document.querySelector("#fecha_inicio").value = res.fecha_inicio;
      document.querySelector("#fecha_fin").value = res.fecha_fin;
      document.querySelector("#imagen_actual").value = res.imagen;
      container_img.innerHTML = `<img class="img-thumbnail" src="${base_url + res.imagen}" width="300">`;
      btnAccion.textContent = "Actualizar";
      titleModal.textContent = "MODIFICAR PROMOCIÓN";
      myModal.show();
    }
  };
}