<?php
include_once "Heladeria.php";

$sabor = $_POST["sabor"];
$tipo = $_POST["tipo"];

$heladeria = new Heladeria($sabor, "", $tipo, "", "", "");

//Heladeria::ValidarHeladeria($heladeria);

if(Heladeria::ValidarHeladeria($heladeria)){
    echo "\nExiste.";
}else{
    echo "\nNo existe el sabor $sabor y el tipo $tipo";
}

?>