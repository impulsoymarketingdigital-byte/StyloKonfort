const btnAddDeseo = document.querySelectorAll(".btnAddDeseo");
const btnAddcarrito = document.querySelectorAll(".btnAddcarrito");
const btnDeseo = document.querySelector("#btnCantidadDeseo");
const btnCarrito = document.querySelector("#btnCantidadCarrito");
const verCarrito = document.querySelector("#verCarrito");
const contentCarrito = document.querySelector("#contentCarrito");
const contentListaDeseo = document.querySelector("#contentListaDeseo");
const errorBusqueda = document.querySelector("#errorBusqueda");
//ver carrito
const quickview = new bootstrap.Modal(document.getElementById("quick-view"));

let listaDeseo, listaCarrito;
document.addEventListener("DOMContentLoaded", function () {
  if (localStorage.getItem("listaDeseo") != null) {
    listaDeseo = JSON.parse(localStorage.getItem("listaDeseo"));
  } else {
    listaDeseo = [];
  }
  if (localStorage.getItem("listaCarrito") != null) {
    listaCarrito = JSON.parse(localStorage.getItem("listaCarrito"));
  } else {
    listaCarrito = [];
  }
  for (let i = 0; i < btnAddDeseo.length; i++) {
    btnAddDeseo[i].addEventListener("click", function () {
      let idProducto = btnAddDeseo[i].getAttribute("prod");
      agregarDeseo(idProducto, 1, 1);
    });
  }
  for (let i = 0; i < btnAddcarrito.length; i++) {
    btnAddcarrito[i].addEventListener("click", function () {
      let idProducto = btnAddcarrito[i].getAttribute("prod");
      agregarCarrito(idProducto, 1, 0, 0);
    });
  }
  cantidadDeseo();
  cantidadCarrito();
});
//agregar productos a la lista de deseos
function agregarDeseo(idProducto, size, color) {
  for (let i = 0; i < listaDeseo.length; i++) {
    if (
      listaDeseo[i]["idProducto"] == idProducto &&
      listaDeseo[i]["size"] == size &&
      listaDeseo[i]["color"] == color
    ) {
      alertaPerzanalizada("EL PRODUCTO YA ESTA EN LISTA DE DESEO", "warning");
      return;
    }
  }
  listaDeseo.concat(localStorage.getItem("listaDeseo"));
  listaDeseo.push({
    idProducto: idProducto,
    cantidad: 1,
    size: size,
    color: color,
  });
  localStorage.setItem("listaDeseo", JSON.stringify(listaDeseo));
  alertaPerzanalizada("PRODUCTO AGREGADO A LA LISTA DE DESEOS", "success");
  cantidadDeseo();
}

function cantidadDeseo() {
  let listas = JSON.parse(localStorage.getItem("listaDeseo"));
  if (listas != null) {
    btnDeseo.textContent = listas.length;
  } else {
    btnDeseo.textContent = 0;
  }
}

//agregar productos al carrito
function agregarCarrito(idProducto, cantidad, size, color, accion = false) {
  // Primero verificar si hay stock disponible
  const url =
    base_url + "principal/getStock/" + size + "/" + color + "/" + idProducto;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);

      // Validar que haya stock
      if (!res || res.stock <= 0) {
        alertaPerzanalizada("ESTE PRODUCTO NO TIENE STOCK DISPONIBLE", "error");
        return;
      }

      // Verificar si ya está en el carrito
      for (let i = 0; i < listaCarrito.length; i++) {
        if (accion) {
          eliminarListaDeseo(idProducto, size, color, false);
        }
        if (
          listaCarrito[i]["idProducto"] == idProducto &&
          listaCarrito[i]["size"] == size &&
          listaCarrito[i]["color"] == color
        ) {
          alertaPerzanalizada("EL PRODUCTO YA ESTÁ AGREGADO", "warning");
          return;
        }
      }

      // Validar que no exceda el stock disponible
      if (cantidad > res.stock) {
        alertaPerzanalizada(
          "SOLO HAY " + res.stock + " UNIDADES DISPONIBLES",
          "warning"
        );
        return;
      }

      listaCarrito.concat(localStorage.getItem("listaCarrito"));
      listaCarrito.push({
        idProducto: idProducto,
        cantidad: cantidad,
        size: size,
        color: color,
      });
      localStorage.setItem("listaCarrito", JSON.stringify(listaCarrito));
      alertaPerzanalizada("PRODUCTO AGREGADO AL CARRITO", "success");
      cantidadCarrito();
    }
  };
}

