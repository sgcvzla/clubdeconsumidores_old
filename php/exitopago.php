<?php 
include_once("conexion.php");
include_once("funciones.php");
session_start();

$query = "Select * from afiliados where tit_codigo='".trim($_SESSION["codigo"])."'";
$result = mysql_query($query,$link);
if ($row = mysql_fetch_array($result)) {
	if ($envio) {
		$direccion_envio = $row["direccion_envio"];
	} else {
		$direccion_envio = 'Calle '.trim($row["calle"]).',cruce '.trim($row["cruce"]).', casa No. '.trim($row["casa"]).', piso '.trim($row["piso"]).', apto. '.trim($row["apto"]).', sector '.trim($row["sector"]).', referencia '.trim($row["referencia"]).', parroquia '.trim($row["parroquia"]).', ciudad '.trim($row["ciudad"]).', municipio '.trim($row["municipio"]).', estado '.trim($row["estado"]).utf8_decode(', código postal ').trim($row["cod_postal"]).utf8_decode(', país ').trim($row["pais"]);
	}	
} 
$codigo = $_SESSION["codigo"];
$tipo_orden = 'Afiliado';
$patroc_codigo = $_SESSION["codigo"];
$fecha = date('Y-m-d H:i:s');
$fectr = date('Y-m-d');
$monto = $_SESSION["monto"]*(1+($_SESSION["iva2"]/100));
$valor_comisionable = $_SESSION["comisionable"];
$puntos = $_SESSION["puntos"];
$tipo = '06';

// REGISTRAR TRANSACCIÓN
$query = "INSERT INTO transacciones (fecha, afiliado, cliente, cliente_pref, tipo, precio, monto, puntos, valor_punto, documento, bancoorigen, status_comision, orden_id) VALUES ('".$fectr."','".$codigo."','','','".$tipo."',".$monto.", ".$valor_comisionable.",".$puntos.",".$_SESSION["valor_punto"].",'".$_GET["tk"]."','Pago flash','Conciliada',".$_GET["orden"].")";
echo $query;
echo '<br>';

