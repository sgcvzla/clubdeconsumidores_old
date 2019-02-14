<?php
include_once("../_config/conexion.php");
include_once("funciones.php");
include_once("../lib/phpqrcode/qrlib.php");

// Buscar datos de proveedor
$query = "select * from proveedores";
$result = mysqli_query($link, $query);
while ($row = mysqli_fetch_array($result)) {
	$id_proveedor=$row["id"];
	$nombreproveedor=$row["nombre"];
	// código qr
	// $dir = 'https://www.clubdeconsumidores.com.ve/php/temp/';
	$dir = 'temp/';
	
	// $filename = $dir.'test.png';
	$filename = $dir.$id_proveedor.'.png';
	$tamanio = 5;
	$level = 'H';
	$frameSize = 1;
	$contenido = 'https://www.clubdeconsumidores.com.ve/cupones/cupon.html?reg={"id_proveedor":'.$id_proveedor.'}';

	QRcode::png($contenido, $filename, $level, $tamanio, $frameSize);

	$mensaje = '<p style="text-align:center;">';
		$mensaje .= $nombreproveedor.'<br/>';
		$mensaje .= '<img src="'.$filename.'" height="200" width="200" />';
	$mensaje .= '</p>';
	$mensaje .= '<p></p>';
	echo $mensaje;
	// Hasta aqui
}

?>
