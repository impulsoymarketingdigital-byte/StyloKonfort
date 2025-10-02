<!-- footer start -->
<footer>
  <div class="footer1 ">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="footer-main">
            <div class="footer-box">
              <div class="footer-title mobile-title">
                <h5>acerca de</h5>
              </div>
              <div class="footer-contant">
                <div class="footer-logo">
                  <a href="<?php echo BASE_URL; ?>"> <img src="<?php echo BASE_URL; ?>assets/images/logo.png" class="img-fluid" alt="logo"> </a>
                </div>
                <p><?php echo $empresa['mensaje']; ?></p>
                <ul class="sosiyal">
                  <li><a href="<?php echo $empresa['facebook']; ?>" target="_blank"><i class="fa fa-facebook"></i></a></li>
                  <li><a href="<?php echo $empresa['twitter']; ?>" target="_blank"><i class="fa fa-twitter"></i></a></li>
                  <li><a href="<?php echo $empresa['instagram']; ?>" target="_blank"><i class="fa fa-instagram"></i></a></li>
                </ul>
              </div>
            </div>
            <div class="footer-box">
              <div class="footer-title">
                <h5>mi cuenta</h5>
              </div>
              <div class="footer-contant">
                <ul>
                  <li><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                  <li><a href="<?php echo BASE_URL . 'principal/shop'; ?>">Tienda</a></li>
                  <li><a href="<?php echo BASE_URL . 'principal/about'; ?>">Servicios</a></li>
                  <li><a href="<?php echo BASE_URL . 'principal/contactos'; ?>">Contactos</a></li>
                </ul>
              </div>
            </div>
            <div class="footer-box">
              <div class="footer-title">
                <h5>contactos</h5>
              </div>
              <div class="footer-contant">
                <ul class="contact-list">
                  <li><i class="fa fa-map-marker"></i><?php echo $empresa['nombre']; ?>
                  </li>
                  <li><i class="fa fa-phone"></i>Telefono: <span><?php echo $empresa['telefono']; ?></span></li>
                  <li><i class="fa fa-envelope-o"></i>Correo: <?php echo $empresa['correo']; ?></li>
                  <li><i class="fa fa-home"></i><span><?php echo $empresa['direccion']; ?></span></li>
                </ul>
              </div>
            </div>
            <div class="footer-box">
              <div class="footer-title">
                <h5>Ubicación</h5>
              </div>
              <div class="footer-contant">
                <div class="newsletter-second">
                  <?php echo $empresa['ubicacion']; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="subfooter footer-border">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="footer-left">
            <p><?php echo date('Y'); ?></p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="footer-right">
            <ul class="payment">
              <li>
                <a href="javascript:void(0)"><img src="<?php echo BASE_URL; ?>assets/images/pay/1.png" class="img-fluid" alt="pay"></a>
              </li>
              <li>
                <a href="javascript:void(0)"><img src="<?php echo BASE_URL; ?>assets/images/pay/2.png" class="img-fluid" alt="pay"></a>
              </li>
              <li>
                <a href="javascript:void(0)"><img src="<?php echo BASE_URL; ?>assets/images/pay/3.png" class="img-fluid" alt="pay"></a>
              </li>
              <li>
                <a href="javascript:void(0)"><img src="<?php echo BASE_URL; ?>assets/images/pay/4.png" class="img-fluid" alt="pay"></a>
              </li>
              <li>
                <a href="javascript:void(0)"><img src="<?php echo BASE_URL; ?>assets/images/pay/5.png" class="img-fluid" alt="pay"></a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>
<!-- footer end -->

<!-- Quick-view modal popup start-->
<div class="modal fade bd-example-modal-lg theme-modal" id="quick-view" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content quick-view-modal">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="row" id="content-quick">
          
        </div>
        <input type="hidden" id="idSize">
        <input type="hidden" id="idColor">
      </div>
    </div>
  </div>
</div>
<!-- Quick-view modal popup end-->

<!-- Add to cart bar -->
<div id="cart_side" class="add_to_cart right">
  <a href="javascript:void(0)" class="overlay" onclick="closeCart()"></a>
  <div class="cart-inner">
    <div class="cart_top">
      <h3>mi carrito</h3>
      <div class="close-cart">
        <a href="javascript:void(0)" onclick="closeCart()">
          <i class="fa fa-times" aria-hidden="true"></i>
        </a>
      </div>
    </div>
    <div class="cart_media">
      <ul class="cart_product" id="contentCarrito">
      </ul>
      <ul class="cart_total" id="contentTotal">
      </ul>
    </div>
  </div>