function cantidadCarrito() {
  let listas = JSON.parse(localStorage.getItem("listaCarrito"));
  if (listas != null) {
    btnCarrito.textContent = listas.length;
  } else {
    btnCarrito.textContent = 0;
  }
}

//ver carrito
function getListaCarrito() {
  if (listaCarrito.length > 0) {
    const url = base_url + "principal/listaProductos";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(JSON.stringify(listaCarrito));
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        let acciones = "";
        if (res.login == 1) {
          acciones = `<a href="${
            base_url + "clientes"
          }" class="btn btn-solid btn-sm ">Comprar</a>`;
        } else {
          acciones = `<a href="javascript:;" onclick="openAccount()" class="btn btn-solid btn-sm ">login</a>`;
        }
        let html = "";
        res.productos.forEach((producto) => {
          let verify =
            producto.stock == "Ilimitado" ? "" : `max="${producto.stock}"`;
          html += `<li>
                    <div class="media">
                      <a href="javascript:;">
                        <img alt="megastore1" class="me-3" src="${
                          base_url + producto.imagen
                        }">
                      </a>
                      <div class="media-body">
                        <a href="javascript:;">
                          <h6>${producto.nombre}</h6>
                          <p>${producto.atributo}</p>
                        </a>
                        <h6>
                        ${res.moneda + " " + producto.precio}
                        </h6>
                        <div class="addit-box">
                            <div class="input-group">
                                <input class="form-control agregarCantidad" type="number" value="${
                                  producto.cantidad
                                }" id="${producto.id}" size="${
            producto.size
          }" color="${producto.color}" value="${
            producto.cantidad
          }" min="1" ${verify}>
                                <button class="input-group-text btnDeletecart" prod="${
                                  producto.id
                                }" size="${producto.size}" color="${
            producto.color
          }" type="button"><i class="fa fa-trash text-danger"></i></button>
                            </div>
                        </div>
                      </div>
                    </div>
                  </li>`;
        });
        contentCarrito.innerHTML = html;
        document.querySelector("#contentTotal").innerHTML = `<li>
                        <div class="total">
                        total<span>${res.moneda + " " + res.total}</span>
                        </div>
                    </li>
                    <li>
                        <div class="buttons">
                            ${acciones}
                        </div>
                    </li>`;
        btnEliminarCarrito();
        cambiarCantidad();
      }
    };
  } else {
    document.querySelector("#contentTotal").innerHTML = "";
    contentCarrito.innerHTML = `<li>Carrito vacio</li>`;
  }
}

//ver lista de deso
function getListaDeseo() {
  if (listaDeseo.length > 0) {
    const url = base_url + "principal/listaProductos";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(JSON.stringify(listaDeseo));
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        let html = "";
        res.productos.forEach((producto) => {
          let verify =
            producto.stock == "Ilimitado" ? "" : `max="${producto.stock}"`;
          html += `<li>
                    <div class="media">
                      <a href="javascript:;">
                        <img alt="megastore1" class="me-3" src="${
                          base_url + producto.imagen
                        }">
                      </a>
                      <div class="media-body">
                        <a href="javascript:;">
                          <h6>${producto.nombre}</h6>
                          <p>${producto.atributo}</p>
                        </a>
                        <h6>
                        ${res.moneda + " " + producto.precio_venta} <span>${
            res.moneda + " " + producto.precio_venta
          }</span>
                        </h6>
                        <div class="addit-box">
                            <div class="input-group">
                                <button class="input-group-text btnAddCart" prod="${
                                  producto.id
                                }" size="${producto.size}" color="${
            producto.color
          }" type="button"><i class="fa fa-shopping-cart text-primary"></i></button>
                                <button class="input-group-text btnEliminarDeseo" prod="${
                                  producto.id
                                }" size="${producto.size}" color="${
            producto.color
          }" type="button"><i class="fa fa-trash text-danger"></i></button>
                            </div>
                        </div>
                      </div>
                    </div>
                  </li>`;
        });
        contentListaDeseo.innerHTML = html;
        document.querySelector("#contentTotalDeseo").innerHTML = `<li>
                        <div class="total">
                        total<span>${res.moneda + " " + res.total}</span>
                        </div>
                    </li>`;
        btnEliminarDeseo();
        btnAgregarProducto();
      }
    };
  } else {
    document.querySelector("#contentTotalDeseo").innerHTML = "";
    contentListaDeseo.innerHTML = `<li>Lista de deseo vacio</li>`;
  }
}

