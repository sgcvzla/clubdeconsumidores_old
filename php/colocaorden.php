<?php 
header('Content-Type: application/json');
include_once("../_config/conexion.php");
$exito = false;

$json = (isset($_POST['orden'])) ? $_POST['orden'] : '' ;

$aorden = json_decode($json,true);
$id_cliente = 1;
$direccion_envio = "Dirección de envío";

$monto_orden = 0.00;

$iva = 16;
// $querx = "Select iva1 from empresa";
// $resulx = mysql_query($querx,$link);
// $rox = mysql_fetch_array($resulx);
// $_SESSION["iva1"] = $rox["iva1"];
$pi = $iva/100; 

foreach ($aorden as $key => $value) {
    $iv = $value['precio_pro']*$pi;
    $pr = $value['precio_pro']+$iv;
    $tp = $value['cantidad']*$pr;
	$monto_orden += $tp;
}

// Busca el próximo número de orden
$querx = "select auto_increment from information_schema.tables where table_schema='clubdeconsumidores' and table_name='ordenes'";
$resulx = mysqli_query($link,$querx);
if($rox = mysqli_fetch_array($resulx)) {
	$orden_id = $rox["auto_increment"];
} else {
	$orden_id = 0;
}

$fecha = date('Y-m-d');

$query = "INSERT INTO ordenes (id, id_cliente, fecha, monto, saldo, direccion_envio, status_orden) VALUES (".$orden_id.",".$id_cliente.",'".$fecha."',".$monto_orden.",".$monto_orden.",'".$direccion_envio."','Pendiente')";
if ($result = mysql_query($query,$link)) {
	$exito = true;
	foreach ($aorden as $key => $value) {
	    $iv = $value['precio_pro']*$pi;
    	$pr = $value['precio_pro']+$iv;
		$tp = $value['cantidad']*$pr;
		$query = "INSERT INTO det_orden (orden_id, id_pro, cantidad, precio) VALUES (".$orden_id.",'".$value["id_pro"]."',".$value["cantidad"].",".$pr.")";
		if ($result = mysql_query($query,$link)) {
			$exito = true;
		} else {
			$exito = false;
			break;
		}
	}
	if ($exito) {
// 				$mensaje = '';
// 				$mensaje .= '<b>'.utf8_decode('Número de Orden: ').trim($orden_id).'</b><br>';
// 				$mensaje .= '<b>Cliente: '.trim($codigo).' - '.utf8_decode(trim($cliente)).'</b>, C.I. '.number_format($cedula,0,',','.').'<br>';
// 				$mensaje .= '<b>'.utf8_decode('Teléfono: ').'</b>'.trim($telefono).'<br>';
// 				$mensaje .= '<b>'.utf8_decode('Dirección: ').'</b>'.trim($direccion).'<br><br>';
// 				$mensaje .= '<b>Enviar a: </b>'.trim($direccion_envio).'<br><br>';
// 				$mensaje .= utf8_decode('Al cancelar esta orden se sumarán ').'<b>'.number_format($totpuntos,0,',','.').' puntos</b> personales.<br><br>';
// 				$mensaje .= '<table border="1" width="auto">';
// 					$mensaje .= '<tr>';
// 						$mensaje .= '<th align="center" width="380px">'.utf8_decode('Descripción').'</th>';
// 						$mensaje .= '<th align="center" width="100px">Cantidad</th>';
// 						$mensaje .= '<th align="center" width="105px">Precio</th>';
// 						$mensaje .= '<th align="center" width="120px">A pagar</th>';
// 					$mensaje .= '</tr>';
// 					$subtotal = 0.00;
// 					foreach ($aorden as $key => $value) {
// 						$query = "SELECT * FROM productos where id_pro='".$value["id_pro"]."'";
// 						$result = mysql_query($query,$link);
// 						if ($row = mysql_fetch_array($result)) {
// 							$id_pro = $row["id_pro"];
// 							$desc_corta = utf8_decode($row["desc_corta"]);
// 							$precio_pro = $row["pvp_dist"]/round(1+($_SESSION["iva1"]/100),2);
// 							$valor_comisionable_pro = $row["com_dist"];
// 							$puntos_pro = $row["pts_dist"];
// 							$mensaje .= '<tr>';
// 								$mensaje .= '<td align="left" width="380px">'.trim($id_pro).' - '.utf8_encode(trim($desc_corta)).'</td>';
// 								$mensaje .= '<td align="center" width="100px">'.number_format($value["cantidad"],0,',','.').'</td>';
// 								$mensaje .= '<td align="right" width="105px">Bs. '.number_format($value["pvp_dist"],2,',','.').'</td>';
// 								$mensaje .= '<td align="right" width="120px">Bs. '.number_format($value["pvp_dist"]*$value["cantidad"],2,',','.').'</td>';
// 								$subtotal += $value["pvp_dist"]*$value["cantidad"];
// 							$mensaje .= '</tr>';
// 						}
// 					}
// 					$mensaje .= '<tr>';
// 						//$mensaje .= '<td> </td>';
// 						//$mensaje .= '<td> </td>';
// 						$mensaje .= '<td colspan="3" align="right" style="padding-right:2%;"><b>SUBTOTAL</b></td>';
// 						$mensaje .= '<td align="right"><b>Bs. '.number_format($subtotal,2,',','.').'</b></td>';
// 					$mensaje .= '</tr>';
// 					$mensaje .= '<tr>';
// 						//$mensaje .= '<td> </td>';
// 						//$mensaje .= '<td> </td>';
// 						$mensaje .= '<td colspan="3" align="right" style="padding-right:2%;"><b>I.V.A. '.number_format($_SESSION["iva1"],2,',','.').'%</b></td>';
// 						$mensaje .= '<td align="right"><b>Bs. '.number_format($subtotal*$_SESSION["iva1"]/100,2,',','.').'</b></td>';
// 					$mensaje .= '</tr>';
// 					$mensaje .= '<tr>';
// 						//$mensaje .= '<td> </td>';
// 						//$mensaje .= '<td> </td>';
// 						$mensaje .= '<td colspan="3" align="right" style="padding-right:2%;"><b>TOTAL ORDEN</b></td>';
// 						$mensaje .= '<td align="right"><b>Bs. '.number_format($subtotal+($subtotal*$_SESSION["iva1"]/100),2,',','.').'</b></td>';
// 					$mensaje .= '</tr>';
// 				$mensaje .= '</table>';
// 				$asunto = "Orden de pedido No.: ".trim($orden_id);
// 				$cabeceras = 'Content-type: text/html;';
// 				if (strpos($_SERVER["SERVER_NAME"],'localhost')===FALSE) {	           	
// 					mail("ordenesmanna@gmail.com",$asunto,$mensaje,$cabeceras);
// 					mail("soluciones2000@gmail.com",$asunto,$mensaje,$cabeceras);
// 					mail($_SESSION["email"],$asunto,$mensaje,$cabeceras);
// 				}
// 			} else {
// 				$exito = false;
// 			}			
		// } else {
		// 	$exito = false;
		// }		
	} else {
		$exito = false;
	}
}

if ($exito) {
	$respuesta = '{"exito":"SI","orden_id":'.$orden_id.',"monto_orden":'.$monto_orden.'}';
} else {
	$respuesta = '{"exito":"NO","orden_id":0,"monto_orden":0}';
}

echo $respuesta;
?>
