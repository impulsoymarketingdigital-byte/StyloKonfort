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
      url: base_url + "traspasos/listar",
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
      { data: "numero_traspaso" },
      { data: "almacen_origen" },
      { data: "almacen_destino" },
      { data: "total_productos" },
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


function verDetalle(idTraspaso) {
  const url = base_url + "traspasos/detalle/" + idTraspaso;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
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
