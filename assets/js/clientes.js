const tableLista = document.querySelector("#tableListaProductos tbody");
const tblPendientes = document.querySelector("#tblPendientes");
const btnFinalizarPedido = document.querySelector("#btnFinalizarPedido");
let tblCalificacion, mensaje;
const estadoEnviado = document.querySelector("#estadoEnviado");
const estadoProceso = document.querySelector("#estadoProceso");
const btnTestimonio = document.querySelector("#btnTestimonio");
const comentario = document.querySelector("#comentario");
const frmDatos = document.querySelector("#frmDatos");
const nomCliente = document.querySelector("#nomCliente");
const apeCliente = document.querySelector("#apeCliente");
const corCliente = document.querySelector("#corCliente");
const telCliente = document.querySelector("#telCliente");
const dirCliente = document.querySelector("#dirCliente");
const estadoCompletado = document.querySelector("#estadoCompletado");

document.addEventListener("DOMContentLoaded", function () {
  if (tableLista) {
    getListaProductos();
  }

  // Evento para el botón Finalizar Pedido
  if (btnFinalizarPedido) {
    btnFinalizarPedido.addEventListener("click", function () {
      if (listaCarrito.length === 0) {
        alertaPerzanalizada("EL CARRITO ESTÁ VACÍO", "warning");
        return;
      }

      Swal.fire({
        title: "¿Confirmar Pedido?",
        text: "Se registrará tu pedido para pago contra entrega",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, confirmar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          registrarPedido();
        }
      });
    });
  }

  //cargar datos pendientes con DataTables
  $("#tblPendientes").DataTable({
    ajax: {
      url: base_url + "clientes/listarPendientes",
      dataSrc: "",
    },
    columns: [
      { data: "id" },
      { data: "id_transaccion" },
      { data: "monto" },
      { data: "metodo" },
      { data: "fecha" },
      { data: "accion" },
    ],
    language,
    responsive: true,
    order: [[0, "desc"]],
  });

  tblCalificacion = $("#tblProductos").DataTable({
    ajax: {
      url: base_url + "clientes/listarProductos",
      dataSrc: "",
    },
    columns: [
      { data: "id_producto" },
      { data: "producto" },
      { data: "precio" },
      { data: "cantidad" },
      { data: "calificacion" },
    ],
    language,
  });

  ClassicEditor.create(document.querySelector("#comentario"), {
    toolbar: {
      items: [
        "heading",
        "|",
        "bold",
        "italic",
        "strikethrough",
        "underline",
        "|",
        "undo",
        "redo",
        "|",
        "alignment",
        "|",
        "link",
        "blockQuote",
        "insertTable",
        "mediaEmbed",
      ],
      shouldNotGroupWhenFull: true,
    },
  })
    .then((newEditor) => {
      mensaje = newEditor;
    })
    .catch((error) => {
      console.error(error);
    });

  btnTestimonio.addEventListener("click", function () {
    if (mensaje.getData() == "") {
      alertaPerzanalizada("INGRESA UN COMENTARIO", "warning");
    } else {
      const url = base_url + "clientes/agregarMensaje";
      const http = new XMLHttpRequest();
      http.open("POST", url, true);
      http.send(
        JSON.stringify({
          mensaje: mensaje.getData(),
        })
      );
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          console.log(this.responseText);
          const res = JSON.parse(this.responseText);
          alertaPerzanalizada(res.msg, res.icono);
        }
      };
    }
  });

  frmDatos.addEventListener("submit", function (e) {
    e.preventDefault();
    if (
      nomCliente.value == "" ||
      apeCliente.value == "" ||
      telCliente.value == "" ||
      corCliente.value == "" ||
      dirCliente.value == ""
    ) {
      alertaPerzanalizada("TODO LOS CAMPOS CON * SON REQUERIDOS", "warning");
    } else {
      const url = base_url + "clientes/modificarDatos";
      const http = new XMLHttpRequest();
      http.open("POST", url, true);
      http.send(new FormData(this));
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          alertaPerzanalizada(res.msg, res.type);
        }
      };
    }
  });
});
let productosConPrecio = [];

