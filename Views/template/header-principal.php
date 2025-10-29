<?php $empresa = $this->base->getEmpresa(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title><?php echo TITLE . ' - ' . $data['title']; ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="big-deal">
  <meta name="keywords" content="big-deal">
  <meta name="author" content="big-deal">
  <link rel="icon" href="<?php echo BASE_URL; ?>assets/admin/images/favicon.ico" type="image/png" />
  <link rel="shortcut icon" href="<?php echo BASE_URL . 'assets/images/favicon.ico'; ?>" type="image/x-icon">
  <!--Google font-->
  <link href="https://fonts.googleapis.com/css?family=PT+Sans:400,700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
  <!--icon css-->
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/themify.css">
  <!--Slick slider css-->
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/slick.css">
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/slick-theme.css">
  <!--Animate css-->
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/animate.css">
  <!-- Bootstrap css -->
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/bootstrap.css">
  <link rel="stylesheet" href="<?php echo BASE_URL . 'assets/admin/css/jquery-ui.min.css'; ?>">
  <!-- Theme css -->
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/color4.css" media="screen" id="color">
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/header.css" media="screen" id="color">
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/carrito.css" media="screen" id="color">
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/home.css" media="screen" id="color">
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>assets/css/app.css" media="screen" id="color">
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL . 'assets/DataTables/datatables.min.css'; ?>">
  <script
    src="https://www.paypal.com/sdk/js?client-id=<?php echo CLIENT_ID; ?>&currency=<?php echo CURRENCY; ?>"></script>
  <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>

<body class="bg-light ">
  <!-- loader start -->
  <div class="loader-wrapper">
    <div> <img src="<?php echo BASE_URL; ?>assets/images/loader.gif" alt="loader"> </div>
  </div>
  <!-- loader end -->
  <!--header start-->
  <header class="hed-wrapper" id="stickyheader">

    <!-- Top Bar -->
    <div class="hed-topbar">
      <div class="container">
        <div class="hed-topbar-content">
          <!-- Contacto Izquierda -->
          <div class="hed-contact-block">
            <div class="hed-contact-item">
              <svg enable-background="new 0 0 511.999 511.999" viewBox="0 0 511.999 511.999"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="m171.966 210.754c17.546-17.546 17.546-46.094 0-63.64l-61.707-61.707c-9.439-9.44-22.524-14.223-35.887-13.123-13.108 1.079-24.964 7.761-32.527 18.331-1.57 2.194-3.086 4.42-4.571 6.664l124.083 124.083z" />
                <path
                  d="m426.592 401.74-61.707-61.707c-17.544-17.544-46.094-17.546-63.64 0l-10.609 10.609 124.088 124.088c2.244-1.485 4.466-3.007 6.659-4.576 10.57-7.563 17.252-19.419 18.332-32.527 1.101-13.365-3.683-26.446-13.123-35.887z" />
                <path
                  d="m248.213 374.426c-12.021 0-23.32-4.681-31.82-13.18l-65.64-65.64c-8.499-8.499-13.181-19.8-13.181-31.82 0-6.828 1.515-13.422 4.377-19.404l-120.002-120.003c-16.88 35.443-24.35 75.152-21.268 115.01 4.153 53.71 27.315 104.164 65.221 142.069l64.642 64.641c37.904 37.905 88.359 61.067 142.069 65.221 5.869.454 11.733.679 17.583.679 33.875 0 67.202-7.552 97.426-21.946l-120.004-120.004c-5.981 2.863-12.575 4.377-19.403 4.377z" />
              </svg>
              <div class="hed-contact-info">
                <span class="hed-contact-label">Llámanos</span>
                <span class="hed-contact-value"><?php echo $empresa['telefono']; ?></span>
              </div>
            </div>
          </div>

          <!-- Logo Central -->
          <div class="hed-logo">
            <a href="<?php echo BASE_URL; ?>">
              <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="<?php echo $empresa['nombre']; ?>">
            </a>
          </div>
          <!-- Iconos Derecha -->
          <div class="hed-icons">
            <?php if (empty($_SESSION['correoCliente'])) { ?>
              <!-- Usuario NO logueado -->
              <div class="hed-icon-item" onclick="openAccount()">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                  <path
                    d="M256,0c-74.439,0-135,60.561-135,135s60.561,135,135,135s135-60.561,135-135S330.439,0,256,0z M256,240 c-57.897,0-105-47.103-105-105c0-57.897,47.103-105,105-105c57.897,0,105,47.103,105,105C361,192.897,313.897,240,256,240z" />
                  <path
                    d="M297.833,301h-83.667C144.964,301,76.669,332.951,31,401.458V512h450V401.458C435.397,333.05,367.121,301,297.833,301z M451.001,482H451H61v-71.363C96.031,360.683,152.952,331,214.167,331h83.667c61.215,0,118.135,29.683,153.167,79.637V482z" />
                </svg>
              </div>
            <?php } else { ?>
              <!-- Usuario LOGUEADO -->
              <div class="hed-user-dropdown">
                <div class="hed-user-trigger" aria-expanded="false">
                  <?php
                  $perfil = (empty($_SESSION['perfilCliente']) || $_SESSION['perfilCliente'] == null)
                    ? 'default.png'
                    : $_SESSION['perfilCliente'];
                  ?>
                  <img class="hed-user-avatar" src="<?php echo BASE_URL . 'assets/images/clientes/' . $perfil; ?>"
                    alt="Usuario">
                  <div class="hed-user-info">
                    <span class="hed-user-name">
                      <?php echo $_SESSION['nombreCliente']; ?>
                    </span>
                    <span class="hed-user-email">
                      <?php echo $_SESSION['correoCliente']; ?>
                    </span>
                  </div>
                  <i class="fa fa-chevron-down hed-user-arrow"></i>
                </div>

                <div class="hed-user-menu">
                  <a href="javascript:;" class="hed-user-menu-item" data-bs-toggle="modal" data-bs-target="#modalPerfil">
                    <i class="fa fa-user"></i>
                    <span>Mi Perfil</span>
                  </a>
                  <a href="<?php echo BASE_URL . 'clientes'; ?>" class="hed-user-menu-item">
                    <i class="fa fa-shopping-bag"></i>
                    <span>Mis Pedidos</span>
                  </a>
                  <a href="<?php echo BASE_URL . 'clientes/salir'; ?>" class="hed-user-menu-item">
                    <i class="fa fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                  </a>
                </div>
              </div>
            <?php } ?>


          </div>
        </div>
      </div>
    </div>

    <!-- Main Navigation -->
    <div class="hed-navbar">
      <div class="container">
        <div class="hed-navbar-content">
          <!-- Categorías -->
          <div class="hed-categories">
            <button class="hed-categories-btn" type="button" data-bs-toggle="collapse" data-bs-target="#hedMegaMenu"
              aria-expanded="false">
              <span class="hed-categories-icon">
                <i class="fa fa-arrow-down"></i>
              </span>
              <h5 class="hed-categories-title">Compra por categoría</h5>
            </button>

            <div class="collapse hed-mega-menu" id="hedMegaMenu">
              <?php foreach ($this->base->getCategorias() as $index => $categoria) { ?>
                <div class="hed-category-card">
                  <a href="<?php echo BASE_URL . 'principal/categorias/' . $categoria['slug']; ?>">
                    <div class="hed-category-img">
                      <?php
                      $imagenCategoria = (!empty($categoria['imagen']) && file_exists($categoria['imagen']))
                        ? BASE_URL . $categoria['imagen']
                        : BASE_URL . 'assets/images/categorias/default.jpg';
                      ?>
                      <img src="<?php echo $imagenCategoria; ?>" alt="<?php echo $categoria['categoria']; ?>"
                        loading="lazy">
                      <?php if ($index < 3): ?>
                        <span class="hed-category-label">New</span>
                      <?php endif; ?>
                    </div>
                    <div class="hed-category-info">
                      <h6 class="hed-category-name">
                        <?php echo $categoria['categoria']; ?>
                        <i class="fa fa-angle-right hed-category-arrow"></i>
                      </h6>
                    </div>
                  </a>
                </div>
              <?php } ?>
            </div>
          </div>

          <!-- Menú Principal -->
          <nav>
            <ul class="hed-menu">
              <li class="hed-menu-item">
                <a href="<?php echo BASE_URL; ?>">Inicio</a>
              </li>
              <li class="hed-menu-item">
                <a href="<?php echo BASE_URL . 'principal/shop'; ?>">Tienda</a>
              </li>

            </ul>
          </nav>

          <!-- Buscador -->
          <div class="hed-search">
            <form class="hed-search-form">
              <div class="hed-search-input-wrap">
                <span class="hed-search-icon">
                  <i class="fa fa-search"></i>
                </span>
                <input type="text" class="hed-search-input" id="inputSearch" autocomplete="off"
                  placeholder="¿Qué estás buscando?">
              </div>
              <div class="hed-search-results list-group" id="resultBusqueda"></div>
            </form>
          </div>

          <!-- Iconos Derecha -->
          <div class="hed-actions">
            <!-- Botón Hamburguesa (solo móvil) -->
            <div class="hed-mobile-toggle" id="mobileMenuBtn">
              <div class="hed-hamburger">
                <span></span>
                <span></span>
                <span></span>
              </div>
            </div>

            <div class="hed-action-item" onclick="openWishlist()">
              <svg viewBox="0 -28 512.001 512" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="m256 455.515625c-7.289062 0-14.316406-2.640625-19.792969-7.4375-20.683593-18.085937-40.625-35.082031-58.21875-50.074219l-.089843-.078125c-51.582032-43.957031-96.125-81.917969-127.117188-119.3125-34.644531-41.804687-50.78125-81.441406-50.78125-124.742187 0-42.070313 14.425781-80.882813 40.617188-109.292969 26.503906-28.746094 62.871093-44.578125 102.414062-44.578125 29.554688 0 56.621094 9.34375 80.445312 27.769531 12.023438 9.300781 22.921876 20.683594 32.523438 33.960938 9.605469-13.277344 20.5-24.660157 32.527344-33.960938 23.824218-18.425781 50.890625-27.769531 80.445312-27.769531 39.539063 0 75.910156 15.832031 102.414063 44.578125 26.191406 28.410156 40.613281 67.222656 40.613281 109.292969 0 43.300781-16.132812 82.9375-50.777344 124.738281-30.992187 37.398437-75.53125 75.355469-127.105468 119.308594-17.625 15.015625-37.597657 32.039062-58.328126 50.167969-5.472656 4.789062-12.503906 7.429687-19.789062 7.429687zm-112.96875-425.523437c-31.066406 0-59.605469 12.398437-80.367188 34.914062-21.070312 22.855469-32.675781 54.449219-32.675781 88.964844 0 36.417968 13.535157 68.988281 43.882813 105.605468 29.332031 35.394532 72.960937 72.574219 123.476562 115.625l.09375.078126c17.660156 15.050781 37.679688 32.113281 58.515625 50.332031 20.960938-18.253907 41.011719-35.34375 58.707031-50.417969 50.511719-43.050781 94.136719-80.222656 123.46875-115.617188 30.34375-36.617187 43.878907-69.1875 43.878907-105.605468 0-34.515625-11.605469-66.109375-32.675781-88.964844-20.757813-22.515625-49.300782-34.914062-80.363282-34.914062-22.757812 0-43.652344 7.234374-62.101562 21.5-16.441406 12.71875-27.894532 28.796874-34.609375 40.046874-3.453125 5.785157-9.53125 9.238282-16.261719 9.238282s-12.808594-3.453125-16.261719-9.238282c-6.710937-11.25-18.164062-27.328124-34.609375-40.046874-18.449218-14.265626-39.34375-21.5-62.097656-21.5zm0 0" />
              </svg>
              <span class="hed-action-badge" id="btnCantidadDeseo">0</span>
            </div>

            <div class="hed-action-item" onclick="openCart()">
              <svg enable-background="new 0 0 512 512" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="m497 401.667c-415.684 0-397.149.077-397.175-.139-4.556-36.483-4.373-34.149-4.076-34.193 199.47-1.037-277.492.065 368.071.065 26.896 0 47.18-20.377 47.18-47.4v-203.25c0-19.7-16.025-35.755-35.725-35.79l-124.179-.214v-31.746c0-17.645-14.355-32-32-32h-29.972c-17.64 0-31.99 14.351-31.99 31.99v31.594l-133.21-.232-9.985-54.992c-2.667-14.694-15.443-25.36-30.378-25.36h-68.561c-8.284 0-15 6.716-15 15s6.716 15 15 15c72.595 0 69.219-.399 69.422.719 16.275 89.632 5.917 26.988 49.58 306.416l-38.389.2c-18.027.069-32.06 15.893-29.81 33.899l4.252 34.016c1.883 15.06 14.748 26.417 29.925 26.417h26.62c-18.8 36.504 7.827 80.333 49.067 80.333 41.221 0 67.876-43.813 49.067-80.333h142.866c-18.801 36.504 7.827 80.333 49.067 80.333 41.22 0 67.875-43.811 49.066-80.333h31.267c8.284 0 15-6.716 15-15s-6.716-15-15-15zm-209.865-352.677c0-1.097.893-1.99 1.99-1.99h29.972c1.103 0 2 .897 2 2v111c0 8.284 6.716 15 15 15h22.276l-46.75 46.779c-4.149 4.151-10.866 4.151-15.015 0l-46.751-46.779h22.277c8.284 0 15-6.716 15-15v-111.01zm-30 61.594v34.416h-25.039c-20.126 0-30.252 24.394-16.014 38.644l59.308 59.342c15.874 15.883 41.576 15.885 57.452 0l59.307-59.342c14.229-14.237 4.13-38.644-16.013-38.644h-25.039v-34.254l124.127.214c3.186.005 5.776 2.603 5.776 5.79v203.25c0 10.407-6.904 17.4-17.18 17.4h-299.412l-35.477-227.039zm-56.302 346.249c0 13.877-11.29 25.167-25.167 25.167s-25.166-11.29-25.166-25.167 11.29-25.167 25.167-25.167 25.166 11.291 25.166 25.167zm241 0c0 13.877-11.289 25.167-25.166 25.167s-25.167-11.29-25.167-25.167 11.29-25.167 25.167-25.167 25.166 11.291 25.166 25.167z" />
              </svg>
              <span class="hed-action-badge" id="btnCantidadCarrito">0</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <div class="hed-mobile-overlay" id="mobileOverlay"></div>
  <nav class="hed-mobile-nav" id="mobileNav">
    <div class="hed-mobile-nav-header">
      <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Logo" style="max-width: 45px;">
      <button class="hed-mobile-nav-close" id="mobileNavClose">
        <i class="fa fa-times"></i>
      </button>
    </div>

    <ul class="hed-mobile-menu-list">
      <li class="hed-mobile-menu-item">
        <a href="<?php echo BASE_URL; ?>">
          <i class="fa fa-home" style="margin-right: 10px;"></i> Inicio
        </a>
      </li>
      <li class="hed-mobile-menu-item">
        <a href="<?php echo BASE_URL . 'principal/shop'; ?>">
          <i class="fa fa-shopping-bag" style="margin-right: 10px;"></i> Tienda
        </a>
      </li>
    </ul>
  </nav>


  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // MENÚ MÓVIL HAMBURGUESA
      const mobileMenuBtn = document.getElementById('mobileMenuBtn');
      const mobileNav = document.getElementById('mobileNav');
      const mobileOverlay = document.getElementById('mobileOverlay');
      const mobileNavClose = document.getElementById('mobileNavClose');

      if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          mobileNav.classList.add('active');
          mobileOverlay.classList.add('active');
        });
      }

      if (mobileNavClose) {
        mobileNavClose.addEventListener('click', function () {
          mobileNav.classList.remove('active');
          mobileOverlay.classList.remove('active');
        });
      }

      if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function () {
          mobileNav.classList.remove('active');
          mobileOverlay.classList.remove('active');
        });
      }

      // DROPDOWN USUARIO LOGUEADO
      const userTrigger = document.querySelector('.hed-user-trigger');
      const userMenu = document.querySelector('.hed-user-menu');

      if (userTrigger && userMenu) {
        userTrigger.addEventListener('click', function (e) {
          e.stopPropagation();
          const isExpanded = this.getAttribute('aria-expanded') === 'true';
          this.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
          userMenu.classList.toggle('show');
        });

        // Cerrar al hacer click fuera
        document.addEventListener('click', function (e) {
          if (!userTrigger.contains(e.target) && !userMenu.contains(e.target)) {
            userTrigger.setAttribute('aria-expanded', 'false');
            userMenu.classList.remove('show');
          }
        });
      }

      // MENÚ DE CATEGORÍAS
      const categoryBtn = document.querySelector('.hed-categories-btn');
      const megaMenu = document.querySelector('.hed-mega-menu');

      if (categoryBtn && megaMenu) {
        // Click en el botón
        categoryBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          const isExpanded = this.getAttribute('aria-expanded') === 'true';

          // Toggle: si está abierto lo cierra, si está cerrado lo abre
          this.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
          megaMenu.classList.toggle('show');
        });

        // Cerrar al hacer click fuera del menú
        document.addEventListener('click', function (e) {
          // Si el click NO es en el botón NI en el menú, cerrar
          if (!categoryBtn.contains(e.target) && !megaMenu.contains(e.target)) {
            categoryBtn.setAttribute('aria-expanded', 'false');
            megaMenu.classList.remove('show');
          }
        });

        // Evitar que los clicks dentro del menú lo cierren
        megaMenu.addEventListener('click', function (e) {
          e.stopPropagation();
        });
      }

      // SIDEBAR DE FILTROS (para móvil/tablet)
      const filterBtn = document.querySelector('.filter-btn');
      const filterSidebar = document.querySelector('.collection-filter.category-page-side');
      const filterBack = document.querySelector('.collection-mobile-back');

      if (filterBtn && filterSidebar) {
        filterBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          filterSidebar.classList.add('open');
        });

        if (filterBack) {
          filterBack.addEventListener('click', function () {
            filterSidebar.classList.remove('open');
          });
        }

        filterSidebar.addEventListener('click', function (e) {
          if (e.target === this) {
            this.classList.remove('open');
          }
        });
      }
    });
  </script>