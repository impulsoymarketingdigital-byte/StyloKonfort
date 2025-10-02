<?php
include_once 'Views/template/header-principal.php'; ?>

<!-- breadcrumb start -->
<div class="breadcrumb-main ">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="breadcrumb-contain">
                    <div>
                        <h2>registrarse</h2>
                        <ul>
                            <li><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                            <li><i class="fa fa-angle-double-right"></i></li>
                            <li><a href="javascript:void(0)">registrarse</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumb End -->

<!--section start-->
<section class="login-page section-big-py-space b-g-light">
    <div class="custom-container">
        <div class="row">
            <div class="col-lg-4 offset-lg-4">
                <div class="theme-card">
                    <h3 class="text-center">Crear cuenta</h3>
                    <form class="theme-form" autocomplete="off" id="frmRegister">
                        <div class="row g-3">
                            <div class="col-md-12 form-group">
                                <label for="nombreRegistro">Nombre</label>
                                <input type="text" class="form-control" name="nombreRegistro" id="nombreRegistro" placeholder="Nombre" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="apellidoRegistro">Apellido</label>
                                <input type="text" class="form-control" name="apellidoRegistro" id="apellidoRegistro" placeholder="Apellido" required="">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12 form-group">
                                <label for="correoRegistro">email</label>
                                <input type="email" class="form-control" name="correoRegistro" id="correoRegistro"  placeholder="Correo" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="claveRegistro">Contraseña</label>
                                <input type="password" class="form-control" id="claveRegistro" name="claveRegistro"  placeholder="Contraseña" required="">
                            </div>
                            <div class="col-md-12 form-group">
                                <button type="submit" class="btn btn-normal">crear cuenta</button></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12 ">
                                <p >Ya tienes una cuenta? <a href="#" class="txt-default" onclick="openAccount()">click aqui</a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Section ends-->

<?php include_once 'Views/template/footer-principal.php'; ?>

<script src="<?php echo BASE_URL; ?>assets/js/registro.js"></script>
</body>

</html>