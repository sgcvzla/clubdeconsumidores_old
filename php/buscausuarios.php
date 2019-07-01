<?php 
header('Content-Type: application/json');
include_once("../_config/conexion.php");

$quer0 = 'SELECT * FROM usuarios where email="'.$_GET["email"].'"';
// $quer0 = 'SELECT * FROM usuarios where email="'.$_GET["email"].'" and tipo="'.$_GET["tipo"].'" or tipo="ambos"';
// var_dump($_GET);
// echo $quer0;
$resul0 = mysqli_query($link, $quer0);
$id = 0;
if ($ro0 = mysqli_fetch_array($resul0)) {
    if ($ro0["tipo"]==$_GET["tipo"] or $ro0["tipo"]=="ambos") {
        if ($ro0["tipo"]=='comercio') {
            $query = 'SELECT id FROM proveedores where email="'.$_GET["email"].'"';
        } else {
            $query = 'SELECT id FROM socios where email="'.$_GET["email"].'"';
        }
        $result = mysqli_query($link, $query);
        if ($row = mysqli_fetch_array($result)) {
            $id = $row['id'];
        }
        $respuesta = '{"exito":"SI",';
        $respuesta .= '"id":'.$id.',';
        $respuesta .= '"hashp":"'. $ro0["hashp"] .'",';
        $respuesta .= '"pregunta":"' . utf8_encode($ro0["pregunta"]) . '",';
        $respuesta .= '"hashr":"' . $ro0["hashr"] . '",';
        $respuesta .= '"mensaje":"exito"}';
    } else {
        $respuesta = '{"exito":"NO",';
        $respuesta .= '"id":'.$id.',';
        $respuesta .= '"hashp":"",';
        $respuesta .= '"pregunta":"",';
        $respuesta .= '"hashr":"",';
        $respuesta .= '"mensaje":"error de tipo"}';
    }
    
} else {
    $respuesta = '{"exito":"NO",';
    $respuesta .= '"id":'.$id.',';
    $respuesta .= '"hashp":"",';
    $respuesta .= '"pregunta":"",';
    $respuesta .= '"hashr":"",';
    $respuesta .= '"mensaje":"correo no registrado"}';
}
echo $respuesta;
?>
