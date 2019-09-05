<?php
header('Content-Type: application/json');
include_once("../_config/conexion.php");
include_once("funciones.php");
include_once("../lib/phpqrcode/qrlib.php");

$query = 'select * from paises where id='.$_POST['pais'].';';
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) { $nombre_pais=$row["pais"]; } else { $nombre_pais=""; }

$query = 'select * from estados where id='.$_POST['estado'].';';
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) { $nombre_estado=$row["estado"]; } else { $nombre_estado=""; }

$query = 'select * from ciudades where id='.$_POST['ciudad'].';';
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) { $nombre_ciudad=$row["ciudad"]; } else { $nombre_ciudad=""; }

$archivojson = "../registro/registro.json";
$socio = 1;

$status="No existe";

$query = 'select * from socios where id='.$_POST['id_socio'].';';
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
	$status=$row["status"];
	$email=$row["email"];
	$telefono=$row["telefono"];
	$nombres=$row["nombres"];
	$apellidos=$row["apellidos"];
} else {
	$status="No existe";
}

if ($status=="Pendiente") {

	$objetojson = json_decode(file_get_contents($archivojson),true);

	$query = 'update socios set status="Activo",';
	$coma = ',';
	for ($i=0; $i < count($objetojson["campos"]); $i++) {
		if ($objetojson["campos"][$i]["nombre"]!="id_proveedor" && $objetojson["campos"][$i]["nombre"]!="id_socio") {
			$coma = ($i<count($objetojson["campos"])-3) ? ',' : '' ;
			if ($objetojson["campos"][$i]["tipo"]=="number" || $objetojson["campos"][$i]["tipo"]=="boolean") {
				$query .= $objetojson["campos"][$i]["nombre"].'='.$_POST[$objetojson["campos"][$i]["nombre"]].$coma;
			} else {
				$query .= $objetojson["campos"][$i]["nombre"].'="'.$_POST[$objetojson["campos"][$i]["nombre"]].'"'.$coma;
			}
		} 
	}
	$query .= ',nombre_pais="'.$nombre_pais.'",nombre_estado="'.$nombre_estado.'",nombre_ciudad="'.$nombre_ciudad.'",fecha_afiliacion="'.date('Y-m-d').'" where id='.$_POST['id_socio'].';';
	// echo $query;
	if ($result = mysqli_query($link, $query)) {

		generarprepago($link,$socio,$email,$telefono,$nombres,$apellidos);

		$respuesta = '{"exito":"SI","mensaje":' . mensajes($archivojson,"exitoregistro") . '}';
		cupondebienvenida($link,$socio,$email,$telefono,$nombres,$apellidos,$archivojson);
	} else {
		$respuesta = '{"exito":"NO","mensaje":' . mensajes($archivojson,"fallaregistro") . '}';
	}
} else {
	if ($status=="Activo") {
		$respuesta = '{"exito":"NO","mensaje":' . mensajes($archivojson,"yaregistrado") . '}';
	} else {
		if ($status=="Inactivo") {
			$respuesta = '{"exito":"NO","mensaje":' . mensajes($archivojson,"socioinactivo") . '}';
		} else {
			if ($status=="Suspendido") {
				$respuesta = '{"exito":"NO","mensaje":' . mensajes($archivojson,"sociosuspendido") . '}';
			} else {
				$respuesta = '{"exito":"NO","mensaje":' . mensajes($archivojson,"socionoexiste") . '}';
			}
		}
	}
}
echo $respuesta;

