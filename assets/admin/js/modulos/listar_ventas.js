const tblNuevaVenta = document.querySelector("#tblNuevaVenta tbody");


//para filtro por rango de fechas
const desde = document.querySelector("#desde");
const hasta = document.querySelector("#hasta");

document.addEventListener("DOMContentLoaded", function () {
  const hoy = new Date();
  const yyyy = hoy.getFullYear();
  const mm = String(hoy.getMonth() + 1).padStart(2, "0");
  const dd = String(hoy.getDate()).padStart(2, "0");

  const fechaHoy = `${yyyy}-${mm}-${dd}`;
  desde.value = fechaHoy;
  hasta.value = fechaHoy;

  tblHistorial = $("#tblHistorial").DataTable({
    ajax: {
      url: base_url + "ventas/listar",
      dataSrc: "",
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      { data: "fecha" },
      { data: "monto" },
      { data: "nombre" },
      { data: "estado" },
      { data: "acciones" },
    ],
    language,
    responsive: true,
    order: [[0, "desc"]],
  });
  //filtro rango de fechas
  desde.addEventListener("change", function () {
    tbl.draw();
  });
  hasta.addEventListener("change", function () {
    tbl.draw();
  });
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    var FilterStart = desde.value; 
    var FilterEnd = hasta.value; 

    var DataTableDate = data[1].trim().substring(0, 10); 

    if (FilterStart === "" || FilterEnd === "") {
      return true;
    }

    return DataTableDate >= FilterStart && DataTableDate <= FilterEnd;
  });
});

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
      const url = base_url + "ventas/anularPendiente/" + idVenta;
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
