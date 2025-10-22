const inputBuscarNombre = document.querySelector("#buscarProductoNombre");
const nombre = document.querySelector("#nombre");
const containerNombre = document.querySelector("#containerNombre");

const errorBusqueda = document.querySelector("#errorBusqueda");

const btnAccion = document.querySelector("#btnAccion");
const totalPagar = document.querySelector("#totalPagar");
const size = document.querySelector("#size");
const color = document.querySelector("#color");
const idProducto = document.querySelector("#idProducto");
const btnAgregar = document.querySelector("#btnAgregar");
const cantidad = document.querySelector("#cantidad");

let listaCarrito, tblHistorial;

document.addEventListener("DOMContentLoaded", function () {
  //comprobar productos en localStorage
  if (localStorage.getItem(nombreKey) != null) {
    listaCarrito = JSON.parse(localStorage.getItem(nombreKey));
  }

  $("#modalSizeColor").on("hidden.bs.modal", function () {
    limpiarModal();
  });

  //autocomplete productos
  $("#buscarProductoNombre").autocomplete({
    source: function (request, response) {
      $.ajax({
        url: base_url + "ventas/buscarPorNombre",
        dataType: "json",
        data: {
          term: request.term,
        },
        success: function (data) {
          response(data);
          if (data.length > 0) {
            errorBusqueda.textContent = "";
          } else {
            errorBusqueda.textContent = "NO HAY PRODUCTO CON ESE NOMBRE";
          }
        },
      });
    },
    minLength: 2,
    select: function (event, ui) {
      if (ui.item.descarga) {
        agregarProducto(ui.item.id, 1, ui.item.stock, 0, 0, true);
        inputBuscarNombre.value = "";
        inputBuscarNombre.focus();
      } else {
        idProducto.value = ui.item.id;
        cargarSizeTalla(ui.item.id);
        $("#modalSizeColor").modal("show");
      }

      return false;
    },
  });

  size.addEventListener("change", function (e) {
    if (e.target.value != "") {
      cargarColoresPorSize(e.target.value);
    } else {
      color.innerHTML = '<option value="">Seleccionar</option>';
      cantidad.value = "";
      btnAgregar.classList.add("d-none");
    }
  });

  color.addEventListener("change", function (e) {
    if (e.target.value != "" && size.value != "") {
      cambiarStock(size.value, e.target.value);
    } else {
      cantidad.value = "";
      btnAgregar.classList.add("d-none");
    }
  });

  function cargarColoresPorSize(sizeId) {
    let data = new FormData();
    data.append("size", sizeId);
    data.append("color", "");
    data.append("id_producto", idProducto.value);

    const url = base_url + "principal/cambiarStock";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);

        let html = '<option value="">Seleccionar</option>';
        if (res.colores && res.colores.length > 0) {
          res.colores.forEach((color1) => {
            html += `<option value="${color1.id}">${color1.nombre}</option>`;
          });
        }
        color.innerHTML = html;
        cantidad.value = "";
        btnAgregar.classList.add("d-none");
      }
    };
  }

  function limpiarModal() {
    idProducto.value = "";
    size.innerHTML = '<option value="">Seleccionar</option>';
    color.innerHTML = '<option value="">Seleccionar</option>';
    cantidad.value = "";
    btnAgregar.classList.add("d-none");
  }

  btnAgregar.addEventListener("click", function () {
    if (size.value != "" && color.value != "") {
      const stockDisponible = parseInt(cantidad.value);

      if (stockDisponible > 0) {
        agregarProducto(
          idProducto.value,
          1,
          stockDisponible,
          size.value,
          color.value,
          false
        );

        idProducto.value = "";
        inputBuscarNombre.value = "";
        $("#modalSizeColor").modal("hide");
      } else {
        alertas("STOCK NO DISPONIBLE", "warning");
      }
    } else {
      alertas("SELECCIONA TALLA Y COLOR", "warning");
    }
  });

  $.datetimepicker.setLocale("es");
});

function cambiarStock(size, color) {
  if (!size || !color) {
    cantidad.value = "";
    btnAgregar.classList.add("d-none");
    return;
  }

  let data = new FormData();
  data.append("size", size);
  data.append("color", color);
  data.append("id_producto", idProducto.value);
  const url = base_url + "principal/cambiarStock";
  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(data);
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);

      cantidad.value = res.atrib ? res.atrib.stock : "";

      let html = '<option value="">Seleccionar</option>';
      if (res.colores && res.colores.length > 0) {
        res.colores.forEach((color1) => {
          html += `<option value="${color1.id}" ${
            color == color1.id ? "selected" : ""
          }>${color1.nombre}</option>`;
        });
      }
      document.querySelector("#color").innerHTML = html;

      if (res.atrib && res.atrib.stock && parseInt(res.atrib.stock) > 0) {
        btnAgregar.classList.remove("d-none");
      } else {
        btnAgregar.classList.add("d-none");
      }
    }
  };
}

