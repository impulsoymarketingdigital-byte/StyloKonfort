<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/admin/images/favicon.ico" type="image/png" />
    <!--plugins-->
    <link href="<?php echo BASE_URL; ?>assets/admin/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="<?php echo BASE_URL; ?>assets/admin/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <!-- loader-->
    <link href="<?php echo BASE_URL; ?>assets/admin/css/pace.min.css" rel="stylesheet" />
    <script src="<?php echo BASE_URL; ?>assets/admin/js/pace.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="<?php echo BASE_URL; ?>assets/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/admin/css/bootstrap-extended.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/admin/css/app.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/admin/css/icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL . 'assets/admin/css/toastr.min.css'; ?>">
    <!-- LOGIN CSS PERSONALIZADO -->
    <link href="<?php echo BASE_URL; ?>assets/admin/css/login.css" rel="stylesheet">
    <title><?php echo $data['title']; ?></title>
</head>

<body class="lg-bg-login">
    <!--wrapper-->
    <div class="wrapper">
        <div class="lg-section-authentication d-flex align-items-center justify-content-center my-5 my-lg-0">
            <div class="container-fluid">
                <div class="row row-cols-1 row-cols-lg-3 row-cols-xl-4">
                    <div class="col mx-auto">
                        <!-- Logo -->
                        <div class="lg-logo-container lg-text-center">
                            <img src="<?php echo BASE_URL; ?>assets/admin/images/login.png" width="180" alt="LOGO" />
                        </div>

                        <!-- Card Principal -->
                        <div class="lg-card">
                            <div class="lg-card-body">
                                <div class="lg-form-container">
                                    <!-- Título -->
                                    <div class="lg-text-center">
                                        <h3 class="lg-title">Iniciar Sesión</h3>
                                    </div>

                                    <!-- Separador -->
                                    <div class="lg-separater lg-text-center">
                                        <span>ACCEDE CON TU CORREO</span>
                                        <hr />
                                    </div>

                                    <!-- Formulario -->
                                    <div class="form-body">
                                        <form class="lg-g-3" id="formulario">
                                            <!-- Campo Email -->
                                            <div class="lg-col-12">
                                                <label for="email" class="lg-form-label">Correo Electrónico</label>
                                                <input type="email" class="lg-form-control" id="email" name="email"
                                                    value="luissantander2002@gmail.com" placeholder="ejemplo@correo.com"
                                                    required>
                                            </div>

                                            <!-- Campo Password -->
                                            <div class="lg-col-12">
                                                <label for="clave" class="lg-form-label">Contraseña</label>
                                                <div class="lg-input-group" id="show_hide_password">
                                                    <input type="password" class="lg-form-control" id="clave"
                                                        name="clave" value="12345" placeholder="Ingresa tu contraseña"
                                                        required>
                                                    <a href="javascript:;" class="lg-input-group-text">
                                                        <i class='bx bx-hide'></i>
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Link Olvidaste Contraseña -->
                                            <div class="lg-col-12 lg-text-end">
                                                <a href="authentication-forgot-password.html" class="lg-forgot-link">
                                                    ¿Olvidaste tu contraseña?
                                                </a>
                                            </div>

                                            <!-- Botón Submit -->
                                            <div class="lg-col-12">
                                                <div class="lg-d-grid">
                                                    <button type="submit" class="lg-btn-primary">
                                                        <i class="bx bxs-lock-open"></i>Acceder
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
    </div>
    <!--end wrapper-->

    <!-- Bootstrap JS -->
    <script src="<?php echo BASE_URL; ?>assets/admin/js/bootstrap.bundle.min.js"></script>
    <!--plugins-->
    <script src="<?php echo BASE_URL; ?>assets/admin/js/jquery-3.6.0.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/admin/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/admin/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/admin/js/sweetalert2.all.min.js"></script>

    <!--Password show & hide js -->
    <script>
        $(document).ready(function () {
            $("#show_hide_password a").on('click', function (event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });
        });

        function alertas(mensaje, type, titulo = '') {
            Swal.fire({
                toast: true,
                position: 'bottom-right',
                icon: type,
                title: mensaje,
                showConfirmButton: false,
                timer: 2000,
                zIndex: 999999
            })
        }

        const base_url = '<?php echo BASE_URL; ?>';
    </script>

    <!--app JS-->
    <script src="<?php echo BASE_URL; ?>assets/admin/js/app.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/admin/js/modulos/login.js"></script>

</body>

</html>