let tblPedidosPendientes;
const modalProcesarPago = new bootstrap.Modal("#modalProcesarPago");
const modalDetallePedido = new bootstrap.Modal("#modalDetallePedido");

let pedidoActual = null;

document.addEventListener("DOMContentLoaded", function () {
  
  // Inicializar DataTable
  tblPedidosPendientes = $("#tblPedidosPendientes").DataTable({
    ajax: {
      url: base_url + "ventas/listarPendientes",
      dataSrc: "",
    },
    columns: [
      { data: "id" },
      { data: "fecha" },
      { data: "cliente" },
      { data: "vendedor" },
      { data: "monto" },
      { data: "estado" },
      { data: "acciones" },
    ],
    language: {
      url: base_url + "assets/js/espanol.json",
    },
    responsive: true,
    order: [[0, "desc"]],
  });

  // Buscar pedido
  document.getElementById("buscarPedido").addEventListener("keyup", function () {
    tblPedidosPendientes.search(this.value).draw();
  });

  // Calcular cambio automáticamente
  document.getElementById("montoPagado").addEventListener("input", function () {
    calcularCambio();
  });

  // Confirmar pago
  document.getElementById("btnConfirmarPago").addEventListener("click", function () {
    confirmarPago();
  });
});

// Función para abrir modal de pago
function abrirModalPago(idPedido) {
  const url = base_url + "ventas/detalle/" + idPedido;
  const http = new XMLHttpRequest();
  
  http.open("GET", url, true);
  http.send();
  
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      pedidoActual = res;

      // Llenar información
      document.getElementById("numPedidoPago").textContent = res.pedido.id;
      document.getElementById("pagoCliente").textContent = res.pedido.nombre + " " + res.pedido.apellido;
      document.getElementById("pagoTelefono").textContent = res.pedido.telefono || "N/A";
      document.getElementById("pagoDireccion").textContent = res.pedido.direccion || "N/A";
      document.getElementById("pagoFecha").textContent = res.pedido.fecha;
      document.getElementById("pagoVendedor").textContent = res.pedido.vendedor || "N/A";
      document.getElementById("pagoMetodo").textContent = res.pedido.metodo;

      // Llenar productos
      let html = "";
      let total = 0;

      if (res.detalle && res.detalle.length > 0) {
        res.detalle.forEach((item) => {
          const subtotal = parseFloat(item.precio) * parseInt(item.cantidad);
          total += subtotal;

          html += `<tr>
            <td>${item.producto}</td>
            <td>${item.nombre_corto || item.talla || 'N/A'}</td>
            <td>
              ${item.color_hexa ? 
                `<span class="badge" style="background: ${item.color_hexa}">${item.color_nombre}</span>` 
                : 'N/A'}
            </td>
            <td>${item.cantidad}</td>
            <td>COP ${parseFloat(item.precio).toFixed(2)}</td>
            <td>COP ${subtotal.toFixed(2)}</td>
          </tr>`;
        });
      }

      document.getElementById("pagoProductos").innerHTML = html;
      document.getElementById("pagoTotal").textContent = "COP " + (total > 0 ? total : parseFloat(res.pedido.monto)).toFixed(2);

      // Limpiar campos
      document.getElementById("montoPagado").value = "";
      document.getElementById("cambioDevolver").value = "0.00";

      modalProcesarPago.show();
    }
  };
}

// Función para calcular cambio
function calcularCambio() {
  const totalTexto = document.getElementById("pagoTotal").textContent.replace("COP ", "");
  const total = parseFloat(totalTexto) || 0;
  const montoPagado = parseFloat(document.getElementById("montoPagado").value) || 0;
  const cambio = montoPagado - total;

  document.getElementById("cambioDevolver").value = cambio >= 0 ? cambio.toFixed(2) : "0.00";
}