//agregar productos a localStorage
function agregarProducto(
  idProducto,
  cantidad,
  stockActual,
  size,
  color,
  descarga
) {
  if (localStorage.getItem(nombreKey) == null) {
    listaCarrito = [];
  } else {
    listaCarrito = JSON.parse(localStorage.getItem(nombreKey));
  }

  let productoExistente = false;
  let cantidadTotal = 0;

  for (let i = 0; i < listaCarrito.length; i++) {
    if (
      listaCarrito[i]["idProducto"] == idProducto &&
      listaCarrito[i]["size"] == size &&
      listaCarrito[i]["color"] == color
    ) {
      productoExistente = true;
      cantidadTotal =
        parseInt(listaCarrito[i]["cantidad"]) + parseInt(cantidad);

      if (!descarga) {
        if (
          cantidadTotal > parseInt(stockActual) ||
          parseInt(stockActual) == 0
        ) {
          alertas(
            "STOCK NO DISPONIBLE. Stock actual: " + stockActual,
            "warning"
          );
          return;
        }
      }

      listaCarrito[i]["cantidad"] = cantidadTotal;
      break;
    }
  }

  if (!productoExistente) {
    if (!descarga) {
      if (
        parseInt(cantidad) > parseInt(stockActual) ||
        parseInt(stockActual) == 0
      ) {
        alertas("STOCK NO DISPONIBLE. Stock actual: " + stockActual, "warning");
        return;
      }
    }

    listaCarrito.push({
      idProducto: idProducto,
      cantidad: cantidad,
      size: size,
      color: color,
    });
  }

  localStorage.setItem(nombreKey, JSON.stringify(listaCarrito));
  alertas(
    productoExistente ? "CANTIDAD ACTUALIZADA" : "PRODUCTO AGREGADO",
    "success"
  );
  mostrarProducto();
}

//agregar evento click para eliminar
function btnEliminarProducto() {
  let lista = document.querySelectorAll(".btnEliminar");
  for (let i = 0; i < lista.length; i++) {
    lista[i].addEventListener("click", function () {
      let idProducto = lista[i].getAttribute("data-id");
      let size = lista[i].getAttribute("size");
      let color = lista[i].getAttribute("color");
      eliminarProducto(idProducto, size, color);
    });
  }
}
//eliminar productos del table
function eliminarProducto(idProducto, size, color) {
  for (let i = 0; i < listaCarrito.length; i++) {
    if (
      listaCarrito[i]["idProducto"] == idProducto &&
      listaCarrito[i]["size"] == size &&
      listaCarrito[i]["color"] == color
    ) {
      listaCarrito.splice(i, 1);
    }
  }
  localStorage.setItem(nombreKey, JSON.stringify(listaCarrito));
  alertas("PRODUCTO ELIMINADO", "success");
  mostrarProducto();
}

//agregar eventa change para cambiar la cantidad
function agregarCantidad() {
  let lista = document.querySelectorAll(".inputCantidad");
  for (let i = 0; i < lista.length; i++) {
    lista[i].addEventListener("change", function () {
      let idProducto = lista[i].getAttribute("data-id");
      let size = lista[i].getAttribute("size");
      let color = lista[i].getAttribute("color");
      let cantidad = lista[i].value;

      if (isNaN(cantidad) || cantidad == "" || parseInt(cantidad) <= 0) {
        alertas("CANTIDAD INVÁLIDA", "warning");
        mostrarProducto();
        return;
      }

      cambiarCantidad(idProducto, cantidad, size, color);
    });
  }
}

function cambiarCantidad(idProducto, cantidad, size, color) {
  for (let i = 0; i < listaCarrito.length; i++) {
    if (
      listaCarrito[i]["idProducto"] == idProducto &&
      listaCarrito[i]["size"] == size &&
      listaCarrito[i]["color"] == color
    ) {
      listaCarrito[i]["cantidad"] = cantidad;
    }
  }
  localStorage.setItem(nombreKey, JSON.stringify(listaCarrito));
  mostrarProducto();
}

function cargarSizeTalla(idProducto) {
  const url = base_url + "ventas/size/" + idProducto;
  //hacer una instancia del objeto XMLHttpRequest
  const http = new XMLHttpRequest();
  //Abrir una Conexion - POST - GET
  http.open("GET", url, true);
  //Enviar Datos
  http.send();
  //verificar estados
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      let sizes = '<option value="">Seleccionar</option>';
      res.forEach((size) => {
        sizes += `<option value="${size.id}">${size.nombre}</option>`;
      });
      size.innerHTML = sizes;
    }
  };
}