function cupondebienvenida($link,$socio,$email,$telefono,$nombres,$apellidos,$archivojson) {
	// Buscar datos de proveedor
	$query = "select * from proveedores where id=".$_POST['id_proveedor'];
	// $query = "select * from proveedores where id=1";
	$result = mysqli_query($link, $query);
	if ($row = mysqli_fetch_array($result)) {
		$nombreproveedor=$row["nombre"];
	}

	// Buscar premio activo
	$query = "select * from premios where id_proveedor=".$_POST['id_proveedor'] . " and clasepremio='bienvenida' and activo=1";
	// $query = "select * from premios where id_proveedor=1 and activo=1";
	$result = mysqli_query($link, $query);
	if ($row = mysqli_fetch_array($result)) {
		$id_premio=$row["id"];
		$tipopremio=$row["tipopremio"];
		$montopremio=$row["montopremio"];
		$descpremio=$row["descpremio"];
		$diasvalidez=$row["diasvalidez"];
	}

	// Asignar el número de cupón
	$query = "select max(cupon) as ultcupon from cupones";
	$result = mysqli_query($link, $query);
	if ($row = mysqli_fetch_array($result)) {
		if (strlen($row["ultcupon"])==0) {
			$numcupon = asignacodigo('0000000000');
			$cuponlargo = asignacodigolargo2($numcupon,$email,$nombres,$apellidos,$telefono);
		} else {
			$numcupon = asignacodigo($row["ultcupon"]);
			$cuponlargo = asignacodigolargo2($numcupon,$email,$nombres,$apellidos,$telefono);
		}
	}

	// Verificar si ya existe el cupón, si existe responder, si no, agregar y responder 
	$query = "select * from cupones where id_socio=".$_POST['id_socio']." and factura='00000'";
	// $query = "select * from cupones where id_proveedor=1 and factura='8888888'";
	$result = mysqli_query($link, $query);
	if ($row = mysqli_fetch_array($result)) {
		$respuesta = '{"exito":"NO","mensaje":'. mensajes($archivojson,"cuponyaregistrado") .',"cupon":"0"}';
	} else {
		$fechacupon = date ('Y-m-d');
		$fechavencimiento = strtotime('+'.$diasvalidez.' days', strtotime ($fechacupon));
		$fechavencimiento = date ('Y-m-d' , $fechavencimiento);
		$fechavencstr = substr($fechavencimiento,8,2).'/'.substr($fechavencimiento,5,2).'/'.substr($fechavencimiento,0,4);

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
		$hash = hash("sha256",$numcupon.$_POST['id_proveedor']. $id.$tipopremio.$montopremio.$descpremio."Generado");

		$query = "INSERT INTO cupones (cupon,cuponlargo,id_proveedor,id_socio,status,factura,monto,id_premio,tipopremio,montopremio,descpremio,socio,email,telefono,nombres,apellidos,fechacupon,fechavencimiento,hash) VALUES ('".$numcupon."','".$cuponlargo."'," . $_POST['id_proveedor'] . "," . $_POST['id_socio'] . ",'Generado','00000',0,".$id_premio.",'".$tipopremio."',".$montopremio.",'Bienvenida'," . $socio . ",'" . $email . "','" . $telefono . "','" . $nombres . "','" . $apellidos . "','".$fechacupon."','".$fechavencimiento."','".$hash."')";

		if ($result = mysqli_query($link, $query)) {

			$correo = $email;

			$mensaje = utf8_decode('Hola '.trim($nombres).',<br/><br/>');
			$mensaje .= utf8_decode('¡Bienvenido a tu club!<br/><br/>');

			$mensaje .= utf8_decode('Queremos darte un obsequio de bienvenida, ');
			$mensaje .= utf8_decode('la próxima que visites <b>'.trim($nombreproveedor).'</b> podrás reclamar el siguiente premio:'.'<br/><br/>');
			switch ($tipopremio) {
				case 'porcentaje':
					$mensaje .= utf8_decode('<h3 style="text-align:center;"><b>'.number_format($montopremio,2,',','.').'% de descuento sobre el monto total de tu factura.</b></h3>');
					break;
				case 'monto':
					$mensaje .= utf8_decode('<h3 style="text-align:center;"><b>'.number_format($montopremio,2,',','.').' Bs. de descuento en sobre el monto total de tu factura.</b></h3>');
					break;
				case 'producto':
					$mensaje .= utf8_decode('<h3 style="text-align:center;"><b>'.trim($descpremio).'.</b></h3>');
					break;
				default:
					$mensaje .= utf8_decode('<h3 style="text-align:center;"><b>Premio especial sorpresa.</b></h3>');
					break;
			}

			$mensaje .= utf8_decode('Este premio podrás reclamarlo cualquier día, siempre que sea antes del <b>'.$fechavencstr.'</b>.<br/><br/>');
			$mensaje .= utf8_decode('Sólo debes presentar este correo electrónico o indicar el siguiente código:'.'<br/>');
			$mensaje .= utf8_decode('<h2 style="text-align:center"><b>'.$cuponlargo.'</b></h2>');

			// codigo de barras
			$mensaje .= '<p style="text-align:center;">';
				$mensaje .= '<img src="https://www.clubdeconsumidores.com.ve/php/barcode.php?';
				$mensaje .= 'text='.$cuponlargo;
				$mensaje .= '&size=50';
				$mensaje .= '&orientation=horizontal';
				$mensaje .= '&codetype=Code39';
				$mensaje .= '&print=true';
				$mensaje .= '&sizefactor=1" />';
			$mensaje .= '</p>';

			// código qr
			$mensaje .= utf8_decode('<p style="text-align:center;">Para canjear desde el móvil:</p>');

	//		$dir = 'https://www.clubdeconsumidores.com.ve/php/temp/';
	//		if(!file_exists($dir)) mkdir($dir);
			$ruta = 'https://www.clubdeconsumidores.com.ve/php/';
			$dir = 'qr/';
			if(!file_exists($dir)) mkdir($dir);

	//		$filename = $dir.'test.png';
			$tamanio = 5;
			$level = 'H';
			$frameSize = 1;
	//		$contenido = $cuponlargo;
	//		$contenido = '{"id_proveedor":'.$_POST['id_proveedor'].',"cupon":"'.$cuponlargo.'"}';
			$contenido = 'https://www.clubdeconsumidores.com.ve/canje/canje.html?cJson={"id_proveedor":'.$_POST['id_proveedor'].',"cupon":"'.$cuponlargo.'"}';

	//		QRcode::png($contenido, $filename, $level, $tamanio, $frameSize);
			QRcode::png($contenido,$dir.$numcupon.'.png', $level, $tamanio, $frameSize);
			$mensaje .= '<p style="text-align:center;">';
				$mensaje .= '<img src="'.$ruta.$dir.$numcupon.'.png" height="200" width="200" />';
			$mensaje .= '</p>';
			// Hasta aqui
			$mensaje .= '<p style="text-align:center;">'.$hash.'</p>';

			$mensaje .= utf8_decode('¡Te esperamos!'.'<br/><br/>');

			$mensaje .= utf8_decode('Atentamente'.'<br/><br/>');
			$mensaje .= utf8_decode('Club de consumidores'.'<br/><br/>');

			$mensaje .= utf8_decode('<b>Nota:</b> Esta cuenta no es monitoreada, por favor no respondas este email, si deseas comunicarte con tu club escribe a: <b><a href="mailto:info@clubdeconsumidores.com.ve">info@clubdeconsumidores.com.ve</a></b>'.'<br/><br/>');

			// $mensaje .= $numcupon;

			$asunto = utf8_decode('Hola '.trim($nombres).', recibe este obsequio de bienvenida al club de consumidores.');
			$cabeceras = 'Content-type: text/html;';
	//		if (strpos($_SERVER["SERVER_NAME"],'localhost')===FALSE) {	           	
				// mail($correo,$asunto,$mensaje,$cabeceras);
	//		}

			$a = fopen('log.html','w+');
			fwrite($a,$asunto);
			fwrite($a,'-');
			fwrite($a,$mensaje);

			// $respuesta = '{"exito":"SI","mensaje":' . mensajes($archivojson,"exitoregistrocupon") . ',"cupon":"'.$numcupon.'"}';
	//		$respuesta = '{"exito":"SI","mensaje":' . mensajes($archivojson,"exitoregistrocupon") . ',"cupon":"'.$numcupon.'",';
	//		$respuesta .= '"contenido":'.$contenido.',"filename":"'.$filename.'"}';

		// } else {
			// $respuesta = '{"exito":"NO","mensaje":' . mensajes($archivojson,"fallaregistrocupon") . ',"cupon":"0"}';
		}
	}
	// echo $respuesta;
}

