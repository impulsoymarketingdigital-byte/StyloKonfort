-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-10-2025 a las 15:46:47
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ecommerce`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacenes`
--

CREATE TABLE `almacenes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `almacenes`
--

INSERT INTO `almacenes` (`id`, `nombre`, `codigo`, `direccion`, `id_sucursal`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Almacén Central', 'A001', 'Zona Industrial #100', 1, 1, '2025-10-02 15:21:45', '2025-10-02 15:21:45'),
(2, 'Bodega Sucursal Central', 'A002', 'Av. Principal #123 - Subsuelo', 1, 1, '2025-10-02 15:21:45', '2025-10-02 15:21:45'),
(3, 'Bodega Norte', 'A003', 'Av. Norte #456 - Depósito', 2, 1, '2025-10-02 15:21:45', '2025-10-02 15:21:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id`, `cantidad`, `id_producto`, `id_cliente`) VALUES
(1, 4, 1, 1),
(2, 5, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `imagen` varchar(150) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `categoria`, `slug`, `imagen`, `estado`) VALUES
(1, 'ZAPATILLAS', 'zapatillas', 'assets/images/categorias/20231108200628.jpg', 1),
(2, 'ZAPATOS', 'zapatos', 'assets/images/categorias/20231108200849.jpg', 1),
(3, 'ABRIGOS', 'abrigos', 'assets/images/categorias/20231108202550.jpg', 1),
(4, 'POLOS', 'polos', 'assets/images/categorias/20231108203755.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `correo` varchar(80) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `clave` varchar(100) DEFAULT NULL,
  `perfil` varchar(100) NOT NULL DEFAULT 'default.png',
  `token` varchar(100) DEFAULT NULL,
  `verify` int(11) NOT NULL DEFAULT 0,
  `estado` int(11) NOT NULL DEFAULT 1,
  `accion` varchar(20) NOT NULL DEFAULT 'PRINCIPAL',
  `metodo` varchar(30) NOT NULL DEFAULT 'directo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `apellido`, `correo`, `telefono`, `direccion`, `clave`, `perfil`, `token`, `verify`, `estado`, `accion`, `metodo`) VALUES
(1, 'ANGEL', 'SIFUENTES', 'info@angelsifuentes.net', NULL, NULL, '$2y$10$viOqVFp82XOVmMqUM9EScu.5vPOGDWhKpidWdJtAaSPyd/4OauOiq', 'default.png', '0bf9395070f9f3077843dda3930ef0e2', 1, 1, 'PRINCIPAL', 'directo'),
(2, 'luis', 'santnader', 'luissantander2002@gmail.com', NULL, NULL, '$2y$10$EdtPiH2bBT7paVdqtWJnpO.ZZ1Dl8/GKjFfMLzPL2Ep60NiUbgAWO', 'default.png', NULL, 1, 1, 'PRINCIPAL', 'directo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colores`
--

CREATE TABLE `colores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `color` varchar(15) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `colores`
--

INSERT INTO `colores` (`id`, `nombre`, `color`, `estado`) VALUES
(1, 'ROJO', '#f90101', 1),
(2, 'AZUL', '#0522ff', 1),
(3, 'GRIS', '#5a5858', 1),
(4, 'CELESTE', '#0b9ef9', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `ruc` varchar(15) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `whatsapp` varchar(15) DEFAULT NULL,
  `facebook` varchar(200) DEFAULT NULL,
  `twitter` varchar(200) DEFAULT NULL,
  `instagram` varchar(200) DEFAULT NULL,
  `ubicacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `ruc`, `nombre`, `telefono`, `correo`, `direccion`, `mensaje`, `whatsapp`, `facebook`, `twitter`, `instagram`, `ubicacion`) VALUES
(1, '342234342', 'VIDA INFORMATICO', '900897537', 'info@angelsifuentes.net', 'AV. SIN NUMERO, EN TU CORAZON', 'GRACIAS POR LA PREFERENCIA', '51900897537', 'https://es-la.facebook.com/', 'https://twitter.com/', 'https://www.instagram.com/', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d499488.9826370486!2d-77.31746056605779!3d-12.025773243490905!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105c5f619ee3ec7%3A0x14206cb9cc452e4a!2sLima!5e0!3m2!1ses-419!2spe!4v1699454014287!5m2!1ses-419!2spe\" width=\"400\" height=\"300\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos`
--

CREATE TABLE `detalle_pedidos` (
  `id` int(11) NOT NULL,
  `producto` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `atributos` longtext DEFAULT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedidos`
--

INSERT INTO `detalle_pedidos` (`id`, `producto`, `precio`, `cantidad`, `atributos`, `id_pedido`, `id_producto`) VALUES
(1, 'TERCER PRODUCTO', 10.00, 3, '{\"id_size\":\"3\",\"id_color\":\"3\",\"size\":\"EXTRA LARGE\",\"color\":\"GRIS\",\"hexa\":\"#5a5858\"}', 1, 2),
(2, 'PRIMER PRODUCTO', 10.00, 1, '{\"id_size\":3,\"id_color\":4,\"size\":\"EXTRA LARGE\",\"color\":\"CELESTE\",\"hexa\":\"#0b9ef9\"}', 1, 1),
(3, 'Tenis Nike Air Max', 50.00, 26, '{\"id_size\":\"1\",\"id_color\":\"2\",\"size\":\"SMALL\",\"color\":\"AZUL\",\"hexa\":\"#0522ff\"}', 4, 6),
(4, 'Tenis Nike Air Max', 50.00, 26, '{\"id_size\":\"1\",\"id_color\":\"2\",\"size\":\"SMALL\",\"color\":\"AZUL\",\"hexa\":\"#0522ff\"}', 5, 6),
(5, 'Tenis Nike Air Max', 50.00, 26, '{\"id_size\":\"1\",\"id_color\":\"2\",\"size\":\"SMALL\",\"color\":\"AZUL\",\"hexa\":\"#0522ff\"}', 6, 6),
(6, 'Tenis Nike Air Max', 50.00, 1, '{\"id_size\":\"2\",\"id_color\":\"1\",\"size\":\"LARGE\",\"color\":\"ROJO\",\"hexa\":\"#f90101\"}', 6, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventarios`
--

CREATE TABLE `inventarios` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_talla` int(11) NOT NULL,
  `id_color` int(11) NOT NULL,
  `id_almacen` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT 0,
  `precio_compra` decimal(10,2) DEFAULT 0.00,
  `precio_venta` decimal(10,2) DEFAULT 0.00,
  `estado` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id` int(11) NOT NULL,
  `marca` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id`, `marca`, `slug`, `imagen`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'marca', 'marca', 'assets/images/marcas/default.png', 1, '2025-10-02 15:29:27', '2025-10-02 15:29:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_transaccion` varchar(80) NOT NULL,
  `metodo` varchar(50) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estado` varchar(30) NOT NULL,
  `fecha` datetime NOT NULL,
  `email` varchar(80) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `ciudad` varchar(50) DEFAULT NULL,
  `id_cliente` int(11) NOT NULL,
  `proceso` enum('1','2','3') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_transaccion`, `metodo`, `monto`, `estado`, `fecha`, `email`, `nombre`, `apellido`, `direccion`, `ciudad`, `id_cliente`, `proceso`) VALUES
(1, '1319431025', 'MERCADO PAGO', 40.00, 'approved', '2023-11-09 01:19:13', 'info@angelsifuentes.net', 'ANGEL', 'SIFUENTES', NULL, NULL, 1, '1'),
(2, 'LLEVAR-68e01df5b255b', 'LLEVAR', 0.00, 'PENDIENTE', '2025-10-03 21:03:17', 'luissantander2002@gmail.com', 'luis', 'santnader', NULL, NULL, 2, '1'),
(3, 'LLEVAR-68e01e94a6516', 'LLEVAR', 1350.00, 'PENDIENTE', '2025-10-03 21:05:56', 'luissantander2002@gmail.com', 'luis', 'santnader', NULL, NULL, 2, '1'),
(4, 'LLEVAR-68e01f4a4d923', 'LLEVAR', 1350.00, 'PENDIENTE', '2025-10-03 21:08:58', 'luissantander2002@gmail.com', 'luis', 'santnader', NULL, NULL, 2, '2'),
(5, 'LLEVAR-68e01f8d6b76f', 'LLEVAR', 1350.00, 'PENDIENTE', '2025-10-03 21:10:05', 'luissantander2002@gmail.com', 'luis', 'santnader', NULL, NULL, 2, '1'),
(6, 'LLEVAR-68e01ff75aa2a', 'LLEVAR', 1350.00, 'PENDIENTE', '2025-10-03 21:11:51', 'luissantander2002@gmail.com', 'luis', 'santnader', NULL, NULL, 2, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `id_marca` int(11) NOT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `precio_compra` decimal(10,2) DEFAULT 0.00,
  `precio_venta` decimal(10,2) DEFAULT 0.00,
  `estado` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `codigo`, `nombre`, `slug`, `descripcion`, `genero`, `id_categoria`, `id_marca`, `id_sucursal`, `precio_compra`, `precio_venta`, `estado`, `created_at`, `updated_at`) VALUES
(6, 'TNK001', 'Tenis Nike Air Max', 'tenis-nike-air-max', 'Tenis deportivos cómodos para correr', 'Masculino', 2, 1, 1, 25.00, 50.00, 1, '2025-10-03 16:01:00', '2025-10-03 18:21:13'),
(7, 'TNK002', 'Tenis Adidas Ultraboost', 'tenis-adidas-ultraboost', 'Tenis con amortiguación superior para running', 'Masculino', 1, 1, 1, 300.00, 450.00, 1, '2025-10-03 16:01:00', '2025-10-03 17:57:06'),
(8, 'TNK003', 'Tenis Puma RS-X', 'tenis-puma-rs-x', 'Tenis casual con diseño moderno y colorido', 'Masculino', 1, 1, 1, 200.00, 350.00, 1, '2025-10-03 16:01:00', '2025-10-03 16:02:34'),
(9, 'TNK004', 'Tenis Reebok Classic', 'tenis-reebok-classic', 'Tenis clásico para uso diario', 'Masculino', 1, 1, 1, 180.00, 300.00, 1, '2025-10-03 16:01:00', '2025-10-03 16:02:34'),
(10, 'TNK005', 'Tenis New Balance 574', 'tenis-new-balance-574', 'Tenis con estilo retro y buena amortiguación', 'Masculino', 1, 1, 1, 220.00, 370.00, 1, '2025-10-03 16:01:00', '2025-10-03 16:02:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sliders`
--

CREATE TABLE `sliders` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `subtitulo` text NOT NULL,
  `link` text DEFAULT NULL,
  `imagen` varchar(150) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sliders`
--

INSERT INTO `sliders` (`id`, `titulo`, `subtitulo`, `link`, `imagen`, `estado`) VALUES
(1, 'Zapatillas Fashión', 'Lorem ipsum dolor sit amet.', 'http://localhost/tienda-virtual/', 'assets/images/carrusel/1.jpg', 1),
(2, 'Cartera Fashión', 'Lorem ipsum dolor sit amet.', 'http://localhost/tienda-virtual/principal/detail/abrigo-dorado', 'assets/images/carrusel/2.jpg', 1),
(3, 'Tecnologias Modernas', 'Lorem ipsum dolor sit amet.', 'http://localhost/tienda-virtual/principal/detail/lentes-de-sol', 'assets/images/carrusel/3.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

CREATE TABLE `sucursales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`id`, `nombre`, `codigo`, `direccion`, `telefono`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Sucursal Central', 'S001', 'Av. Principal #123, Centro', '777-11111', 1, '2025-10-02 15:21:14', '2025-10-02 15:25:40'),
(2, 'Sucursal Norte', 'S002', 'Av. Norte #456', '777-22222', 1, '2025-10-02 15:21:14', '2025-10-02 15:21:14'),
(3, 'SUCURSAL', 'COD', 'Av Miraflores 123 Zona Saavedra', '76863368', 1, '2025-10-02 15:26:26', '2025-10-02 15:26:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tallas`
--

CREATE TABLE `tallas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `nombre_corto` varchar(50) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tallas`
--

INSERT INTO `tallas` (`id`, `nombre`, `nombre_corto`, `estado`) VALUES
(1, 'SMALL', 'S', 1),
(2, 'LARGE', 'L', 1),
(3, 'EXTRA LARGE', 'XL', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tallas_colores`
--

CREATE TABLE `tallas_colores` (
  `id` int(11) NOT NULL,
  `id_talla` int(11) NOT NULL,
  `id_color` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_almacen` int(11) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tallas_colores`
--

INSERT INTO `tallas_colores` (`id`, `id_talla`, `id_color`, `id_producto`, `id_almacen`, `stock`, `created_at`, `updated_at`) VALUES
(13, 1, 1, 6, 1, 10, '2025-10-03 16:06:57', '2025-10-03 16:06:57'),
(14, 1, 2, 6, 1, -52, '2025-10-03 16:06:57', '2025-10-03 19:11:51'),
(15, 1, 3, 6, 1, 6, '2025-10-03 16:06:57', '2025-10-03 16:06:57'),
(16, 1, 4, 6, 1, 5, '2025-10-03 16:06:57', '2025-10-03 16:06:57'),
(17, 2, 1, 6, 1, 6, '2025-10-03 16:06:57', '2025-10-03 19:11:51'),
(18, 2, 2, 6, 1, 10, '2025-10-03 16:06:57', '2025-10-03 16:06:57'),
(25, 1, 2, 6, 3, -52, '2025-10-03 16:20:17', '2025-10-03 19:11:51'),
(26, 2, 3, 6, 3, 0, '2025-10-03 16:20:36', '2025-10-03 16:20:36'),
(28, 2, 4, 6, 2, 0, '2025-10-03 16:22:48', '2025-10-03 16:22:48'),
(29, 1, 1, 7, 1, 0, '2025-10-03 17:29:34', '2025-10-03 17:29:34'),
(30, 1, 3, 6, 2, 0, '2025-10-03 17:42:26', '2025-10-03 17:42:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `testimonial`
--

CREATE TABLE `testimonial` (
  `id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `testimonial`
--

INSERT INTO `testimonial` (`id`, `mensaje`, `fecha`, `id_cliente`) VALUES
(1, '<p>Buena calidad</p>', '2023-11-09 00:21:33', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `perfil` varchar(50) DEFAULT NULL,
  `id_sucursal` int(11) DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombres`, `apellidos`, `correo`, `clave`, `perfil`, `id_sucursal`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'JUAN ANGEL', 'SIFUENTES', 'luissantander2002@gmail.com', '$2y$10$rdZI4KwCTlG0ERv9TTd0BuJzw3kB74H5NWBisLS4nV.3martitd/6', 'assets/images/perfil/20230326174601.jpg', 1, 1, '2025-10-02 15:23:03', '2025-10-02 15:23:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `productos` longtext NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `almacenes`
--
ALTER TABLE `almacenes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `colores`
--
ALTER TABLE `colores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indices de la tabla `inventarios`
--
ALTER TABLE `inventarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_talla` (`id_talla`),
  ADD KEY `id_color` (`id_color`),
  ADD KEY `id_almacen` (`id_almacen`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_marca` (`id_marca`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indices de la tabla `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `tallas`
--
ALTER TABLE `tallas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tallas_colores`
--
ALTER TABLE `tallas_colores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_talla` (`id_talla`),
  ADD KEY `id_color` (`id_color`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_almacen` (`id_almacen`);

--
-- Indices de la tabla `testimonial`
--
ALTER TABLE `testimonial`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuario_sucursal` (`id_sucursal`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `almacenes`
--
ALTER TABLE `almacenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `colores`
--
ALTER TABLE `colores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `inventarios`
--
ALTER TABLE `inventarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tallas`
--
ALTER TABLE `tallas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tallas_colores`
--
ALTER TABLE `tallas_colores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `testimonial`
--
ALTER TABLE `testimonial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `almacenes`
--
ALTER TABLE `almacenes`
  ADD CONSTRAINT `almacenes_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD CONSTRAINT `detalle_pedidos_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`);

--
-- Filtros para la tabla `inventarios`
--
ALTER TABLE `inventarios`
  ADD CONSTRAINT `inventarios_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventarios_ibfk_2` FOREIGN KEY (`id_talla`) REFERENCES `tallas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventarios_ibfk_3` FOREIGN KEY (`id_color`) REFERENCES `colores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventarios_ibfk_4` FOREIGN KEY (`id_almacen`) REFERENCES `almacenes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_ibfk_3` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `tallas_colores`
--
ALTER TABLE `tallas_colores`
  ADD CONSTRAINT `tallas_colores_ibfk_1` FOREIGN KEY (`id_talla`) REFERENCES `tallas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tallas_colores_ibfk_2` FOREIGN KEY (`id_color`) REFERENCES `colores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tallas_colores_ibfk_3` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tallas_colores_ibfk_4` FOREIGN KEY (`id_almacen`) REFERENCES `almacenes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
