let tbl;
//MODALES
const closeModal = new bootstrap.Modal("#closeModal");
const theModal = new bootstrap.Modal("#theModal");
const movementModal = new bootstrap.Modal("#movementCashBox");

//BOTONES
const btnNuevo = document.querySelector("#btnNuevo");
const btnCierre = document.querySelector("#btnCierre");
const btnMovimiento = document.querySelector("#btnMovimiento");

//FORMULARIOS
const frmRegistro = document.querySelector("#frmRegistro");
const frmMovimiento = document.querySelector("#frmMovimiento");
const frmCierre = document.querySelector("#frmCierre");

//CAMPOS
const monto_inicial = document.querySelector("#monto_inicial");
const errorMontoInicial = document.querySelector("#errorMontoInicial");

document.addEventListener("DOMContentLoaded", function () {
  cargarSaldoActual();

  tbl = $("#tbl").DataTable({
    ajax: {
      url: base_url + "cajas/listar",
      dataSrc: "",
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      { data: "created_at" },
      { data: "usuario" },
      { data: "tipo" },
      { data: "tipo_movimiento" },
      { data: "descripcion" },
      { data: "ingreso" },
      { data: "egreso" },
    ],
    language: {
      url: base_url + "assets/js/espanol.json",
    },
    responsive: true,
    order: [[1, "desc"]],
    footerCallback: function (row, data, start, end, display) {
      let api = this.api();

      // Total de ingresos (columna 6)
      let totalIngreso = api
        .column(6, { page: 'current' })
        .data()
        .reduce(function (a, b) {
          let valor = typeof b === 'string' ? parseFloat(b.replace(/[^\d.-]/g, '')) : parseFloat(b);
          return a + (isNaN(valor) ? 0 : valor);
        }, 0);

      // Total de egresos (columna 7)
      let totalEgreso = api
        .column(7, { page: 'current' })
        .data()
        .reduce(function (a, b) {
          let valor = typeof b === 'string' ? parseFloat(b.replace(/[^\d.-]/g, '')) : parseFloat(b);
          return a + (isNaN(valor) ? 0 : valor);
        }, 0);

      // Mostrar totales en el footer
      $(api.column(6).footer()).html("COP. " + totalIngreso.toFixed(2));
      $(api.column(7).footer()).html("COP. " + totalEgreso.toFixed(2));
    },
  });

  // Abrir modal de apertura
  btnNuevo.addEventListener("click", function () {
    frmRegistro.reset();
    errorMontoInicial.textContent = "";
    theModal.show();
  });

  // Abrir modal de movimiento
  btnMovimiento.addEventListener("click", function () {
    frmMovimiento.reset();
    cargarSaldoActual();
    movementModal.show();
  });

  // Abrir modal de cierre
  btnCierre.addEventListener("click", function () {
    frmCierre.reset();
    cargarDatosCierre();
    closeModal.show();
  });

  // Submit apertura de caja
  frmRegistro.addEventListener("submit", function (e) {
    e.preventDefault();
    
    if (monto_inicial.value == "" || monto_inicial.value == "0" || monto_inicial.value == "0.00") {
      errorMontoInicial.textContent = "EL MONTO INICIAL ES REQUERIDO";
      monto_inicial.focus();
      return;
    }
    
    const url = base_url + "cajas/abrirCaja";
    const http = new XMLHttpRequest();
    
    http.open("POST", url, true);
    http.send(
      JSON.stringify({
        monto: monto_inicial.value,
      })
    );
    
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        alertas(res.msg, res.type);
        
        if (res.type == "success") {
          tbl.ajax.reload();
          theModal.hide();
        }
      }
    };
  });

  // Submit movimiento de caja
  frmMovimiento.addEventListener("submit", function (e) {
    e.preventDefault();
    guardarMovimiento();
  });

  // ✅ VALIDACIÓN AGREGADA: Submit cierre de caja
  frmCierre.addEventListener("submit", function (e) {
    e.preventDefault();

    const physical_amount = parseFloat(document.getElementById("fisicoInput").value) || 0;
    const final_amount = parseFloat(document.getElementById("saldoInput").value) || 0;

    // Validar que el físico no esté vacío
    if (physical_amount == 0) {
      alertas("EL MONTO FÍSICO ES REQUERIDO", "warning");
      document.getElementById("fisicoInput").focus();
      return;
    }

    if (physical_amount < final_amount) {
      alertas("EL MONTO FÍSICO NO PUEDE SER MENOR AL SALDO DEL SISTEMA. FALTA DINERO EN CAJA", "error");
      document.getElementById("fisicoInput").focus();
      return;
    }

    const url = base_url + "cajas/cerrarCaja";
    const http = new XMLHttpRequest();

    http.open("POST", url, true);
    http.send(
      JSON.stringify({
        physical_amount: physical_amount,
        final_amount: final_amount,
      })
    );

    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        alertas(res.msg, res.type);

        if (res.type == "success") {
          tbl.ajax.reload();
          closeModal.hide();
        }
      }
    };
  });
});

