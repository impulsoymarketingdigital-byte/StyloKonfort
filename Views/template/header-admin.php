<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/admin/images/favicon.ico" type="image/png" />
    <link href="<?php echo BASE_URL; ?>assets/admin/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="<?php echo BASE_URL; ?>assets/admin/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <!-- loader-->
    <link href="<?php echo BASE_URL; ?>assets/admin/css/pace.min.css" rel="stylesheet" />
    <script src="<?php echo BASE_URL; ?>assets/admin/js/pace.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="<?php echo BASE_URL; ?>assets/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/admin/css/bootstrap-extended.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo BASE_URL . 'assets/admin/plugins/select2/css/select2.min.css'; ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL . 'assets/admin/css/select2-bootstrap-5-theme.rtl.min.css'; ?>">

    <link href="<?php echo BASE_URL; ?>assets/admin/css/app.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/admin/css/icons.css" rel="stylesheet">
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/admin/css/dark-theme.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/admin/css/semi-dark.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/admin/css/header-colors.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL . 'assets/DataTables/datatables.min.css'; ?>">
    <link href="<?php echo BASE_URL; ?>assets/admin/css/dropzone.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="<?php echo BASE_URL . 'assets/admin/css/principal/checkbox.css'; ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL . 'assets/admin/css/jquery.datetimepicker.min.css'; ?>">

    <link rel="stylesheet" href="<?php echo BASE_URL . 'assets/admin/css/jquery-ui.min.css'; ?>">

    <title><?php echo TITLE . ' - ' . $data['title']; ?></title>
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!--sidebar wrapper -->
        <div class="sidebar-wrapper" data-simplebar="true">
            <div class="sidebar-header">
                <div>
                    <img src="<?php echo BASE_URL; ?>assets/admin/images/login.png" class="logo-icon" alt="logo icon">
                </div>
                <div>
                    <h4 class="logo-text"><?php echo TITLE; ?></h4>
                </div>
                <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
                </div>
            </div>
            <!--navigation-->
            <ul class="metismenu" id="menu">
                <li>
                    <a href="<?php echo BASE_URL . 'admin/home'; ?>">
                        <div class="parent-icon"><i class='fas fa-home'></i></div>
                        <div class="menu-title">Dashboard</div>
                    </a>
                </li>

                <?php if (verificar('configuracion') || verificar('usuarios') || verificar('roles') || verificar('almacenes') || verificar('sucursales') || verificar('sliders') || verificar('promociones')) { ?>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='fas fa-tools'></i></div>
                        <div class="menu-title">Administración</div>
                    </a>
                    <ul>
                        <?php if (verificar('configuracion')) { ?>
                        <li><a href="<?php echo BASE_URL . 'admin/empresa'; ?>"><i class="bx bx-right-arrow-alt"></i>Configuración</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('usuarios')) { ?>
                        <li><a href="<?php echo BASE_URL . 'usuarios'; ?>"><i class="bx bx-right-arrow-alt"></i>Usuarios</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('roles')) { ?>
                        <li><a href="<?php echo BASE_URL . 'roles'; ?>"><i class="bx bx-right-arrow-alt"></i>Roles y Permisos</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('almacenes')) { ?>
                        <li><a href="<?php echo BASE_URL . 'almacenes'; ?>"><i class="bx bx-right-arrow-alt"></i>Almacenes</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('sucursales')) { ?>
                        <li><a href="<?php echo BASE_URL . 'sucursales'; ?>"><i class="bx bx-right-arrow-alt"></i>Sucursales</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('sliders')) { ?>
                        <li><a href="<?php echo BASE_URL . 'sliders'; ?>"><i class="bx bx-right-arrow-alt"></i>Sliders</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('promociones')) { ?>
                        <li><a href="<?php echo BASE_URL . 'promociones'; ?>"><i class="bx bx-right-arrow-alt"></i>Promociones</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if (verificar('productos') || verificar('marcas') || verificar('categorias') || verificar('sizes') || verificar('colores')) { ?>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='fas fa-list'></i></div>
                        <div class="menu-title">Inventario</div>
                    </a>
                    <ul>
                        <?php if (verificar('productos')) { ?>
                        <li><a href="<?php echo BASE_URL . 'productos'; ?>"><i class="bx bx-right-arrow-alt"></i>Productos</a></li>
                        <?php } ?>

                         <?php if (verificar('stock')) { ?>
                        <li><a href="<?php echo BASE_URL . 'productos/stock'; ?>"><i class="bx bx-right-arrow-alt"></i>Stock</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('marcas')) { ?>
                        <li><a href="<?php echo BASE_URL . 'marcas'; ?>"><i class="bx bx-right-arrow-alt"></i>Marcas</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('categorias')) { ?>
                        <li><a href="<?php echo BASE_URL . 'categorias'; ?>"><i class="bx bx-right-arrow-alt"></i>Categorias</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('sizes')) { ?>
                        <li><a href="<?php echo BASE_URL . 'sizes'; ?>"><i class="bx bx-right-arrow-alt"></i>Tamaños</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('colores')) { ?>
                        <li><a href="<?php echo BASE_URL . 'colores'; ?>"><i class="bx bx-right-arrow-alt"></i>Colores</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if (verificar('clientes')) { ?>
                <li>
                    <a href="<?php echo BASE_URL . 'clientes/admin'; ?>">
                        <div class="parent-icon"><i class='fas fa-users'></i></div>
                        <div class="menu-title">Clientes</div>
                    </a>
                </li>
                <?php } ?>

                <?php if (verificar('pedidos')) { ?>
                <li>
                    <a href="<?php echo BASE_URL . 'pedidos'; ?>">
                        <div class="parent-icon"><i class='fas fa-receipt'></i></div>
                        <div class="menu-title">Pedidos</div>
                    </a>
                </li>
                <?php } ?>

                <!-- ✅ PROVEEDORES -->
                <?php if (verificar('proveedores')) { ?>
                <li>
                    <a href="<?php echo BASE_URL . 'proveedores'; ?>">
                        <div class="parent-icon"><i class='fas fa-truck-loading'></i></div>
                        <div class="menu-title">Proveedores</div>
                    </a>
                </li>
                <?php } ?>

                <!-- ✅ COMPRAS -->
                <?php if (verificar('compras') || verificar('listar_compras')) { ?>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="fas fa-shopping-basket"></i></div>
                        <div class="menu-title">Compras</div>
                    </a>
                    <ul>
                        <?php if (verificar('compras')) { ?>
                        <li><a href="<?php echo BASE_URL . 'compras'; ?>"><i class="bx bx-radio-circle"></i>Nueva Compra</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('listar_compras')) { ?>
                        <li><a href="<?php echo BASE_URL . 'compras/listar_compras'; ?>"><i class="bx bx-radio-circle"></i>Listar Compras</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <!-- ✅ VENTAS -->
                <?php if (verificar('ventas') || verificar('listar_ventas')) { ?>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="fas fa-cash-register"></i></div>
                        <div class="menu-title">Ventas</div>
                    </a>
                    <ul>
                        <?php if (verificar('ventas')) { ?>
                        <li><a href="<?php echo BASE_URL . 'ventas'; ?>"><i class="bx bx-radio-circle"></i>Nueva Venta</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('listar_ventas')) { ?>
                        <li><a href="<?php echo BASE_URL . 'ventas/listar_ventas'; ?>"><i class="bx bx-radio-circle"></i>Listar Ventas</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if (verificar('traspasos') || verificar('listar_traspasos')) { ?>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="fas fa-exchange-alt"></i></div>
                        <div class="menu-title">Traspasos</div>
                    </a>
                    <ul>
                        <?php if (verificar('traspasos')) { ?>
                        <li><a href="<?php echo BASE_URL . 'traspasos'; ?>"><i class="bx bx-radio-circle"></i>Nuevo Traspaso</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('listar_traspasos')) { ?>
                        <li><a href="<?php echo BASE_URL . 'traspasos/listar_traspasos'; ?>"><i class="bx bx-radio-circle"></i>Listar Traspasos</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

                <?php if (verificar('reporte_ventas') || verificar('reporte_compras')) { ?>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="fas fa-chart-bar"></i></div>
                        <div class="menu-title">Reportes</div>
                    </a>
                    <ul>
                        <?php if (verificar('reporte_ventas')) { ?>
                        <li><a href="<?php echo BASE_URL . 'reportes/reporte_ventas'; ?>"><i class="bx bx-radio-circle"></i>Reporte de Ventas</a></li>
                        <?php } ?>
                        
                        <?php if (verificar('reporte_compras')) { ?>
                        <li><a href="<?php echo BASE_URL . 'reportes/reporte_compras'; ?>"><i class="bx bx-radio-circle"></i>Reporte de Compras</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>

            </ul>
            <!--end navigation-->
        </div>
        <!--end sidebar wrapper -->
        <!--start header -->
        <header>
            <div class="topbar d-flex align-items-center">
                <nav class="navbar navbar-expand">
                    <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
                    </div>
                    <div class="search-bar flex-grow-1">

                    </div>
                    <div class="user-box dropdown">
                        <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if ($_SESSION['perfil_usuario'] == null) {
                                $perfil = BASE_URL . 'assets/images/logo.png';
                            } else {
                                $perfil = BASE_URL . $_SESSION['perfil_usuario'];
                            } ?>
                            <img src="<?php echo $perfil; ?>" class="user-img" alt="user avatar">
                            <div class="user-info ps-3">
                                <p class="user-name mb-0"><?php echo $_SESSION['nombre_usuario']; ?></p>
                                <p class="designattion mb-0"><?php echo $_SESSION['email']; ?></p>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL . 'usuarios/profile'; ?>"><i
                                        class="bx bx-user"></i><span>Profile</span></a>
                            </li>
                            <li>
                                <div class="dropdown-divider mb-0"></div>
                            </li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL . 'admin/salir'; ?>"><i
                                        class='bx bx-log-out-circle'></i><span>Logout</span></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
        <!--end header -->
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">