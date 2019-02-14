<?php
include_once("../_config/conexion.php");
include_once("funciones.php");
include_once("../lib/phpqrcode/qrlib.php");

// Buscar datos de proveedor
$query = "select * from proveedores";
$result = mysqli_query($link, $query);
while ($row = mysqli_fetch_array($result)) {
	$id_proveedor=$row["id"];
	$nombreproveedor=$row["nombre"];
	$logo=$row["logo"];

	$dir = 'temp/';

	$filename = $dir.$id_proveedor.'.png';
	$tamanio = 5;
	$level = 'H';
	$frameSize = 1;
	$contenido = 'https://www.clubdeconsumidores.com.ve/cupones/cupon.html?id='.$id_proveedor;

	QRcode::png($contenido, $filename, $level, $tamanio, $frameSize);

	if ($logo!="") {
		
		// $filename = $dir.'test.png';
		$filename = $dir.$id_proveedor.'.png';

		$original = imagecreatefrompng($filename);
		$logotipo = imagecreatefromjpeg('../img/'.$logo);

		$dataorig = getimagesize($filename);
		$datalogo = getimagesize('../img/'.$logo);

		list($wsour, $hsour) = getimagesize($filename);
		list($wtarg, $htarg) = getimagesize('../img/'.$logo);

		$newqr = imagecreatetruecolor($wsour, $hsour);

		imagecopy($newqr, $original, 0, 0, 0, 0, $wsour, $hsour);

		$porcentaje = 50;

		$a = ($wsour/$wtarg);
		$b = $a*100/$wsour;
		$ancho = $a/$b * $porcentaje;

		$a = ($htarg/$wtarg);
		$alto = $ancho * $a;

		$x = ($wsour - $ancho) / 2;
		$y = ($hsour - $alto) / 2;

		// imagecopy($newqr, $logotipo, ($wsour/2)-($wtarg/2), ($hsour/2)-($htarg/2), 0, 0, $wtarg, $htarg);
		// imagecopyresized($newqr, $logotipo, ($wsour/3), ($hsour/3), 0, 0, ($wsour/$wtarg)*($wtarg/3), ($hsour/$htarg)*($htarg/3), $wtarg, $htarg);
		imagecopyresized($newqr, $logotipo, $x, $y, 0, 0, $ancho, $alto, $wtarg, $htarg);

		imagepng($newqr, $dir.$id_proveedor."_logo.png", 0);
		$codigo = $dir.$id_proveedor."_logo.png";
	} else {
		$codigo = $dir.$id_proveedor.".png";		
	}

	$mensaje = '<p style="text-align:center;">';
		$mensaje .= $nombreproveedor.'<br/>';
		// $mensaje .= '<img src="'.$dir.$id_proveedor."_logo.png".'" height="200" width="200" />';
		$mensaje .= '<img src="'.$codigo.'" height="200" width="200" />';
	$mensaje .= '</p>';
	$mensaje .= '<p><br/><br/><br/><br/><br/></p>';
	echo $mensaje;
	// Hasta aqui
}

?>
