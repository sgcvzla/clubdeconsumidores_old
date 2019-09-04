<?php 
// session_start();
header('Content-Type: application/json');
include_once("../_config/conexion.php");

// Buscar socio
$query = "SELECT * from socios where id=".$_GET["idsocio"];
$result = mysqli_query($link, $query);
$row = mysqli_fetch_array($result);
$respuesta = '{"nombresocio":"'.utf8_encode(trim($row["nombres"]).' '.trim($row["apellidos"])).'"';

// Buscar prepago
$query = "SELECT * from prepago where id_socio=".$_GET["idsocio"];
$result = mysqli_query($link, $query);
$respuesta .= ',"cards":[';
$first = true;
while ($row = mysqli_fetch_array($result)) {
	if ($first) {
		$coma = "";
		$first = false;
	} else {
		$coma = ",";
	}

	// Buscar proveedor
	$quer2 = "SELECT * from proveedores where id=".$row["id_proveedor"];
	$resul2 = mysqli_query($link, $quer2);
	if ($ro2 = mysqli_fetch_array($resul2)) {
		$color = $ro2["color"];
		if ($ro2["logo"]=="") {
			$logoproveedor = 'logoclub.png';
		} else {
			$logoproveedor = $ro2["logo"];
		}
	}


	// Buscar proveedor
	$quer2 = "SELECT * from _monedas where moneda='".$row["moneda"]."'";
	$resul2 = mysqli_query($link, $quer2);
	if ($ro2 = mysqli_fetch_array($resul2)) {
		$simbolo = $ro2["simbolo"];
		$dibujo = $ro2["dibujo"];
	}

	$respuesta .= $coma.'{"logoproveedor":"'.$logoproveedor.'","card":"'.$row["card"].'","saldo":'.$row["saldo"].',"moneda":"'.$row["moneda"].'","simbolomoneda":"'.$simbolo.'","dibujomoneda":"'.$dibujo.'","status":"'.$row["status"].'","color":"'.$color.'"}';
}
$respuesta .= ']}';

echo $respuesta;
?>
