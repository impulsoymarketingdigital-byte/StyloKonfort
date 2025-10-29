<?php
class Home extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    public function index()
    {
        $data['perfil'] = 'no';
        $data['title'] = 'Pagina Principal';
        $data['testimonios'] = $this->model->getTestimonial();

        // ============ NUEVOS PRODUCTOS ============
        $productos = $this->model->getNuevosProductos();
        for ($i = 0; $i < count($productos); $i++) {
            $calificacion = $this->model->getCalificacion('SUM', $productos[$i]['id']);
            $cantidad = $this->model->getCalificacion('COUNT', $productos[$i]['id']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $productos[$i]['calificacion'] = round($total);
            $productos[$i]['imagen'] = $this->model->getPrimeraImagen($productos[$i]['id']);
        }
        $data['nuevoProductos'] = $productos;

        // ============ PRODUCTOS DESTACADOS ============
        $destacados = $this->model->getProductosDestacados();
        for ($i = 0; $i < count($destacados); $i++) {
            // Ya traemos los datos del producto en el query
            $destacados[$i]['prod'] = [
                'id' => $destacados[$i]['id'],
                'nombre' => $destacados[$i]['nombre'],
                'slug' => $destacados[$i]['slug'],
                'precio_venta' => $destacados[$i]['precio_venta'],
                'imagen' => $this->model->getPrimeraImagen($destacados[$i]['id_producto'])
            ];
            
            // Calcular calificación
            $cantidad = $this->model->getCalificacion('COUNT', $destacados[$i]['id_producto']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $destacados[$i]['calificacion'] = round($destacados[$i]['cantidad'] / $totalCantidad);
        }
        $data['destacados'] = $destacados;

        // ============ PRODUCTOS MÁS VENDIDOS ============
        $especiales = $this->model->getProductosEspeciales();
        for ($i = 0; $i < count($especiales); $i++) {
            // Ya traemos los datos del producto en el query
            $especiales[$i]['prod'] = [
                'id' => $especiales[$i]['id'],
                'nombre' => $especiales[$i]['nombre'],
                'slug' => $especiales[$i]['slug'],
                'precio_venta' => $especiales[$i]['precio_venta'],
                'imagen' => $this->model->getPrimeraImagen($especiales[$i]['id_producto'])
            ];
            
            // Calcular calificación
            $calificacion = $this->model->getCalificacion('SUM', $especiales[$i]['id_producto']);
            $cantidad = $this->model->getCalificacion('COUNT', $especiales[$i]['id_producto']);
            $totalCantidad = ($cantidad['total'] == 0) ? 5 : $cantidad['total'];
            $total = ($calificacion['total'] != null) ? $calificacion['total'] / $totalCantidad : $totalCantidad;
            $especiales[$i]['calificacion'] = round($total);
        }
        $data['especiales'] = $especiales;

        $this->views->getView('home', "index", $data);
    }
}
?>