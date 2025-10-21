const tblNuevaCompra = document.querySelector("#tblNuevaCompra tbody");

const idProveedor = document.querySelector("#idProveedor");
const rucProveedor = document.querySelector("#rucProveedor");
const telefonoProveedor = document.querySelector("#telefonoProveedor");
const emailProveedor = document.querySelector("#emailProveedor");
const errorProveedor = document.querySelector("#errorProveedor");
const filtroProveedor = document.querySelector("#filtroProveedor");
const btnLimpiarFiltros = document.querySelector("#btnLimpiarFiltros");

document.addEventListener("DOMContentLoaded", function () {
  mostrarProducto();

  $("#buscarProveedor").autocomplete({
    source: function (request, response) {
      $.ajax({
        url: base_url + "compras/buscarProveedor",
        dataType: "json",
        data: {
          term: request.term,
        },
        success: function (data) {
          response(data);
          if (data.length > 0) {
            errorProveedor.textContent = "";
          } else {
            errorProveedor.textContent = "NO HAY PROVEEDOR CON ESE NOMBRE";
          }
        },
      });
    },
    minLength: 2,
    select: function (event, ui) {
      rucProveedor.value = ui.item.ruc || "";
      telefonoProveedor.value = ui.item.telefono || "";
      emailProveedor.value = ui.item.email || "";
      idProveedor.value = ui.item.id;
    },
  });

  // Completar compra
  btnAccion.addEventListener("click", function () {
    let filas = document.querySelectorAll("#tblNuevaCompra tbody tr").length;
    if (filas === 0) {
      alertas("CARRITO VACIO", "warning");
      return;
    }

    if (!idProveedor.value) {
      alertas("SELECCIONA UN PROVEEDOR", "warning");
      return;
    }

    const tipoComprobante = document.querySelector("#tipoComprobante").value;
    const almacen = document.querySelector("#almacen").value;

    const url = base_url + "compras/registrarCompra";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(
      JSON.stringify({
        productos: listaCarrito,
        idProveedor: idProveedor.value,
        idAlmacen: almacen,
        tipoComprobante: tipoComprobante,
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
            setTimeout(() => {
              const ruta = base_url + "compras/reporte/ticked," + res.idCompra;
              window.open(ruta, "_blank");
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
  if (localStorage.getItem(nombreKey) != null) {
    const url = base_url + "principal/listaProductos";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(JSON.stringify(listaCarrito));
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        let html = "";
        let totalDescuentos = 0;
        let subtotalGeneral = 0;

        if (res.productos.length > 0) {
          res.productos.forEach((producto) => {
            const itemCarrito = listaCarrito.find(
              (item) =>
                item.idProducto == producto.id &&
                item.size == producto.size &&
                item.color == producto.color
            );

            const precioCompra = itemCarrito
              ? parseFloat(itemCarrito.precioCompra || producto.precio)
              : parseFloat(producto.precio);
            const descuento = itemCarrito
              ? parseFloat(itemCarrito.descuento || 0)
              : 0;
            const cantidad = parseInt(producto.cantidad);
            const subTotal = precioCompra * cantidad - descuento;

            totalDescuentos += descuento;
            subtotalGeneral += subTotal;

            html += `<tr>
                            <td>${producto.nombre}</td>
                            <td>${producto.atributo || "Sin atributo"}</td>
                            <td width="100">
                                <input type="number" class="form-control inputPrecio" 
                                    data-id="${producto.id}" 
                                    size="${producto.size}" 
                                    color="${producto.color}" 
                                    value="${precioCompra.toFixed(2)}" 
                                    min="0" 
                                    step="0.01">
                            </td>                            
                            <td width="100">
                                <input type="number" class="form-control inputCantidad" 
                                    data-id="${producto.id}" 
                                    size="${producto.size}" 
                                    color="${producto.color}" 
                                    value="${cantidad}" 
                                    min="1">
                            </td>
                            <td width="100">
                                <input type="number" class="form-control inputDescuento" 
                                    data-id="${producto.id}" 
                                    size="${producto.size}" 
                                    color="${producto.color}" 
                                    value="${descuento.toFixed(2)}" 
                                    min="0" 
                                    step="0.01">
                            </td>
                            <td>${subTotal.toFixed(2)}</td>
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

          tblNuevaCompra.innerHTML = html;
          totalPagar.value = subtotalGeneral.toFixed(2);
          totalDescuento.value = totalDescuentos.toFixed(2);
          btnEliminarProducto();
          agregarCantidad();
          agregarPrecio();
          agregarDescuento();
        } else {
          tblNuevaCompra.innerHTML = "";
          totalPagar.value = "0.00";
          totalDescuento.value = "0.00";
        }
      }
    };
  } else {
    tblNuevaCompra.innerHTML = `<tr>
            <td colspan="7" class="text-center">CARRITO VACIO</td>
        </tr>`;
    totalPagar.value = "0.00";
    totalDescuento.value = "0.00";
  }
}
