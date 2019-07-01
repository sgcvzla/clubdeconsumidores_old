<?php 
header('Content-Type: application/json');
include_once("../_config/conexion.php");

$quer0 = 'SELECT * FROM usuarios where email="'.$_GET["email"].'"';
$resul0 = mysqli_query($link, $quer0);
if ($ro0 = mysqli_fetch_array($resul0)) {
    $respuesta = '{"exito":"NO",';
    $respuesta .= '"mensaje":"Correo ya registrado"}';
} else {
    $query = 'INSERT INTO usuarios (email, tipo, pregunta, hashp, hashr) VALUES ("'.$_GET["email"].'","'.$_GET["tipo"].'","'.$_GET["question"].'","'.$_GET["hashp"].'","'.$_GET["hashr"].'")';
    if($result = mysqli_query($link, $query)) {
        $respuesta = '{"exito":"SI",';
        $respuesta .= '"mensaje":"Registro exitoso"}';
    }
}
echo $respuesta;
?>
