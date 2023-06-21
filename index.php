<?php


switch ($_SERVER['REQUEST_METHOD']) {
    case "POST":
        switch ($_POST["accion"]) {
            case "HeladeriaAlta":
                require_once "HeladeriaAlta.php";
                break;
            case "HeladoConsultar":
                require_once "HeladoConsultar.php";
                break;
            case "AltaVenta":
                require_once "AltaVenta.php";
                break;
            case "DevolverHelado":
                require_once "DevolverHelado.php";
                break;
        }
        break;

    case "GET":
        switch ($_GET["accion"]) {
            case "ConsultasVentas":
                require_once "ConsultasVentas.php";
                break;

            case "ConsultaDevoluciones":
                require_once "ConsultaDevoluciones.php";
                break;
        }
        break;

    case "PUT":
        require_once "ModificarVenta.php";
        break;

    case "DELETE":
        require_once "BorrarVenta.php";
        break;
}



?>