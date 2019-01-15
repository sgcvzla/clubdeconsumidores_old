<?php 
session_start();
if (strpos($_SERVER["SERVER_NAME"],'localhost')!==FALSE) {
	// local
	$servidor = "localhost";
	$cuenta = "root";
	$password = "rootmyapm";
	$database = "clubdeconsumidores";
} else {
	// produccion
	$servidor = "localhost:3306";
	$cuenta = "sgcco_root";
	$password = "sgcpasarela12345**";
	$database = "sgcconsu_sgcvzla";
}

$link = mysqli_connect($servidor, $cuenta, $password) or die ("Error al conectar al servidor.");
mysqli_select_db($link, $database) or die ("Error al conectar a la base de datos.");
date_default_timezone_set('America/Caracas');
?>