function btnEliminarDeseo() {
  let listaEliminar = document.querySelectorAll(".btnEliminarDeseo");
  for (let i = 0; i < listaEliminar.length; i++) {
    listaEliminar[i].addEventListener("click", function () {
      let idProducto = listaEliminar[i].getAttribute("prod");
      let size = listaEliminar[i].getAttribute("size");
      let color = listaEliminar[i].getAttribute("color");
      eliminarListaDeseo(idProducto, size, color);
    });
  }
}

function eliminarListaDeseo(idProducto, size, color, accion = true) {
  for (let i = 0; i < listaDeseo.length; i++) {
    if (
      listaDeseo[i]["idProducto"] == idProducto &&
      listaDeseo[i]["size"] == size &&
      listaDeseo[i]["color"] == color
    ) {
      listaDeseo.splice(i, 1);
    }
  }
  localStorage.setItem("listaDeseo", JSON.stringify(listaDeseo));
  getListaDeseo();
  cantidadDeseo();
  if (accion) {
    alertaPerzanalizada("PRODUCTO ELIMINADO DE TU LISTA", "success");
  }
}

//agregar productos desde la lista de deseos
function btnAgregarProducto() {
  let listaAgregar = document.querySelectorAll(".btnAddCart");
  for (let i = 0; i < listaAgregar.length; i++) {
    listaAgregar[i].addEventListener("click", function () {
      let idProducto = listaAgregar[i].getAttribute("prod");
      let size = listaAgregar[i].getAttribute("size");
      let color = listaAgregar[i].getAttribute("color");
      agregarCarrito(idProducto, 1, size, color, true);
    });
  }
}

function btnEliminarCarrito() {
  let listaEliminar = document.querySelectorAll(".btnDeletecart");
  for (let i = 0; i < listaEliminar.length; i++) {
    listaEliminar[i].addEventListener("click", function () {
      let idProducto = listaEliminar[i].getAttribute("prod");
      let size = listaEliminar[i].getAttribute("size");
      let color = listaEliminar[i].getAttribute("color");
      eliminarListaCarrito(idProducto, size, color);
    });
  }
}

function eliminarListaCarrito(idProducto, size, color) {
  for (let i = 0; i < listaCarrito.length; i++) {
    if (
      listaCarrito[i]["idProducto"] == idProducto &&
      listaCarrito[i]["size"] == size &&
      listaCarrito[i]["color"] == color
    ) {
      listaCarrito.splice(i, 1);
    }
  }
  localStorage.setItem("listaCarrito", JSON.stringify(listaCarrito));
  getListaCarrito();
  cantidadCarrito();
  alertaPerzanalizada("PRODUCTO ELIMINADO DEL CARRITO", "success");
}
//cambiar la cantidad
function cambiarCantidad() {
  let listaCantidad = document.querySelectorAll(".agregarCantidad");
  for (let i = 0; i < listaCantidad.length; i++) {
    listaCantidad[i].addEventListener("change", function () {
      let idProducto = listaCantidad[i].id;
      let size = listaCantidad[i].getAttribute("size");
      let color = listaCantidad[i].getAttribute("color");
      let cantidad = listaCantidad[i].value;
      incrementarCantidad(idProducto, cantidad, size, color);
    });
  }
}

function incrementarCantidad(idProducto, cantidad, size, color) {
  for (let i = 0; i < listaCarrito.length; i++) {
    if (
      listaCarrito[i]["idProducto"] == idProducto &&
      listaCarrito[i]["size"] == size &&
      listaCarrito[i]["color"] == color
    ) {
      listaCarrito[i].cantidad = cantidad;
    }
  }
  localStorage.setItem("listaCarrito", JSON.stringify(listaCarrito));
  getListaCarrito();
}

