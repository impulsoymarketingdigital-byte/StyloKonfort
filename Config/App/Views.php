<?php
class Views{
    public $base;
    public function getView($ruta, $vista, $data="")
    {
        //CARGAR MODELO EN LA VISTA
        if (file_exists("Models/BaseModel.php")) {
            require_once "Models/BaseModel.php";
            $this->base = new BaseModel();
        }

        if ($ruta == "home") {
            $vista = "Views/".$vista.".php";
        }else{
            $vista = "Views/".$ruta."/".$vista.".php";
        }
        require $vista;
    }
}
?>