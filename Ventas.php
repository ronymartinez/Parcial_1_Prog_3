<?php
include_once "Heladeria.php";
include_once "Cupon.php";

class Venta
{
    public $id;
    public $pedido;
    public $fecha;
    public $sabor;
    public $tipo;
    public $vaso;
    public $stock;
    public $mail;
    public $deleted;
    public $costoFinal;
    public $id_cupon;
    public $descuento;

    function __construct($pedido, $fecha, $sabor, $tipo, $vaso, $stock, $mail, $id, $id_cupon)
    {
        $this->pedido = $pedido;
        $this->fecha = $fecha;
        $this->sabor = $sabor;
        $this->tipo = $tipo;
        $this->vaso = $vaso;
        $this->stock = $stock;
        $this->mail = $mail;
        $this->id = $id;
        $this->id_cupon = $id_cupon;
        $this->deleted = false;
        $this->descuento = Venta::ObtenerDescuento($id_cupon, $stock, $sabor, $tipo);
        $this->costoFinal = Heladeria::ObtenerCostoTotalSinDescuento($sabor, $tipo, $stock);
    }
    function ObtenerCostoConDescuento($sabor, $tipo, $stock, $id_cupon)
    {
        $costoTotalConDescuento = 0;
        $descuento = Venta::ObtenerDescuento($id_cupon, $stock, $sabor, $tipo);
        $costoTotalSinDescuento = Heladeria::ObtenerCostoTotalSinDescuento($stock, $sabor, $tipo);
        $costoTotalConDescuento = $costoTotalSinDescuento -  $descuento;
        
       return $costoTotalConDescuento;
    }

    function ObtenerDescuento($id_cupon, $stock, $sabor, $tipo)
    {
        $porcentaje = Cupon::ObtenerPorcentaje($id_cupon);
        $costoTotalSinDescuento = Heladeria::ObtenerCostoTotalSinDescuento($sabor, $tipo, $stock);
        $descuento = 0;
        if (Cupon::ValidarCupon($id_cupon))
        {
            $descuento = $costoTotalSinDescuento *  ($porcentaje / 100);
        }
        return $descuento;
    }

    // function ObtenerCostoTotalSinDescuento($stock, $sabor, $tipo)
    // {
    //     $precioUnidad = Heladeria::ObtenerCostoUnidad($sabor, $tipo);
    //     $stock = intVal($stock);

    //     $precioTotalSinDescuento = $precioUnidad * $stock;    
       
    //     return $precioTotalSinDescuento;
    // }


    static function CargarVentasActivas()
    {
        $archivo = fopen("ventas.json", "a+");
        $tamañoArchivo = filesize("ventas.json");
        $listaVentas = [];

        if ($tamañoArchivo != 0) {

            $retornoFRead = json_decode(fread($archivo, $tamañoArchivo));

            foreach ($retornoFRead as $value) {

                if ($value->deleted == false) {
                    $pedido = $value->pedido;
                    $fecha = $value->fecha;
                    $sabor = $value->sabor;
                    $tipo = $value->tipo;
                    $vaso = $value->vaso;
                    $stock = $value->stock;
                    $mail = $value->mail;
                    $id = $value->id;
                    $id_cupon = $value->id_cupon;
                    $nuevaVenta = new Venta($pedido, $fecha, $sabor, $tipo, $vaso, $stock, $mail, $id, $id_cupon);
                    array_push($listaVentas, $nuevaVenta);
                }

            }

            fclose($archivo);
            return $listaVentas;
        }

        fclose($archivo);
        return $listaVentas;
    }


