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
      http.send(JSON.stringify({ mensaje: mensaje.getData() }));
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
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
        productosConPrecio = [];
        res.productos.forEach((producto) => {
          productosConPrecio.push({
            id: producto.id,
            precio: producto.precio,
            cantidad: producto.cantidad,
            size: producto.size || 0,
            color: producto.color || 0,
          });

          const atributoHTML = generarAtributo(
            producto.size,
            producto.color,
            producto.nombreTalla,
            producto.nombreColor,
            producto.colorHexa,
            producto.colorSecundario || null
          );

          html += `<tr>
            <td class="text-center">
              <img class="crrt-product-img" src="${base_url}${producto.imagen}" alt="">
            </td>
            <td><h5 class="crrt-product-name">${producto.nombre}</h5></td>
            <td>${atributoHTML}</td>
            <td class="text-end"><span class="crrt-price-badge">${res.moneda} ${producto.precio}</span></td>
            <td class="text-center"><span class="crrt-quantity-badge">${producto.cantidad}</span></td>
            <td class="text-end"><strong class="crrt-subtotal">${res.moneda} ${producto.subTotal}</strong></td>
            <td class="text-center">
              <button class="crrt-btn-delete" onclick="eliminarProductoCarrito(${producto.id}, ${producto.size}, ${producto.color})" title="Eliminar">
                <i class="fa fa-trash"></i>
              </button>
            </td>
          </tr>`;
        });
        tableLista.innerHTML = html;
        document.querySelector("#totalProducto").innerHTML =
          '<i class="fa fa-shopping-bag"></i> TOTAL A PAGAR: ' + res.moneda + " " + res.total;
      } else {
        document.querySelector("#totalProducto").innerHTML =
          '<i class="fa fa-shopping-bag"></i> TOTAL: ' + res.moneda + " 0.00";
        tableLista.innerHTML = `
          <tr>
            <td colspan="7">
              <div class="crrt-empty-cart">
                <i class="fa fa-shopping-cart"></i>
                <div class="crrt-empty-cart-text">CARRITO VACÍO</div>
              </div>
            </td>
          </tr>`;
      }
    }
  };
}

function generarAtributo(size, color, nombreTalla, nombreColor, colorHexa, colorSecundario) {
  if (!nombreTalla || !nombreColor) {
    return '<span class="badge bg-secondary">Sin atributos</span>';
  }
  
  let colorHTML = '';
  if (colorSecundario && colorSecundario.trim() !== '') {
    // Color combinado (gradiente)
    colorHTML = `<span class="crrt-color-circle-split" style="background: linear-gradient(90deg, ${colorHexa} 50%, ${colorSecundario} 50%);" title="${nombreColor}"></span>`;
  } else {
    // Color sólido
    colorHTML = `<span class="crrt-color-circle" style="background-color: ${colorHexa};" title="${nombreColor}"></span>`;
  }
  
  return `
    <div class="crrt-attributes">
      <span class="crrt-size-badge"><i class="fa fa-ruler-combined"></i> ${nombreTalla}</span>
      ${colorHTML}
      <span class="crrt-color-name">${nombreColor}</span>
    </div>`;
}

function eliminarProductoCarrito(idProducto, size, color) {
  Swal.fire({
    title: "¿Eliminar producto?",
    text: "Se quitará del carrito",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      listaCarrito = listaCarrito.filter(
        (item) =>
          !(
            item.idProducto == idProducto &&
            item.size == size &&
            item.color == color
          )
      );
      localStorage.setItem("listaCarrito", JSON.stringify(listaCarrito));
      alertaPerzanalizada("PRODUCTO ELIMINADO", "success");
      cantidadCarrito();
      getListaProductos();
    }
  });
}

function eliminarProductoCarrito(idProducto, size, color) {
  Swal.fire({
    title: "¿Eliminar producto?",
    text: "Se quitará del carrito",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      listaCarrito = listaCarrito.filter(
        (item) =>
          !(
            item.idProducto == idProducto &&
            item.size == size &&
            item.color == color
          )
      );
      localStorage.setItem("listaCarrito", JSON.stringify(listaCarrito));
      alertaPerzanalizada("PRODUCTO ELIMINADO", "success");
      cantidadCarrito();
      getListaProductos();
    }
  });
}

function registrarPedido() {
  const url = base_url + "clientes/registrarPedido";
  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(JSON.stringify({ productos: productosConPrecio }));
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      alertaPerzanalizada(res.msg, res.icono);
      if (res.icono == "success") {
        localStorage.removeItem("listaCarrito");
        enviarTicketCorreo(res.idPedido);

        enviarTicketWhatsApp(res.idPedido);
      }
    }
  };
}

function enviarTicketCorreo(idPedido) {
  const formData = new FormData();
  formData.append("idPedido", idPedido);
  const url = base_url + "clientes/enviarTicket";
  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(formData);
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      if (res.icono == "success") {
        alertaPerzanalizada(res.msg, res.icono);
      }
    }
  };
}

function enviarTicketWhatsApp(idPedido) {
  const formData = new FormData();
  formData.append("idPedido", idPedido);
  const url = base_url + "clientes/enviarTicketWhatsApp";
  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(formData);
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      if (res.icono == "success" && res.whatsappLink) {
        window.open(res.whatsappLink, "_blank");

        setTimeout(() => {
          window.location.reload();
        }, 1500);
      } else {
        window.location.reload();
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
        let atributos = JSON.parse(row.atributos);
        let subTotal = parseFloat(row.precio) * parseInt(row.cantidad);

        html += `<tr>
                    <td>${row.producto}</td>
                    <td>
                        <span class="badge rounded-pill me-2">${
                          atributos.size
                        }</span>
                        <span class="badge rounded-pill" style="background-color: ${
                          atributos.hexa
                        }; color: ${
          getLuminance(atributos.hexa) > 0.5 ? "#000" : "#fff"
        };">
                            ${atributos.color}
                        </span>
                    </td>
                    <td><span class="badge bg-warning text-dark">${
                      res.moneda + " " + row.precio
                    }</span></td>
                    <td><span class="badge bg-primary">${
                      row.cantidad
                    }</span></td>
                    <td>${subTotal.toFixed(2)}</td>
                </tr>`;
      });

      document.querySelector("#tablePedidos tbody").innerHTML = html;
      mPedido.show();
    }
  };
}

function getLuminance(hex) {
  const rgb = parseInt(hex.slice(1), 16);
  const r = (rgb >> 16) & 0xff;
  const g = (rgb >> 8) & 0xff;
  const b = (rgb >> 0) & 0xff;
  return (0.299 * r + 0.587 * g + 0.114 * b) / 255;
}
function agregarCalificacion(id_producto, cantidad) {
  const url = base_url + "clientes/agregarCalificacion";
  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(JSON.stringify({ id_producto: id_producto, cantidad: cantidad }));
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      alertaPerzanalizada(res.msg, res.icono);
      if (res.icono == "success") {
        tblCalificacion.ajax.reload();
      }
    }
  };
}
