<?php
class PromocionesModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPromociones()
    {
        $sql = "SELECT *, 
            CASE 
                WHEN CURDATE() < fecha_inicio THEN 'Próximamente'
                WHEN CURDATE() <= fecha_fin THEN 'Vigente'
                ELSE 'Vencida'
            END AS vigencia_estado
            FROM promociones";
        return $this->selectAll($sql);
    }

    public function registrar($titulo, $descripcion, $imagen, $link, $fecha_inicio, $fecha_fin)
    {
        $sql = "INSERT INTO promociones (titulo, descripcion, imagen, link, fecha_inicio, fecha_fin) VALUES (?,?,?,?,?,?)";
        $array = array($titulo, $descripcion, $imagen, $link, $fecha_inicio, $fecha_fin);
        return $this->insertar($sql, $array);
    }

    public function verificarPromocion($titulo)
    {
        $sql = "SELECT titulo FROM promociones WHERE titulo = '$titulo' AND estado = 1";
        return $this->select($sql);
    }

    public function eliminar($estado, $id)
    {
        $sql = "UPDATE promociones SET estado = ? WHERE id = ?";
        $array = array($estado, $id);
        return $this->save($sql, $array);
    }

    public function getPromocion($id)
    {
        $sql = "SELECT * FROM promociones WHERE id = $id";
        return $this->select($sql);
    }

    public function modificar($titulo, $descripcion, $imagen, $link, $fecha_inicio, $fecha_fin, $id)
    {
        $sql = "UPDATE promociones SET titulo=?, descripcion=?, imagen=?, link=?, fecha_inicio=?, fecha_fin=? WHERE id = ?";
        $array = array($titulo, $descripcion, $imagen, $link, $fecha_inicio, $fecha_fin, $id);
        return $this->save($sql, $array);
    }


}
?>