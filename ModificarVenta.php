<?php
include_once "Ventas.php";

//pedido, el email del usuario, el nombre, tipo, vaso y cantidad,
//$pedido = $_PUT["pedido"];
//$mail = $_PUT["mail"];

parse_str(file_get_contents("php://input"), $datos);

$pedido = $datos["pedido"];
$mail = $datos["mail"];
$tipo = $datos["tipo"];
$vaso = $datos["vaso"];
$cantidad = intVal($datos["cantidad"]);

$nuevaVenta = new Venta ($pedido, "", "", $tipo, $vaso, $cantidad, $mail,"", "");

Venta::MofidificarVenta($nuevaVenta);

?>