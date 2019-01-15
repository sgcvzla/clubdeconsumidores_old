<?php 
// session_start();
header('Content-Type: application/json');
include_once("../config/conexion.php");

$ticket = $_GET["ticket"];

$destip = '';
$desimp = '';
$query = "SELECT * from tickets where ticket=".$ticket;
$result = mysqli_query($link,$query);
if ($row = mysqli_fetch_array($result)) {
	$status = $row["status"];
	if ($status=="Pendiente") {
		$cliente = $row["cliente"];
		$sistema = $row["sistema"];
		$modulo = $row["modulo"];
		$tipo = $row["tipo"];
		switch ($tipo) {
			case 'comen':
				$destip = 'Comentario';
				break;
			case 'falla':
				$destip = 'Reporte de falla';
				break;
			case 'mejor':
				$destip = 'Oportunidad de mejora';
				break;
			case 'nuevo':
				$destip = 'Nuevo requerimiento';
				break;
		}
		$detalles = $row["detalles"];
		$impacto = $row["impacto"];
		switch ($impacto) {
			case 'alto':
				$desimp = 'Impacto alto (puede ocasionar perjuicio)';
				break;
			case 'bajo':
				$desimp = 'No tiene impacto significativo';
				break;
			case 'medio':
				$desimp = 'Impacto medio (no compromete el negocio)';
				break;
		}
		$nombre = $row["nombre"];
		$email = $row["email"];
		$telefono = $row["telefono"];
		$fechaticket = $row["fechaticket"];
		$severidad = $row["severidad"];
		$status = $row["status"];
		$respuesta = '{"exito":"SI","ticket":'.'{"cliente":"'.$cliente.'","sistema":"'.$sistema.'","modulo":"'.$modulo.'","tipo":"'.$destip.'","detalles":"'.$detalles.'","impacto":"'.$desimp.'","nombre":"'.$nombre.'","email":"'.$email.'","telefono":"'.$telefono.'","fechaticket":"'.$fechaticket.'","severidad":'.$severidad.',"status":"'.$status.'"},"consultores":[';

		$quer2 = "SELECT * from consultores";
		$resul2 = mysqli_query($link,$quer2);
		$first = true;
		$coma = '';
		while($ro2 = mysqli_fetch_array($resul2)) {
			if ($first) {
				$coma = '';
				$first = false;
			} else {
				$coma = ',';
			}
			$respuesta .= $coma.'{"id":"'.$ro2["id"].'","nombre":"'.utf8_encode($ro2["nombre"]).'"}';
		}
		$respuesta .= ']}';
	} else {
		$respuesta = '{"exito":"NO","ticket":{},"consultores":[]}';
	}
} else {
	$respuesta = '{"exito":"NO","ticket":{},"consultores":[]}';
}
echo $respuesta;
?>