if ($result = mysql_query($query,$link)) {
	$quer2 = "select id from transacciones where orden_id=".trim($_GET["orden"])." and fecha='".$fectr."'";
	echo $quer2;
	echo '<br>';

	$resul2 = mysql_query($quer2,$link);
	if ($row = mysql_fetch_array($resul2)) {
		$idtran = $row["id"];
	} else {
		$idtran = 0;
	}

	$quer4 = "select monto from ordenes WHERE orden_id=".trim($_GET["orden"]);
	echo $quer4;
	echo '<br>';
	$resul4 = mysql_query($quer4,$link);
	$saldo = ($ro4 = mysql_fetch_array($resul4)) ? $ro4["monto"] : $monto ;
	$saldo -= $monto;

	$quer3 = "UPDATE ordenes SET id_transaccion=".trim($idtran).",monto=".$saldo.",status_orden='Conciliada por despachar' WHERE orden_id=".trim($_GET["orden"]);
	echo $quer3;
	echo '<br>';

	$resul3 = mysql_query($quer3,$link);

// Registro de puntos y bonos

	// Buscar en la red de patrocinados la fecha de caducidad del bono de patrocinio
	$querx = "select * from patrocinio where tit_codigo='".$afiliado."'";
	$resulx = mysql_query($querx,$link);
	$rox = mysql_fetch_array($resulx);
	$fecha_afiliacion = $rox["fecha_afiliacion"];
	$fecha_fin_bono = $rox["fecha_fin_bono"];

	// Verifica si el bono está vigente
	if (date('Y-m-d')<=$fecha_fin_bono) {
		bono_patrocinio($link,$afiliado,$precio,$fecha_afiliacion,$fecha_fin_bono,$fecha,$idtran);
	} else {
		bono_unilevel($link,$afiliado,$fecha,$precio_orden,$precio,$idtran);
	}

	calificacion($afiliado,$_SESSION['pm'],$_SESSION['pmo'],$link);

	bono_de_reembolso($link,$afiliado,$fecha,$precio_orden,$precio,$puntos,$idtran);

	bono_aci_potencial($link,$afiliado,$_SESSION['pm'],$puntos,$orden_id,$fecha,$idtran);

	$_SESSION['pm'] += $puntos;


// HASTA AQUI

	$orden_id = $_GET["orden"];
	$query = "SELECT * from afiliados WHERE tit_codigo='".trim($_SESSION["codigo"])."'";
	$result = mysql_query($query,$link);
	$row = mysql_fetch_array($result);
	$cliente = trim($row["tit_nombres"]).' '.trim($row["tit_apellidos"]);
	$cedula = $row["tit_cedula"];
	$telefono = trim($row["tel_local"]).' / '.trim($row["tel_celular"]);
	$direccion = 'Calle '.trim($row["calle"]).',cruce '.trim($row["cruce"]).', casa No. '.trim($row["casa"]).', piso '.trim($row["piso"]).', apto. '.trim($row["apto"]).', sector '.trim($row["sector"]).', referencia '.trim($row["referencia"]).', parroquia '.trim($row["parroquia"]).', ciudad '.trim($row["ciudad"]).', municipio '.trim($row["municipio"]).', estado '.trim($row["estado"]).utf8_decode(', código postal ').trim($row["cod_postal"]).utf8_decode(', país ').trim($row["pais"]);
	$direccion_envio = $direccion_envio;
	$_SESSION["direccion_envio"] = $direccion_envio;

	$mensaje = '';
	$mensaje .= '<b>'.utf8_decode('Número de Orden: ').trim($orden_id).'</b><br>';
	$mensaje .= '<b>Cliente: '.trim($codigo).' - '.utf8_decode(trim($cliente)).'</b>, C.I. '.number_format($cedula,0,',','.').'<br>';
	$mensaje .= '<b>'.utf8_decode('Teléfono: ').'</b>'.trim($telefono).'<br>';
	$mensaje .= '<b>'.utf8_decode('Dirección: ').'</b>'.trim($direccion).'<br><br>';
	$mensaje .= '<b>Enviar a: </b>'.trim($direccion_envio).'<br><br>';
	$mensaje .= '<b>'.utf8_decode('Puntos en esta órden: ').number_format($puntos,0,',','.').'</b><br><br>';
	$mensaje .= '<b>'.utf8_decode('Cancelada con tarjeta de crédito.').'</b><br>';
	$mensaje .= '<b>'.utf8_decode('Número de token: ').$_GET["tk"].'</b><br>';
	$mensaje .= '<b>'.utf8_decode('Número de transacción: ').$idtran.'</b><br><br>';
	$mensaje .= '<table border="1" width="auto">';
		$mensaje .= '<tr>';
			$mensaje .= '<th align="center" width="380px">'.utf8_decode('Descripción').'</th>';
			$mensaje .= '<th align="center" width="100px">Cantidad</th>';
			$mensaje .= '<th align="center" width="105px">Precio</th>';
			$mensaje .= '<th align="center" width="120px">A pagar</th>';
		$mensaje .= '</tr>';
		$subtotal = 0.00;
		foreach ($_SESSION["orden"] as $prod => $value) {
			$query = "SELECT * FROM productos where id_pro='".$prod."'";
			$result = mysql_query($query,$link);
			if ($row = mysql_fetch_array($result)) {
				$id_pro = $row["id_pro"];
				$desc_corta = utf8_decode($row["desc_corta"]);
				$precio_pro = $row["pvp_dist"]/round(1+($_SESSION["iva1"]/100),2);
				$valor_comisionable_pro = $row["com_dist"];
				$puntos_pro = $row["pts_dist"];
				$_SESSION["precio_pro"][$prod] = $precio_pro;
				$mensaje .= '<tr>';
					$mensaje .= '<td align="left" width="380px">'.trim($id_pro).' - '.utf8_encode(trim($desc_corta)).'</td>';
					$mensaje .= '<td align="center" width="100px">'.number_format($_SESSION["orden"][$prod],0,',','.').'</td>';
					$mensaje .= '<td align="right" width="105px">Bs. '.number_format($precio_pro,2,',','.').'</td>';
					$mensaje .= '<td align="right" width="120px">Bs. '.number_format($_SESSION["orden"][$prod]*$precio_pro,2,',','.').'</td>';
					$subtotal += $_SESSION["orden"][$prod]*$precio_pro;
				$mensaje .= '</tr>';
			}
		}
		$mensaje .= '<tr>';
			$mensaje .= '<td> </td>';
			$mensaje .= '<td> </td>';
			$mensaje .= '<td align="right" style="padding:2%;"><b>SUBTOTAL</b></td>';
			$mensaje .= '<td align="right"><b>Bs. '.number_format($subtotal,2,',','.').'</b></td>';
		$mensaje .= '</tr>';
		$mensaje .= '<tr>';
			$mensaje .= '<td> </td>';
			$mensaje .= '<td> </td>';
			$mensaje .= '<td align="right" style="padding:2%;"><b>I.V.A. '.number_format($_SESSION["iva1"],2,',','.').'% (*)</b></td>';
			$mensaje .= '<td align="right"><b>Bs. '.number_format($subtotal*$_SESSION["iva1"]/100,2,',','.').'</b></td>';
		$mensaje .= '</tr>';
		$mensaje .= '<tr>';
			$mensaje .= '<td> </td>';
			$mensaje .= '<td> </td>';
			$mensaje .= '<td align="right" style="padding:2%;"><b>TOTAL ORDEN</b></td>';
			$mensaje .= '<td align="right"><b>Bs. '.number_format($subtotal+($subtotal*$_SESSION["iva1"]/100),2,',','.').'</b></td>';
		$mensaje .= '</tr>';
	$mensaje .= '</table>';
	$asunto = "Orden de pedido No.: ".trim($orden_id);
	$cabeceras = 'Content-type: text/html;';
	if (strpos($_SERVER["HTTP_HOST"],'localhost')===FALSE) {	           	
		mail("ordenesmanna@gmail.com",$asunto,$mensaje,$cabeceras);
		mail("soluciones2000@gmail.com",$asunto,$mensaje,$cabeceras);
		mail($_SESSION["email"],$asunto,$mensaje,$cabeceras);
	}
	unset($_SESSION["orden"]);
	unset($_SESSION["precio_pro"]);
	unset($_SESSION["valor_comisionable_pro"]);
	unset($_SESSION["puntos_pro"]);
	unset($_SESSION["email"]);

	$_SESSION['cantidad'] = 0;
	$_SESSION["monto"] = 0.00;
	$_SESSION["comisionable"] = 0.00;
	$_SESSION["puntos"] = 0;
	$cadena = 'exito.php?orden='.trim($orden_id); 
} else {
	$mensaje = "error 4";
	$cadena = 'error.php?error='.$mensaje; 
}

echo '
<script>
	parent.opener.location.assign("'.$cadena.'");
	window.close();
</script>
';
?>
