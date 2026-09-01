let tblHistorialCajas;
const desde = document.querySelector("#desde");
const hasta = document.querySelector("#hasta");

document.addEventListener("DOMContentLoaded", function () {
  // Establecer fecha de hoy por defecto
  const hoy = new Date();
  const yyyy = hoy.getFullYear();
  const mm = String(hoy.getMonth() + 1).padStart(2, "0");
  const dd = String(hoy.getDate()).padStart(2, "0");

  const fechaHoy = `${yyyy}-${mm}-${dd}`;
  desde.value = fechaHoy;
  hasta.value = fechaHoy;

  // Inicializar DataTable
  tblHistorialCajas = $("#tblHistorialCajas").DataTable({
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
      { 
        data: "fecha_cierre",
        render: function(data) {
          return data ? data : '-';
        }
      },
      { data: "monto_inicial" },
      { data: "monto_final" },
      { data: "monto_fisico" },
      { data: "apertura" },
      { data: "usuario" },
    ],
    language: {
      url: base_url + "assets/js/espanol.json",
    },
    responsive: true,
    order: [[1, "desc"]],
  });

  // Filtro por rango de fechas
  desde.addEventListener("change", function () {
    tblHistorialCajas.draw();
  });

  hasta.addEventListener("change", function () {
    tblHistorialCajas.draw();
  });

  // Función de filtrado personalizado
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    if (settings.nTable.id !== 'tblHistorialCajas') {
      return true;
    }

    const FilterStart = desde.value;
    const FilterEnd = hasta.value;

    // Extraer solo la fecha (primeros 10 caracteres) de la columna fecha_apertura (índice 1)
    const DataTableDate = data[1].trim().substring(0, 10);

    if (FilterStart === "" || FilterEnd === "") {
      return true;
    }

    return DataTableDate >= FilterStart && DataTableDate <= FilterEnd;
  });
});