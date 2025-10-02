<?php
class SlidersModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function getSliders($estado)
    {
        $sql = "SELECT * FROM sliders WHERE estado = $estado";
        return $this->selectAll($sql);
    }
    
    public function verificarTitulo($titulo, $id)
    {
        $sql = "SELECT titulo FROM sliders WHERE titulo = '$titulo' AND id != $id";
        return $this->select($sql);
    }

    public function getSlider($idSli)
    {
        $sql = "SELECT * FROM sliders WHERE id = $idSli";
        return $this->select($sql);
    }

    public function modificar($titulo, $subtilo, $enlace, $imagen, $id)
    {
        $sql = "UPDATE sliders SET titulo=?, subtitulo=?, link=?, imagen=? WHERE id = ?";
        $array = array($titulo, $subtilo, $enlace, $imagen, $id);
        return $this->save($sql, $array);
    }
}
 
?>