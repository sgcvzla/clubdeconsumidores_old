<?php
function mensajes($archivojson,$texto){
    $parametros = json_decode(file_get_contents($archivojson),true);
    $mensaje = '[';
    for ($i = 0; $i < count($parametros["mensajes"][$texto]); $i++) {
        $mensaje .= '"' . $parametros["mensajes"][$texto][$i] . '"';
        if (count($parametros["mensajes"][$texto]) > 1 && $i + 1 < count($parametros["mensajes"][$texto])) {
            $mensaje .= ',';
        }
    }
    $mensaje .= ']';
    return $mensaje;
}


function asignacodigo($ultcupon){
    $valores = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $a = strlen($valores)-1;
    $base = 36;
    $codigo = '';
    $arriba = 1;
    $newcodigo = '';
    $numero = $ultcupon;
    // echo $numero.'<br>';
    for ($i=strlen($ultcupon)-1 ; $i>=0 ; $i--) { 
        $pos = strpos($valores, substr($numero,$i,1));
        if ($arriba==1) {
            if ($pos==$a) {
                $codigo = substr($valores,0,1);
            } else {
                $codigo = substr($valores,$pos+1,1);
                $arriba = 0;
            }
        } else {
            $codigo = substr($numero,$i,1);
        }
        $newcodigo = $codigo.$newcodigo;
    }
    // switch (strlen($newcodigo)) {
    //  case '1':
    //      $newcodigo = '0000'.$newcodigo;
    //      break;
    //  case '2':
    //      $newcodigo = '000'.$newcodigo;
    //      break;
    //  case '3':
    //      $newcodigo = '00'.$newcodigo;
    //      break;
    //  case '4':
    //      $newcodigo = '0'.$newcodigo;
    //      break;
    // }
    for ($i=0 ; $i< strlen($newcodigo); $i++) { 
        // echo substr($newcodigo,$i,1).'<br>';
    }

    return $newcodigo;
}

function asignacodigolargo($ultcupon){
    $newcodigo = $ultcupon;

    $cuponlargo = substr($newcodigo,0,2);
    $cuponlargo .= codigocaracter(strtoupper(substr($_POST["email"],-1)));
    $cuponlargo .= substr($newcodigo,2,2);
    $cuponlargo .= codigocaracter(strtoupper(substr($_POST["nombres"],-1)));
    $cuponlargo .= substr($newcodigo,4,2);
    $cuponlargo .= codigocaracter(strtoupper(substr($_POST["apellidos"],-1)));
    $cuponlargo .= substr($newcodigo,6,2);
    $cuponlargo .= codigocaracter(strtoupper(substr($_POST["telefono"],-1)));
    $cuponlargo .= substr($newcodigo,8,2);

    return $cuponlargo;
}

function asignacodigolargo2($ultcupon,$email,$nombres,$apellidos,$telefono){
    $newcodigo = $ultcupon;

    $cuponlargo = substr($newcodigo,0,2);
    $cuponlargo .= codigocaracter(strtoupper(substr($email,-1)));
    $cuponlargo .= substr($newcodigo,2,2);
    $cuponlargo .= codigocaracter(strtoupper(substr($nombres,-1)));
    $cuponlargo .= substr($newcodigo,4,2);
    $cuponlargo .= codigocaracter(strtoupper(substr($apellidos,-1)));
    $cuponlargo .= substr($newcodigo,6,2);
    $cuponlargo .= codigocaracter(strtoupper(substr($telefono,-1)));
    $cuponlargo .= substr($newcodigo,8,2);

    return $cuponlargo;
}

function codigocaracter($valor) {
    $llaves = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    $codigos =  '111213141A1B1C1D212223242A2B2C2D3132';
    $codigos .= '33343A3B3C3D414243444A4B4C4DA1A2A3A4';

    $posicion = strpos($llaves, $valor);
    $pos2 = $posicion*2;
    $newvalor = substr($codigos,$pos2,2);

    return $newvalor;
}

function generacodigo($letra,$link) {
    $query = "select codigo from _codigo where valor='".$letra."'";
    $result = mysqli_query($link, $query);
    if ($row = mysqli_fetch_array($result)) {
        $codigo = $row["codigo"];
    } else {
        $query = "select codigo from _codigo where valor='?'";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);
        $codigo = $row["codigo"];
    }
    return $codigo;
}


?>