// Función para cargar saldo actual
function cargarSaldoActual() {
  const url = base_url + "cajas/getSaldo";
  const http = new XMLHttpRequest();

  http.open("GET", url, true);
  http.send();

  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      
      if (res.type == "success") {
        document.getElementById("balance").value = "COP. " + res.saldo;
      } else {
        document.getElementById("balance").value = "COP. 0.00";
      }
    }
  };
}

// Función para guardar movimiento
function guardarMovimiento() {
  const type = document.querySelector('select[name="type"]').value;
  const description = document.querySelector('textarea[name="description"]').value;
  const amount = document.querySelector('input[name="amount"]').value;

  // Validar campos
  if (!type) {
    alertas("El tipo es obligatorio", "warning");
    return;
  }

  if (!description.trim()) {
    alertas("La descripción es obligatoria", "warning");
    return;
  }

  if (!amount || isNaN(amount) || parseFloat(amount) <= 0) {
    alertas("El monto debe ser un número válido mayor a 0", "warning");
    return;
  }

  // Preparar datos
  const datos = {
    type: type,
    description: description.trim(),
    amount: parseFloat(amount),
  };

  const url = base_url + "cajas/guardarMovimiento";
  const http = new XMLHttpRequest();

  http.open("POST", url, true);
  http.setRequestHeader("Content-Type", "application/json");
  http.send(JSON.stringify(datos));

  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      alertas(res.msg, res.type);

      if (res.type == "success") {
        movementModal.hide();
        tbl.ajax.reload();
        frmMovimiento.reset();
      }
    }
  };
}

// Función para cargar datos de cierre
function cargarDatosCierre() {
  const url = base_url + "cajas/getDatosCierre";
  const http = new XMLHttpRequest();

  http.open("GET", url, true);
  http.send();

  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);

      if (res.type && res.type === "warning") {
        alertas(res.msg, res.type);
        closeModal.hide();
        return;
      }

      // Actualizar valores
      document.getElementById("montoInicial").textContent = res.montoInicial;
      document.getElementById("totalVentas").textContent = res.totalVentas;
      document.getElementById("totalCompras").textContent = res.totalCompras;
      document.getElementById("saldoFinal").textContent = res.saldoFinal;
      document.getElementById("saldoInput").value = res.saldoFinal;

      // Limpiar campos
      document.getElementById("fisicoInput").value = "";
      document.getElementById("diferenciaInput").value = "0.00";
    }
  };
}

// ✅ MEJORADA: Función para calcular diferencia con indicador visual
function calcularDiferencia() {
  const saldo = parseFloat(document.getElementById("saldoInput").value) || 0;
  const fisico = parseFloat(document.getElementById("fisicoInput").value) || 0;
  const diferencia = fisico - saldo;
  
  const diferenciaInput = document.getElementById("diferenciaInput");
  diferenciaInput.value = diferencia.toFixed(2);
  
  // Cambiar color según la diferencia
  if (diferencia < 0) {
    // FALTA DINERO (diferencia negativa) - ROJO
    diferenciaInput.style.color = "red";
    diferenciaInput.style.fontWeight = "bold";
  } else if (diferencia > 0) {
    // SOBRA DINERO (diferencia positiva) - AZUL
    diferenciaInput.style.color = "blue";
    diferenciaInput.style.fontWeight = "bold";
  } else {
    // EXACTO (diferencia = 0) - VERDE
    diferenciaInput.style.color = "green";
    diferenciaInput.style.fontWeight = "bold";
  }
}