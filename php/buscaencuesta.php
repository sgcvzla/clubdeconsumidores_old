<?php 
header('Content-Type: application/json');
include_once("../_config/conexion.php");

$quer1 = "SELECT * from encuesta where idp=" . $_GET["prov"] . " and status=1 and desde<'".date('Y-m-d'). "' and hasta>='".date('Y-m-d')."'";
// echo $quer1;
$resul1 = mysqli_query($link, $quer1);
if ($ro1 = mysqli_fetch_array($resul1)) {
    $ide = $ro1["id"];
    $quer2 = "SELECT * from detalle_encuesta where ide=" . $ide;
    if ($resul2 = mysqli_query($link, $quer2)) {
        $respuesta = '{"preguntas":[';
        $cierto = true;
        $coma = '';
        $cierre = false;
        while($ro2 = mysqli_fetch_array($resul2)) {
            if ($cierto) {
                $cierto = false;
                $coma = '';
            } else {
                $coma = ',';            
            }
            $respuesta .= $coma.'{"pregunta":"'.utf8_encode($ro2["pregunta"]).'","tiporespuesta":"'.$ro2["tiporespuesta"].'"}';
        }
        $respuesta .= ']}';
    } else {
        $respuesta = '{"preguntas":[]}';
    }
}
echo $respuesta;
?>