function getListaProductos() {
  let html = "";
  const url = base_url + "principal/listaProductos";
  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(JSON.stringify(listaCarrito));
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      if (res.totalPaypal > 0) {
        productosConPrecio = []; // Limpiar array
        res.productos.forEach((producto) => {
          // Guardar producto con precio
          productosConPrecio.push({
            id: producto.id,
            precio: producto.precio,
            cantidad: producto.cantidad,
            size: producto.size || 0,
            color: producto.color || 0,
          });

          html += `<tr>
                            <td>
                                <img class="img-thumbnail rounded-circle" src="${
                                  producto.imagen
                                }" alt="" width="100">
                            </td>
                            <td>${producto.nombre}</td>
                            <td>${producto.atributo}</td>
                            <td>${producto.stock}</td>
                            <td><span class="badge bg-warning">${
                              res.moneda + " " + producto.precio
                            }</span></td>
                            <td><span class="badge bg-primary">${
                              producto.cantidad
                            }</span></td>
                            <td>${producto.subTotal}</td>
                        </tr>`;
        });
        tableLista.innerHTML = html;
        document.querySelector("#totalProducto").textContent =
          "TOTAL A PAGAR: " + res.moneda + " " + res.total;
      } else {
        document.querySelector("#totalProducto").textContent =
          res.moneda + " 0.00";
        tableLista.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center">CARRITO VACIO</td>
                </tr>`;
      }
    }
  };
}

function registrarPedido() {
  const url = base_url + "clientes/registrarPedido";
  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(
    JSON.stringify({
      productos: productosConPrecio, // Usar los productos con precio
    })
  );
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
      const res = JSON.parse(this.responseText);
      alertaPerzanalizada(res.msg, res.icono);
      if (res.icono == "success") {
        localStorage.removeItem("listaCarrito");
        setTimeout(() => {
          window.location.reload();
        }, 2000);
      }
    }
  };
}
function verPedido(idPedido) {
  estadoEnviado.classList.remove("border-success");
  estadoProceso.classList.remove("border-success");
  estadoCompletado.classList.remove("border-success");
  const mPedido = new bootstrap.Modal(document.getElementById("modalPedido"));
  const url = base_url + "clientes/verPedido/" + idPedido;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
      const res = JSON.parse(this.responseText);
      let html = "";
      if (res.pedido.proceso == 1) {
        estadoEnviado.classList.add("border-success");
      } else if (res.pedido.proceso == 2) {
        estadoProceso.classList.add("border-success");
      } else {
        estadoCompletado.classList.add("border-success");
      }
      res.productos.forEach((row) => {
        let verify =
          row.atributos == "Descargable"
            ? `<a href="${res.descarga.ruta}" class="btn btn-danger"><i class="fas fa-download"></i></a>`
            : "";
        let subTotal = parseFloat(row.precio) * parseInt(row.cantidad);
        html += `<tr>
                    <td>${row.producto}</td>
                    <td>${row.atributos}</td>
                    <td><span class="badge bg-warning">${
                      res.moneda + " " + row.precio
                    }</span></td>
                    <td><span class="badge bg-primary">${
                      row.cantidad
                    }</span></td>
                    <td>${subTotal.toFixed(2)}</td>
                    <td>${verify}</td>
                </tr>`;
      });
      document.querySelector("#tablePedidos tbody").innerHTML = html;
      mPedido.show();
    }
  };
}

function agregarCalificacion(id_producto, cantidad) {
  const url = base_url + "clientes/agregarCalificacion";
  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(
    JSON.stringify({
      id_producto: id_producto,
      cantidad: cantidad,
    })
  );
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
      const res = JSON.parse(this.responseText);
      alertaPerzanalizada(res.msg, res.icono);
      if (res.icono == "success") {
        tblCalificacion.ajax.reload();
      }
    }
  };
}
