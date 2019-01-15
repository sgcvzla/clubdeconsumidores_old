<?php 
// session_start();
header('Content-Type: application/json');
include_once("../config/conexion.php");

$fechastatus = date('Y-m-d H:i:s');
$respuesta = '';

$ticket = $_POST["ticket"];
$idcons = $_POST["asignado"];

$query = "SELECT * from consultores where id=".$idcons;
$result = mysqli_query($link,$query);
$row = mysqli_fetch_array($result);
$nomasig = $row["nombre"];
$emailas = $row["email"];

$query = "update tickets set fechastatus='".$fechastatus."',prioridad= ".$_POST["prioridad"].",asignado='".$nomasig."',plandeaccion='".$_POST["plandeaccion"]."',status='Asignado' where ticket=".$ticket;
$result = mysqli_query($link,$query);

$query = "select * from tickets where ticket=".$ticket;
$result = mysqli_query($link,$query);
if ($row = mysqli_fetch_array($result)) {
    $mensaje = utf8_decode('Buen día ').$nomasig.',<br/><br/>';
    $mensaje .= 'Se le ha asignado el siguiente ticket de soporte:<br/><br/>';

    $mensaje .= '<b>Ticket: </b>'.$ticket.'<br/>';
    $mensaje .= '<b>Cliente: </b>'.utf8_decode($row["cliente"]).'<br/>';
    $mensaje .= '<b>Sistema: </b>'.utf8_decode($row["sistema"]).'<br/>';
    $mensaje .= utf8_decode('<b>Módulo: </b>'.$row["modulo"].'<br/>');

	$mensaje .= '<b>Detalles: </b>'.utf8_decode($row["detalles"]).'<br/>';
	$mensaje .= '<b>Impacto sobre el negocio: </b>'.$row["impacto"].'<br/>';
	$fecha = $row["fechaticket"];
	$desfecha = substr($fecha,8,2).'/'.substr($fecha,5,2).'/'.substr($fecha,0,4).' a las '.substr($fecha,11,5).' horas.';

	$mensaje .= utf8_decode('<b>Fecha y hora en que se reportó: </b>'.$desfecha.'<br/>');
	$x = $_POST["severidad"];
	switch ($x) {
		case '3':
			$plazo = '5 días hábiles';
			break;
		case '2':
			$plazo = '3 días hábiles';
			break;
		case '1':
			$plazo = '1 día hábil';
			break;
		default:
			$plazo = '5 días hábiles';
			break;
	}
	$mensaje .= '<b>Severidad asignada por el sistema: </b>'.$row["severidad"].'<br/>';
	$mensaje .= utf8_decode('<b>Severidad asignada por soporte técnico: </b>').$_POST["severidad"].'<br/>';
	$mensaje .= utf8_decode('<b>Plazo estimado para la solución: </b>'.$plazo.'<br/>');

	$mensaje .= '<b>Prioridad: </b>'.$_POST["prioridad"].'<br/><br/>';

    $mensaje .= '<b>Respuesta ofrecido al usuario: </b>'.utf8_decode($_POST["respuesta"]).'<br/>';
	$mensaje .= utf8_decode('<b>Plan de acción: </b>'.$_POST["plandeaccion"].'<br/><br/>');

	// if (strpos($_SERVER["SERVER_NAME"],'localhost')===FALSE) {	           	
	// 	$mensaje .= '<a href="localhost/sgcnew/soporte/respuesta.html?ticket='.$ticket.'">Responder al ticket</a><br/><br/>';
	// } else {
	// 	$mensaje .= '<a href="https://www.sgc-consultores.com.ve/soporte/respuesta.html?ticket='.$ticket.'">Responder al ticket</a><br/><br/>';
	// }

	$asunto = utf8_decode("Asignación de ticket de soporte No.: ".$ticket);
	$cabeceras = 'Content-type: text/html;';
	// if (strpos($_SERVER["SERVER_NAME"],'localhost')===FALSE) {	           	
		mail($emailas,$asunto,$mensaje,$cabeceras);
	// }
//////////////////////////////////////////////
    $mensaje = utf8_decode('Buen día '.$row["nombre"].',<br/><br/>');
    $mensaje .= 'Su ticket de soporte ha recibido una respuesta:<br/><br/>';

	$mensaje .= '<b>Los detalles ofrecidos por usted fueron: </b>'.utf8_decode($row["detalles"]).'<br/>';
	$mensaje .= utf8_decode('<b>Fecha y hora en que se reportó: </b>'.$desfecha.'<br/>');
    $mensaje .= '<b>La respuesta a su planteamiento: </b>'.utf8_decode($_POST["respuesta"]).'<br/><br/>';

    // $mensaje .= '<b>Severidad asignada por el sistema: </b>'.$row["severidad"].'<br/>';
	$mensaje .= utf8_decode('<b>Severidad asignada por soporte técnico: </b>').$_POST["severidad"].'<br/>';
	$mensaje .= utf8_decode('<b>Plazo estimado para la solución: </b>'.$plazo.'<br/>');
	$mensaje .= '<b>Prioridad: </b>'.$_POST["prioridad"].'<br/><br/>';

	$mensaje .= utf8_decode('<b>El plan de acción a seguir es el siguiente: </b>'.$_POST["plandeaccion"].'<br/><br/>');

	$desfechastatus = substr($fechastatus,8,2).'/'.substr($fechastatus,5,2).'/'.substr($fechastatus,0,4).' a las '.substr($fechastatus,11,5).' horas.';
	$mensaje .= '<b>Fecha y hora de la respuesta: </b>'.$desfechastatus.'<br/><br/>';

	// if (strpos($_SERVER["SERVER_NAME"],'localhost')===FALSE) {	           	
	// 	$mensaje .= '<a href="localhost/sgcnew/soporte/respuesta.html?ticket='.$ticket.'">Responder al ticket</a><br/><br/>';
	// } else {
	// 	$mensaje .= '<a href="https://www.sgc-consultores.com.ve/soporte/respuesta.html?ticket='.$ticket.'">Responder al ticket</a><br/><br/>';
	// }

	$asunto = "Respuesta al ticket de soporte No.: ".$ticket;
	$cabeceras = 'Content-type: text/html;';
	// if (strpos($_SERVER["SERVER_NAME"],'localhost')===FALSE) {	           	
		mail($row["email"],$asunto,$mensaje,$cabeceras);
	// }
//////////////////////////////////////////////
	$query = "INSERT INTO historial (ticket, detalles, fechastatus) VALUES (".$ticket.",'Respuesta: ".$row["detalles"]."','".$fecha."')";
	$result = mysqli_query($link,$query);

	$respuesta = '{"exito":"SI","ticket":'.$row["ticket"].'}';
} else {
	$respuesta = '{"exito":"NO","ticket":0}';
}
echo $respuesta;
?>
