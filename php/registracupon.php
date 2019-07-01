<?php
header('Content-Type: application/json');
include_once("../_config/conexion.php");
include_once("funciones.php");
include_once("../lib/phpqrcode/qrlib.php");

$archivojson = "../cupones/cupon.json";

// Si no es socio, verificar si se quiere afiliar o no
$socio = ($_POST["socio"]=="true") ? 1 : 0 ;
// $socio = 1;
// $email = 'soluciones3000@gmail.com';

// Buscar datos de socio
$query = "select * from socios where email='".$_POST['email']."'";
// $query = "select * from socios where email='".$email."'";
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
	$id=$row["id"];
} else {
	if ($socio) {
		$query = "INSERT INTO socios (email,telefono,nombres,apellidos,status) VALUES ('".$_POST["email"]."','".$_POST["telefono"]."','".$_POST["nombres"]."','".$_POST["apellidos"]."','Pendiente')";
		// $query = "INSERT INTO socios (email,telefono,nombres,apellidos) VALUES ('".$email."','0414','xxx','yyy')";
		$result = mysqli_query($link,$query);
		$query = "select * from socios where email='".$_POST['email']."'";
		// $query = "select * from socios where email='".$email."'";
		$result = mysqli_query($link, $query);
		if ($row = mysqli_fetch_array($result)) {
			$id=$row["id"];
			mensajebienvenida($row);
		} else {
			$id=0;
		}
	} else {
		$id=0;
	}
}

// Buscar datos de proveedor
$query = "select * from proveedores where id=".$_POST['id_proveedor'];
// $query = "select * from proveedores where id=1";
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
	$nombreproveedor=$row["nombre"];
}

// Buscar premio activo
$query = "select * from premios where id_proveedor=".$_POST['id_proveedor'] . " and clasepremio='consumo' and activo=1";
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
		$cuponlargo = asignacodigolargo($numcupon);
	} else {
		$numcupon = asignacodigo($row["ultcupon"]);
		$cuponlargo = asignacodigolargo($numcupon);
	}
}


// Verificar si ya existe el cupón, si existe responder, si no, agregar y responder 
$query = "select * from cupones where id_proveedor=".$_POST['id_proveedor']." and factura='" . $_POST['factura'] . "'";
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

	$query = "INSERT INTO cupones (cupon,cuponlargo,id_proveedor,id_socio,status,factura,monto,id_premio,tipopremio,montopremio,descpremio,socio,email,telefono,nombres,apellidos,fechacupon,fechavencimiento,hash) VALUES ('".$numcupon."','".$cuponlargo."'," . $_POST['id_proveedor'] . "," . $id . ",'Generado','" . $_POST["factura"] . "'," . $_POST["monto"] . ",".$id_premio.",'".$tipopremio."',".$montopremio.",'".$descpremio."'," . $socio . ",'" . $_POST["email"] . "','" . $_POST["telefono"] . "','" . $_POST["nombres"] . "','" . $_POST["apellidos"] . "','".$fechacupon."','".$fechavencimiento."','".$hash."')";
	if ($result = mysqli_query($link, $query)) {

		$correo = $_POST["email"];

		$mensaje = utf8_decode('Hola '.trim($_POST["nombres"]).',<br/><br/>');
		$mensaje .= utf8_decode('¡Gracias por preferir a <b>'.trim($nombreproveedor).'</b> para tus compras!<br/><br/>');
		$mensaje .= utf8_decode('En reconimiento a tu preferencia, queremos darte un obsequio, ');
		$mensaje .= utf8_decode('la próxima que nos visites podrás reclamar el siguiente premio:'.'<br/><br/>');
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

		$asunto = utf8_decode(trim($_POST["nombres"]).' ganaste un premio en '.($nombreproveedor).' por tu compra.');
		$cabeceras = 'Content-type: text/html;';
//		if (strpos($_SERVER["SERVER_NAME"],'localhost')===FALSE) {	           	
			mail($correo,$asunto,$mensaje,$cabeceras);
//		}

		$a = fopen('log.html','w+');
		fwrite($a,$asunto);
		fwrite($a,'-');
		fwrite($a,$mensaje);

		$respuesta = '{"exito":"SI","mensaje":' . mensajes($archivojson,"exitoregistrocupon") . ',"cupon":"'.$numcupon.'"}';
//		$respuesta = '{"exito":"SI","mensaje":' . mensajes($archivojson,"exitoregistrocupon") . ',"cupon":"'.$numcupon.'",';
//		$respuesta .= '"contenido":'.$contenido.',"filename":"'.$filename.'"}';

	} else {
		$respuesta = '{"exito":"NO","mensaje":' . mensajes($archivojson,"fallaregistrocupon") . ',"cupon":"0"}';
	}
}
echo $respuesta;


