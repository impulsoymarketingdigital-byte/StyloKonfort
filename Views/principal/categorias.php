<?php include_once 'Views/template/header-principal.php'; ?>

<div class="breadcrumb-main ">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="breadcrumb-contain">
                    <div>
                        <h2><?php echo $data['categoria']['categoria']; ?></h2>
                        <ul>
                            <li><a href="<?php echo BASE_URL; ?>">Inicio</a></li>
                            <li><i class="fa fa-angle-double-right"></i></li>
                            <li><a href="javascript:void(0)">Categorias</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="section-big-py-space ratio_asos b-g-light">
    <div class="collection-wrapper">
        <div class="custom-container">
            <div class="row">
                <div class="col-sm-3 collection-filter category-side category-page-side">
                    <!-- side-bar colleps block stat -->
                    <div class="collection-filter-block creative-card creative-inner category-side">
                        <!-- brand filter start -->
                        <div class="collection-mobile-back">
                            <span class="filter-back"><i class="fa fa-angle-left" aria-hidden="true"></i> Atras</span>
                        </div>
                        <!-- color filter start here -->
                        <div class="collection-collapse-block open">
                            <h3 class="collapse-block-title">colores</h3>
                            <div class="collection-collapse-block-content">
                                <div class="color-selector">
                                    <input type="hidden" id="colors">
                                    <ul>
                                        <?php foreach ($data['colores'] as $color) { ?>
                                            <li>
                                                <div style="background-color: <?php echo $color['color']; ?>;" data-id="<?php echo $color['id']; ?>"></div>
                                                <?php echo $color['nombre']; ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- size filter start here -->
                        <div class="collection-collapse-block open">
                            <h3 class="collapse-block-title">size</h3>
                            <div class="collection-collapse-block-content">
                                <div class="size-selector">
                                    <div class="collection-brand-filter">
                                        <?php foreach ($data['sizes'] as $size) { ?>
                                            <div class="custom-control custom-checkbox  form-check collection-filter-checkbox">
                                                <input type="checkbox" class="custom-control-input form-check-input sizes" id="size_<?php echo $size['id']; ?>" name="sizes[]" value="<?php echo $size['id']; ?>">
                                                <label class="custom-control-label form-check-label" for="small"><?php echo $size['nombre']; ?></label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- price filter start here -->
                        <div class="collection-collapse-block border-0 open">
                            <h3 class="collapse-block-title">price</h3>
                            <div class="collection-collapse-block-content">
                                <div class="filter-slide">
                                    <input class="js-range-slider" type="text" id="my_range" value="" data-type="double" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collection-content col">
                    <div class="page-main-content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="collection-product-wrapper">
                                    <div class="product-top-filter">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="filter-main-btn"><span class="filter-btn btn btn-theme"><i class="fa fa-filter" aria-hidden="true"></i> Filter</span></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="product-filter-content">
                                                    <div class="collection-view">
                                                        <ul>
                                                            <li><i class="fa fa-th grid-layout-view"></i></li>
                                                            <li><i class="fa fa-list-ul list-layout-view"></i></li>
                                                        </ul>
                                                    </div>
                                                    <div class="collection-grid-view">
                                                        <ul>
                                                            <li><img src="<?php echo BASE_URL; ?>assets/images/category/icon/2.png" alt="" class="product-2-layout-view"></li>
                                                            <li><img src="<?php echo BASE_URL; ?>assets/images/category/icon/3.png" alt="" class="product-3-layout-view"></li>
                                                            <li><img src="<?php echo BASE_URL; ?>assets/images/category/icon/4.png" alt="" class="product-4-layout-view"></li>
                                                            <li><img src="<?php echo BASE_URL; ?>assets/images/category/icon/6.png" alt="" class="product-6-layout-view"></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-wrapper-grid product">
                                        <div id="loading-indicator" style="display: none">
                                            <!-- Aquí puedes agregar un indicador de carga, como un spinner o un mensaje de carga -->
                                            Cargando...
                                        </div>
                                        <div class="row" id="contentProductos">
                                            <?php foreach ($data['productos'] as $producto) { ?>
                                                <div class="col-xl-3 col-md-4 col-6 col-grid-box">
                                                    <div class="product-box">
                                                        <div class="product-imgbox">
                                                            <div class="product-front">
                                                                <a href="#"> <img src="<?php echo BASE_URL . $producto['imagen']; ?>" class="img-fluid  " alt="product" loading="lazy"> </a>
                                                            </div>
                                                            <div class="product-back">
                                                                <a href="#"> <img src="<?php echo BASE_URL . $producto['imagen']; ?>" class="img-fluid  " alt="product" loading="lazy"> </a>
                                                            </div>
                                                        </div>
                                                        <div class="product-detail detail-center detail-inverse">
                                                            <div class="detail-title">
                                                                <div class="detail-left">
                                                                    <div class="rating-star">
                                                                        <?php
                                                                        $uno = ($producto['calificacion'] >= 1) ? 'text-warning' : 'text-muted';
                                                                        $dos = ($producto['calificacion'] >= 2) ? 'text-warning' : 'text-muted';
                                                                        $tres = ($producto['calificacion'] >= 3) ? 'text-warning' : 'text-muted';
                                                                        $cuatro = ($producto['calificacion'] >= 4) ? 'text-warning' : 'text-muted';
                                                                        $cinco = ($producto['calificacion'] == 5) ? 'text-warning' : 'text-muted';
                                                                        ?>
                                                                        <i class="<?php echo $uno; ?> fa fa-star"></i>
                                                                        <i class="<?php echo $dos; ?> fa fa-star"></i>
                                                                        <i class="<?php echo $tres; ?> fa fa-star"></i>
                                                                        <i class="<?php echo $cuatro; ?> fa fa-star"></i>
                                                                        <i class="<?php echo $cinco; ?> fa fa-star"></i>
                                                                    </div>
                                                                    <p><?php echo $producto['descripcion']; ?></p>
                                                                    <a href="#">
                                                                        <h6 class="price-title">
                                                                            <?php echo $producto['nombre']; ?>
                                                                        </h6>
                                                                    </a>
                                                                </div>
                                                                <div class="detail-right">
                                                                    <div class="price">
                                                                        <div class="price">PRECIO VARIADO</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="icon-detail">
                                                                <a href="javascript:void(0)" onclick="verDetalle(<?php echo $producto['id']; ?>)" class="tooltip-top" data-tippy-content="Quick View"> <i data-feather="eye"></i> </a>
                                                                <a href="<?php echo BASE_URL . 'principal/detail/' . $producto['slug']; ?>" class="tooltip-top" data-tippy-content="Ver Detalle"><i data-feather="refresh-cw"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="product-pagination">
                                        <div class="theme-paggination-block">
                                            <div class="row">
                                                <div class="col-xl-12 col-md-12 col-sm-12">
                                                    <nav aria-label="Page navigation">
                                                        <ul class="pagination">
                                                            <?php
                                                            $anterior = $data['pagina'] - 1;
                                                            $siguiente = $data['pagina'] + 1;
                                                            $url = BASE_URL . 'principal/categorias/' . $data['slug'] . '/';
                                                            if ($data['pagina'] > 1) {
                                                                echo '<li class="page-item"><a class="page-link" href="' . $url . $anterior . '" aria-label="Previous"><span aria-hidden="true"><i class="fa fa-chevron-left" aria-hidden="true"></i></span> <span class="sr-only">Previous</span></a></li>';
                                                            }
                                                            if ($data['total'] >= $siguiente) {
                                                                echo '<li class="page-item"><a class="page-link" href="' . $url . $siguiente . '" aria-label="Next"><span aria-hidden="true"><i class="fa fa-chevron-right" aria-hidden="true"></i></span> <span class="sr-only">Next</span></a></li>';
                                                            }
                                                            ?>
                                                        </ul>
                                                    </nav>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once 'Views/template/footer-principal.php'; ?>

<script src="<?php echo BASE_URL; ?>assets/js/ion.rangeSlider.js"></script>

<script>
    const sizes = document.querySelectorAll('.sizes');
    const my_range = document.querySelector('#my_range');
    const colors = document.querySelector('#colors');

    $('.color-selector ul li > div').on('click', function(e) {
        $(".color-selector ul li > div").removeClass("active");
        $(this).addClass("active");
        colors.value = $(this).attr('data-id');
        filtrarProductos(my_range.value);
    });

    $(".js-range-slider").ionRangeSlider({
       type: "double",
        grid: true,
        min: 0,
        max: <?php echo MAXPRECIO; ?>,
        from: 3,
        to: <?php echo MAXPRECIO; ?>,
        prefix: "$",
        onFinish: function (data) {
         const precios = data.from + ';' + data.to;
         filtrarProductos(precios);
       }
    });

    sizes.forEach(function(checkbox) {
        checkbox.addEventListener("click", function() {
            filtrarProductos(my_range.value);
        });
    });

    function filtrarProductos(precios) {
        const selectedCategories = [<?php echo $data['categoria']['id']; ?>];
        const selectedSizes = [];

        sizes.forEach(function(checkbox) {
            if (checkbox.checked) {
                selectedSizes.push(checkbox.value);
            }
        });

        document.getElementById('loading-indicator').style.display = 'block';
        const url = base_url + "principal/filtro";
        let data = new FormData();
        data.append('categorias', selectedCategories);
        data.append('sizes', selectedSizes);
        data.append('color', colors.value);
        data.append('precios', precios);
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(data);
        http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                const res = JSON.parse(this.responseText);
                html = '';
                res.productos.forEach(producto => {
                    let uno = (producto.calificacion >= 1) ? 'text-warning' : 'text-muted';
                    let dos = (producto.calificacion >= 2) ? 'text-warning' : 'text-muted';
                    let tres = (producto.calificacion >= 3) ? 'text-warning' : 'text-muted';
                    let cuatro = (producto.calificacion >= 4) ? 'text-warning' : 'text-muted';
                    let cinco = (producto.calificacion == 5) ? 'text-warning' : 'text-muted';
                    html += `<div class="col-xl-3 col-md-4 col-6 col-grid-box">
                        <div class="product-box">
                            <div class="product-imgbox">
                                <div class="product-front">
                                    <a href="#"> <img src="${base_url + producto.imagen}" class="img-fluid  " alt="product" loading="lazy"> </a>
                                </div>
                                <div class="product-back">
                                    <a href="#"> <img src="${base_url + producto.imagen}" class="img-fluid  " alt="product" loading="lazy"> </a>
                                </div>
                            </div>
                            <div class="product-detail detail-center detail-inverse">
                                <div class="detail-title">
                                    <div class="detail-left">
                                        <div class="rating-star">
                                            <i class="${ uno } fa fa-star"></i>
                                            <i class="${ dos } fa fa-star"></i>
                                            <i class="${ tres } fa fa-star"></i>
                                            <i class="${ cuatro } fa fa-star"></i>
                                            <i class="${ cinco } fa fa-star"></i>
                                        </div>
                                        <p>${ producto.descripcion} </p>
                                        <a href="#">
                                            <h6 class="price-title">
                                                ${ producto.nombre }
                                            </h6>
                                            <span class="badge" style="background-color: ${producto.color};">${producto.colornombre}</span>
                                            <span class="badge" style="background-color: ${producto.color};">${producto.size}</span>
                                        </a>
                                    </div>
                                    <div class="detail-right">
                                        <div class="check-price"> ${ res.moneda + producto.precio } </div>
                                        <div class="price">
                                            <div class="price"> ${ res.moneda + producto.precio } </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="icon-detail">
                                    <a href="javascript:void(0)" onclick="agregarCarrito(${ producto.id }, 1, ${ producto.id_talla }, ${ producto.id_color })" class="tooltip-top" data-tippy-content="Quick View"> <i class="fa fa-shopping-cart"></i> </a>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
                document.getElementById('loading-indicator').style.display = 'none';
                document.querySelector('#contentProductos').innerHTML = html;
            }
        }
    }
</script>

<!-- End Script -->
</body>

</html>