function verDetalle(idProducto) {
  const url = base_url + "principal/getProducto/" + idProducto;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      document.querySelector("#idSize").value = "";
      document.querySelector("#idColor").value = "";

      let imagenesSliderHTML = "";
      let miniaturasHTML = "";

      if (res.imagenes && res.imagenes.length > 0) {
        res.imagenes.forEach((imagen, index) => {
          const rutaImagen = `${base_url}assets/images/productos/${res.producto.id}/${imagen}`;

          imagenesSliderHTML += `
            <div class="dtl-slide ${
              index === 0 ? "active" : ""
            }" data-image="${rutaImagen}">
              <img src="${rutaImagen}" 
                   alt="${res.producto.nombre}" 
                   class="dtl-zoom-image"
                   onerror="this.src='${base_url}assets/images/productos/product.png'">
            </div>`;

          miniaturasHTML += `
            <div class="dtl-thumbnail ${
              index === 0 ? "active" : ""
            }" onclick="cambiarImagenModal(${index})">
              <img src="${rutaImagen}" 
                   alt="${res.producto.nombre}"
                   onerror="this.src='${base_url}assets/images/productos/product.png'">
            </div>`;
        });
      } else {
        const imagenProducto =
          res.producto.imagen && res.producto.imagen.trim() !== ""
            ? base_url + res.producto.imagen
            : base_url + "assets/images/productos/product.png";

        imagenesSliderHTML = `
          <div class="dtl-slide active" data-image="${imagenProducto}">
            <img src="${imagenProducto}" 
                 alt="${res.producto.nombre}" 
                 class="dtl-zoom-image"
                 onerror="this.src='${base_url}assets/images/productos/product.png'">
          </div>`;

        miniaturasHTML = `
          <div class="dtl-thumbnail active">
            <img src="${imagenProducto}" 
                 alt="${res.producto.nombre}"
                 onerror="this.src='${base_url}assets/images/productos/product.png'">
          </div>`;
      }

      const showArrows = res.imagenes && res.imagenes.length > 1;

      // Estrellas
      let uno = res.calificacion >= 1 ? "text-warning" : "text-muted";
      let dos = res.calificacion >= 2 ? "text-warning" : "text-muted";
      let tres = res.calificacion >= 3 ? "text-warning" : "text-muted";
      let cuatro = res.calificacion >= 4 ? "text-warning" : "text-muted";
      let cinco = res.calificacion == 5 ? "text-warning" : "text-muted";

      // Crear tallas
      let contentSize = "";
      if (!res.tiene_stock) {
        // NUEVO ALERT CON WHATSAPP DINÁMICO
        const whatsappNumber = res.whatsapp.replace(/[^0-9]/g, ""); // Limpiar formato

        const mensaje = encodeURIComponent(
          `Hola! Me interesa el producto: ${res.producto.nombre}. ¿Tienen disponibilidad?`
        );
        const whatsappLink = `https://wa.me/${whatsappNumber}?text=${mensaje}`;

        contentSize = `
      <div class="dtl-alert-whatsapp">
        <div class="dtl-whatsapp-icon">
          <i class="fa fa-whatsapp"></i>
        </div>
        <div class="dtl-whatsapp-content">
          <strong>¿No encuentras tu talla?</strong>
          <p>Consúltanos por WhatsApp y te ayudamos</p>
          <a href="${whatsappLink}" target="_blank" class="dtl-whatsapp-btn">
            <i class="fa fa-whatsapp"></i>
            Consultar disponibilidad
          </a>
        </div>
      </div>`;
      } else if (res.sizes.length > 0) {
        res.sizes.forEach((size) => {
          if (size.stock_disponible > 0) {
            contentSize += `<label class="dtl-size-btn dtl-size-available">
              <input type="radio" name="size" onclick="coloresDisponible(${res.producto.id}, ${size.id})">
              <span class="dtl-size-label">${size.nombre}</span>
              <span class="dtl-stock-badge">${size.stock_disponible} unid.</span>
            </label>`;
          } else {
            contentSize += `<label class="dtl-size-btn dtl-size-disabled">
              <input type="radio" name="size" disabled>
              <span class="dtl-size-label">${size.nombre}</span>
              <span class="dtl-size-cross">✕</span>
            </label>`;
          }
        });
      }

      document.querySelector("#content-quick").innerHTML = `
        <div class="col-lg-5 col-xs-12">
          <div class="dtl-product-gallery">
            <div class="dtl-main-slider" id="zoomContainer">
              ${imagenesSliderHTML}
              <div class="dtl-zoom-icon">
                <i class="fa fa-search-plus"></i>
              </div>
            </div>
            
            ${
              showArrows
                ? `
            <div class="dtl-thumbnails-wrapper">
              <button class="dtl-thumb-arrow dtl-thumb-prev" onclick="navegarMiniaturas(-1)">
                <i class="fa fa-chevron-left"></i>
              </button>
              
              <div class="dtl-thumbnails-container" id="thumbnailsContainer">
                ${miniaturasHTML}
              </div>
              
              <button class="dtl-thumb-arrow dtl-thumb-next" onclick="navegarMiniaturas(1)">
                <i class="fa fa-chevron-right"></i>
              </button>
            </div>
            `
                : `
            <div class="dtl-thumbnails-container">
              ${miniaturasHTML}
            </div>
            `
            }
          </div>
        </div>
        
        <div class="col-lg-7">
          <div class="product-right">
            <h2 style="font-size: 22px; margin-bottom: 8px;">${
              res.producto.nombre
            }</h2>
            <div class="dtl-price">${
              res.moneda + " " + res.producto.precio_venta
            }</div>
            <div class="revieu-box">
              <ul>
                <li><i class="${uno} fa fa-star"></i></li>
                <li><i class="${dos} fa fa-star"></i></li>
                <li><i class="${tres} fa fa-star"></i></li>
                <li><i class="${cuatro} fa fa-star"></i></li>
                <li><i class="${cinco} fa fa-star"></i></li>
              </ul>
              <a href="javascript:;"><span>(${res.totalCantidad} reviews)</span></a>
            </div>
            
            <p class="dtl-description">${res.producto.descripcion}</p>
            
            <h6 class="dtl-section-title"><i class="fa fa-ruler-combined"></i> Tamaño</h6>
            <div class="dtl-size-container">${contentSize}</div>
            
            ${
              res.tiene_stock
                ? `
            <h6 class="dtl-section-title"><i class="fa fa-palette"></i> Color</h6>
            <div id="content-color">
              <div class="dtl-select-size-msg">
                <i class="fa fa-hand-point-up"></i> Selecciona una talla primero
              </div>
            </div>
            
            <h6 class="dtl-section-title"><i class="fa fa-sort-numeric-up"></i> Cantidad</h6>
            <div class="dtl-quantity-box">
              <button class="dtl-qty-btn" onclick="modificarStockQuick(0, 'quantity')">
                <i class="fa fa-minus"></i>
              </button>
              <input class="dtl-qty-input" type="number" min="1" value="1" id="quantity">
              <button class="dtl-qty-btn" onclick="modificarStockQuick(1, 'quantity')">
                <i class="fa fa-plus"></i>
              </button>
            </div>
            
            <div class="dtl-action-buttons">
              <a href="javascript:void(0)" onclick="addCart(${res.producto.id}, 'quantity')" class="dtl-btn dtl-btn-cart">
                <i class="fa fa-shopping-cart"></i> Añadir
              </a>
              <a href="javascript:void(0)" onclick="addDeseo(${res.producto.id})" class="dtl-btn dtl-btn-wishlist">
                <i class="fa fa-heart"></i> Deseos
              </a>
            </div>
            `
                : ""
            }
          </div>
        </div>`;

      inicializarZoomInteractivo();

      quickview.show();
    }
  };
}

