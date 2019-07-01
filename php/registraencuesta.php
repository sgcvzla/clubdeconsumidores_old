<?php
header('Content-Type: application/json');
include_once("../_config/conexion.php");
include_once("funciones.php");
include_once("../lib/phpqrcode/qrlib.php");

$archivojson = "../encuesta/encuesta.json";
$objetojson = json_decode(file_get_contents($archivojson),true);

$status="Pendiente";

$quer1 = "SELECT * from encuesta where idp=" . $_POST["id_proveedor"] . " and status=1 and desde<'".date('Y-m-d'). "' and hasta>='".date('Y-m-d')."'";
$resul1 = mysqli_query($link, $quer1);
if ($ro1 = mysqli_fetch_array($resul1)) {
    $ide = $ro1["id"];
} else {
	$ide=0;
}

$quer0 = 'select * from resultado_encuesta where email="'.$_POST['email'].'" and ide='.$ide.' and fechaencuesta="'.date("Y-m-d").'"';
$resul0 = mysqli_query($link, $quer0);
if ($ro0 = mysqli_fetch_array($resul0)) {
	$status="Contestada";
}

if ($status=="Pendiente") {
	$quer1 = "SELECT * from encuesta where idp=" . $_POST["id_proveedor"] . " and status=1 and desde<'".date('Y-m-d'). "' and hasta>='".date('Y-m-d')."'";
	$resul1 = mysqli_query($link, $quer1);
	if ($ro1 = mysqli_fetch_array($resul1)) {
	    $ide = $ro1["id"];
	    $quer2 = "SELECT * from detalle_encuesta where ide=" . $ide;
	    if ($resul2 = mysqli_query($link, $quer2)) {
	    	$exito = 0;
	        while($ro2 = mysqli_fetch_array($resul2)) {
	        	$idd = $ro2["orden"];
	        	$tiporespuesta = $ro2["tiporespuesta"];
				$query = 'INSERT INTO resultado_encuesta (ide, idd, email, nombre, telefono, valorrespuesta1, valorrespuesta2, descrespuesta3,fechaencuesta) VALUES ('.$ide.','.$idd.',"'.$_POST["email"].'","'.$_POST["nombre"].'","'.$_POST["telefono"].'",';
				switch ($tiporespuesta) {
					case 'si o no':
						$query .= $_POST[$idd].',"","","'.date('Y-m-d').'")';
						break;
					case 'rango':
						$query .= '0,"'.$_POST[$idd].'","","'.date('Y-m-d').'")';

						break;
					case 'desarrollo':
						$query .= '0,"","'.$_POST[$idd].'","'.date('Y-m-d').'")';

						break;
				}
				if ($result = mysqli_query($link, $query)) {
			    	$exito = 1;
				} else {
			    	$exito = 2;
				}
	    	}
	    	if ($exito==0) {
				$respuesta = '{"exito":"NO","mensaje":'.mensajes($archivojson,"noencuesta").'}';
	    	} elseif ($exito=1) {
				$respuesta = '{"exito":"SI","mensaje":'.mensajes($archivojson,"exitoregistro").'}';
	    	} else {
				$respuesta = '{"exito":"NO","mensaje":'.mensajes($archivojson,"fallaregistro").'}';
	    	}
	    }
	}
} else {
	$respuesta = '{"exito":"NO","mensaje":'.mensajes($archivojson,"yaregistrado").'}';
}
echo $respuesta;

// function cupondebienvenida($link,$socio,$email,$telefono,$nombres,$apellidos) {
// 	// Buscar datos de proveedor
// 	$query = "select * from proveedores where id=".$_POST['id_proveedor'];
// 	// $query = "select * from proveedores where id=1";
// 	$result = mysqli_query($link, $query);
// 	if ($row = mysqli_fetch_array($result)) {
// 		$nombreproveedor=$row["nombre"];
// 	}

// 	// Buscar premio activo
// 	$query = "select * from premios where id_proveedor=".$_POST['id_proveedor'] . " and clasepremio='encuesta' and activo=1";
// 	// $query = "select * from premios where id_proveedor=1 and clasepremio='encuesta' and activo=1";
// 	$result = mysqli_query($link, $query);
// 	if ($row = mysqli_fetch_array($result)) {
// 		$id_premio=$row["id"];
// 		$tipopremio=$row["tipopremio"];
// 		$montopremio=$row["montopremio"];
// 		$descpremio=$row["descpremio"];
// 		$diasvalidez=$row["diasvalidez"];
// 	}

