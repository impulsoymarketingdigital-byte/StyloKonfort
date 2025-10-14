const inputBuscarNombre = document.querySelector("#buscarProductoNombre");
const containerNombre = document.querySelector("#containerNombre");
const errorBusqueda = document.querySelector("#errorBusqueda");

const btnAccion = document.querySelector("#btnAccion");
const totalPagar = document.querySelector("#totalPagar");
const totalDescuento = document.querySelector("#totalDescuento");
const size = document.querySelector("#size");
const color = document.querySelector("#color");
const idProducto = document.querySelector("#idProducto");
const btnAgregar = document.querySelector("#btnAgregar");
const stockActual = document.querySelector("#stockActual");
const precioCompra = document.querySelector("#precioCompra");
const cantidadCompra = document.querySelector("#cantidadCompra");

const desde = document.querySelector("#desde");
const hasta = document.querySelector("#hasta");

let listaCarrito, tblHistorial;

document.addEventListener("DOMContentLoaded", function () {
  if (localStorage.getItem(nombreKey) != null) {
    listaCarrito = JSON.parse(localStorage.getItem(nombreKey));
  }

  $("#modalSizeColor").on("hidden.bs.modal", function () {
    limpiarModal();
  });

  $("#buscarProductoNombre").autocomplete({
    source: function (request, response) {
      $.ajax({
        url: base_url + "compras/buscarPorNombre",
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
      idProducto.value = ui.item.id;
      cargarSizeTalla(ui.item.id);
      $("#modalSizeColor").modal("show");
      inputBuscarNombre.value = "";
      return false;
    },
  });

  size.addEventListener("change", function (e) {
    if (e.target.value != "") {
      cargarColoresPorSize(e.target.value);
    } else {
      color.innerHTML = '<option value="">Seleccionar</option>';
      stockActual.value = "";
      precioCompra.value = "";
      btnAgregar.classList.add("d-none");
    }
  });

  color.addEventListener("change", function (e) {
    if (e.target.value != "" && size.value != "") {
      cambiarStock(size.value, e.target.value);
    } else {
      stockActual.value = "";
      precioCompra.value = "";
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
        stockActual.value = "";
        precioCompra.value = "";
        btnAgregar.classList.add("d-none");
      }
    };
  }

  function limpiarModal() {
    idProducto.value = "";
    size.innerHTML = '<option value="">Seleccionar</option>';
    color.innerHTML = '<option value="">Seleccionar</option>';
    stockActual.value = "";
    precioCompra.value = "";
    cantidadCompra.value = "1";
    btnAgregar.classList.add("d-none");
  }

  btnAgregar.addEventListener("click", function () {
    if (size.value != "" && color.value != "") {
      const precio = parseFloat(precioCompra.value);
      const cantidad = parseInt(cantidadCompra.value);

      if (isNaN(precio) || precio <= 0) {
        alertas("INGRESA UN PRECIO VÁLIDO", "warning");
        return;
      }

      if (isNaN(cantidad) || cantidad <= 0) {
        alertas("INGRESA UNA CANTIDAD VÁLIDA", "warning");
        return;
      }

      agregarProducto(
        idProducto.value,
        cantidad,
        size.value,
        color.value,
        precio,
        0 
      );

      idProducto.value = "";
      inputBuscarNombre.value = "";
      $("#modalSizeColor").modal("hide");
    } else {
      alertas("SELECCIONA TALLA Y COLOR", "warning");
    }
  });

  $.datetimepicker.setLocale("es");
});

function cambiarStock(size, color) {
  if (!size || !color) {
    stockActual.value = "";
    precioCompra.value = "";
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

      stockActual.value = res.atrib ? res.atrib.stock : "";
      precioCompra.value = res.atrib ? res.atrib.precio_venta : "";

      let html = '<option value="">Seleccionar</option>';
      if (res.colores && res.colores.length > 0) {
        res.colores.forEach((color1) => {
          html += `<option value="${color1.id}" ${
            color == color1.id ? "selected" : ""
          }>${color1.nombre}</option>`;
        });
      }
      document.querySelector("#color").innerHTML = html;

      if (res.atrib) {
        btnAgregar.classList.remove("d-none");
      } else {
        btnAgregar.classList.add("d-none");
      }
    }
  };
}

function agregarProducto(
  idProducto,
  cantidad,
  size,
  color,
  precioCompra,
  descuento
) {
  if (localStorage.getItem(nombreKey) == null) {
    listaCarrito = [];
  } else {
    listaCarrito = JSON.parse(localStorage.getItem(nombreKey));
  }

  let productoExistente = false;

  for (let i = 0; i < listaCarrito.length; i++) {
    if (
      listaCarrito[i]["idProducto"] == idProducto &&
      listaCarrito[i]["size"] == size &&
      listaCarrito[i]["color"] == color
    ) {
      productoExistente = true;
      listaCarrito[i]["cantidad"] =
        parseInt(listaCarrito[i]["cantidad"]) + parseInt(cantidad);
      listaCarrito[i]["precioCompra"] = precioCompra;
      listaCarrito[i]["descuento"] =
        parseFloat(listaCarrito[i]["descuento"]) + parseFloat(descuento);
      break;
    }
  }

  if (!productoExistente) {
    listaCarrito.push({
      idProducto: idProducto,
      cantidad: cantidad,
      size: size,
      color: color,
      precioCompra: precioCompra,
      descuento: descuento,
    });
  }

  localStorage.setItem(nombreKey, JSON.stringify(listaCarrito));
  alertas(
    productoExistente ? "CANTIDAD ACTUALIZADA" : "PRODUCTO AGREGADO",
    "success"
  );
  mostrarProducto();
}

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

function agregarPrecio() {
  let lista = document.querySelectorAll(".inputPrecio");
  for (let i = 0; i < lista.length; i++) {
    lista[i].addEventListener("change", function () {
      let idProducto = lista[i].getAttribute("data-id");
      let size = lista[i].getAttribute("size");
      let color = lista[i].getAttribute("color");
      let precio = lista[i].value;

      if (isNaN(precio) || precio == "" || parseFloat(precio) <= 0) {
        alertas("PRECIO INVÁLIDO", "warning");
        mostrarProducto();
        return;
      }

      cambiarPrecio(idProducto, precio, size, color);
    });
  }
}

function cambiarPrecio(idProducto, precio, size, color) {
  for (let i = 0; i < listaCarrito.length; i++) {
    if (
      listaCarrito[i]["idProducto"] == idProducto &&
      listaCarrito[i]["size"] == size &&
      listaCarrito[i]["color"] == color
    ) {
      listaCarrito[i]["precioCompra"] = precio;
    }
  }
  localStorage.setItem(nombreKey, JSON.stringify(listaCarrito));
  mostrarProducto();
}

function agregarDescuento() {
  let lista = document.querySelectorAll(".inputDescuento");
  for (let i = 0; i < lista.length; i++) {
    lista[i].addEventListener("change", function () {
      let idProducto = lista[i].getAttribute("data-id");
      let size = lista[i].getAttribute("size");
      let color = lista[i].getAttribute("color");
      let descuento = lista[i].value;

      if (isNaN(descuento) || descuento == "") {
        descuento = 0;
      }

      cambiarDescuento(idProducto, descuento, size, color);
    });
  }
}

function cambiarDescuento(idProducto, descuento, size, color) {
  for (let i = 0; i < listaCarrito.length; i++) {
    if (
      listaCarrito[i]["idProducto"] == idProducto &&
      listaCarrito[i]["size"] == size &&
      listaCarrito[i]["color"] == color
    ) {
      listaCarrito[i]["descuento"] = descuento;
    }
  }
  localStorage.setItem(nombreKey, JSON.stringify(listaCarrito));
  mostrarProducto();
}

function cargarSizeTalla(idProducto) {
  const url = base_url + "compras/size/" + idProducto;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
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