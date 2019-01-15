<?php 
// session_start();
header('Content-Type: application/json');
include_once("../_config/conexion.php");

var_dump($_POST);
$query = "select * from socios where email='".$_POST['email']."'";
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
	$id=$row["id"];
} else {
	$query = "INSERT INTO socios (email,telefono,nombres,apellidos) VALUES ('".$_POST["email"]."','".$_POST["telefono"]."','".$_POST["nombres"]."','".$_POST["apellidos"]."')";
	$result = mysqli_query($link,$query);
	$query = "select * from socios where email='".$_POST['email']."'";
	$result = mysqli_query($link, $query);
	if ($row = mysqli_fetch_array($result)) {
		$id=$row["id"];
	}
}

$socio = ($_POST["socio"]=="true") ? 1 : 0 ;

$query = "select * from cupones where id_proveedor=".$_POST['id_proveedor']." and factura='" . $_POST['factura'] . "'";
echo $query;
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
	$respuesta = '{"exito":"NO","mensaje":"Falló el registro del cupón, \nEl número de factura ya esta registrado","cupon":"0"}';
} else {
	$query = "INSERT INTO cupones (cupon,id_proveedor,id_socio,status,factura,monto,tipopremio,premio,socio,email,telefono,nombres,apellidos) VALUES ('CUPON'," . $_POST['id_proveedor'] . "," . $id . ",'Generado','" . $_POST["factura"] . "'," . $_POST["monto"] . ",'Descuento',10," . $socio . ",'" . $_POST["email"] . "','" . $_POST["telefono"] . "','" . $_POST["nombres"] . "','" . $_POST["apellidos"] . "')";
	if ($result = mysqli_query($link, $query)) {
	// $cupon = $row["id"];
	// $mensaje = utf8_decode('Buen día,<br/><br/>');
	// $mensaje .= 'Se ha generado el siguiente ticket de soporte:<br/><br/>';
	// $mensaje .= '<b>Ticket: </b>'.$ticket.'<br/>';

	// $mensaje .= '<b>Cliente: </b>'.utf8_decode($_POST["cliente"]).'<br/>';
	// $mensaje .= '<b>Sistema: </b>'.utf8_decode($_POST["sistema"]).'<br/>';
	// $mensaje .= utf8_decode('<b>Módulo: </b>'.$_POST["modulo"].'<br/>');

	// $mensaje .= '<b>Tipo de solicitud: </b>'.$destip.'<br/>';
	// $mensaje .= '<b>Detalles: </b>'.utf8_decode($_POST["detalles"]).'<br/>';
	// $mensaje .= '<b>Impacto sobre el negocio: </b>'.$_POST["impacto"].'<br/>';
	// $mensaje .= utf8_decode('<b>Reportó: </b>'.$_POST["nombre"].'<br/>');
	// $mensaje .= '<b>email: </b><a href="mailto:'.$_POST["email"].'?subject=Respuesta al ticket No. '.$ticket.'">'.$_POST["email"].'</a><br/>';
	// $mensaje .= utf8_decode('<b>Teléfono: </b>'.$_POST["telefono"].'<br/>');
	// $desfecha = substr($fecha,8,2).'/'.substr($fecha,5,2).'/'.substr($fecha,0,4).' a las '.substr($fecha,11,5).' horas.';
	// $mensaje .= utf8_decode('<b>Fecha y hora en que se reportó: </b>'.$desfecha.'<br/>');
	// $mensaje .= '<b>Severidad asignada por el sistema: </b>'.$severidad.'<br/><br/>';

	// // if (strpos($_SERVER["SERVER_NAME"],'localhost')===FALSE) {	           	
	// // 	$mensaje .= '<a href="localhost/sgcnew/soporte/respuesta.html?ticket='.$ticket.'">Responder al ticket</a><br/><br/>';
	// // } else {
	// // 	$mensaje .= '<a href="https://www.sgc-consultores.com.ve/soporte/respuesta.html?ticket='.$ticket.'">Responder al ticket</a><br/><br/>';
	// // }

	// $asunto = "Ticket de soporte No.: ".$ticket;
	// $cabeceras = 'Content-type: text/html;';
	// if (strpos($_SERVER["SERVER_NAME"],'localhost')===FALSE) {	           	
	// 	mail($correo,$asunto,$mensaje,$cabeceras);
	// }

		$respuesta = '{"exito":"SI","mensaje":"Cupón registrado exitosamente, \nPuede canjearlo en el punto de venta.","cupon":"CUPON"}';
	} else {
		$respuesta = '{"exito":"NO","mensaje":"Falló el registro del cupón, \ncomuniquese por Whatsapp al +584244071820","cupon":"0"}';
	}
}
echo $respuesta;
?>