</div>
<!-- Add to cart bar end-->

<!-- wishlistbar bar -->
<div id="wishlist_side" class="add_to_cart right ">
  <a href="javascript:void(0)" class="overlay" onclick="closeWishlist()"></a>
  <div class="cart-inner">
    <div class="cart_top">
      <h3>mi lista de deseos</h3>
      <div class="close-cart">
        <a href="javascript:void(0)" onclick="closeWishlist()">
          <i class="fa fa-times" aria-hidden="true"></i>
        </a>
      </div>
    </div>
    <div class="cart_media">
    <ul class="cart_product" id="contentListaDeseo">
      </ul>
      <ul class="cart_total" id="contentTotalDeseo">
      </ul>
    </div>
  </div>
</div>
<!-- wishlistbar bar end-->

<!-- My account bar start-->
<div id="myAccount" class="add_to_cart right account-bar">
  <a href="javascript:void(0)" class="overlay" onclick="closeAccount()"></a>
  <div class="cart-inner">
    <div class="cart_top">
      <h3>mi cuenta</h3>
      <div class="close-cart">
        <a href="javascript:void(0)" onclick="closeAccount()">
          <i class="fa fa-times" aria-hidden="true"></i>
        </a>
      </div>
    </div>
    <form class="theme-form" id="frmLogin" autocomplete="off">
      <span id="errorLogin" class=""></span>
      <div class="form-group mt-2">
        <label for="correoLogin">Correo electrónico</label>
        <input type="text" class="form-control" id="correoLogin"  name="correoLogin"placeholder="Correo electrónico">
      </div>
      <div class="form-group">
        <label for="claveLogin">Contraseña</label>
        <input type="password" class="form-control" id="claveLogin" name="claveLogin" placeholder="Contraseña">
      </div>
      <div class="form-group">
        <button class="btn btn-solid btn-md btn-block " type="submit" id="login">Login</button>
      </div>
      <div class="accout-fwd">
        <!-- <a href="<?php echo BASE_URL . 'clientes/forgot'; ?>" class="d-block">
          <h5>¿contraseña olvidada?</h5>
        </a> -->
        <a href="<?php echo BASE_URL . 'clientes/registro'; ?>" class="d-block">
          <h6>no tienes cuenta?<span>Regístrate ahora</span></h6>
        </a>
      </div>
    </form>
  </div>
</div>
<!-- Add to account bar end-->

<!-- latest jquery-->
<script src="<?php echo BASE_URL; ?>assets/admin/js/jquery-3.6.0.min.js"></script>
<!-- slick js-->
<script src="<?php echo BASE_URL; ?>assets/js/slick.js"></script>
<!-- tool tip js -->
<script src="<?php echo BASE_URL; ?>assets/js/tippy-popper.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/tippy-bundle.iife.min.js"></script>
<!-- popper js-->
<script src="<?php echo BASE_URL; ?>assets/js/popper.min.js"></script>
<!-- Height js-->
<script src="<?php echo BASE_URL; ?>assets/js/equal-height.js"></script>
<!-- Timer js-->
<script src="<?php echo BASE_URL; ?>assets/js/menu.js"></script>
<!-- father icon -->
<script src="<?php echo BASE_URL; ?>assets/js/feather.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/feather-icon.js"></script>
<!-- Bootstrap js-->
<script src="<?php echo BASE_URL; ?>assets/js/bootstrap-notify.min.js"></script>
<script src="<?php echo BASE_URL . 'assets/admin/js/jquery-ui.min.js'; ?>"></script>
<!-- Bootstrap js-->
<script src="<?php echo BASE_URL; ?>assets/js/bootstrap.js"></script>
<script src="<?php echo BASE_URL; ?>assets/admin/js/sweetalert2.all.min.js"></script>

<script>
  function alertaPerzanalizada(mensaje, type, titulo = '') {
    Swal.fire({
      toast: true,
      position: 'bottom-right',
      icon: type,
      title: mensaje,
      showConfirmButton: false,
      timer: 2000
    })
  }
  const base_url = '<?php echo BASE_URL; ?>';
</script>

<script src="<?php echo BASE_URL; ?>assets/js/carrito.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/login.js"></script>
<!-- Theme js-->
<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/modal.js"></script>