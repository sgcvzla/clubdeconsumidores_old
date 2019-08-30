<?php 
// session_start();
header('Content-Type: application/json');
include_once("../_config/conexion.php");

$query = "SELECT * from socios where id=".$_GET["idsocio"];
$result = mysqli_query($link, $query);
$respuesta = '{"socio":';
if ($row = mysqli_fetch_array($result)) {
	$respuesta .= '"'.trim($row["nombres"]).' '.trim($row["apellidos"]).'"';
}
$respuesta .= ',';

$query = "SELECT distinct proveedores.id,proveedores.nombre from cupones left outer join proveedores on cupones.id_proveedor=proveedores.id where cupones.status='Generado' and cupones.id_socio=".$_GET["idsocio"]." order by nombre";
$result = mysqli_query($link, $query);
$respuesta .= '"proveedores":[';
$first = true;
while ($row = mysqli_fetch_array($result)) {
	if ($first) {
		$coma = "";
		$first = false;
	} else {
		$coma = ",";
	}
	$respuesta .= $coma.'{"id_proveedor":'.$row["id"].',"nombreproveedor":"'.$row["nombre"].'"}';
}
$respuesta .= ']';


$query = "SELECT * from cupones  where cupones.status='Generado' and cupones.id_socio=".$_GET["idsocio"]." order by tipopremio,montopremio,descpremio";
$result = mysqli_query($link, $query);
$respuesta .= ',"premios":[';
while ($row = mysqli_fetch_array($result)) {
	$premio = "";
	switch ($row["tipopremio"]) {
		case 'monto':
			$premio = 'Bs. '.number_format($row["montopremio"],2,',','.').' de descuento';
			break;
		case 'porcentaje':
			$premio = number_format($row["montopremio"],0,',','.').'% de descuento';
			break;
		case 'producto':
			$premio = $row["descpremio"];
			break;
	}
	$premios[] = $premio;
}
$first = true;
for ($i=0; $i < count($premios); $i++) { 
	if ($first) {
		$coma = "";
		$first = false;
	} else {
		$coma = ",";
	}
	$agregar = true;
	for ($j=0; $j < $i; $j++) { 
		if ($premios[$i]==$premios[$j]) {
			$agregar = false;
			break;
		}
	}
	if ($agregar) {
		$respuesta .= $coma.'{"premio":"'.$premios[$i].'"}';
	}
}
$respuesta .= ']';

$query = "SELECT cupones.cuponlargo,cupones.tipopremio,cupones.montopremio,cupones.descpremio,cupones.fechavencimiento,proveedores.nombre from cupones left outer join proveedores on cupones.id_proveedor=proveedores.id where cupones.status='Generado' and cupones.id_socio=".$_GET["idsocio"];
$result = mysqli_query($link, $query);
$respuesta .= ',"cupones":[';
$first = true;
while ($row = mysqli_fetch_array($result)) {
	if ($first) {
		$coma = "";
		$first = false;
	} else {
		$coma = ",";
	}
	$premio = "";
	switch ($row["tipopremio"]) {
		case 'monto':
			$premio = 'Bs. '.number_format($row["montopremio"],2,',','.').' de descuento';
			break;
		case 'porcentaje':
			$premio = number_format($row["montopremio"],0,',','.').'% de descuento';
			break;
		case 'producto':
			$premio = $row["descpremio"];
			break;
	}
	$fechavencimiento = substr($row["fechavencimiento"],8,2).'/'.substr($row["fechavencimiento"],5,2).'/'.substr($row["fechavencimiento"],0,4);

	$respuesta .= $coma.'{"cuponlargo":"'.$row["cuponlargo"].'","premio":"'.$premio.'","fechavencimiento":"'.$fechavencimiento.'","nombreproveedor":"'.$row["nombre"].'"}';
}
$respuesta .= ']}';

echo $respuesta;
?>
