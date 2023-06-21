<?php
include_once "Devolucion.php";

$causa = $_POST["causa"];
$pedido = intVal($_POST["pedido"]);

$devolucion = new Devolucion($pedido, $causa, "");
Devolucion::GuardarDevolucion($devolucion);
?>