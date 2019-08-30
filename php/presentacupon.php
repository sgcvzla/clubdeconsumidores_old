<?php
header('Content-Type: application/json');
include_once("../_config/conexion.php");
include_once("funciones.php");
include_once("../lib/phpqrcode/qrlib.php");

// Buscar datos de socio y premio
$query = "select * from cupones where cuponlargo='".$_POST['cupon']."'";
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
	$id_proveedor = $row["id_proveedor"];
	$id_socio = $row["id_socio"];
	$tipopremio = $row["tipopremio"];
	$montopremio = $row["montopremio"];
	$descpremio = $row["descpremio"];
}

/*
Hash para insertar en el blockchain
-----------------------------------
El hash se va a armar con los siguientes datos:
- Cupon
- Proveedor
- Socio
- Tipo premio
- Monto premio
- Descripción premio
- Status cupón
*/
$hash = hash("sha256",$_POST['cupon'].$_POST['id_proveedor'].$id_socio.$tipopremio.$montopremio.$descpremio."Generado");

// codigo de barras
$barras = 'https://www.clubdeconsumidores.com.ve/php/barcode.php?';
$barras .= 'text='.$_POST['cupon'];
$barras .= '&size=50';
// $barras .= 'text=';
// $barras .= '&size=50';
$barras .= '&orientation=horizontal';
$barras .= '&codetype=Code39';
$barras .= '&print=true';
$barras .= '&sizefactor=1';

// código qr
$ruta = 'https://www.clubdeconsumidores.com.ve/php/';
$dir = 'qr/';
if(!file_exists($dir)) mkdir($dir);

$tamanio = 5;
$level = 'H';
$frameSize = 1;
//$contenido = $cuponlargo;
//$contenido = '{"id_proveedor":'.$_POST['id_proveedor'].',"cupon":"'.$cuponlargo.'"}';
$contenido = 'https://www.clubdeconsumidores.com.ve/canje/canje.html?cJson={"id_proveedor":'.$id_proveedor.',"cupon":"'.$_POST['cupon'].'"}';

QRcode::png($contenido,$dir.$_POST['cupon'].'.png', $level, $tamanio, $frameSize);
$codigoqr = $ruta.$dir.$_POST['cupon'].'.png';
// Hasta aqui

$mensaje .= '<p style="text-align:center;">'.$hash.'</p>';

$respuesta = '{"exito":"SI","barras":"'.$barras.'","qr":"'.$codigoqr.'","hash":"'.$hash.'"}';
echo $respuesta;
?>
