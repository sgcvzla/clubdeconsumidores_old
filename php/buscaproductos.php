<?php 
header('Content-Type: application/json');
include_once("../_config/conexion.php");

$quer0 = "SELECT familia FROM productos group by familia order by familia";
if($resul0 = mysqli_query($link,$quer0)) {
	$familia = '';
	$cierto = true;
	$coma = '';
	$cierre = false;
	while ($ro0 = mysqli_fetch_array($resul0)) {
		if ($cierto) {
			$familia .= '"filtros":';
			$cierto = false;
			$cierre = true;
			$coma = '[';
		} else {
			$coma = ',';
		}
		$familia .= $coma.'"'.trim($ro0["familia"]).'"';
	}
	$familia .= ($cierre) ? ']' : '' ;
}

$quer0 = "select * from productos where precio_pro>0 order by familia,desc_corta";
if($resul0 = mysqli_query($link,$quer0)) {
	$respuesta = '';
	$cierto = true;
	$coma = '';
	$cierre = false;
	while ($ro0 = mysqli_fetch_array($resul0)) {
		if ($cierto) {
			$respuesta .= '{'.$familia.',"iva":16.00,"registros":';
			$cierto = false;
			$cierre = true;
			$coma = '[';
		} else {
			$coma = ',';
		}
		$archivo = 'img/'.trim($ro0["imagen"]).'.jpg';

		$precio_publico = $ro0["precio_pro"];
		
		$respuesta .= $coma.'{"id_pro":"'.trim($ro0["id_pro"]).'","desc_pro":"'.trim($ro0["desc_pro"]).'","precio_pro":'.trim($precio_publico).',"desc_corta":"'.trim($ro0["desc_corta"]).'","imagen":"'.trim($archivo).'","familia":"'.trim($ro0["familia"]).'","aviso":'.trim($ro0["aviso"]).',"fecha_aviso":"'.trim($ro0["fecha_aviso"]).'"}';
	}
	$respuesta .= ($cierre) ? ']' : '' ;
	$respuesta .= '}';
} else {
	$respuesta .= 'No';
}
echo $respuesta;
?>
