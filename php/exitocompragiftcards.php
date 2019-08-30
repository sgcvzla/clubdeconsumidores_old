<?php
include_once("../_config/conexion.php");

$card = (isset($_GET['card'])) ? $_GET['card'] : '' ;

$query = "select * from giftcards where card='".$_GET["card"]."'";
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
	$idsocio = $row["id_socio"];
	$idproveedor = $row["id_proveedor"];
	$monto = $row["monto"];
	$moneda = $row["moneda"];
}

$status = 'Lista para usarse';
$hash = hash("sha256",$card.$idsocio.$idproveedor.$monto.$moneda.$status);

$query = "UPDATE giftcards SET status='".$status."',hash='".$hash."' WHERE card='".$_GET["card"]."'";
$result = mysqli_query($link,$query);

$cadena = '../giftcards/index.html'; 

echo '
<script>
	parent.opener.location.assign("'.$cadena.'");
	window.close();
</script>
';
?>
