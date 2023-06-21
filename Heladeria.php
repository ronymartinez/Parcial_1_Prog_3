<?php

class Heladeria
{
    public $id;
    public $sabor;
    public $precio;
    public $tipo;
    public $vaso;
    public $stock;

    function __construct($sabor, $precio, $tipo, $vaso, $stock, $id)
    {
        $this->id = $id;
        $this->sabor = $sabor;
        $this->precio = $precio;
        $this->tipo = $tipo;
        $this->vaso = $vaso;
        $this->stock = $stock;
    }

    static function CargarHeladeria()
    {
        $archivo = fopen("heladeria.json", "a+");
        $tamañoArchivo = filesize("heladeria.json");
        $listaHeladeria = [];

        if ($tamañoArchivo != 0) {

            $retornoFRead = json_decode(fread($archivo, $tamañoArchivo));

            foreach ($retornoFRead as $value) {
                $sabor = $value->sabor;
                $precio = $value->precio;
                $tipo = $value->tipo;
                $vaso = $value->vaso;
                $stock = $value->stock;
                $id = $value->id;
                $nuevaHeladeria = new Heladeria($sabor, $precio, $tipo, $vaso, $stock, $id);
                array_push($listaHeladeria, $nuevaHeladeria);
            }

            fclose($archivo);
            return $listaHeladeria;
        }

        fclose($archivo);
        return $listaHeladeria;
    }
    static function CrearID()
    {
        $nuevoID = 101;
        $listaHeladeria = Heladeria::CargarHeladeria();

        if ($listaHeladeria != null) {
            foreach ($listaHeladeria as $value) {
                $nuevoID = $value->id + 1;
            }
        }
        return $nuevoID;
    }

    public static function GuardarHeladeria($heladeria)
    {
        $listaHeladeria = Heladeria::CargarHeladeria();

        $retorno = "No se ha realizado ningún cambio";

        if (Heladeria::ValidarHeladeria($heladeria) == true) {
            foreach ($listaHeladeria as $value) {
                if ($value->sabor == $heladeria->sabor && $value->tipo == $heladeria->tipo) {
                    $value->stock += $heladeria->stock;
                    $value->precio = $heladeria->precio;
                    break;
                }
            }
            $retorno = "Se ha actualizado el stock y el precio";
        } else {
            $heladeria->id = Heladeria::CrearID();


            $nombreImagen = "$heladeria->sabor" . "_" . "$heladeria->tipo" . ".jpg";
            $ruta = "C:\\xampp\\htdocs\\Parcial_1\\ImagenesDeHelados\\2023\\" . "$nombreImagen";
            $cargaImagenOK = move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);

            if ($cargaImagenOK) {
                array_push($listaHeladeria, $heladeria);
                $retorno = "Se ha ingresado una nueva Heladeria";
            } else {
                $retorno = "No se pudo guardar la imagen\n";
            }

        }

        $heladerias = fopen("heladeria.json", "w+");
        $heladeriasJson = json_encode($listaHeladeria);
        fwrite($heladerias, $heladeriasJson);
        fclose($heladerias);

        echo $retorno;
    }
    static function ValidarHeladeria($heladeria)
    {
        $listaHeladeria = Heladeria::CargarHeladeria();

        foreach ($listaHeladeria as $value) {

            if (strtolower($value->sabor) == strtolower($heladeria->sabor) && strtolower($value->tipo) == strtolower($heladeria->tipo)) {
                return true;
            }
        }
        return false;
    }
    static function ObtenerCostoUnidad($sabor, $tipo)
    {
        $listaHeladeria = Heladeria::CargarHeladeria();
        $precioHelado = 0;

            foreach ($listaHeladeria as $heladeria) {
                if ($heladeria->sabor == $sabor && $heladeria->tipo == $tipo) {
                    $precioHelado = $heladeria->precio;
                    break;
                }
        
        }
        return $precioHelado;
    }
    static function ObtenerCostoTotalSinDescuento($sabor, $tipo, $stock)
    {
        $costoUnidad = Heladeria::ObtenerCostoUnidad($sabor, $tipo);
        $stock = intVal($stock);
        $costoTotal = $costoUnidad * $stock;

        return $costoTotal;
    }


}
?>