<?php
header('Content-Type: application/json');
include_once("../_config/conexion.php");

$nombres = $_POST['nombres'];
$apellidos = $_POST['apellidos'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$idsocio = $_POST['idsocio'];
$idproveedor = $_POST['idproveedor'];
$moneda = $_POST['moneda'];
$tipotransaccion = $_POST['tipotransaccion'];
$documento = $_POST['documento'];
$origen = $_POST['origen'];
$monto = $_POST['monto'];
$fecha = date('Y-m-d');
$montodolares="0";
$montocripto="0";
$tasadolar="0";
$tasadolarcripto="0";


$query = "INSERT INTO giftcards (id, card, nombres, apellidos, telefono, email, saldo, moneda, fechacompra, status, socio, id_socio, id_proveedor, hash) VALUES (NULL, '".$card."','".$nombres."','".$apellidos."','".$telefono."','".$email."',".$monto.",'".$moneda."','".$fecha."','".$status."',".$xprop.",".$idsocio.",".$idproveedor.",'".$hash."')";

$SQL = "INSERT INTO giftcards_transacciones (id, idsocio, fecha, tipotransaccion, tipomoneda, montobs, montodolares, montocripto, tasadolarbs, tasadolarcripto, documento, origen) VALUES (NULL, '".$idsocio."','".$fecha."','".$tipotransaccion."','".$moneda."','".$monto."','".$montodolares."','".$montocripto."','".$tasadolar."','".$tasadolarcripto."','".$documento."','".$origen."')";


if (!mysqli_query($link, $query)) $response = '{"exito":"NO","mensaje":"Error al agregar una GiftCard"}';
if (!mysqli_query($link, $SQL)) return '{"exito":"NO","mensaje":"Error al agregar transacciones a la tarjeta"}';
$response = '{"exito":"SI","mensaje":"Exitoso"}';
echo $response;
?>