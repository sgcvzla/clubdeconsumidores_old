<?php 
header('Content-Type: application/json');
include_once("../_config/conexion.php");

$quer0 = "SELECT categoria FROM proveedores group by categoria order by categoria";

if ($resul0 = mysqli_query($link, $quer0)) {
    $categoria = '';
    $cierto = true;
    $coma = '';
    $cierre = false;
    while ($ro0 = mysqli_fetch_array($resul0)) {
        if ($cierto) {
            $categoria .= '"filtros":';
            $cierto = false;
            $cierre = true;
            $coma = '[';
        } else {
            $coma = ',';
        }
        $categoria .= $coma . '"' . utf8_encode(trim($ro0["categoria"])) . '"';
    }
    $categoria .= ($cierre) ? ']' : '';
}

$quer0 = "select * from proveedores order by categoria,nombre";
if ($resul0 = mysqli_query($link,$quer0)) {
    $respuesta = '';
    $cierto = true;
    $coma = '';
    $cierre = false;
    while ($ro0 = mysqli_fetch_array($resul0)) {
        if ($cierto) {
            $respuesta .= '{' . $categoria . ',"registros":';
            $cierto = false;
            $cierre = true;
            $coma = '[';
        } else {
            $coma = ',';
        }
        $respuesta .= $coma . '{"id":'. $ro0["id"] .',"nombre":"' . utf8_encode(trim($ro0["nombre"])) . '","imagen":"' . trim($ro0["logo"]) . '","categoria":"' . utf8_encode(trim($ro0["categoria"])) . '"}';
    }
    $respuesta .= ($cierre) ? ']' : '';
    $respuesta .= '}';
} else {
    $respuesta .= 'No';
}
echo $respuesta;
?>
