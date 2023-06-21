<?php

include_once "Ventas.php";

if(isset($_GET["pedido"])){

    Venta::BorrarVenta(intval($_GET["pedido"]));
}


?>