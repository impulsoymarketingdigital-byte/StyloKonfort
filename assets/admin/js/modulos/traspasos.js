const inputBuscarNombre = document.querySelector("#buscarProductoNombre");
const containerNombre = document.querySelector("#containerNombre");
const errorBusqueda = document.querySelector("#errorBusqueda");

const btnAccion = document.querySelector("#btnAccion");
const totalProductos = document.querySelector("#totalProductos");
const size = document.querySelector("#size");
const color = document.querySelector("#color");
const idProducto = document.querySelector("#idProducto");
const btnAgregar = document.querySelector("#btnAgregar");
const stockActual = document.querySelector("#stockActual");
const cantidadTraspaso = document.querySelector("#cantidadTraspaso");

const almacenOrigen = document.querySelector("#almacenOrigen");
const almacenDestino = document.querySelector("#almacenDestino");
const errorAlmacenOrigen = document.querySelector("#errorAlmacenOrigen");
const errorAlmacenDestino = document.querySelector("#errorAlmacenDestino");

let listaCarrito = [];

document.addEventListener("DOMContentLoaded", function () {
  // Inicializar carrito desde localStorage
  if (localStorage.getItem(nombreKey) != null) {
    listaCarrito = JSON.parse(localStorage.getItem(nombreKey));
  } else {
    listaCarrito = [];
  }

  // Habilitar búsqueda solo si hay almacén origen seleccionado
  almacenOrigen.addEventListener("change", function () {
    if (almacenOrigen.value != "") {
      inputBuscarNombre.disabled = false;
      errorAlmacenOrigen.textContent = "";
      
      // Si cambia el almacén origen, limpiar el carrito
      if (listaCarrito && listaCarrito.length > 0) {
        Swal.fire({
          title: "¿Cambiar almacén origen?",
          text: "Se vaciará el carrito actual",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Sí, cambiar",
          cancelButtonText: "Cancelar",
        }).then((result) => {
          if (result.isConfirmed) {
            localStorage.removeItem(nombreKey);
            listaCarrito = [];
            mostrarProducto();
          } else {
            almacenOrigen.value = listaCarrito[0]?.idAlmacenOrigen || "";
          }
        });
      }
    } else {
      inputBuscarNombre.disabled = true;
      inputBuscarNombre.value = "";
    }
  });

  $("#modalSizeColor").on("hidden.bs.modal", function () {
    limpiarModal();
  });

  $("#buscarProductoNombre").autocomplete({
    source: function (request, response) {
      $.ajax({
        url: base_url + "traspasos/buscarPorNombre",
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
      if (!almacenOrigen.value) {
        alertas("SELECCIONA PRIMERO EL ALMACÉN ORIGEN", "warning");
        return false;
      }
      
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
      btnAgregar.classList.add("d-none");
    }
  });

  color.addEventListener("change", function (e) {
    if (e.target.value != "" && size.value != "") {
      cambiarStock(size.value, e.target.value);
    } else {
      stockActual.value = "";
      btnAgregar.classList.add("d-none");
    }
  });

  function cargarColoresPorSize(sizeId) {
    if (!almacenOrigen.value) {
      alertas("SELECCIONA EL ALMACÉN ORIGEN", "warning");
      return;
    }

    let data = new FormData();
    data.append("size", sizeId);
    data.append("color", "");
    data.append("id_producto", idProducto.value);
    data.append("id_almacen", almacenOrigen.value);

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
        btnAgregar.classList.add("d-none");
      }
    };
  }

  function limpiarModal() {
    idProducto.value = "";
    size.innerHTML = '<option value="">Seleccionar</option>';
    color.innerHTML = '<option value="">Seleccionar</option>';
    stockActual.value = "";
    cantidadTraspaso.value = "1";
    btnAgregar.classList.add("d-none");
  }

  btnAgregar.addEventListener("click", function () {
    if (size.value != "" && color.value != "") {
      const cantidad = parseInt(cantidadTraspaso.value);
      const stock = parseInt(stockActual.value);

      if (isNaN(cantidad) || cantidad <= 0) {
        alertas("INGRESA UNA CANTIDAD VÁLIDA", "warning");
        return;
      }

      if (cantidad > stock) {
        alertas("CANTIDAD MAYOR AL STOCK DISPONIBLE", "warning");
        return;
      }

      agregarProducto(
        idProducto.value,
        cantidad,
        size.value,
        color.value,
        almacenOrigen.value
      );

      idProducto.value = "";
      inputBuscarNombre.value = "";
      $("#modalSizeColor").modal("hide");
    } else {
      alertas("SELECCIONA TALLA Y COLOR", "warning");
    }
  });
});