// function asignacodigo($ultcupon){
// 	$valores = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
// 	$a = strlen($valores)-1;
// 	$base = 36;
// 	$codigo = '';
// 	$arriba = 1;
// 	$newcodigo = '';
// 	$numero = $ultcupon;
// 	// echo $numero.'<br>';
// 	for ($i=strlen($ultcupon)-1 ; $i>=0 ; $i--) { 
// 		$pos = strpos($valores, substr($numero,$i,1));
// 		if ($arriba==1) {
// 			if ($pos==$a) {
// 				$codigo = substr($valores,0,1);
// 			} else {
// 				$codigo = substr($valores,$pos+1,1);
// 				$arriba = 0;
// 			}
// 		} else {
// 			$codigo = substr($numero,$i,1);
// 		}
// 		$newcodigo = $codigo.$newcodigo;
// 	}
// 	// switch (strlen($newcodigo)) {
// 	// 	case '1':
// 	// 		$newcodigo = '0000'.$newcodigo;
// 	// 		break;
// 	// 	case '2':
// 	// 		$newcodigo = '000'.$newcodigo;
// 	// 		break;
// 	// 	case '3':
// 	// 		$newcodigo = '00'.$newcodigo;
// 	// 		break;
// 	// 	case '4':
// 	// 		$newcodigo = '0'.$newcodigo;
// 	// 		break;
// 	// }
// 	for ($i=0 ; $i< strlen($newcodigo); $i++) { 
// 		// echo substr($newcodigo,$i,1).'<br>';
// 	}

// 	return $newcodigo;
// }

// function asignacodigolargo($ultcupon){
// 	$newcodigo = $ultcupon;

// 	$cuponlargo = substr($newcodigo,0,2);
// 	// $cuponlargo .= codigocaracter(strtoupper(substr($_POST["email"],-1)));
// 	$cuponlargo .= substr($newcodigo,2,2);
// 	// $cuponlargo .= codigocaracter(strtoupper(substr($_POST["nombres"],-1)));
// 	$cuponlargo .= substr($newcodigo,4,2);
// 	// $cuponlargo .= codigocaracter(strtoupper(substr($_POST["apellidos"],-1)));
// 	$cuponlargo .= substr($newcodigo,6,2);
// 	// $cuponlargo .= codigocaracter(strtoupper(substr($_POST["telefono"],-1)));
// 	$cuponlargo .= substr($newcodigo,8,2);

// 	return $cuponlargo;
// }

// function codigocaracter($valor) {
// 	$llaves = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

// 	$codigos =  '111213141A1B1C1D212223242A2B2C2D3132';
// 	$codigos .= '33343A3B3C3D414243444A4B4C4DA1A2A3A4';

// 	$posicion = strpos($llaves, $valor);
// 	$pos2 = $posicion*2;
// 	$newvalor = substr($codigos,$pos2,2);

// 	return $newvalor;
// }

function mensajebienvenida($reg) {
	$correo = $reg["email"];

	$mensaje = utf8_decode('Hola '.trim($reg["nombres"]).',<br/><br/>');
	$mensaje .= utf8_decode('¡Gracias por querer formar parte de nuestro club!<br/><br/>');

	$mensaje .= utf8_decode('Queremos conocerte un poco más y ofrecerte premios, promociones o productos/servicios especialmente diseñados para ti, pero necesitamos que nos brindes alguna información que nos ayudará a prestarte un mejor servicio, innovar en nuestros premios y hacerte la vida mucho más fácil y gratificante, además desde ya comenzaras a ganar, luego de completar <a href="https://www.clubdeconsumidores.com.ve/registro/registro.html?idp='.$_POST['id_proveedor'].'&ids='.$reg["id"].'">este formulario</a> recibirás un premio de bienvenida.<br/><br/>');

	$mensaje .= utf8_decode('<b>Te garantizamos que tu información será guardada celosamente y nunca será compartida con ningún tercero sin tu consentimiento y te aseguramos que siempre cumpliremos con las Leyes vigentes en lo relacionado al tratamiento de tus datos personales.</b><br/><br/>');

	$mensaje .= utf8_decode('Nuestro club está en permanente evolución y tú como un miembro muy importante puedes aportarnos ideas o sugerencias que harán crecer esta comunidad, ten la certeza que serás escuchado(a) y tus sugerencias o comentarios serán repondidos en un lapso de tiempo razonable con mucho entusiasmo por resolver tus inquietudes, para nosotros será un placer atenderte por medio del email: <a href="mailto:info@clubdeconsumidores.com.ve">info@clubdeconsumidores.com.ve</a>.<br/><br/>');

	$mensaje .= utf8_decode('Bienvenido!!!'.'<br/><br/>');
	$mensaje .= utf8_decode('Club de consumidores'.'<br/><br/>');

	$mensaje .= utf8_decode('<b>Nota:</b> Esta cuenta no es monitoreada, por favor no respondas este email, si deseas comunicarte con tu club escribe a: <b><a href="mailto:info@clubdeconsumidores.com.ve">info@clubdeconsumidores.com.ve</a></b>'.'<br/><br/>');

	$asunto = utf8_decode(trim($reg["nombres"]).', Bienvenido a tu club de consumidores!!!');
	$cabeceras = 'Content-type: text/html;';
//	if (strpos($_SERVER["SERVER_NAME"],'localhost')===FALSE) {	           	
		mail($correo,$asunto,$mensaje,$cabeceras);
//	}
}

?>
