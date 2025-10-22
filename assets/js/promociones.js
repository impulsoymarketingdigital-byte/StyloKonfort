// Archivo: assets/js/promociones-cliente.js

let modalPromociones;

document.addEventListener("DOMContentLoaded", function () {
  const modalElement = document.getElementById("modal-promociones");
  if (modalElement) {
    modalPromociones = new bootstrap.Modal(modalElement);
  }

  const promocionesVistas = sessionStorage.getItem("promocionesVistas");

  if (!promocionesVistas) {
    getPromocionesActivas();
  }
});

function getPromocionesActivas() {
  const url = base_url + "principal/getPromocionesActivas";
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);

      if (res.length > 0) {
        mostrarPromociones(res);
        sessionStorage.setItem("promocionesVistas", "true");
      }
    }
  };
}

// 🎠 SIEMPRE CARRUSEL (incluso con 1 sola promoción)
function mostrarPromociones(promociones) {
  const contentPromociones = document.querySelector("#content-promociones");
  
  let indicadores = "";
  let slides = "";

  promociones.forEach((promo, index) => {
    const active = index === 0 ? "active" : "";
    const imagenPromo = promo.imagen
      ? base_url + promo.imagen
      : base_url + "assets/images/promociones/default.png";
    const linkPromo = promo.link ? promo.link : "#";
    const targetLink = promo.link ? "_blank" : "_self";

    // Indicadores del carrusel
    indicadores += `
      <button type="button" 
              data-bs-target="#carouselPromociones" 
              data-bs-slide-to="${index}" 
              class="${active}" 
              aria-label="Slide ${index + 1}">
      </button>
    `;

    // Slides del carrusel
    slides += `
      <div class="carousel-item ${active}">
        ${
          promo.link
            ? `<a href="${linkPromo}" target="${targetLink}">
                <img src="${imagenPromo}" 
                     class="d-block w-100" 
                     alt="${promo.titulo}"
                     onerror="this.src='${base_url}assets/images/promociones/default.png'">
              </a>`
            : `<img src="${imagenPromo}" 
                   class="d-block w-100" 
                   alt="${promo.titulo}"
                   onerror="this.src='${base_url}assets/images/promociones/default.png'">`
        }
        <div class="carousel-caption">
          <h5>${promo.titulo}</h5>
          ${promo.descripcion ? `<p class="d-none d-md-block">${promo.descripcion}</p>` : ""}
          ${
            promo.link
              ? `<a href="${linkPromo}" target="_blank" class="btn btn-solid btn-sm">Ver promoción</a>`
              : ""
          }
        </div>
      </div>
    `;
  });

  contentPromociones.innerHTML = `
    <div id="carouselPromociones" class="carousel slide" data-bs-ride="carousel">
      ${promociones.length > 1 ? `<div class="carousel-indicators">${indicadores}</div>` : ""}
      <div class="carousel-inner">
        ${slides}
      </div>
      ${promociones.length > 1 ? `
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselPromociones" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselPromociones" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Siguiente</span>
        </button>
      ` : ""}
    </div>
    <div class="carousel-footer">
      <button type="button" class="btn btn-solid" onclick="cerrarModalPromociones()">
        <i class="fa fa-check me-2"></i>Entendido
      </button>
    </div>
  `;

  setTimeout(() => {
    if (modalPromociones) {
      modalPromociones.show();
    }
  }, 1500);
}

function cerrarModalPromociones() {
  if (modalPromociones) {
    modalPromociones.hide();
  }
}

function resetearPromociones() {
  sessionStorage.removeItem("promocionesVistas");
  alertaPerzanalizada(
    "Las promociones se mostrarán en la próxima carga",
    "success"
  );
}