function cambiarStock(size, color) {
  if (!size || !color || !almacenOrigen.value) {
    stockActual.value = "";
    btnAgregar.classList.add("d-none");
    return;
  }

  let data = new FormData();
  data.append("size", size);
  data.append("color", color);
  data.append("id_producto", idProducto.value);
  data.append("id_almacen", almacenOrigen.value);
  
  const url = base_url + "principal/cambiarStock";
  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(data);
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);

      stockActual.value = res.atrib ? res.atrib.stock : "";

      let html = '<option value="">Seleccionar</option>';
      if (res.colores && res.colores.length > 0) {
        res.colores.forEach((color1) => {
          html += `<option value="${color1.id}" ${
            color == color1.id ? "selected" : ""
          }>${color1.nombre}</option>`;
        });
      }
      document.querySelector("#color").innerHTML = html;

      if (res.atrib && res.atrib.stock > 0) {
        btnAgregar.classList.remove("d-none");
      } else {
        btnAgregar.classList.add("d-none");
        if (res.atrib && res.atrib.stock == 0) {
          alertas("SIN STOCK EN ESTE ALMACÉN", "warning");
        }
      }
    }
  };
}

function agregarProducto(idProducto, cantidad, size, color, idAlmacenOrigen) {
  if (localStorage.getItem(nombreKey) == null) {
    listaCarrito = [];
  } else {
    listaCarrito = JSON.parse(localStorage.getItem(nombreKey));
  }

  // Validar que todos los productos sean del mismo almacén origen
  if (listaCarrito.length > 0 && listaCarrito[0].idAlmacenOrigen != idAlmacenOrigen) {
    alertas("TODOS LOS PRODUCTOS DEBEN SER DEL MISMO ALMACÉN ORIGEN", "warning");
    return;
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
      break;
    }
  }

  if (!productoExistente) {
    listaCarrito.push({
      idProducto: idProducto,
      cantidad: cantidad,
      size: size,
      color: color,
      idAlmacenOrigen: idAlmacenOrigen
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
      break;
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
      let stockMax = parseInt(lista[i].getAttribute("data-stock"));

      if (isNaN(cantidad) || cantidad == "" || parseInt(cantidad) <= 0) {
        alertas("CANTIDAD INVÁLIDA", "warning");
        mostrarProducto();
        return;
      }

      if (parseInt(cantidad) > stockMax) {
        alertas("CANTIDAD MAYOR AL STOCK DISPONIBLE", "warning");
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
      break;
    }
  }
  localStorage.setItem(nombreKey, JSON.stringify(listaCarrito));
  mostrarProducto();
}

function cargarSizeTalla(idProducto) {
  if (!almacenOrigen.value) {
    alertas("SELECCIONA EL ALMACÉN ORIGEN", "warning");
    return;
  }

  const url = base_url + "traspasos/size/" + idProducto + "?almacen=" + almacenOrigen.value;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      let sizes = '<option value="">Seleccionar</option>';
      
      if (res.length > 0) {
        res.forEach((size) => {
          sizes += `<option value="${size.id}">${size.nombre}</option>`;
        });
      } else {
        alertas("NO HAY STOCK EN ESTE ALMACÉN", "warning");
      }
      
      size.innerHTML = sizes;
    }
  };
}