// Función para confirmar pago
function confirmarPago() {
  if (!pedidoActual) {
    alertas("ERROR: No hay pedido seleccionado", "error");
    return;
  }

  const totalTexto = document.getElementById("pagoTotal").textContent.replace("COP ", "");
  const total = parseFloat(totalTexto) || 0;
  const montoPagado = parseFloat(document.getElementById("montoPagado").value) || 0;

  if (montoPagado < total) {
    alertas("EL MONTO RECIBIDO ES MENOR AL TOTAL", "warning");
    document.getElementById("montoPagado").focus();
    return;
  }

  // Confirmar con el usuario
  Swal.fire({
    title: "¿Confirmar Pago?",
    html: `
      <p><strong>Pedido:</strong> #${pedidoActual.pedido.id}</p>
      <p><strong>Total:</strong> COP ${total.toFixed(2)}</p>
      <p><strong>Recibido:</strong> COP ${montoPagado.toFixed(2)}</p>
      <p><strong>Cambio:</strong> COP ${(montoPagado - total).toFixed(2)}</p>
    `,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, Confirmar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      procesarPago(pedidoActual.pedido.id);
    }
  });
}

// Función para procesar el pago
function procesarPago(idPedido) {
  const url = base_url + "ventas/procesarPago";
  const http = new XMLHttpRequest();

  http.open("POST", url, true);
  http.send(
    JSON.stringify({
      idPedido: idPedido,
    })
  );

  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      alertas(res.msg, res.type);

      if (res.type == "success") {
        modalProcesarPago.hide();
        tblPedidosPendientes.ajax.reload();

        // Abrir ticket
        setTimeout(() => {
          const ruta = base_url + "ventas/reporte/ticked/" + idPedido;
          window.open(ruta, "_blank");
        }, 1000);
      }
    }
  };
}

// Función para ver detalle
function verDetallePedido(idPedido) {
  const url = base_url + "ventas/detalle/" + idPedido;
  const http = new XMLHttpRequest();
  
  http.open("GET", url, true);
  http.send();
  
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);

      document.getElementById("numPedidoDetalle").textContent = res.pedido.id;
      document.getElementById("detCliente").textContent = res.pedido.nombre + " " + res.pedido.apellido;
      document.getElementById("detTelefono").textContent = res.pedido.telefono || "N/A";
      document.getElementById("detFecha").textContent = res.pedido.fecha;
      document.getElementById("detVendedor").textContent = res.pedido.vendedor || "N/A";

      let html = "";
      let total = 0;

      if (res.detalle && res.detalle.length > 0) {
        res.detalle.forEach((item) => {
          const subtotal = parseFloat(item.precio) * parseInt(item.cantidad);
          total += subtotal;

          html += `<tr>
            <td>${item.producto}</td>
            <td>${item.nombre_corto || item.talla || 'N/A'}</td>
            <td>
              ${item.color_hexa ? 
                `<span class="badge" style="background: ${item.color_hexa}">${item.color_nombre}</span>` 
                : 'N/A'}
            </td>
            <td>${item.cantidad}</td>
            <td>${parseFloat(item.precio).toFixed(2)}</td>
            <td>${subtotal.toFixed(2)}</td>
          </tr>`;
        });
      }

      document.getElementById("detProductos").innerHTML = html;
      document.getElementById("detTotal").textContent = (total > 0 ? total : parseFloat(res.pedido.monto)).toFixed(2);

      modalDetallePedido.show();
    }
  };
}

// Función para anular pedido
function anularPedido(idPedido) {
  Swal.fire({
    title: "¿Anular Pedido?",
    text: "Esta acción no se puede deshacer",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, Anular",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      const url = base_url + "ventas/anularPendiente/" + idPedido;
      const http = new XMLHttpRequest();
      
      http.open("GET", url, true);
      http.send();
      
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          alertas(res.msg, res.type);
          
          if (res.type == "success") {
            tblPedidosPendientes.ajax.reload();
          }
        }
      };
    }
  });
}