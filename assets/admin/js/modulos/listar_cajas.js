let tbl;
const desde = document.querySelector("#desde");
const hasta = document.querySelector("#hasta");

document.addEventListener("DOMContentLoaded", function () {
  const hoy = new Date();
  const fechaHoy = `${hoy.getFullYear()}-${String(hoy.getMonth() + 1).padStart(2, "0")}-${String(hoy.getDate()).padStart(2, "0")}`;
  
  desde.value = fechaHoy;
  hasta.value = fechaHoy;

  tbl = $("#tbl").DataTable({
    ajax: {
      url: base_url + "cajas/listarCajas",
      dataSrc: "",
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      { data: "fecha_apertura" },
      { data: "fecha_cierre" },
      { data: "monto_inicial" },
      { data: "monto_final" },
      { data: "usuario" },
      { data: "apertura" },
    ],
    language: {
      url: base_url + "assets/js/espanol.json",
    },
    responsive: true,
    order: [[0, "desc"]],
  });

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