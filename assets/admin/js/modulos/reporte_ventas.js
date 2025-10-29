let tblVentas;

document.addEventListener("DOMContentLoaded", function () {
  // Inicializar DataTable
  tblVentas = $("#tblVentas").DataTable({
    ajax: {
      url: base_url + "reportes/listar_ventas",
      dataSrc: "",
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      { data: "numero_venta" },
      { data: "metodo" },
      { data: "cliente" },
      { data: "producto" },
      { data: "cantidad" },
      {
        data: "precio_venta",
        render: function (data) {
          return "COP. " + parseFloat(data).toFixed(2);
        },
      },
      {
        data: "subtotal",
        render: function (data) {
          return "COP. " + parseFloat(data).toFixed(2);
        },
      },
      { data: "usuario" },      // Columna 8
      { data: "almacen" },      // Columna 9
      {
        data: "fecha",          // Columna 10
        render: function (data) {
          return formatearFecha(data);
        },
      },
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json",
    },
    responsive: false,
    order: [[10, "desc"]],
    drawCallback: function () {
      calcularTotales();
    },
  });

  // Filtrar por fechas
  $("#desde, #hasta").on("change", function () {
    filtrarPorFechas();
  });

  // Filtrar por usuario
  $("#usuario").on("change", function () {
    const usuario = $(this).val();
    if (usuario === "") {
      // Si selecciona "TODOS", mostrar todos
      tblVentas.column(8).search("").draw();
    } else {
      // Buscar por el nombre del usuario en la columna 8
      const nombreUsuario = $(this).find("option:selected").text();
      tblVentas.column(8).search(nombreUsuario).draw();
    }
    calcularTotales();
  });

  // Filtrar por almacén
  $("#almacen").on("change", function () {
    const almacen = $(this).val();
    if (almacen === "") {
      // Si selecciona "TODOS", mostrar todos
      tblVentas.column(9).search("").draw();
    } else {
      // Buscar por el nombre del almacén en la columna 9
      const nombreAlmacen = $(this).find("option:selected").text();
      tblVentas.column(9).search(nombreAlmacen).draw();
    }
    calcularTotales();
  });

  // Establecer fecha actual
  const hoy = new Date().toISOString().split("T")[0];
  document.getElementById("hasta").value = hoy;

  // Fecha de hace 30 días
  const hace30Dias = new Date();
  hace30Dias.setDate(hace30Dias.getDate() - 30);
  document.getElementById("desde").value = hace30Dias
    .toISOString()
    .split("T")[0];
});

function filtrarPorFechas() {
  const desde = document.getElementById("desde").value;
  const hasta = document.getElementById("hasta").value;

  if (desde && hasta) {
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
      const fecha = data[10]; // Columna 10 = fecha
      const fechaSinHora = fecha.split(" ")[0];

      return fechaSinHora >= desde && fechaSinHora <= hasta;
    });
    
    tblVentas.draw();
    $.fn.dataTable.ext.search.pop();
    calcularTotales();
  }
}

function calcularTotales() {
  let totalProductos = 0;
  let totalCantidad = 0;
  let totalVentas = 0;

  tblVentas.rows({ search: "applied" }).every(function () {
    const data = this.data();
    totalProductos++;
    totalCantidad += parseInt(data.cantidad);
    totalVentas += parseFloat(data.subtotal);
  });

  document.getElementById("totalProductos").textContent =
    totalProductos + " (items)";
  document.getElementById("totalCantidad").textContent = totalCantidad;
  document.getElementById("totalVentas").textContent =
    "COP. " + totalVentas.toFixed(2);
}

function formatearFecha(fecha) {
  const date = new Date(fecha);
  const dia = String(date.getDate()).padStart(2, "0");
  const mes = String(date.getMonth() + 1).padStart(2, "0");
  const año = date.getFullYear();
  const horas = String(date.getHours()).padStart(2, "0");
  const minutos = String(date.getMinutes()).padStart(2, "0");

  return `${dia}/${mes}/${año} ${horas}:${minutos}`;
}

// Funciones para generar reportes PDF y Excel con filtros
function generarPDF() {
  const desde = document.getElementById('desde').value;
  const hasta = document.getElementById('hasta').value;
  const usuario = document.getElementById('usuario').value;
  const almacen = document.getElementById('almacen').value;
  
  const url = base_url + 'reportes/reporte_ventas_pdf?desde=' + desde + 
              '&hasta=' + hasta + 
              '&usuario=' + usuario + 
              '&almacen=' + almacen;
  
  window.open(url, '_blank');
}

function generarExcel() {
  const desde = document.getElementById('desde').value;
  const hasta = document.getElementById('hasta').value;
  const usuario = document.getElementById('usuario').value;
  const almacen = document.getElementById('almacen').value;
  
  const url = base_url + 'reportes/reporte_ventas_excel?desde=' + desde + 
              '&hasta=' + hasta + 
              '&usuario=' + usuario + 
              '&almacen=' + almacen;
  
  window.open(url, '_blank');
}