function inicializarZoomInteractivo() {
  const container = document.getElementById("zoomContainer");
  if (!container) return;

  container.addEventListener("mousemove", function (e) {
    const activeSlide = container.querySelector(".dtl-slide.active");
    const img = activeSlide.querySelector(".dtl-zoom-image");
    if (!img) return;

    const rect = container.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;

    img.style.transformOrigin = `${x}% ${y}%`;
    img.style.transform = "scale(2.5)";
  });

  container.addEventListener("mouseleave", function () {
    const activeSlide = container.querySelector(".dtl-slide.active");
    const img = activeSlide.querySelector(".dtl-zoom-image");
    if (!img) return;

    img.style.transform = "scale(1)";
  });
}

function cambiarImagenModal(index) {
  const slides = document.querySelectorAll(".dtl-slide");
  const thumbnails = document.querySelectorAll(".dtl-thumbnail");

  slides.forEach((slide, i) => {
    slide.classList.toggle("active", i === index);
  });

  thumbnails.forEach((thumb, i) => {
    thumb.classList.toggle("active", i === index);
  });

  centrarMiniaturaActiva();
}

let thumbnailScrollPosition = 0;

function navegarMiniaturas(direction) {
  const container = document.getElementById("thumbnailsContainer");
  if (!container) return;

  const scrollAmount = 100;
  thumbnailScrollPosition += direction * scrollAmount;

  const maxScroll = container.scrollWidth - container.clientWidth;
  thumbnailScrollPosition = Math.max(
    0,
    Math.min(thumbnailScrollPosition, maxScroll)
  );

  container.scrollTo({
    left: thumbnailScrollPosition,
    behavior: "smooth",
  });
}

