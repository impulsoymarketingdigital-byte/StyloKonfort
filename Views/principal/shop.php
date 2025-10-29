<?php include_once 'Views/template/header-principal.php'; ?>

<!-- Overlay -->
<div class="filter-overlay" id="filterOverlay"></div>

<!-- Sidebar de filtros -->
<div class="filter-sidebar" id="filterSidebar">
    <div class="filter-header">
        <h3><i class="fa fa-filter"></i> Filtros</h3>
        <button class="filter-close" id="closeFilter">&times;</button>
    </div>

    <div class="filter-body">
        <!-- Categorías -->
        <div class="collection-collapse-block open">
            <h3 class="collapse-block-title mt-0">Categorías</h3>
            <div class="collection-collapse-block-content">
                <div class="collection-brand-filter">
                    <?php foreach ($this->base->getCategorias() as $categoria) { ?>
                        <div class="custom-control custom-checkbox form-check collection-filter-checkbox">
                            <input type="checkbox" class="custom-control-input form-check-input categorias"
                                id="cat_<?php echo $categoria['id']; ?>" name="categorias[]"
                                value="<?php echo $categoria['id']; ?>">
                            <label class="custom-control-label form-check-label"
                                for="cat_<?php echo $categoria['id']; ?>"><?php echo $categoria['categoria']; ?></label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>


        <!-- GÉNERO -->
        <div class="collection-collapse-block open">
            <h3 class="collapse-block-title">Género</h3>
            <div class="collection-collapse-block-content">
                <div class="collection-brand-filter">
                    <div class="custom-control custom-checkbox form-check collection-filter-checkbox">
                        <input type="checkbox" class="custom-control-input form-check-input generos"
                            id="genero_masculino" name="generos[]" value="Masculino">
                        <label class="custom-control-label form-check-label" for="genero_masculino">
                            <i class="fa fa-mars"></i> Masculino
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox form-check collection-filter-checkbox">
                        <input type="checkbox" class="custom-control-input form-check-input generos"
                            id="genero_femenino" name="generos[]" value="Femenino">
                        <label class="custom-control-label form-check-label" for="genero_femenino">
                            <i class="fa fa-venus"></i> Femenino
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marcas con imágenes -->
        <div class="collection-collapse-block open">
            <h3 class="collapse-block-title">Marcas</h3>
            <div class="collection-collapse-block-content">
                <div class="shop-marcas-grid">
                    <?php foreach ($data['marcas'] as $marca) { ?>
                        <div class="shop-marca-item">
                            <input type="checkbox" class="shop-marca-checkbox marcas" id="marca_<?php echo $marca['id']; ?>"
                                name="marcas[]" value="<?php echo $marca['id']; ?>">
                            <label class="shop-marca-label" for="marca_<?php echo $marca['id']; ?>">
                                <div class="shop-marca-image-container">
                                    <?php
                                    $imagenMarca = (!empty($marca['imagen'])) ? BASE_URL . $marca['imagen'] : BASE_URL . 'assets/images/productos/product.png';
                                    ?>
                                    <img src="<?php echo $imagenMarca; ?>" alt="<?php echo $marca['marca']; ?>"
                                        class="shop-marca-image"
                                        onerror="this.src='<?php echo BASE_URL; ?>assets/images/productos/product.png'">
                                    <div class="shop-marca-checkmark">
                                        <i class="fa fa-check"></i>
                                    </div>
                                </div>
                                <span class="shop-marca-name"><?php echo $marca['marca']; ?></span>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Colores -->
        <div class="collection-collapse-block open">
            <h3 class="collapse-block-title">Colores</h3>
            <div class="collection-collapse-block-content">
                <div class="collection-brand-filter">
                    <?php foreach ($data['colores'] as $color) { ?>
                        <div
                            class="custom-control custom-checkbox form-check collection-filter-checkbox d-flex align-items-center">
                            <input type="checkbox" class="custom-control-input form-check-input colores"
                                id="color_<?php echo $color['id']; ?>" name="colores[]" value="<?php echo $color['id']; ?>">
                            <label class="custom-control-label form-check-label d-flex align-items-center"
                                for="color_<?php echo $color['id']; ?>">
                                <?php if (!empty($color['color_secundario'])): ?>
                                    <span class="color-circle"
                                        style="background: linear-gradient(90deg, <?php echo $color['color']; ?> 50%, <?php echo $color['color_secundario']; ?> 50%);"></span>
                                <?php else: ?>
                                    <span class="color-circle" style="background-color: <?php echo $color['color']; ?>;"></span>
                                <?php endif; ?>
                                <?php echo $color['nombre']; ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Tallas -->
        <div class="collection-collapse-block open">
            <h3 class="collapse-block-title">Tallas</h3>
            <div class="collection-collapse-block-content">
                <div class="size-selector">
                    <div class="collection-brand-filter">
                        <?php foreach ($data['sizes'] as $size) { ?>
                            <div class="custom-control custom-checkbox form-check collection-filter-checkbox">
                                <input type="checkbox" class="custom-control-input form-check-input sizes"
                                    id="size_<?php echo $size['id']; ?>" name="sizes[]" value="<?php echo $size['id']; ?>">
                                <label class="custom-control-label form-check-label"
                                    for="size_<?php echo $size['id']; ?>"><?php echo $size['nombre']; ?></label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Precio -->
        <div class="collection-collapse-block border-0 open">
            <h3 class="collapse-block-title">Precio</h3>
            <div class="collection-collapse-block-content">
                <div class="filter-slide">
                    <input class="js-range-slider" type="text" id="my_range" value="" data-type="double" />
                </div>
            </div>
        </div>
    </div>

    <div class="filter-footer">
        <button class="btn-clear-filter" onclick="cancelarFiltros()">CANCELAR</button>
        <button class="btn-apply-filter" onclick="aplicarFiltros()">APLICAR</button>
    </div>
</div>

<!-- section start -->
<section class="section-big-py-space ratio_asos b-g-light">
    <div class="collection-wrapper">
        <div class="custom-container">
            <div class="row">
                <div class="collection-content col-12">
                    <div class="page-main-content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="collection-product-wrapper">
                                    <div class="product-top-filter">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="filter-main-btn">
                                                    <button class="btn btn-theme" id="openFilterBtn">
                                                        <i class="fa fa-filter" aria-hidden="true"></i> Filtrar
                                                    </button>
                                                </div>
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
                                                            <li><img src="<?php echo BASE_URL; ?>assets/images/category/icon/2.png"
                                                                    alt="" class="product-2-layout-view"></li>
                                                            <li><img src="<?php echo BASE_URL; ?>assets/images/category/icon/3.png"
                                                                    alt="" class="product-3-layout-view"></li>
                                                            <li><img src="<?php echo BASE_URL; ?>assets/images/category/icon/4.png"
                                                                    alt="" class="product-4-layout-view"></li>
                                                            <li><img src="<?php echo BASE_URL; ?>assets/images/category/icon/6.png"
                                                                    alt="" class="product-6-layout-view"></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-wrapper-grid product">
                                        <div id="loading-indicator" style="display: none">
                                            Cargando...
                                        </div>
                                        <div class="row" id="contentProductos">
                                            <?php foreach ($data['productos'] as $producto) {
                                                $imagenProducto = (!empty($producto['imagen'])) ? BASE_URL . $producto['imagen'] : BASE_URL . 'assets/images/productos/product.png';
                                                ?>
                                                <div class="col-xl col-lg-3 col-md-4 col-6 col-grid-box">
                                                    <div class="product-box shop-product-box">
                                                        <div class="product-imgbox shop-product-imgbox">
                                                            <div class="product-front">
                                                                <a
                                                                    href="<?php echo BASE_URL . 'principal/detail/' . $producto['slug']; ?>">
                                                                    <img src="<?php echo $imagenProducto; ?>"
                                                                        class="img-fluid" alt="product" loading="lazy"
                                                                        onerror="this.src='<?php echo BASE_URL; ?>assets/images/productos/product.png'">
                                                                </a>
                                                            </div>
                                                            <div class="product-back">
                                                                <a
                                                                    href="<?php echo BASE_URL . 'principal/detail/' . $producto['slug']; ?>">
                                                                    <img src="<?php echo $imagenProducto; ?>"
                                                                        class="img-fluid" alt="product" loading="lazy"
                                                                        onerror="this.src='<?php echo BASE_URL; ?>assets/images/productos/product.png'">
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="product-detail detail-center detail-inverse shop-product-detail">
                                                            <div class="detail-title shop-detail-title">
                                                                <div class="detail-left shop-detail-left">
                                                                    <div class="rating-star shop-rating-star">
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
                                                                    <a
                                                                        href="<?php echo BASE_URL . 'principal/detail/' . $producto['slug']; ?>">
                                                                        <h6 class="price-title shop-price-title">
                                                                            <?php echo $producto['nombre']; ?>
                                                                        </h6>
                                                                    </a>
                                                                </div>
                                                                <div class="detail-right shop-detail-right">
                                                                    <div class="price shop-price">
                                                                        <?php
                                                                        if ($data['tipo_cliente'] == 'mayorista') {
                                                                            echo '<div class="price">' . MONEDA . ' ' . $producto['precio_mayorista'] . '</div>';
                                                                        } else {
                                                                            echo '<div class="price">' . MONEDA . ' ' . $producto['precio_venta'] . '</div>';
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="icon-detail shop-icon-detail">
                                                                <a href="javascript:void(0)"
                                                                    onclick="verDetalle(<?php echo $producto['id']; ?>)"
                                                                    class="shop-icon-btn shop-icon-btn-view tooltip-top"
                                                                    data-tippy-content="Ver opciones">
                                                                    <i data-feather="eye"></i>
                                                                </a>
                                                                <a href="<?php echo BASE_URL . 'principal/detail/' . $producto['slug']; ?>"
                                                                    class="shop-icon-btn shop-icon-btn-detail tooltip-top"
                                                                    data-tippy-content="Ver página completa">
                                                                    <i data-feather="external-link"></i>
                                                                </a>
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
                                                        <ul class="pagination" id="paginacion">
                                                            <?php
                                                            $anterior = $data['pagina'] - 1;
                                                            $siguiente = $data['pagina'] + 1;
                                                            $url = BASE_URL . 'principal/shop/';
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
<!-- section End -->

<?php include_once 'Views/template/footer-principal.php'; ?>

<script src="<?php echo BASE_URL; ?>assets/js/ion.rangeSlider.js"></script>

<script>
    let currentPage = 1;
    const productsPerPage = <?php echo PORPAGINA; ?>;
    const categorias = document.querySelectorAll('.categorias');
    const marcasCheckbox = document.querySelectorAll('.marcas');
    const coloresCheckbox = document.querySelectorAll('.colores');
    const sizes = document.querySelectorAll('.sizes');
    const generosCheckbox = document.querySelectorAll('.generos');
    const my_range = document.querySelector('#my_range');
    const defaultImage = base_url + 'assets/images/productos/product.png';

    const filterOverlay = document.getElementById('filterOverlay');
    const filterSidebar = document.getElementById('filterSidebar');
    const openFilterBtn = document.getElementById('openFilterBtn');
    const closeFilter = document.getElementById('closeFilter');

    openFilterBtn.addEventListener('click', function () {
        filterOverlay.classList.add('active');
        filterSidebar.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    closeFilter.addEventListener('click', function () {
        filterOverlay.classList.remove('active');
        filterSidebar.classList.remove('active');
        document.body.style.overflow = '';
    });

    filterOverlay.addEventListener('click', function () {
        filterOverlay.classList.remove('active');
        filterSidebar.classList.remove('active');
        document.body.style.overflow = '';
    });

    $(".js-range-slider").ionRangeSlider({
        type: "double",
        grid: true,
        min: 0,
        max: <?php echo MAXPRECIO; ?>,
        from: 0,
        to: <?php echo MAXPRECIO; ?>,
        prefix: "Bs."
    });

    function cancelarFiltros() {
        categorias.forEach(function (checkbox) {
            checkbox.checked = false;
        });

        marcasCheckbox.forEach(function (checkbox) {
            checkbox.checked = false;
        });

        coloresCheckbox.forEach(function (checkbox) {
            checkbox.checked = false;
        });

        sizes.forEach(function (checkbox) {
            checkbox.checked = false;
        });

        const generosCheckbox = document.querySelectorAll('.generos');
        generosCheckbox.forEach(function (checkbox) {
            checkbox.checked = false;
        });

        const rangeSlider = $("#my_range").data("ionRangeSlider");
        rangeSlider.update({
            from: 0,
            to: <?php echo MAXPRECIO; ?>
        });

        filterOverlay.classList.remove('active');
        filterSidebar.classList.remove('active');
        document.body.style.overflow = '';

        location.reload();
    }

    function aplicarFiltros() {
        currentPage = 1;
        const rangeSlider = $("#my_range").data("ionRangeSlider");
        const precios = rangeSlider.result.from + ';' + rangeSlider.result.to;
        filtrarProductos(precios);

        filterOverlay.classList.remove('active');
        filterSidebar.classList.remove('active');
        document.body.style.overflow = '';
    }

    function filtrarProductos(precios) {
        const selectedCategories = [];
        const selectedMarcas = [];
        const selectedColors = [];
        const selectedSizes = [];
        const selectedGeneros = [];

        categorias.forEach(function (checkbox) {
            if (checkbox.checked) {
                selectedCategories.push(checkbox.value);
            }
        });

        marcasCheckbox.forEach(function (checkbox) {
            if (checkbox.checked) {
                selectedMarcas.push(checkbox.value);
            }
        });

        coloresCheckbox.forEach(function (checkbox) {
            if (checkbox.checked) {
                selectedColors.push(checkbox.value);
            }
        });

        sizes.forEach(function (checkbox) {
            if (checkbox.checked) {
                selectedSizes.push(checkbox.value);
            }
        });

        const generosCheckbox = document.querySelectorAll('.generos');
        generosCheckbox.forEach(function (checkbox) {
            if (checkbox.checked) {
                selectedGeneros.push(checkbox.value);
            }
        });


        document.getElementById('loading-indicator').style.display = 'block';
        const url = base_url + "principal/filtro";
        let data = new FormData();
        data.append('categorias', selectedCategories.join(','));
        data.append('marcas', selectedMarcas.join(','));
        data.append('colores', selectedColors.join(','));
        data.append('sizes', selectedSizes.join(','));
        data.append('generos', selectedGeneros.join(','));
        data.append('precios', precios);
        data.append('page', currentPage);

        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(data);
        http.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                const res = JSON.parse(this.responseText);
                let html = '';
                const products = res.productos;

                for (let i = 0; i < products.length; i++) {
                    const producto = products[i];
                    let uno = (producto.calificacion >= 1) ? 'text-warning' : 'text-muted';
                    let dos = (producto.calificacion >= 2) ? 'text-warning' : 'text-muted';
                    let tres = (producto.calificacion >= 3) ? 'text-warning' : 'text-muted';
                    let cuatro = (producto.calificacion >= 4) ? 'text-warning' : 'text-muted';
                    let cinco = (producto.calificacion == 5) ? 'text-warning' : 'text-muted';

                    const imagenProducto = (producto.imagen && producto.imagen.trim() !== '') ? base_url + producto.imagen : defaultImage;

                    html += `<div class="col-xl col-lg-3 col-md-4 col-6 col-grid-box">
                <div class="product-box shop-product-box">
                    <div class="product-imgbox shop-product-imgbox">
                        <div class="product-front">
                            <a href="${base_url}principal/detail/${producto.slug}"> 
                                <img src="${imagenProducto}" 
                                     class="img-fluid" 
                                     alt="product" 
                                     loading="lazy" 
                                     onerror="this.onerror=null; this.src='${defaultImage}'"> 
                            </a>
                        </div>
                        <div class="product-back">
                            <a href="${base_url}principal/detail/${producto.slug}"> 
                                <img src="${imagenProducto}" 
                                     class="img-fluid" 
                                     alt="product" 
                                     loading="lazy" 
                                     onerror="this.onerror=null; this.src='${defaultImage}'"> 
                            </a>
                        </div>
                    </div>
                    <div class="product-detail detail-center detail-inverse shop-product-detail">
                        <div class="detail-title shop-detail-title">
                            <div class="detail-left shop-detail-left">
                                <div class="rating-star shop-rating-star">
                                    <i class="${uno} fa fa-star"></i>
                                    <i class="${dos} fa fa-star"></i>
                                    <i class="${tres} fa fa-star"></i>
                                    <i class="${cuatro} fa fa-star"></i>
                                    <i class="${cinco} fa fa-star"></i>
                                </div>
                                <p>${producto.descripcion}</p>
                                <a href="${base_url}principal/detail/${producto.slug}">
                                    <h6 class="price-title shop-price-title">${producto.nombre}</h6>
                                </a>
                            </div>
                            <div class="detail-right shop-detail-right">
    <div class="price shop-price">
        <div class="price">${res.moneda + (res.tipo_cliente == 'mayorista' ? producto.precio_mayorista : producto.precio_venta)}</div>
    </div>
</div>
                        </div>
                        <div class="icon-detail shop-icon-detail">
                            <a href="javascript:void(0)" 
                                onclick="verDetalle(${producto.id})" 
                                class="shop-icon-btn shop-icon-btn-view tooltip-top" 
                                data-tippy-content="Ver opciones">
                                <i data-feather="eye"></i>
                            </a>
                            <a href="${base_url}principal/detail/${producto.slug}" 
                                class="shop-icon-btn shop-icon-btn-detail tooltip-top" 
                                data-tippy-content="Ver página completa">
                                <i data-feather="external-link"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>`;
                }

                document.getElementById('loading-indicator').style.display = 'none';
                document.querySelector('#contentProductos').innerHTML = html;
                document.querySelector('#paginacion').innerHTML = '';

                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        }
    }
</script>

</body>

</html>