    static function CargarVentasTotales()
    {
        $archivo = fopen("ventas.json", "a+");
        $tamañoArchivo = filesize("ventas.json");
        $listaVentas = [];

        if ($tamañoArchivo != 0) {

            $retornoFRead = json_decode(fread($archivo, $tamañoArchivo));

            foreach ($retornoFRead as $value) {

                $pedido = $value->pedido;
                $fecha = $value->fecha;
                $sabor = $value->sabor;
                $tipo = $value->tipo;
                $vaso = $value->vaso;
                $stock = $value->stock;
                $mail = $value->mail;
                $id = $value->id;
                $id_cupon = $value->id_cupon;

                $nuevaVenta = new Venta($pedido, $fecha, $sabor, $tipo, $vaso, $stock, $mail, $id, $id_cupon);
                array_push($listaVentas, $nuevaVenta);
            }

            fclose($archivo);
            return $listaVentas;
        }

        fclose($archivo);
        return $listaVentas;
    }
    static function CrearID()
    {
        $nuevoID = 101;
        $listaVentas = Venta::CargarVentasTotales();

        if ($listaVentas != null) {
            foreach ($listaVentas as $value) {
                $nuevoID = $value->id + 1;
            }
        }
        return $nuevoID;
    }
    static function CrearNroPedido()
    {
        $nuevoPedido = 501;
        $listaVentas = Venta::CargarVentasTotales();

        if ($listaVentas != null) {
            foreach ($listaVentas as $value) {
                $nuevoPedido = $value->pedido + 1;
            }
        }
        return $nuevoPedido;
    }
    public static function GuardarVenta($venta)
    {
        $listaVentas = Venta::CargarVentasTotales();
        $listaHeladeria = Heladeria::CargarHeladeria();
        $stockPedido = $venta->stock;
        $retorno = "";
        if (Venta::ValidarVenta($venta) == 0) {

            foreach ($listaHeladeria as $heladeria) {

                if ($heladeria->sabor == $venta->sabor && $heladeria->tipo == $venta->tipo) {

                    //Actualizo stock de Heladeria
                    $heladeria->stock -= $stockPedido;

                    //Guardo nueva venta
                    $venta->id = Venta::CrearID();
                    $venta->pedido = Venta::CrearNroPedido();
                    $venta->vaso = $heladeria->vaso;
                    $usuario = Venta::FormatMail($venta->mail);
                   
                    //nombre: sabor + tipo + vaso + usuario
                    $nombreImagen = "$venta->sabor" . "_" . "$venta->tipo" . "_" . "$venta->vaso" . "_" . "$usuario" . ".jpg";

                    $ruta = "C:\\xampp\\htdocs\\Parcial_1\\ImagenesDeLaVenta\\2023\\" . $nombreImagen;
                    $cargaImagenOK = move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);

                    if ($cargaImagenOK) {
                        //Actualizo cupon
                        if($venta->id_cupon != null){

                        if(Cupon::ValidarCupon($venta->id_cupon)){
                            Cupon::ActualizarCupon($venta->id_cupon);
                            array_push($listaVentas, $venta);
                            $archivoVentas = fopen("ventas.json", "w+");
                            $ventasJson = json_encode($listaVentas);
                            fwrite($archivoVentas, $ventasJson);
                            fclose($archivoVentas);
    
                            $heladeriaJson = json_encode($listaHeladeria);
                            $archivoHeladeria = fopen("heladeria.json", "w+");
                            fwrite($archivoHeladeria, $heladeriaJson);
                            fclose($archivoHeladeria);  
    
    
                            $retorno = "-Se ha ingresado una nueva venta y se ha actualizado el stock\n";

                        }else{
                            $retorno = "\nEl cupón ingresado no es válido";
                        }
                    }else{
                        array_push($listaVentas, $venta);
                            $archivoVentas = fopen("ventas.json", "w+");
                            $ventasJson = json_encode($listaVentas);
                            fwrite($archivoVentas, $ventasJson);
                            fclose($archivoVentas);
    
                            $heladeriaJson = json_encode($listaHeladeria);
                            $archivoHeladeria = fopen("heladeria.json", "w+");
                            fwrite($archivoHeladeria, $heladeriaJson);
                            fclose($archivoHeladeria);
                            $retorno = "-Se ha ingresado una nueva venta y se ha actualizado el stock\n";
                    }

                    } else {
                        $retorno = "-No se pudo guardar la imagen\n";
                    }
                    break;
                }
            }
        }