// 	// Asignar el número de cupón
// 	$query = "select max(cupon) as ultcupon from cupones";
// 	$result = mysqli_query($link, $query);
// 	if ($row = mysqli_fetch_array($result)) {
// 		if (strlen($row["ultcupon"])==0) {
// 			$numcupon = asignacodigo('0000000000');
// 			$cuponlargo = asignacodigolargo($numcupon);
// 		} else {
// 			$numcupon = asignacodigo($row["ultcupon"]);
// 			$cuponlargo = asignacodigolargo($numcupon);
// 		}
// 	}

// 	// Verificar si ya existe el cupón, si existe responder, si no, agregar y responder 
// 	$query = "select * from cupones where id_proveedor=".$_POST['id_proveedor']." and factura='00000'";
// 	// $query = "select * from cupones where id_proveedor=1 and factura='8888888'";
// 	$result = mysqli_query($link, $query);
// 	if ($row = mysqli_fetch_array($result)) {
// 		$respuesta = '{"exito":"NO","mensaje":'. mensajes($archivojson,"cuponyaregistrado") .',"cupon":"0"}';
// 	} else {
// 		$fechacupon = date ('Y-m-d');
// 		$fechavencimiento = strtotime('+'.$diasvalidez.' days', strtotime ($fechacupon));
// 		$fechavencimiento = date ('Y-m-d' , $fechavencimiento);
// 		$fechavencstr = substr($fechavencimiento,8,2).'/'.substr($fechavencimiento,5,2).'/'.substr($fechavencimiento,0,4);

// 		/*
// 		Hash para insertar en el blockchain
// 		-----------------------------------
// 		El hash se va a armar con los siguientes datos:
// 		- Cupon
// 		- Proveedor
// 		- Socio
// 		- Tipo premio
// 		- Monto premio
// 		- Descripción premio
// 		- Status cupón
// 		*/
// 		$hash = hash("sha256",$numcupon.$_POST['id_proveedor']. $id.$tipopremio.$montopremio.$descpremio."Generado");

// 		$query = "INSERT INTO cupones (cupon,cuponlargo,id_proveedor,id_socio,status,factura,monto,id_premio,tipopremio,montopremio,descpremio,socio,email,telefono,nombres,apellidos,fechacupon,fechavencimiento,hash) VALUES ('".$numcupon."','".$cuponlargo."'," . $_POST['id_proveedor'] . "," . $_POST['id_socio'] . ",'Generado','00000',0,".$id_premio.",'".$tipopremio."',".$montopremio.",'Bienvenida'," . $socio . ",'" . $email . "','" . $telefono . "','" . $nombres . "','" . $apellidos . "','".$fechacupon."','".$fechavencimiento."','".$hash."')";

// 		if ($result = mysqli_query($link, $query)) {

// 			$correo = $email;

// 			$mensaje = utf8_decode('Hola '.trim($nombres).',<br/><br/>');
// 			$mensaje .= utf8_decode('¡Bienvenido a tu club!<br/><br/>');

// 			$mensaje .= utf8_decode('Queremos darte un obsequio de bienvenida, ');
// 			$mensaje .= utf8_decode('la próxima que visites <b>'.trim($nombreproveedor).'</b> podrás reclamar el siguiente premio:'.'<br/><br/>');
// 			switch ($tipopremio) {
// 				case 'porcentaje':
// 					$mensaje .= utf8_decode('<h3 style="text-align:center;"><b>'.number_format($montopremio,2,',','.').'% de descuento sobre el monto total de tu factura.</b></h3>');
// 					break;
// 				case 'monto':
// 					$mensaje .= utf8_decode('<h3 style="text-align:center;"><b>'.number_format($montopremio,2,',','.').' Bs. de descuento en sobre el monto total de tu factura.</b></h3>');
// 					break;
// 				case 'producto':
// 					$mensaje .= utf8_decode('<h3 style="text-align:center;"><b>'.trim($descpremio).'.</b></h3>');
// 					break;
// 				default:
// 					$mensaje .= utf8_decode('<h3 style="text-align:center;"><b>Premio especial sorpresa.</b></h3>');
// 					break;
// 			}

