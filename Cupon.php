<?php
include_once "Devolucion.php";

class Cupon
{
    public $id;
    public $id_devolucion;
    public $porcentaje;
    public $estado;

    function __construct($id_devolucion, $porcentaje, $estado, $id)
    {
        $this->id = $id;
        $this->id_devolucion = $id_devolucion;
        $this->porcentaje = $porcentaje;
        $this->estado = $estado;
    }

    static function CargarCupones()
    {
        $archivo = fopen("cupones.json", "a+");
        $tamañoArchivo = filesize("cupones.json");
        $listaCupones = [];

        if ($tamañoArchivo != 0) {

            $retornoFRead = json_decode(fread($archivo, $tamañoArchivo));

            foreach ($retornoFRead as $value) {
                $id_devolucion = $value->id_devolucion;
                $porcentaje = $value->porcentaje;
                $estado = $value->estado;
                $id = $value->id;

                $nuevoCupon = new Cupon($id_devolucion, $porcentaje, $estado, $id);

                array_push($listaCupones, $nuevoCupon);
            }

            fclose($archivo);
            return $listaCupones;
        }

        fclose($archivo);
        return $listaCupones;
    }
    static function CrearID()
    {
        $nuevoID = 1001;
        $listaCupones = Cupon::CargarCupones();

        if ($listaCupones != null) {
            foreach ($listaCupones as $value) {
                $nuevoID = $value->id + 1;
            }
        }
        return $nuevoID;
    }

    public static function GuardarCupon($cupon)
    {
        $listaCupones = Cupon::CargarCupones();

        $cupon->id = Cupon::CrearID();
        $cupon->estado = "no usado";
        $cupon->porcentaje = "10 %";

        array_push($listaCupones, $cupon);
        $cupones = fopen("cupones.json", "w+");
        $cuponesJson = json_encode($listaCupones);
        if (fwrite($cupones, $cuponesJson)) {
            fclose($cupones);
            echo "\nSe ha guardado un nuevo cupón";
        } else {
            echo "\nNo se pudo guardar el cupón";
        }
    }
    public static function ObtenerPorcentaje($id_cupon)
    {
        $listaCupones = Cupon::CargarCupones();
        $porcentaje = 0;

        foreach ($listaCupones as $cupon) {
            if ($cupon->id == $id_cupon) {
                $porcentajeArray = explode(" ", $cupon->porcentaje);

                $porcentaje = intVal($porcentajeArray[0]);
                break;
            }
        }
        return $porcentaje;

    }

    public static function ValidarCupon($id_cupon)
    {
        $listaCupones = Cupon::CargarCupones();

        $retorno = false;

        foreach ($listaCupones as $cupon ) {

                if ($cupon->id == $id_cupon && $cupon->estado == "no usado") {                 
                    $retorno = true;
                }
          
        }
     

        return $retorno;
    }
    public static function ActualizarCupon($id_cupon)
    {
        $listaCupones = Cupon::CargarCupones();

        foreach ($listaCupones as $cupon) {
            if ($cupon->id == $id_cupon) {
                $cupon->estado = "usado";
                break;
            }
        }
        $cupones = fopen("cupones.json", "w+");
        $cuponesJson = json_encode($listaCupones);

        if (fwrite($cupones, $cuponesJson)) {
            fclose($cupones);
            echo "-Se ha actualizado la lista de cupones\n";
        } else {
            echo "No se pudo actualizar la lista de cupones\n";
        }
    }

    static function ListarDevolucionesconCupones()
    {

        $listaCupones = Cupon::CargarCupones();

        echo "-LISTA DE DEVOLUCIONES CON CUPÓN\n";
        foreach ($listaCupones as $cupon) {
            echo "|   ID cupón: " . $cupon->id . "   |   ID Devolución: " . $cupon->id_devolucion . "   |  Porcentaje : " . $cupon->porcentaje . "   |   Estado: " . $cupon->estado . "\n";
        }
    }

    static function ListarCuponesYEstado()
    {

        $listaCupones = Cupon::CargarCupones();

        echo "\n-LISTA DE CUPONES Y SU ESTADO\n";

        foreach ($listaCupones as $cupon) {
            echo "|   ID cupón: " . $cupon->id . "   |   Estado: " . $cupon->estado . "\n";
        }
    }
    static function ListarCuponesUsados()
    {

        $listaCupones = Cupon::CargarCupones();

        echo "\n-LISTA DE DEVOLUCIONES CON CUPONES USADOS\n";

        foreach ($listaCupones as $cupon) {
            if ($cupon->estado == "usado") {
                echo "|   ID cupón: " . $cupon->id . "   |   ID devolución: " . $cupon->id_devolucion . "   |   Estado: " . "$cupon->estado" . "\n";
            }
        }
    }
}
?>