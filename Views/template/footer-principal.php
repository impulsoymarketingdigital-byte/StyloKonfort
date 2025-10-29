<!-- footer start -->
<!-- Footer Moderno -->
<footer class="ft-main">
  <div class="ft-container">
    <div class="ft-grid">

      <!-- Columna Logo y Descripción -->
      <div class="ft-column ft-brand">
        <div class="ft-logo-wrapper">
          <a href="<?php echo BASE_URL; ?>">
            <img src="<?php echo BASE_URL; ?>assets/images/logo.png" class="ft-logo-img" alt="logo">
          </a>
        </div>
        <p class="ft-description"><?php echo $empresa['mensaje']; ?></p>
        <div class="ft-social">
          <a href="<?php echo $empresa['facebook']; ?>" target="_blank" class="ft-social-link">
            <i class="fa fa-facebook"></i>
          </a>
          <a href="<?php echo $empresa['twitter']; ?>" target="_blank" class="ft-social-link">
            <i class="fa fa-twitter"></i>
          </a>
          <a href="<?php echo $empresa['instagram']; ?>" target="_blank" class="ft-social-link">
            <i class="fa fa-instagram"></i>
          </a>
        </div>
      </div>

      <!-- Columna Enlaces Rápidos -->
      <div class="ft-column">
        <h3 class="ft-title">Enlaces Rápidos</h3>
        <ul class="ft-links">
          <li><a href="<?php echo BASE_URL; ?>" class="ft-link">Inicio</a></li>
          <li><a href="<?php echo BASE_URL . 'principal/shop'; ?>" class="ft-link">Tienda</a></li>
        </ul>
      </div>

      <!-- Columna Contacto -->
      <div class="ft-column">
        <h3 class="ft-title">Contacto</h3>
        <ul class="ft-contact">
          <li class="ft-contact-item">
            <i class="fa fa-map-marker ft-icon"></i>
            <span><?php echo $empresa['direccion']; ?></span>
          </li>
          <li class="ft-contact-item">
            <i class="fa fa-phone ft-icon"></i>
            <span><?php echo $empresa['telefono']; ?></span>
          </li>
          <li class="ft-contact-item">
            <i class="fa fa-envelope ft-icon"></i>
            <span><?php echo $empresa['correo']; ?></span>
          </li>
        </ul>
      </div>

      <!-- Columna Ubicación -->
      <div class="ft-column">
        <h3 class="ft-title">Ubicación</h3>
        <div class="ft-map">
          <?php echo $empresa['ubicacion']; ?>
        </div>
      </div>

    </div>
  </div>

  <!-- Footer Bottom -->
  <div class="ft-bottom">
    <div class="ft-container">
      <p class="ft-copyright">
        © <?php echo date('Y'); ?> <?php echo $empresa['nombre']; ?>. Todos los derechos reservados.
      </p>
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

<!-- Modal Promociones start-->
<div class="modal fade bd-example-modal-lg" id="modal-promociones" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body p-0" id="content-promociones">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-index-1" data-bs-dismiss="modal"
          aria-label="Close" style="z-index: 9999;"></button>
        <!-- Aquí se cargarán las promociones dinámicamente -->
      </div>
    </div>
  </div>
</div>
<!-- Modal Promociones end-->

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
    <form class="theme-form" id="frmLogin">
      <span id="errorLogin" class=""></span>
      <div class="form-group mt-2">
        <label for="correoLogin">Correo electrónico</label>
        <input type="text" class="form-control" id="correoLogin" name="correoLogin" placeholder="Correo electrónico">
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

