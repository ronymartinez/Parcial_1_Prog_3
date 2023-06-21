<?php
include_once "Heladeria.php";

$sabor = $_POST["sabor"];
$precio = intVal($_POST["precio"]);
$tipo = $_POST["tipo"];
$vaso = $_POST["vaso"];
$stock = intVal($_POST["stock"]);



$heladeria = new Heladeria($sabor, $precio, $tipo, $vaso, $stock, "");
Heladeria::GuardarHeladeria($heladeria);


?>