<?php include_once 'Views/template/header-principal.php'; ?>

<!--slider start-->
<section class="hero-slider-section">
  <div class="hero-slider-wrapper">
    <div class="hero-slider-container">
      <div class="hero-slides">
        <?php foreach ($this->base->getSliders() as $slider) { ?>
          <div class="hero-slide-item">
            <div class="hero-slide-content">
              <div class="hero-slide-image">
                <img src="<?php echo BASE_URL . $slider['imagen']; ?>" alt="slider">
              </div>
              <div class="hero-slide-overlay"></div>
              <div class="hero-slide-text">
                <div class="hero-text-inner">
                  <h3 class="hero-subtitle"><?php echo $slider['titulo']; ?></h3>
                  <h2 class="hero-title"><?php echo $slider['subtitulo']; ?></h2>

                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</section>
<!--slider end-->

<!--tab product-->
<section class="hm-products-section">
  <div class="hm-container">
    <div class="hm-tabs-wrapper">
      <div class="hm-tabs-header">
        <button class="hm-tab-btn active" data-tab="hm-tab-nuevos">
          <span class="hm-tab-icon"></span>
          Nuevos Productos
        </button>
        <button class="hm-tab-btn" data-tab="hm-tab-destacados">
          <span class="hm-tab-icon"></span>
          Destacados
        </button>
        <button class="hm-tab-btn" data-tab="hm-tab-vendidos">
          <span class="hm-tab-icon"></span>
          Más Vendidos
        </button>
      </div>

      <div class="hm-tabs-content">
        <!-- TAB NUEVOS -->
        <div id="hm-tab-nuevos" class="hm-tab-panel active">
          <?php if (!empty($data['nuevoProductos'])) { ?>
            <div class="hm-products-grid">
              <?php foreach ($data['nuevoProductos'] as $producto) {
                $imagenProducto = (!empty($producto['imagen']) && file_exists($producto['imagen']))
                  ? BASE_URL . $producto['imagen']
                  : BASE_URL . 'assets/images/productos/product.png';
                ?>
                <div class="hm-product-card">
                  <div class="hm-product-image">
                    <img src="<?php echo $imagenProducto; ?>" alt="<?php echo $producto['nombre']; ?>">
                    <div class="hm-product-badge">Nuevo</div>
                    <div class="hm-product-actions">
                      <button onclick="verDetalle(<?php echo $producto['id']; ?>)" class="hm-action-btn"
                        title="Vista previa">
                        <i data-feather="eye"></i>
                      </button>
                      <a href="<?php echo BASE_URL . 'principal/detail/' . $producto['slug']; ?>" class="hm-action-btn"
                        title="Ver Detalle">
                        <i data-feather="external-link"></i>
                      </a>
                    </div>
                  </div>
                  <div class="hm-product-info">
                    <div class="hm-product-rating">
                      <?php
                      for ($i = 1; $i <= 5; $i++) {
                        echo ($producto['calificacion'] >= $i)
                          ? '<i class="hm-star-filled" data-feather="star"></i>'
                          : '<i class="hm-star-empty" data-feather="star"></i>';
                      }
                      ?>
                    </div>
                    <h3 class="hm-product-title">
                      <a href="<?php echo BASE_URL . 'principal/detail/' . $producto['slug']; ?>">
                        <?php echo $producto['nombre']; ?>
                      </a>
                    </h3>
                    <?php echo MONEDA . number_format((float)($producto['precio_venta'] ?? 0), 0, ',', '.'); ?>
                  </div>
                </div>
              <?php } ?>
            </div>
          <?php } else { ?>
            <div class="hm-empty-state">
              <div class="hm-empty-icon">📦</div>
              <h3>No hay productos nuevos</h3>
              <p>Pronto tendremos novedades para ti</p>
            </div>
          <?php } ?>
        </div>

        <!-- TAB DESTACADOS -->
        <div id="hm-tab-destacados" class="hm-tab-panel">
          <?php if (!empty($data['destacados'])) { ?>
            <div class="hm-products-grid">
              <?php foreach ($data['destacados'] as $producto) {
                $imagenProducto = (!empty($producto['prod']['imagen']) && file_exists($producto['prod']['imagen']))
                  ? BASE_URL . $producto['prod']['imagen']
                  : BASE_URL . 'assets/images/productos/product.png';
                ?>
                <div class="hm-product-card">
                  <div class="hm-product-image">
                    <img src="<?php echo $imagenProducto; ?>" alt="<?php echo $producto['prod']['nombre']; ?>">
                    <div class="hm-product-badge hm-badge-featured">Destacado</div>
                    <div class="hm-product-actions">
                      <button onclick="verDetalle(<?php echo $producto['id_producto']; ?>)" class="hm-action-btn"
                        title="Vista previa">
                        <i data-feather="eye"></i>
                      </button>
                      <a href="<?php echo BASE_URL . 'principal/detail/' . $producto['prod']['slug']; ?>"
                        class="hm-action-btn" title="Ver Detalle">
                        <i data-feather="shopping-cart"></i>
                      </a>
                    </div>
                  </div>
                  <div class="hm-product-info">
                    <div class="hm-product-rating">
                      <?php
                      // ⭐ AGREGAR VALIDACIÓN AQUÍ
                      $calificacion = isset($producto['calificacion']) ? $producto['calificacion'] : 5;
                      for ($i = 1; $i <= 5; $i++) {
                        echo ($calificacion >= $i)
                          ? '<i class="hm-star-filled" data-feather="star"></i>'
                          : '<i class="hm-star-empty" data-feather="star"></i>';
                      }
                      ?>
                    </div>
                    <h3 class="hm-product-title">
                      <a href="<?php echo BASE_URL . 'principal/detail/' . $producto['prod']['slug']; ?>">
                        <?php echo $producto['prod']['nombre']; ?>
                      </a>
                    </h3>
                    <?php echo MONEDA . number_format((float)($producto['prod']['precio_venta'] ?? 0), 0, ',', '.'); ?>
                  </div>
                </div>
              <?php } ?>
            </div>
          <?php } else { ?>
            <div class="hm-empty-state">
              <div class="hm-empty-icon">⭐</div>
              <h3>No hay productos destacados</h3>
              <p>Estamos seleccionando los mejores productos para ti</p>
            </div>
          <?php } ?>
        </div>

        <!-- TAB MÁS VENDIDOS -->
        <div id="hm-tab-vendidos" class="hm-tab-panel">
          <?php if (!empty($data['especiales'])) { ?>
            <div class="hm-products-grid">
              <?php foreach ($data['especiales'] as $producto) {
                $imagenProducto = (!empty($producto['prod']['imagen']) && file_exists($producto['prod']['imagen']))
                  ? BASE_URL . $producto['prod']['imagen']
                  : BASE_URL . 'assets/images/productos/product.png';
                ?>
                <div class="hm-product-card">
                  <div class="hm-product-image">
                    <img src="<?php echo $imagenProducto; ?>" alt="<?php echo $producto['prod']['nombre']; ?>">
                    <div class="hm-product-badge hm-badge-hot">Top Ventas</div>
                    <div class="hm-product-actions">
                      <button onclick="verDetalle(<?php echo $producto['id_producto']; ?>)" class="hm-action-btn"
                        title="Vista previa">
                        <i data-feather="eye"></i>
                      </button>
                      <a href="<?php echo BASE_URL . 'principal/detail/' . $producto['prod']['slug']; ?>"
                        class="hm-action-btn" title="Ver Detalle">
                        <i data-feather="shopping-cart"></i>
                      </a>
                    </div>
                  </div>
                  <div class="hm-product-info">
                    <div class="hm-product-rating">
                      <?php
                      // ⭐ AGREGAR VALIDACIÓN AQUÍ
                      $calificacion = isset($producto['calificacion']) ? $producto['calificacion'] : 5;
                      for ($i = 1; $i <= 5; $i++) {
                        echo ($calificacion >= $i)
                          ? '<i class="hm-star-filled" data-feather="star"></i>'
                          : '<i class="hm-star-empty" data-feather="star"></i>';
                      }
                      ?>
                    </div>
                    <h3 class="hm-product-title">
                      <a href="<?php echo BASE_URL . 'principal/detail/' . $producto['prod']['slug']; ?>">
                        <?php echo $producto['prod']['nombre']; ?>
                      </a>
                    </h3>
                    <?php echo MONEDA . number_format((float)($producto['prod']['precio_venta'] ?? 0), 0, ',', '.'); ?>
                  </div>
                </div>
              <?php } ?>
            </div>
          <?php } else { ?>
            <div class="hm-empty-state">
              <div class="hm-empty-icon">🔥</div>
              <h3>No hay productos más vendidos</h3>
              <p>Pronto tendremos nuestros bestsellers aquí</p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>
<!--tab product end-->

<!--testimonial start-->
<section class="hm-testimonials-section">
  <div class="hm-container">
    <div class="hm-section-header">
      <h2 class="hm-section-title">Lo que dicen nuestros clientes</h2>
      <p class="hm-section-subtitle">Experiencias reales de quienes confían en nosotros</p>
    </div>

    <?php if (!empty($data['testimonios'])) { ?>
      <div class="hm-testimonials-slider">
        <?php foreach ($data['testimonios'] as $testimonio) { ?>
          <div class="hm-testimonial-card">
            <div class="hm-testimonial-quote">"</div>
            <p class="hm-testimonial-message"><?php echo $testimonio['mensaje']; ?></p>
            <div class="hm-testimonial-author">
              <img src="<?php echo BASE_URL . 'assets/images/clientes/' . $testimonio['perfil']; ?>"
                alt="<?php echo $testimonio['nombre']; ?>" class="hm-author-avatar">
              <div class="hm-author-info">
                <h4 class="hm-author-name"><?php echo $testimonio['nombre']; ?></h4>
                <div class="hm-author-rating">
                  <i data-feather="star"></i>
                  <i data-feather="star"></i>
                  <i data-feather="star"></i>
                  <i data-feather="star"></i>
                  <i data-feather="star"></i>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } else { ?>
      <div class="hm-empty-state">
        <div class="hm-empty-icon">💬</div>
        <h3>Aún no hay testimonios</h3>
        <p>Sé el primero en compartir tu experiencia</p>
      </div>
    <?php } ?>
  </div>
</section>
<!--testimonial end-->

<?php include_once 'Views/template/footer-principal.php'; ?>

<script>
  // Slider principal (mantener como está)
  $(document).ready(function () {
    $('.hero-slides').slick({
      dots: true,
      infinite: true,
      speed: 800,
      slidesToShow: 1,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 3000,
      fade: true,
      cssEase: 'cubic-bezier(0.4, 0, 0.2, 1)',
      pauseOnHover: true,
      pauseOnFocus: false,
      arrows: true,
      draggable: true,
      swipe: true,
      touchMove: true,
      adaptiveHeight: false,
      prevArrow: '<button type="button" class="slick-prev"></button>',
      nextArrow: '<button type="button" class="slick-next"></button>',
      responsive: [
        {
          breakpoint: 768,
          settings: {
            arrows: false,
            dots: true
          }
        },
        {
          breakpoint: 576,
          settings: {
            arrows: false,
            dots: true,
            autoplaySpeed: 4000
          }
        }
      ]
    });

    // Tabs de productos
    $('.hm-tab-btn').on('click', function () {
      const targetTab = $(this).data('tab');

      $('.hm-tab-btn').removeClass('active');
      $(this).addClass('active');

      $('.hm-tab-panel').removeClass('active');
      $('#' + targetTab).addClass('active');
    });

    // Slider de testimonios
    if ($('.hm-testimonials-slider').length) {
      $('.hm-testimonials-slider').slick({
        dots: true,
        infinite: true,
        speed: 600,
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 5000,
        pauseOnHover: true,
        arrows: true,
        prevArrow: '<button type="button" class="hm-testimonial-prev"><i data-feather="chevron-left"></i></button>',
        nextArrow: '<button type="button" class="hm-testimonial-next"><i data-feather="chevron-right"></i></button>',
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
              arrows: false
            }
          }
        ]
      });
    }

    // Inicializar iconos de Feather
    if (typeof feather !== 'undefined') {
      feather.replace();
    }
  });
</script>
</body>

</html>