        //
        else if (Venta::ValidarVenta($venta) == 1) {
            $retorno = "\nNo hay stock suficiente";

        } else {
            $retorno = "\nNo hay el sabor y tipo seleccionado";
        }



        echo $retorno;
    }
    public static function FormatMail($mail)
    {
        $nuevoMail = explode("@", $mail);
        return $nuevoMail[0];
    }

    public static function ValidarVenta($venta)
    {

        $listaHeladeria = Heladeria::CargarHeladeria();
        $heladeria = new Heladeria($venta->sabor, "", $venta->tipo, $venta->vaso, "", "");

        foreach ($listaHeladeria as $value) {

            if (Heladeria::ValidarHeladeria($heladeria)) {
                if ($value->stock >= $heladeria->stock) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 2;
            }
        }
    }

    public static function MostrarCantVentaPorFecha($fecha)
    {

        $listaDeVentas = Venta::CargarVentasActivas();

        $contadorDeVentas = 0;
        if ($fecha == null) {

            $fecha_actual = date('d-m-Y');
            $date_future = strtotime('-1 day', strtotime($fecha_actual));
            $fecha = date('d-m-Y', $date_future);
        }

        foreach ($listaDeVentas as $venta) {
            if ($venta->fecha == $fecha) {
                $contadorDeVentas += $venta->stock;
            }
        }
        echo "\nLa cantidad de ventas del día $fecha es de: $contadorDeVentas";
        echo "\n---------------------------------------------------------------";

    }

    public static function MostrarVentasPorUsuario($usuario)
    {
        $nuevoUsuario = Venta::FormatMail($usuario);
        $listaVentas = Venta::CargarVentasActivas();
        $flagVentasOk = false;

        echo "\nVentas del usuario: $nuevoUsuario\n";

        foreach ($listaVentas as $venta) {

            if (Venta::FormatMail($venta->mail) == $nuevoUsuario) {

                echo "\n-ID: $venta->id\n";
                echo "-Nº pedido: $venta->pedido\n";
                echo "-Fecha: $venta->fecha\n";
                echo "-Tipo: $venta->tipo\n";
                echo "-Sabor: $venta->sabor\n";
                echo "-Vaso: $venta->vaso\n";
                echo "-Cantidad: $venta->stock\n";

                $flagVentasOk = true;
            }

        }
        if ($flagVentasOk == false) {
            echo "\n-No hay ventas del usuario indicado";
        }
        echo "\n---------------------------------------------------------------";

    }
    public static function MostrarVentasDosFechas($fechaUno, $fechaDos)
    {
        $listaVentas = Venta::CargarVentasActivas();

        echo "\nVentas entre el $fechaUno y $fechaDos\n";
        $fechaUno = strtotime($fechaUno);
        $fechaDos = strtotime($fechaDos);
        $flagVentasOk = false;
        foreach ($listaVentas as $venta) {

            if (strtotime($venta->fecha) > $fechaUno && strtotime($venta->fecha) < $fechaDos) {

                echo "\n-ID: $venta->id\n";
                echo "-Nº pedido: $venta->pedido\n";
                echo "-Fecha: $venta->fecha\n";
                echo "-Tipo: $venta->tipo\n";
                echo "-Sabor: $venta->sabor\n";
                echo "-Vaso: $venta->vaso\n";
                echo "-Cantidad: $venta->stock\n";
                echo "-Usuario: $venta->mail\n";
                $flagVentasOk = true;
            }

        }
        if ($flagVentasOk == false) {
            echo "\n-No hay ventas entre las fechas indicadas";
        }
        echo "\n---------------------------------------------------------------";
    }
    public static function MostrarVentasPorSabor($sabor)
    {
        $listaVentas = Venta::CargarVentasActivas();
        $flagVentasOk = false;
        echo "\nVentas del sabor: $sabor\n";

        foreach ($listaVentas as $venta) {

            if ($venta->sabor == strtolower($sabor)) {

                echo "\n-ID: $venta->id\n";
                echo "-Nº pedido: $venta->pedido\n";
                echo "-Fecha: $venta->fecha\n";
                echo "-Tipo: $venta->tipo\n";
                echo "-Vaso: $venta->vaso\n";
                echo "-Cantidad: $venta->stock\n";
                echo "-Usuario: $venta->mail\n";
                $flagVentasOk = true;
            }

        }
        if ($flagVentasOk == false) {
            echo "\n-No hay ventas del sabor $sabor";
        }
        echo "\n---------------------------------------------------------------";

    }
    public static function MostrarVentasPorVaso($vaso)
    {
        $listaVentas = Venta::CargarVentasActivas();
        $flagVentasOk = false;
        echo "\nVentas del vaso: $vaso\n";

        foreach ($listaVentas as $venta) {

            if ($venta->vaso == strtolower($vaso)) {

                echo "\n-ID: $venta->id\n";
                echo "-Nº pedido: $venta->pedido\n";
                echo "-Fecha: $venta->fecha\n";
                echo "-Tipo: $venta->tipo\n";
                echo "-Sabor: $venta->sabor\n";
                echo "-Cantidad: $venta->stock\n";
                echo "-Usuario: $venta->mail\n";
                $flagVentasOk = true;
            }

        }

        if ($flagVentasOk == false) {
            echo "\n-No hay ventas del vaso $vaso";
        }
        echo "\n---------------------------------------------------------------";

    }
    public static function MofidificarVenta($venta)
    {

        $listaVentas = Venta::CargarVentasTotales();
        $flagModificacionOk = false;

        foreach ($listaVentas as $value) {

            if ($value->pedido == $venta->pedido) {
                $value->mail = $venta->mail;
                $value->vaso = $venta->vaso;
                $value->tipo = $venta->tipo;
                $value->stock = $venta->stock;

                $archivoVentas = fopen("ventas.json", "w+");
                $ventasJson = json_encode($listaVentas);
                fwrite($archivoVentas, $ventasJson);
                fclose($archivoVentas);
                $flagModificacionOk = true;
                break;
            }
        }
        if ($flagModificacionOk) {
            echo "\nSe ha realizado la modificacion";
        } else {
            echo "\nNo se ha encontrado el número de pedido";
        }
    }
    public static function BorrarVenta($idPedido)
    {
        $listaVentas = Venta::CargarVentasTotales();
        $flagBorradoOk = false;
        foreach ($listaVentas as $venta) {
            if ($venta->pedido == $idPedido) {

                if (Venta::BorrarImagen($venta)) {
                    $venta->deleted = true;
                    $archivoVentas = fopen("ventas.json", "w+");
                    $ventasJson = json_encode($listaVentas);
                    fwrite($archivoVentas, $ventasJson);
                    fclose($archivoVentas);
                    $flagBorradoOk = true;
                    break;
                }
            }
        }
        if ($flagBorradoOk) {
            echo "\nSe ha borrado el pedido Nº $idPedido";
        } else {
            echo "\nNo se ha encontrado el número de pedido";
        }
    }
    public static function BorrarImagen($venta)
    {
        $usuario = Venta::FormatMail($venta->mail);
        $nombreImagen = "$venta->sabor" . "_" . "$venta->tipo" . "_" . "$venta->vaso" . "_" . "$usuario" . ".jpg";

        $rutaNueva = "C:\\xampp\\htdocs\\Parcial_1\\ImagenesBackupVentas\\2023\\" . $nombreImagen;
        $rutaAnterior = "C:\\xampp\\htdocs\\Parcial_1\\ImagenesDeLaVenta\\2023\\" . $nombreImagen;
        $cargaImagenOK = rename($rutaAnterior, $rutaNueva);

        return $cargaImagenOK;

    }

}
?>