function generarprepago($link,$socio,$email,$telefono,$nombres,$apellidos) {
	$query = 'SELECT proveedores.id as idproveedor, proveedores.nombre, moneda FROM proveedores,_monedas';
	$result = mysqli_query($link, $query);
	while ($row = mysqli_fetch_array($result)) {
		$idproveedor = $row["idproveedor"];
		$nombreproveedor = $row["nombre"];
		$moneda = $row["moneda"];

		// Busca el próximo número de giftcard
		$quer0 = "select auto_increment from information_schema.tables where table_schema='clubdeconsumidores' and table_name='prepago'";
		$resul0 = mysqli_query($link,$quer0);
		if($ro0 = mysqli_fetch_array($resul0)) {
			$numgiftcard = $ro0["auto_increment"];
		} else {
			$numgiftcard = 0;
		}
		if ($numgiftcard > 9999) { $numgiftcard -= 9999; }
		if ($numgiftcard < 10) {
		    $txtgiftcard = "000".trim($numgiftcard);
		} elseif ($numgiftcard < 100) {
		    $txtgiftcard = "00".trim($numgiftcard);
		} elseif ($numgiftcard < 1000) {
		    $txtgiftcard = "0".trim($numgiftcard);
		} else {
		    $txtgiftcard = trim($numgiftcard);
		}

		$card = "";
	    $card .= generacodigo(substr($nombres,0,1),$link);
    	$card .= substr($txtgiftcard,0,1);

	    $card .= generacodigo(substr($apellidos,0,1),$link);
    	$card .= substr($txtgiftcard,1,1);

	    $card .= generacodigo(substr($telefono,strlen($telefono)-1,1),$link);
    	$card .= generacodigo(substr($email,0,1),$link);

	    $card .= substr($txtgiftcard,2,1);
    	$card .= generacodigo(substr($nombreproveedor,0,1),$link);

	    $card .= substr($txtgiftcard,3,1);
    	$card .= generacodigo(substr($moneda,0,1),$link);

		$fecha = date('Y-m-d');
		$status = 'Pendiente de pago';
		$monto = 0.00;
		$hash = hash("sha256",$card.$socio.$idproveedor.$monto.$moneda.$status);

		$quer2 = "INSERT INTO prepago (card, nombres, apellidos, telefono, email, saldo, moneda, fechacompra, status, socio, id_socio, id_proveedor, hash) VALUES ('".$card."','".$nombres."','".$apellidos."','".$telefono."','".$email."',".$monto.",'".$moneda."','".$fecha."','".$status."',1,".$socio.",".$idproveedor.",'".$hash."')";
		$resul2 = mysqli_query($link, $quer2);
	}
	return true;
}
?>
