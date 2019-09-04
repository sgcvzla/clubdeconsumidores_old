<?php 
header('Content-Type: application/json');
include_once("../_config/conexion.php");

$card = (isset($_GET['card'])) ? $_GET['card'] : '' ;
$monto = (isset($_GET['monto'])) ? $_GET['monto'] : 0 ;
$ruta = (isset($_GET['ruta'])) ? $_GET['ruta'] : '' ;

$urlok = '';
$urlcb = '';
switch ($ruta) {
    case 'giftcards':
        $urlok = 'https://www.clubdeconsumidores.com.ve/php/exitocompragiftcards.php?card='.$card;
        $urlcb = 'https://www.clubdeconsumidores.com.ve/giftcards';
        // $urlok = 'https://www.clubdeconsumidoes.com.ve/php/exitocompragiftcards.php?orden='.trim($orden).'&orden='.trim($monto);
        break;
    case 'prepago':
        $urlok = 'https://www.clubdeconsumidores.com.ve/php/exitocompraprepago.php?card='.$card;
        $urlcb = 'https://www.clubdeconsumidores.com.ve/prepago';
        // $urlok = 'https://www.clubdeconsumidoes.com.ve/php/exitocompragiftcards.php?orden='.trim($orden).'&orden='.trim($monto);
        break;
    default:
        $urlok = '';
        $urlcb = '';
        break;
}

include_once('../apis/pagoflash.api.client.php');

$urlCallbacks = $urlcb;
// Llaves de SGC ------------------ O J O
$key_public = "XKA20Z8USB8BES5TYUQ1";
$key_secret = "W1RNW52YCU715US6BHNIVVZB21BKT6";
// ------------------ O J O
$api = new apiPagoflash($key_public,$key_secret, $urlCallbacks, false);
$cabeceraDeCompra = array(
	    "pc_order_number"   => trim($card),
	    "pc_amount"         => trim(strval($monto)) 
	);

$ProductItems = array();
//La información conjunta para ser procesada
$pagoFlashRequestData = array(
    'cabecera_de_compra'    => $cabeceraDeCompra, 
    'productos_items'       => $ProductItems,
    "additional_parameters" => array(
            "url_ok_redirect" =>$urlok, // en esta url le muestas a tu cliente que el pago fue exitoso
            "url_ok_request" => "https://www.clubdeconsumidores.com.ve/php/procesapagoenlinea.php" // en esta url debes verificar la transaccion

            // "url_ok_redirect" =>'https://www.corporacionmanna.com/plataforma/oficina/exitotc.php?orden='.trim($orden), // en esta url le muestas a tu cliente que el pago fue exitoso
            // "url_ok_request" => "https://www.corporacionmanna.com/plataforma/oficina/procesarpagoenlinea.php" // en esta url debes verificar la transaccion

	)
);

//Se realiza el proceso de pago, devuelve JSON con la respuesta del servidor
$response = $api->procesarPago($pagoFlashRequestData, $_SERVER['HTTP_USER_AGENT']);
$pfResponse = json_decode($response);

//Si es exitoso, genera y guarda un link de pago en (url_to_buy) el cual se usará para redirigir al formulario de pago
if($pfResponse->success){
    header("Location: ".$pfResponse->url_to_buy);
}else{
    //manejo del error.
}
?>