const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const imagen = document.querySelector("#imagen");
const categoria = document.querySelector("#categoria");
const imagen_actual = document.querySelector("#imagen_actual");
const container_img = document.querySelector("#container-img");
const btnAccion = document.querySelector("#btnAccion");
const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));
let tblCategorias;
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

  tblCategorias = $("#tblCategorias").DataTable({
    ajax: {
      url: base_url + "categorias/listar",
      dataSrc: "",
    },
    columns: [
      { data: "id" },
      { data: "categoria" },
      { data: "imagen" },
      { data: "estado" },
      { data: "accion" },
    ],
    language,
    dom,
    buttons,
  });

  //levantar modal
  nuevo.addEventListener("click", function () {
    document.querySelector("#id").value = "";
    document.querySelector("#imagen_actual").value = "";
    document.querySelector("#imagen").value = "";
    titleModal.textContent = "NUEVA CATEGORIA";
    btnAccion.textContent = "Registrar";
    frm.reset();
    myModal.show();
    //$('#nuevoModal').modal('show');
  });
  //submit categorias
  frm.addEventListener("submit", function (e) {
    e.preventDefault();
    if (categoria.value == "") {
      alertas("LA CATEGORIA ES REQUERIDO", "warning");
    } else {
      let data = new FormData(this);
      const url = base_url + "categorias/registrar";
      const http = new XMLHttpRequest();
      http.open("POST", url, true);
      http.send(data);
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            myModal.hide();
            tblCategorias.ajax.reload();
            document.querySelector("#imagen").value = "";
            document.querySelector("#container-img").innerHTML = "";
          }
          alertas(res.msg.toUpperCase(), res.icono);
        }
      };
    }
  });
});

function eliminar(idCat) {
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
      const url = base_url + "categorias/delete/" + idCat;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblCategorias.ajax.reload();
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
      const url = base_url + "categorias/restaurar/" + id;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblCategorias.ajax.reload();
          }
          alertas(res.msg.toUpperCase(), res.icono);
        }
      };
    }
  });
}


function edit(idCat) {
  const url = base_url + "categorias/edit/" + idCat;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      document.querySelector("#id").value = res.id;
      document.querySelector("#categoria").value = res.categoria;
      document.querySelector("#imagen_actual").value = res.imagen;
      container_img.innerHTML = `<img class="img-thumbnail" src="${
        base_url + res.imagen
      }" width="300">`;
      btnAccion.textContent = "Actualizar";
      titleModal.textContent = "MODIFICAR CATEGORIA";
      myModal.show();
      //$('#nuevoModal').modal('show');
    }
  };
}
