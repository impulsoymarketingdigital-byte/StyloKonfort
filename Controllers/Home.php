<?php
class Home extends Controller
{
    public function __construct() {
        parent::__construct();
        session_start();
    }
   public function index()
{
    $data['perfil'] = 'no';
    $data['title'] = 'Pagina Principal';
    $productos = $this->model->getNuevosProductos();
    $data['testimonios'] = $this->model->getTestimonial();

    for ($i = 0; $i < count($productos); $i++) {
        $calificacion = $this->model->getCalificacion('SUM', $productos[$i]['id']);
        $cantidad = $this->model->getCalificacion('COUNT', $productos[$i]['id']);
        $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
        $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
        $productos[$i]['calificacion'] = round($total);

        // ⭐ AGREGAR LA PRIMERA IMAGEN DE LA GALERÍA
        $primeraImagen = $this->model->getPrimeraImagen($productos[$i]['id']);
        $productos[$i]['imagen'] = $primeraImagen;
    }

    $data['nuevoProductos'] = $productos;

    // PRODUCTOS DESTACADOS
    $data['destacados'] = $this->model->getProductosDestacados();
    for ($i = 0; $i < count($data['destacados']); $i++) {
        $producto = $this->model->getProducto($data['destacados'][$i]['id_producto']);
        $data['destacados'][$i]['prod'] = $producto;

        $cantidad = $this->model->getCalificacion('COUNT', $data['destacados'][$i]['id_producto']);
        $total = $data['destacados'][$i]['cantidad'] / $cantidad['total'];
        $data['destacados'][$i]['calificacion'] = round($total);

        // ⭐ AGREGAR LA PRIMERA IMAGEN DE LA GALERÍA
        $primeraImagen = $this->model->getPrimeraImagen($data['destacados'][$i]['id_producto']);
        $data['destacados'][$i]['imagen'] = $primeraImagen;
    }

    $data['especiales'] = $this->model->getProductosEspeciales();
    for ($i = 0; $i < count($data['especiales']); $i++) {
        $producto = $this->model->getProducto($data['especiales'][$i]['id_producto']);
        $data['especiales'][$i]['prod'] = $producto;

        $calificacion = $this->model->getCalificacion('SUM', $data['especiales'][$i]['id_producto']);
        $cantidad = $this->model->getCalificacion('COUNT', $data['especiales'][$i]['id_producto']);
        $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
        $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
        $data['especiales'][$i]['calificacion'] = round($total);

        // ⭐ AGREGAR LA PRIMERA IMAGEN DE LA GALERÍA
        $primeraImagen = $this->model->getPrimeraImagen($data['especiales'][$i]['id_producto']);
        $data['especiales'][$i]['imagen'] = $primeraImagen;
    }

    $this->views->getView('home', "index", $data);
}

}