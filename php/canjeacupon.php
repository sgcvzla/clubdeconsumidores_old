<?php
header('Content-Type: application/json');
include_once("../_config/conexion.php");
include_once("../php/funciones.php");

$archivojson = "../canje/canje.json";

// var_dump($_POST);
// Buscar datos de proveedor
$query = "select * from cupones where cuponlargo='".$_POST['cuponlargo']."'";;
// $query = "select * from proveedores where id=1";
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
	if ($row["id_proveedor"]==$_POST['id_proveedor']) {
		if ($row["status"]=='Generado') {
			$respuesta = '{"exito":"SI","mensaje":' . mensajes($archivojson,"cuponvalido") . '}';
			$query = "update cupones set status='Usado',fechacanje='".date("Y-m-d")."',facturacanje='".$_POST['factura']."',montocanje=".$_POST['monto']." where cuponlargo='".$_POST['cuponlargo']."'";
			// echo $query;
			$result = mysqli_query($link, $query);
		} else {
			$respuesta = '{"exito":"NO","mensaje":' . mensajes($archivojson,"cuponinvalido") . '}';
		}
	} else {
		$respuesta = '{"exito":"NO","mensaje":' . mensajes($archivojson,"comercioincorrecto") . '}';
	}
} else {
	$respuesta = '{"exito":"NO","mensaje":' . mensajes($archivojson,"cuponincorrecto") . '}';
}

echo $respuesta;

?>
