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
      url: base_url + "compras/listar",
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
      { data: "numero_compra" },
      { data: "total" },
      { data: "proveedor" },
      { data: "almacen" },
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


function verReporte(idCompra) {
  const ruta = base_url + "compras/reporte/ticked," + idCompra;
  window.open(ruta, "_blank");
}

function verDetalle(idCompra) {
  const url = base_url + "compras/detalle/" + idCompra;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      console.log("Respuesta:", this.responseText);
      const res = JSON.parse(this.responseText);

      document.querySelector("#numCompra").textContent =
        res.compra.numero_compra;
      document.querySelector("#detProveedor").textContent =
        res.compra.proveedor;
      document.querySelector("#detRuc").textContent = res.compra.ruc || "N/A";
      document.querySelector("#detTelefono").textContent =
        res.compra.telefono || "N/A";
      document.querySelector("#detFecha").textContent = res.compra.fecha;
      document.querySelector("#detTipoComprobante").textContent =
        res.compra.tipo_comprobante;
      document.querySelector("#detAlmacen").textContent = res.compra.almacen;

      let html = "";
      let total = 0;

      if (res.detalle && res.detalle.length > 0) {
        res.detalle.forEach((item) => {
          const subtotal = parseFloat(item.subtotal);
          total += subtotal;

          html += `<tr>
                        <td>${item.producto}</td>
                        <td>${item.nombre_corto || item.talla || "N/A"}</td>
                        <td><span class="badge" style="background: ${
                          item.color_hexa || "#333"
                        }">${item.color_nombre || "N/A"}</span></td>
                        <td>${item.cantidad}</td>
                        <td>${parseFloat(item.precio_compra).toFixed(2)}</td>
                        <td>${parseFloat(item.descuento).toFixed(2)}</td>
                        <td>${subtotal.toFixed(2)}</td>
                    </tr>`;
        });
      } else {
        html =
          '<tr><td colspan="7" class="text-center">No hay productos</td></tr>';
      }

      document.querySelector("#detProductos").innerHTML = html;
      document.querySelector("#detTotal").textContent = (
        total > 0 ? total : parseFloat(res.compra.total)
      ).toFixed(2);

      $("#modalDetalle").modal("show");
    }
  };
}

function anularCompra(idCompra) {
  Swal.fire({
    title: "¿Estás seguro de anular la compra?",
    text: "El stock de los productos será reducido!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, Anular!",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      const url = base_url + "compras/anular/" + idCompra;
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
