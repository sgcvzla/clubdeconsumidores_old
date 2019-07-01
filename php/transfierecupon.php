<?php
header('Content-Type: application/json');
include_once("../_config/conexion.php");
include_once("../php/funciones.php");

$query = "select * from socios where email='".$_POST['correo']."'";;
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
	$query = "update cupones set id_socio=".$row["id"]." where cuponlargo='".$_POST['cupon']."'";
	if ($result = mysqli_query($link, $query)) {
		$respuesta = '{"exito":"SI","mensaje":"Cupon transferido exitosamente"}';
	} else {
		$respuesta = '{"exito":"NO","mensaje":"Ocurrió un error inesperado,\ninténtelo de nuevo o comuniquese al +58-424-4071820"}';
	}
} else {
	$respuesta = '{"exito":"NO","mensaje":"email inválido o no registrado,\ncorríjelo e intentalo de nuevo\no comuníquese al +58-424-4071820"}';
}

echo $respuesta;

?>
