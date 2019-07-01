<?php 
// session_start();
header('Content-Type: application/json');
include_once("../_config/conexion.php");

$success = 0;
$prov = '';
$socio = '';
$query = "SELECT * from proveedores where id = " . $_GET["prov"];
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
	$success = 1;
	$prov = '"proveedor":{"nombre":"' . utf8_encode($row["nombre"]) . '","logo":"' . $row["logo"] . '"}';
} else {
	$prov = '"proveedor":{}';
}

$query = "SELECT * from socios where id = " . $_GET["socio"];
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
	if ($success==1) {
		$success = 3;
	} else {
		$success = 2;
	}
	$socio = '"socio":{"id":"' .$_GET["socio"]. '","nombres":"' .utf8_encode(trim($row["nombres"])). '"}';
} else {
	$socio = '"socio":{}';
}

$respuesta = '{"exito":'.$success.','.$prov.','.$socio.' }';
echo $respuesta;
?>
