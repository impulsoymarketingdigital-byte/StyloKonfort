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

            // ⭐ CONSTRUIR ATRIBUTO CORRECTAMENTE
            let atributoTexto = "Sin atributo";
            if (producto.nombreTalla && producto.nombreColor) {
              atributoTexto = `${producto.nombreTalla} - ${producto.nombreColor}`;
            } else if (producto.atributoMP) {
              atributoTexto = producto.atributoMP;
            }

            html += `<tr>
                            <td>${producto.nombre}</td>
                            <td>${atributoTexto}</td>
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
