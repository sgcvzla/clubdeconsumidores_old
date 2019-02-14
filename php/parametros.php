<?php 
// session_start();
header('Content-Type: application/json');
include_once("../_config/conexion.php");

$query = "SELECT * from _parametros";
$result = mysqli_query($link,$query);
if ($row = mysqli_fetch_array($result)) {
	$nombresistema = $row["nombresistema"];
	$logosistema = $row["logosistema"];
	$respuesta = '{"exito":"SI","parametros":{"nombresistema":"'. utf8_encode($nombresistema) .'","logosistema":"' . $logosistema . '"}}';
} else {
	$respuesta = '{"exito":"NO","parametros":{}}';
}
echo $respuesta;
?>
