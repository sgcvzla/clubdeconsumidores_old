<?php 
// session_start();
//header('Content-Type: application/json');
include_once("../config/conexion.php");

$destip = '';
$desimp = '';
$query = "SELECT * from tickets order by status,ticket";
$result = mysqli_query($link,$query);
echo '<table border="1">
    <tr>
        <th>#</th>
        <th>Status</th>
        <th>Cliente</th>
        <th>Sistema</th>
        <th>Módulo</th>
        <th>Tipo</th>
        <th>Detalles</th>
        <th>Impacto</th>
        <th>Reportó</th>
        <th>email</th>
        <th>Teléfono</th>
        <th>Fecha ticket</th>
        <th>Severidad</th>
        <th>Asignado a</th>
    </tr>';
while($row = mysqli_fetch_array($result)) {
	$status = $row["status"];
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

    $quer2 = "SELECT * from consultores";
	$resul2 = mysqli_query($link,$quer2);
    $ro2 = mysqli_fetch_array($resul2);
    $asignado = $ro2["nombre"];
    echo '<tr>
            <td>'.$row["ticket"].'</td>
            <td>'.$row["status"].'</td>
            <td>'.$row["cliente"].'</td>
            <td>'.$row["sistema"].'</td>
            <td>'.$row["modulo"].'</td>
            <td>'.$destip.'</td>
            <td>'.$row["detalles"].'</td>
            <td>'.$desimp.'</td>
            <td>'.$row["nombre"].'</td>
            <td>'.$row["email"].'</td>
            <td>'.$row["telefono"].'</td>
            <td>'.$row["fechaticket"].'</td>
            <td>'.$row["severidad"].'</td>
            <td>'.$row["asignado"].'</td>
        </tr>';
}
echo '</table>';
?>
