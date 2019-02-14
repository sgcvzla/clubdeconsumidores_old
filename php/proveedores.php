<?php 
// session_start();
header('Content-Type: application/json');
include_once("../_config/conexion.php");

$query = "SELECT * from proveedores where id = " . $_GET["prov"];
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
	$respuesta = '{"exito":"SI","proveedor":{"nombre":"' . utf8_encode($row["nombre"]) . '","logo":"' . $row["logo"] . '"}}';
} else {
	$respuesta = '{"exito":"NO","proveedor":{}}';
}
echo $respuesta;
?>