// 			$mensaje .= utf8_decode('Este premio podrás reclamarlo cualquier día, siempre que sea antes del <b>'.$fechavencstr.'</b>.<br/><br/>');
// 			$mensaje .= utf8_decode('Sólo debes presentar este correo electrónico o indicar el siguiente código:'.'<br/>');
// 			$mensaje .= utf8_decode('<h2 style="text-align:center"><b>'.$cuponlargo.'</b></h2>');

// 			// codigo de barras
// 			$mensaje .= '<p style="text-align:center;">';
// 				$mensaje .= '<img src="https://www.clubdeconsumidores.com.ve/php/barcode.php?';
// 				$mensaje .= 'text='.$cuponlargo;
// 				$mensaje .= '&size=50';
// 				$mensaje .= '&orientation=horizontal';
// 				$mensaje .= '&codetype=Code39';
// 				$mensaje .= '&print=true';
// 				$mensaje .= '&sizefactor=1" />';
// 			$mensaje .= '</p>';

// 			// código qr
// 			$mensaje .= utf8_decode('<p style="text-align:center;">Para canjear desde el móvil:</p>');

// 	//		$dir = 'https://www.clubdeconsumidores.com.ve/php/temp/';
// 	//		if(!file_exists($dir)) mkdir($dir);
// 			$ruta = 'https://www.clubdeconsumidores.com.ve/php/';
// 			$dir = 'qr/';
// 			if(!file_exists($dir)) mkdir($dir);

// 	//		$filename = $dir.'test.png';
// 			$tamanio = 5;
// 			$level = 'H';
// 			$frameSize = 1;
// 	//		$contenido = $cuponlargo;
// 	//		$contenido = '{"id_proveedor":'.$_POST['id_proveedor'].',"cupon":"'.$cuponlargo.'"}';
// 			$contenido = 'https://www.clubdeconsumidores.com.ve/canje/canje.html?cJson={"id_proveedor":'.$_POST['id_proveedor'].',"cupon":"'.$cuponlargo.'"}';

// 	//		QRcode::png($contenido, $filename, $level, $tamanio, $frameSize);
// 			QRcode::png($contenido,$dir.$numcupon.'.png', $level, $tamanio, $frameSize);
// 			$mensaje .= '<p style="text-align:center;">';
// 				$mensaje .= '<img src="'.$ruta.$dir.$numcupon.'.png" height="200" width="200" />';
// 			$mensaje .= '</p>';
// 			// Hasta aqui
// 			$mensaje .= '<p style="text-align:center;">'.$hash.'</p>';

// 			$mensaje .= utf8_decode('¡Te esperamos!'.'<br/><br/>');

// 			$mensaje .= utf8_decode('Atentamente'.'<br/><br/>');
// 			$mensaje .= utf8_decode('Club de consumidores'.'<br/><br/>');

// 			$mensaje .= utf8_decode('<b>Nota:</b> Esta cuenta no es monitoreada, por favor no respondas este email, si deseas comunicarte con tu club escribe a: <b><a href="mailto:info@clubdeconsumidores.com.ve">info@clubdeconsumidores.com.ve</a></b>'.'<br/><br/>');

// 			// $mensaje .= $numcupon;

// 			$asunto = utf8_decode('Hola '.trim($nombres).', recibe este obsequio de bienvenida al club de consumidores.');
// 			$cabeceras = 'Content-type: text/html;';
// 	//		if (strpos($_SERVER["SERVER_NAME"],'localhost')===FALSE) {	           	
// 				mail($correo,$asunto,$mensaje,$cabeceras);
// 	//		}

// 			$a = fopen('log.html','w+');
// 			fwrite($a,$asunto);
// 			fwrite($a,'-');
// 			fwrite($a,$mensaje);

// 			// $respuesta = '{"exito":"SI","mensaje":' . mensajes($archivojson,"exitoregistrocupon") . ',"cupon":"'.$numcupon.'"}';
// 	//		$respuesta = '{"exito":"SI","mensaje":' . mensajes($archivojson,"exitoregistrocupon") . ',"cupon":"'.$numcupon.'",';
// 	//		$respuesta .= '"contenido":'.$contenido.',"filename":"'.$filename.'"}';

// 		} else {
// 			// $respuesta = '{"exito":"NO","mensaje":' . mensajes($archivojson,"fallaregistrocupon") . ',"cupon":"0"}';
// 		}
// 	}
// 	// echo $respuesta;
// }


?>
