const tblNuevaVenta = document.querySelector("#tblNuevaVenta tbody");

const idCliente = document.querySelector("#idCliente");
const telefonoCliente = document.querySelector("#telefonoCliente");
const direccionCliente = document.querySelector("#direccionCliente");
const errorCliente = document.querySelector("#errorCliente");

document.addEventListener("DOMContentLoaded", function () {
  //cargar productos de localStorage
  mostrarProducto();

  //autocomplete clientes
  $("#buscarCliente").autocomplete({
    source: function (request, response) {
      $.ajax({
        url: base_url + "ventas/buscarCliente",
        dataType: "json",
        data: {
          term: request.term,
        },
        success: function (data) {
          response(data);
          if (data.length > 0) {
            errorCliente.textContent = "";
          } else {
            errorCliente.textContent = "NO HAY CLIENTE CON ESE NOMBRE";
          }
        },
      });
    },
    minLength: 2,
    select: function (event, ui) {
      telefonoCliente.value = ui.item.telefono;
      direccionCliente.innerHTML = ui.item.direccion;
      idCliente.value = ui.item.id;
    },
  });

  //completar venta
  btnAccion.addEventListener("click", function () {
    let filas = document.querySelectorAll("#tblNuevaVenta tr").length;
    if (filas < 2) {
      alertaPersonalizada("warning", "CARRITO VACIO");
      return;
    } else {
      const metodoPago = document.querySelector("#metodoPago").value;
      const url = base_url + "ventas/registrarVenta";
      const http = new XMLHttpRequest();
      http.open("POST", url, true);
      http.send(
        JSON.stringify({
          productos: listaCarrito,
          idCliente: idCliente.value,
          metodo: metodoPago,
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
                const ruta = base_url + "ventas/reporte/ticked/" + res.idVenta;
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
    }
  });

  //cargar datos con el plugin datatables
  tblHistorial = $("#tblHistorial").DataTable({
    ajax: {
      url: base_url + "ventas/listar",
      dataSrc: "",
    },
    columns: [
      { data: "id" },
      { data: "fecha" },
      { data: "monto" },
      { data: "nombre" },
      { data: "estado" },
      { data: "acciones" },
    ],
    language,
    dom,
    buttons,
    responsive: true,
    order: [[0, "desc"]],
  });
});

//cargar productos
function mostrarProducto() {
  if (localStorage.getItem(nombreKey) != null) {
    const url = base_url + "principal/listaProductos";
    //hacer una instancia del objeto XMLHttpRequest
    const http = new XMLHttpRequest();
    //Abrir una Conexion - POST - GET
    http.open("POST", url, true);
    //Enviar Datos
    http.send(JSON.stringify(listaCarrito));
    //verificar estados
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        let html = "";
        if (res.productos.length > 0) {
          res.productos.forEach((producto) => {
            let verify =
              producto.stock == "Ilimitado" ? "" : `max="${producto.stock}"`;
            html += `<tr>
                            <td>${producto.nombre}</td>
                            <td>${producto.atributo}</td>
                            <td>${producto.precio}</td>                            
                            <td width="100">
                            <input type="number" class="form-control inputCantidad" data-id="${producto.id}" size="${producto.size}" color="${producto.color}" value="${producto.cantidad}" min="1" ${verify}>
                            </td>
                            <td>${producto.subTotal}</td>
                            <td><button class="btn btn-danger btnEliminar" data-id="${producto.id}" size="${producto.size}" color="${producto.color}" type="button"><i class="fas fa-trash"></i></button></td>
                        </tr>`;
          });
          tblNuevaVenta.innerHTML = html;
          totalPagar.value = res.total;
          btnEliminarProducto();
          agregarCantidad();
        } else {
          tblNuevaVenta.innerHTML = "";
        }
      }
    };
  } else {
    tblNuevaVenta.innerHTML = `<tr>
            <td colspan="6" class="text-center">CARRITO VACIO</td>
        </tr>`;
  }
}

function verReporte(idVenta) {
  const ruta = base_url + "ventas/reporte/ticked/" + idVenta;
  window.open(ruta, "_blank");
}

function verDetalle(idPedido) {
  const url = base_url + "ventas/detalle/" + idPedido;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      console.log("Respuesta:", this.responseText); // 🔍 DEBUG
      const res = JSON.parse(this.responseText);

      document.querySelector("#numPedido").textContent = res.pedido.id;
      document.querySelector("#detCliente").textContent =
        res.pedido.nombre + " " + res.pedido.apellido;
      document.querySelector("#detTelefono").textContent =
        res.pedido.telefono || "N/A";
      document.querySelector("#detFecha").textContent = res.pedido.fecha;
      document.querySelector("#detMetodo").textContent = res.pedido.metodo;

      let html = "";
      let total = 0;

      if (res.detalle && res.detalle.length > 0) {
        res.detalle.forEach((item) => {
          const subtotal = parseFloat(item.precio) * parseInt(item.cantidad);
          total += subtotal;

          html += `<tr>
                        <td>${item.producto}</td>
                        <td>${item.nombre_corto}</td>
                        <td><span class="badge" style="background: ${
                          item.color_hexa
                        }">${item.color_nombre}</span></td>
                        <td>${item.cantidad}</td>
                        <td>${parseFloat(item.precio).toFixed(2)}</td>
                        <td>${subtotal.toFixed(2)}</td>
                    </tr>`;
        });
      } else {
        html =
          '<tr><td colspan="6" class="text-center">No hay productos</td></tr>';
      }

      document.querySelector("#detProductos").innerHTML = html;
      document.querySelector("#detTotal").textContent = (
        total > 0 ? total : parseFloat(res.pedido.monto)
      ).toFixed(2);

      $("#modalDetalle").modal("show");
    }
  };
}

function anularVenta(idVenta) {
  Swal.fire({
    title: "Esta seguro de anular la venta?",
    text: "El stock de los productos cambiarán!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Anular!",
  }).then((result) => {
    if (result.isConfirmed) {
      const url = base_url + "ventas/anular/" + idVenta;
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
          alertas(res.msg, res.type);
          if (res.type == "success") {
            tblHistorial.ajax.reload();
          }
        }
      };
    }
  });
}