function centrarMiniaturaActiva() {
  const container = document.getElementById("thumbnailsContainer");
  const activeThumb = document.querySelector(".dtl-thumbnail.active");

  if (!container || !activeThumb) return;

  const containerWidth = container.clientWidth;
  const thumbLeft = activeThumb.offsetLeft;
  const thumbWidth = activeThumb.offsetWidth;

  const scrollPosition = thumbLeft - containerWidth / 2 + thumbWidth / 2;

  container.scrollTo({
    left: scrollPosition,
    behavior: "smooth",
  });

  thumbnailScrollPosition = scrollPosition;
}

function coloresDisponible(idProducto, idSize) {
  const url = base_url + "principal/getColores/" + idProducto + "/" + idSize;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      let html = "No disponible";

      if (res.colores.length > 0) {
        html = "";
        res.colores.forEach((color) => {
          if (color.stock > 0) {
            html += `<label class="btn text-white" style="background-color: ${color.color};">
              <input type="radio" name="color" onclick="verificarStock(${idSize}, ${color.id}, ${idProducto}, 'quantity')"> ${color.nombre}
            </label>`;
          } else {
            html += `<label class="btn text-white disabled" style="background-color: ${color.color}; opacity: 0.5; text-decoration: line-through;">
              <input type="radio" name="color" disabled> ${color.nombre} ❌
            </label>`;
          }
        });
      }

      document.querySelector("#idSize").value = idSize;
      document.querySelector("#content-color").innerHTML = html;
    }
  };
}

function verificarStock(idSize, idColor, idProducto, quantity) {
  const url =
    base_url +
    "principal/getStock/" +
    idSize +
    "/" +
    idColor +
    "/" +
    idProducto;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);

      // Validar que haya stock
      if (res && res.stock > 0) {
        document.querySelector("#idColor").value = idColor;
        document.querySelector("#" + quantity).value = 1;
        document.querySelector("#" + quantity).setAttribute("max", res.stock);
      } else {
        alertaPerzanalizada("Este color no tiene stock disponible", "warning");
      }
    }
  };
}

function modificarStockQuick(accion, quantity) {
  let stock = document.querySelector("#" + quantity).value;
  let maximo = document.querySelector("#" + quantity).getAttribute("max");
  if (accion == 1) {
    if (parseInt(maximo) > stock) {
      document.querySelector("#" + quantity).value = parseInt(stock) + 1;
    }
  } else {
    if (parseInt(stock) > 1) {
      document.querySelector("#" + quantity).value = parseInt(stock) - 1;
    }
  }
}

function addCart(idProducto, quant) {
  let idSize = document.querySelector("#idSize");
  let idColor = document.querySelector("#idColor");
  let quantity = document.querySelector("#" + quant);
  if (idSize.value == "" || idColor.value == "") {
    alertaPerzanalizada("SELECCIONA SIZE Y COLOR", "warning");
  } else {
    agregarCarrito(idProducto, quantity.value, idSize.value, idColor.value);
    idSize.value = "";
    idColor.value = "";
    quantity.value = "1";
  }
}

function addDeseo(idProducto) {
  let idSize = document.querySelector("#idSize");
  let idColor = document.querySelector("#idColor");
  if (idSize.value == "" || idColor.value == "") {
    alertaPerzanalizada("SELECCIONA SIZE Y COLOR", "warning");
  } else {
    agregarDeseo(idProducto, idSize.value, idColor.value);
  }
}