<div class="modal fade" id="modalPerfil" tabindex="-1" aria-labelledby="modalPerfilLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPerfilLabel">
          <i class="fa fa-user-circle"></i> Mi Perfil
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form autocomplete="off" id="frmDatos">
        <div class="modal-body">
          <?php if (!empty($_SESSION['idCliente'])) { ?>
            <div class="text-center mb-4">
              <?php
              $perfil = (empty($_SESSION['perfilCliente']) || $_SESSION['perfilCliente'] == null) ? 'default.png' : $_SESSION['perfilCliente'];
              ?>
              <img class="img-thumbnail rounded-circle" id="imgPerfilModal"
                src="<?php echo BASE_URL . 'assets/images/clientes/' . $perfil; ?>" alt="Perfil" width="150" height="150"
                style="object-fit: cover;">
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-3">
                  <label for="nomCliente" class="form-label">
                    <i class="fa fa-user"></i> Nombres <span class="text-danger">*</span>
                  </label>
                  <input id="nomCliente" class="form-control" type="text" name="nombre" placeholder="Nombres">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-3">
                  <label for="apeCliente" class="form-label">
                    <i class="fa fa-user"></i> Apellidos <span class="text-danger">*</span>
                  </label>
                  <input id="apeCliente" class="form-control" type="text" name="apellidos" placeholder="Apellidos">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-3">
                  <label for="corCliente" class="form-label">
                    <i class="fa fa-envelope"></i> Correo <span class="text-danger">*</span>
                  </label>
                  <input id="corCliente" class="form-control" type="email" name="correo" placeholder="Correo">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-3">
                  <label for="telCliente" class="form-label">
                    <i class="fa fa-phone"></i> Teléfono <span class="text-danger">*</span>
                  </label>
                  <input id="telCliente" class="form-control" type="text" name="telefono" placeholder="Teléfono">
                </div>
              </div>
            </div>

            <div class="form-group mb-3">
              <label for="dirCliente" class="form-label">
                <i class="fa fa-map-marker-alt"></i> Dirección <span class="text-danger">*</span>
              </label>
              <textarea id="dirCliente" class="form-control" name="direccion" rows="3"
                placeholder="Dirección completa"></textarea>
            </div>

            <div class="form-group mb-3">
              <label for="fotoCliente" class="form-label">
                <i class="fa fa-camera"></i> Cambiar Foto de Perfil
              </label>
              <input id="fotoCliente" class="form-control" type="file" name="fotoCliente" accept="image/*">
              <small class="text-muted">Formatos: JPG, PNG. Tamaño máximo: 2MB</small>
            </div>
          <?php } else { ?>
            <div class="text-center py-5">
              <i class="fa fa-user-lock fa-3x text-muted mb-3"></i>
              <h5>Debes iniciar sesión</h5>
              <p class="text-muted">Inicia sesión para ver tu perfil</p>
              <button type="button" class="btn btn-primary" onclick="openAccount()" data-bs-dismiss="modal">
                <i class="fa fa-sign-in-alt"></i> Iniciar Sesión
              </button>
            </div>
          <?php } ?>
        </div>
        <?php if (!empty($_SESSION['idCliente'])) { ?>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fa fa-times"></i> Cancelar
            </button>
            <button class="btn btn-primary" type="submit">
              <i class="fa fa-save"></i> Guardar Cambios
            </button>
          </div>
        <?php } ?>
      </form>
    </div>
  </div>
</div>

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


<script>
// Script global para modal de perfil
document.addEventListener('DOMContentLoaded', function() {
    const frmDatos = document.getElementById('frmDatos');
    const modalPerfil = document.getElementById('modalPerfil');
    
    if (modalPerfil) {
        modalPerfil.addEventListener('show.bs.modal', function() {
            cargarDatosCliente();
        });
    }

    if (frmDatos) {
        frmDatos.addEventListener("submit", function (e) {
            e.preventDefault();
            
            const nomCliente = document.getElementById('nomCliente');
            const apeCliente = document.getElementById('apeCliente');
            const telCliente = document.getElementById('telCliente');
            const corCliente = document.getElementById('corCliente');
            const dirCliente = document.getElementById('dirCliente');
            
            if (!nomCliente.value || !apeCliente.value || !telCliente.value || !corCliente.value || !dirCliente.value) {
                alertaPerzanalizada("TODOS LOS CAMPOS CON * SON REQUERIDOS", "warning");
            } else {
                const url = base_url + "principal/modificarDatos";
                const http = new XMLHttpRequest();
                http.open("POST", url, true);
                http.send(new FormData(this));
                http.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        const res = JSON.parse(this.responseText);
                        alertaPerzanalizada(res.msg, res.type);
                        if (res.type == 'success') {
                            setTimeout(() => location.reload(), 1500);
                        }
                    }
                };
            }
        });
    }
});

function cargarDatosCliente() {
    const url = base_url + "principal/getDatosCliente";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.success) {
                document.getElementById('nomCliente').value = res.data.nombre || '';
                document.getElementById('apeCliente').value = res.data.apellido || '';
                document.getElementById('corCliente').value = res.data.correo || '';
                document.getElementById('telCliente').value = res.data.telefono || '';
                document.getElementById('dirCliente').value = res.data.direccion || '';
            }
        }
    };
}

function abrirModalPerfil() {
    const modalPerfil = new bootstrap.Modal(document.getElementById('modalPerfil'));
    modalPerfil.show();
}
</script>

<script src="<?php echo BASE_URL; ?>assets/js/carrito.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/login.js"></script>
<!-- Theme js-->
<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/modal.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/promociones.js"></script>