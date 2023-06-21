<?php
include "Ventas.php";



$fecha = date('d-m-Y');
$sabor = $_POST["sabor"];
$tipo = $_POST["tipo"];
$stock = intVal($_POST["stock"]);
$mail = $_POST["mail"];

if(isset($_POST["cupon"])){
    $id_cupon = $_POST["cupon"];
    $venta = new Venta("", $fecha, $sabor, $tipo, "", $stock, $mail, "", $id_cupon);
}else{
    $venta = new Venta("", $fecha, $sabor, $tipo, "", $stock, $mail, "", "");

}
Venta::GuardarVenta($venta);
//var_dump($venta);

?>