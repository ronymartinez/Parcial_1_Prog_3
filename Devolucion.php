<?php
include_once "Ventas.php";
include_once "Cupon.php";

class Devolucion
{
    public $id;
    public $pedido;
    public $causa;

    function __construct($pedido, $causa, $id)
    {
        $this->id = $id;
        $this->pedido = $pedido;
        $this->causa = $causa;
    }

    static function CargarDevoluciones()
    {
        $archivo = fopen("devoluciones.json", "a+");
        $tamañoArchivo = filesize("devoluciones.json");
        $listaDevoluciones = [];

        if ($tamañoArchivo != 0) {

            $retornoFRead = json_decode(fread($archivo, $tamañoArchivo));

            foreach ($retornoFRead as $value) {
                $pedido = $value->pedido;
                $causa = $value->causa;
                $id = $value->id;
                $nuevaDevolucion = new Devolucion($pedido, $causa, $id);
                array_push($listaDevoluciones, $nuevaDevolucion);
            }

            fclose($archivo);
            return $listaDevoluciones;
        }

        fclose($archivo);
        return $listaDevoluciones;
    }
    static function CrearID()
    {
        $nuevoID = 1001;
        $listaDevoluciones = Devolucion::CargarDevoluciones();

        if ($listaDevoluciones != null) {
            foreach ($listaDevoluciones as $value) {
                $nuevoID = $value->id + 1;
            }
        }
        return $nuevoID;
    }

    public static function GuardarDevolucion($devolucion)
    {
        $listaDevoluciones = Devolucion::CargarDevoluciones();

        $retorno = "No se ha realizado ningún cambio";

        if (Devolucion::ValidarDevolucion($devolucion)) {

            $devolucion->id= Devolucion::CrearID();
            
            $ruta = "C:\\xampp\\htdocs\\Parcial_1\\ImagenesDevoluciones\\" . $_FILES["imagen"]["name"];
            $cargaImagenOK = move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);

            if ($cargaImagenOK) {
                array_push($listaDevoluciones, $devolucion);
                $retorno = "\nSe ha ingresado una nueva devolución";
                
                //Guardar Cupón
                $cupon = new Cupon($devolucion->id, "", "", "");
                Cupon::GuardarCupon($cupon);

            } else {
                $retorno = "\nNo se pudo guardar la imagen\n";
            }
        }else{
            $retorno = "\nNo existe el número de pedido";
        }

        $devoluciones = fopen("devoluciones.json", "w+");
        $devolucionesJson = json_encode($listaDevoluciones);
        fwrite($devoluciones, $devolucionesJson);
        fclose($devoluciones);

        echo $retorno;
    }
    static function ValidarDevolucion($devolucion)
    {       
        $listaVentas =  Venta::CargarVentasActivas();

        foreach ($listaVentas as $value) {

            if ($value->pedido == $devolucion->pedido) {
                return true;
            }
        }
        return false;
    }



}
?>