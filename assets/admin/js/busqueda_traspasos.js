
let tblHistorial;

document.addEventListener("DOMContentLoaded", function () {
  // Inicializar carrito desde localStorage
  if (localStorage.getItem(nombreKey) != null) {
    listaCarrito = JSON.parse(localStorage.getItem(nombreKey));
  } else {
    listaCarrito = [];
  }

  mostrarProducto();

  // Completar traspaso
  btnAccion.addEventListener("click", function () {
    // Verificar que listaCarrito tenga datos
    if (!listaCarrito || listaCarrito.length === 0) {
      alertas("CARRITO VACIO", "warning");
      return;
    }

    if (!almacenOrigen.value) {
      alertas("SELECCIONA EL ALMACÉN ORIGEN", "warning");
      return;
    }

    if (!almacenDestino.value) {
      alertas("SELECCIONA EL ALMACÉN DESTINO", "warning");
      return;
    }

    if (almacenOrigen.value == almacenDestino.value) {
      alertas("EL ALMACÉN ORIGEN Y DESTINO NO PUEDEN SER IGUALES", "warning");
      return;
    }

    const url = base_url + "traspasos/registrarTraspaso";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.setRequestHeader("Content-Type", "application/json");
    http.send(
      JSON.stringify({
        productos: listaCarrito,
        idAlmacenOrigen: almacenOrigen.value,
        idAlmacenDestino: almacenDestino.value,
      })
    );
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        console.log("Respuesta del servidor:", this.responseText);

        try {
          const res = JSON.parse(this.responseText);
          alertas(res.msg, res.type);

          if (res.type == "success") {
            localStorage.removeItem(nombreKey);
            listaCarrito = [];
            setTimeout(() => {
              window.location.reload();
            }, 2000);
          }
        } catch (e) {
          console.error("Error al parsear JSON:", e);
          console.error("Respuesta recibida:", this.responseText);
          alertas("Error en el servidor. Revisa la consola.", "error");
        }
      }
    };
  });

  tblHistorial = $("#tblHistorial").DataTable({
    ajax: {
      url: base_url + "traspasos/listar",
      dataSrc: "",
    },
    columns: [
      { data: "id" },
      { data: "numero_traspaso" },
      { data: "fecha" },
      { data: "almacen_origen" },
      { data: "almacen_destino" },
      { data: "total_productos" },
      { data: "estado" },
      { data: "acciones" },
    ],
    language,
    dom,
    buttons,
    responsive: true,
    order: [[0, "desc"]],
  });

  filtroAlmacen.addEventListener("change", function () {
    tblHistorial.draw();
  });


  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    var FilterStart = desde.value;
    var FilterEnd = hasta.value;
    var DataTableDate = data[2].trim();

    var FilterAlmacen = filtroAlmacen.value;
    var DataTableAlmacenOrigen = data[3].trim();
    var DataTableAlmacenDestino = data[4].trim();

    var dateMatch = true;
    if (FilterStart != "" && FilterEnd != "") {
      if (DataTableDate < FilterStart || DataTableDate > FilterEnd) {
        dateMatch = false;
      }
    }

    var almacenMatch = true;
    if (FilterAlmacen != "") {
      if (
        DataTableAlmacenOrigen.indexOf(FilterAlmacen) === -1 &&
        DataTableAlmacenDestino.indexOf(FilterAlmacen) === -1
      ) {
        almacenMatch = false;
      }
    }

    return dateMatch && almacenMatch;
  });
});

function mostrarProducto() {
  if (listaCarrito && listaCarrito.length > 0) {
    const url = base_url + "principal/listaProductos";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.setRequestHeader("Content-Type", "application/json");
    http.send(JSON.stringify(listaCarrito));
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        let html = "";
        let totalProds = 0;

        if (res.productos && res.productos.length > 0) {
          res.productos.forEach((producto) => {
            const itemCarrito = listaCarrito.find(
              (item) =>
                item.idProducto == producto.id &&
                item.size == producto.size &&
                item.color == producto.color
            );

            const cantidad = parseInt(producto.cantidad);
            totalProds += cantidad;

            html += `<tr>
                            <td>${producto.nombre}</td>
                            <td>${producto.atributo || "Sin atributo"}</td>
                            <td>${producto.stock || 0}</td>
                            <td width="100">
                                <input type="number" class="form-control inputCantidad" 
                                    data-id="${producto.id}" 
                                    size="${producto.size}" 
                                    color="${producto.color}" 
                                    data-stock="${producto.stock || 0}"
                                    value="${cantidad}" 
                                    min="1"
                                    max="${producto.stock || 0}">
                            </td>
                            <td>
                                <button class="btn btn-danger btnEliminar" 
                                    data-id="${producto.id}" 
                                    size="${producto.size}" 
                                    color="${producto.color}" 
                                    type="button">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>`;
          });

          tblNuevoTraspaso.innerHTML = html;
          totalProductos.value = totalProds;
          btnEliminarProducto();
          agregarCantidad();
        } else {
          tblNuevoTraspaso.innerHTML = `<tr>
                    <td colspan="5" class="text-center">CARRITO VACIO</td>
                </tr>`;
          totalProductos.value = "0";
        }
      }
    };
  } else {
    tblNuevoTraspaso.innerHTML = `<tr>
            <td colspan="5" class="text-center">CARRITO VACIO</td>
        </tr>`;
    totalProductos.value = "0";
  }
}

function verDetalle(idTraspaso) {
  const url = base_url + "traspasos/detalle/" + idTraspaso;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      console.log("Respuesta:", this.responseText);
      const res = JSON.parse(this.responseText);

      document.querySelector("#numTraspaso").textContent =
        res.traspaso.numero_traspaso;
      document.querySelector("#detAlmacenOrigen").textContent =
        res.traspaso.almacen_origen;
      document.querySelector("#detAlmacenDestino").textContent =
        res.traspaso.almacen_destino;
      document.querySelector("#detFecha").textContent = res.traspaso.fecha;
      document.querySelector("#detUsuario").textContent = res.traspaso.usuario;

      let html = "";
      let total = 0;

      if (res.detalle && res.detalle.length > 0) {
        res.detalle.forEach((item) => {
          total += parseInt(item.cantidad);

          html += `<tr>
                        <td>${item.producto}</td>
                        <td>${item.nombre_corto || item.talla || "N/A"}</td>
                        <td><span class="badge" style="background: ${
                          item.color_hexa || "#333"
                        }">${item.color_nombre || "N/A"}</span></td>
                        <td>${item.cantidad}</td>
                    </tr>`;
        });
      } else {
        html =
          '<tr><td colspan="4" class="text-center">No hay productos</td></tr>';
      }

      document.querySelector("#detProductos").innerHTML = html;
      document.querySelector("#detTotal").textContent =
        total > 0 ? total : res.traspaso.total_productos;

      $("#modalDetalle").modal("show");
    }
  };
}

function anularTraspaso(idTraspaso) {
  Swal.fire({
    title: "¿Estás seguro de anular el traspaso?",
    text: "Se revertirá el movimiento de stock!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, Anular!",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      const url = base_url + "traspasos/anular/" + idTraspaso;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          alertas(res.msg, res.type);
          if (res.type == "success") {
            tblHistorial.ajax.reload();
          }
        }
      };
    }
  });
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