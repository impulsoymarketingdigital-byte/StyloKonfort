<?php include_once 'Views/template/header-principal.php'; ?>
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

<style>
.rgt-login-section {
    background: #ffffff;
    padding: 60px 0;
}

.rgt-form-container {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    padding: 40px;
    max-width: 700px;
    margin: 0 auto;
}

.rgt-form-title {
    font-size: 28px;
    font-weight: 700;
    color: #057997;
    margin-bottom: 8px;
    text-align: center;
}

.rgt-form-subtitle {
    color: #666666;
    text-align: center;
    margin-bottom: 35px;
    font-size: 14px;
}

.rgt-form-group {
    margin-bottom: 20px;
}

.rgt-form-label {
    display: block;
    font-weight: 500;
    color: #333333;
    margin-bottom: 8px;
    font-size: 14px;
}

.rgt-form-input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: #ffffff;
    color: #333333;
}

.rgt-form-input:focus {
    outline: none;
    border-color: #057997;
    box-shadow: 0 0 0 3px rgba(5, 121, 151, 0.1);
}

.rgt-form-select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: #ffffff;
    cursor: pointer;
    color: #333333;
}

.rgt-form-select:focus {
    outline: none;
    border-color: #057997;
    box-shadow: 0 0 0 3px rgba(5, 121, 151, 0.1);
}

.rgt-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.rgt-btn-submit {
    width: 100%;
    padding: 14px;
    background: #057997;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.rgt-btn-submit:hover {
    background: #046680;
    box-shadow: 0 4px 12px rgba(5, 121, 151, 0.3);
}

.rgt-login-link {
    text-align: center;
    margin-top: 25px;
    color: #666666;
    font-size: 14px;
}

.rgt-login-link a {
    color: #057997;
    font-weight: 600;
    text-decoration: none;
}

.rgt-login-link a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .rgt-row-2 {
        grid-template-columns: 1fr;
    }
    
    .rgt-form-container {
        padding: 25px 20px;
    }
    
    .rgt-login-section {
        padding: 30px 0;
    }
}
</style>

<!--section start-->
<section class="rgt-login-section section-big-py-space">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="rgt-form-container">
                    <h3 class="rgt-form-title">Crear cuenta</h3>
                    <p class="rgt-form-subtitle">Complete el formulario para registrarse</p>
                    
                    <form class="theme-form" autocomplete="off" id="frmRegister">
                        <div class="rgt-row-2">
                            <div class="rgt-form-group">
                                <label for="nombreRegistro" class="rgt-form-label">Nombre</label>
                                <input type="text" class="rgt-form-input" name="nombreRegistro" id="nombreRegistro" placeholder="Ingrese su nombre" required="">
                            </div>
                            <div class="rgt-form-group">
                                <label for="apellidoRegistro" class="rgt-form-label">Apellido</label>
                                <input type="text" class="rgt-form-input" name="apellidoRegistro" id="apellidoRegistro" placeholder="Ingrese su apellido" required="">
                            </div>
                        </div>

                        <div class="rgt-form-group">
                            <label for="correoRegistro" class="rgt-form-label">Correo electrónico</label>
                            <input type="email" class="rgt-form-input" name="correoRegistro" id="correoRegistro" placeholder="ejemplo@correo.com" required="">
                        </div>

                        <div class="rgt-row-2">
                            <div class="rgt-form-group">
                                <label for="telefonoRegistro" class="rgt-form-label">Teléfono</label>
                                <input type="text" class="rgt-form-input" name="telefonoRegistro" id="telefonoRegistro" placeholder="Número de teléfono" required="">
                            </div>
                            <div class="rgt-form-group">
                                <label for="documentoRegistro" class="rgt-form-label">Documento (CI/NIT)</label>
                                <input type="text" class="rgt-form-input" name="documentoRegistro" id="documentoRegistro" placeholder="CI o NIT" required="">
                            </div>
                        </div>

                        <div class="rgt-form-group">
                            <label for="direccionRegistro" class="rgt-form-label">Dirección</label>
                            <input type="text" class="rgt-form-input" name="direccionRegistro" id="direccionRegistro" placeholder="Dirección completa" required="">
                        </div>

                        <div class="rgt-form-group">
                            <label for="tipoClienteRegistro" class="rgt-form-label">Tipo de cliente</label>
                            <select class="rgt-form-select" name="tipoClienteRegistro" id="tipoClienteRegistro" required="">
                                <option value="">Seleccione tipo de cliente</option>
                                <option value="final">Cliente Final</option>
                                <option value="mayorista">Mayorista</option>
                            </select>
                        </div>

                        <div class="rgt-form-group">
                            <label for="claveRegistro" class="rgt-form-label">Contraseña</label>
                            <input type="password" class="rgt-form-input" id="claveRegistro" name="claveRegistro" placeholder="Cree una contraseña segura" required="">
                        </div>

                        <button type="submit" class="rgt-btn-submit">CREAR CUENTA</button>

                        <div class="rgt-login-link">
                            <p>¿Ya tienes una cuenta? <a href="javascript:;" onclick="openAccount()">Inicia sesión aquí</a></p>
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