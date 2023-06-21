<?php
include_once "Ventas.php";

if (isset($_GET["fecha"])) {
    Venta::MostrarCantVentaPorFecha($_GET["fecha"]);
} else {
    Venta::MostrarCantVentaPorFecha("");
}

if (isset($_GET["usuario"])) {
    Venta::MostrarVentasPorUsuario($_GET["usuario"]);
}

if (isset($_GET["fechaUno"]) && isset($_GET["fechaDos"])) {
    Venta::MostrarVentasDosFechas($_GET["fechaUno"], $_GET["fechaDos"]);
}

if (isset($_GET["sabor"])) {
    Venta::MostrarVentasPorSabor($_GET["sabor"]);
}

if (isset($_GET["vaso"])) {
    Venta::MostrarVentasPorVaso($_GET["vaso"]);
} else {
    Venta::MostrarVentasPorVaso("cucurucho");
}


?>