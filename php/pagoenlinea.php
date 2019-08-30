<?php 
header('Content-Type: application/json');
include_once("../_config/conexion.php");

$monto = (isset($_POST['monto'])) ? $_POST['monto'] : 0 ;
$propietario = (isset($_POST['propietario'])) ? $_POST['propietario'] : "NO" ;
$nombres = (isset($_POST['nombres'])) ? $_POST['nombres'] : "" ;
$apellidos = (isset($_POST['apellidos'])) ? $_POST['apellidos'] : "" ;
$telefono = (isset($_POST['telefono'])) ? $_POST['telefono'] : "" ;
$email = (isset($_POST['email'])) ? $_POST['email'] : "" ;
$idsocio = (isset($_POST['idsocio'])) ? $_POST['idsocio'] : 0 ;
$idproveedor = (isset($_POST['idproveedor'])) ? $_POST['idproveedor'] : 0 ;
$moneda = (isset($_POST['moneda'])) ? $_POST['moneda'] : "bs" ;


$query = "select * from proveedores where id=".$idproveedor;
$result = mysqli_query($link, $query);
if ($row = mysqli_fetch_array($result)) {
    $nombreproveedor = $row["nombre"];
}

// if ($propietario=="SI") {
    $query = "select * from socios where id=".$idsocio;
    $result = mysqli_query($link, $query);
    if ($row = mysqli_fetch_array($result)) {
        $nombresocio = $row["nombres"];
        $apellidosocio = $row["apellidos"];
        $emailsocio = $row["email"];
        $telefonosocio = $row["telefono"];
    }
// }

// Busca el próximo número de giftcard
$query = "select auto_increment from information_schema.tables where table_schema='clubdeconsumidores' and table_name='giftcards'";
$result = mysqli_query($link,$query);
if($row = mysqli_fetch_array($result)) {
    $numgiftcard = $row["auto_increment"];
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
if ($propietario=="SI") {
    $card .= generacodigo(substr($nombresocio,0,1),$link);
    $card .= substr($txtgiftcard,0,1);

    $card .= generacodigo(substr($apellidosocio,0,1),$link);
    $card .= substr($txtgiftcard,1,1);

    $card .= generacodigo(substr($telefonosocio,strlen($telefonosocio)-1,1),$link);
    $card .= generacodigo(substr($emailsocio,0,1),$link);

    $card .= substr($txtgiftcard,2,1);
    $card .= generacodigo(substr($nombreproveedor,0,1),$link);

    $card .= substr($txtgiftcard,3,1);
    $card .= generacodigo(substr($moneda,0,1),$link);

    $nombres = $nombresocio;
    $apellidos = $apellidosocio;
    $telefono = $telefonosocio;
    $email = $emailsocio;
    $xprop = 1;
} else {
    $card .= generacodigo(substr($nombres,0,1),$link);
    $card .= substr($txtgiftcard,0,1);

    $card .= generacodigo(substr($apellidos,0,1),$link);
    $card .= substr($txtgiftcard,1,1);

    $card .= generacodigo(substr($telefono,strlen($telefonosocio)-1,1),$link);
    $card .= generacodigo(substr($email,0,1),$link);

    $card .= substr($txtgiftcard,2,1);
    $card .= generacodigo(substr($nombreproveedor,0,1),$link);

    $card .= substr($txtgiftcard,3,1);
    $card .= generacodigo(substr($moneda,0,1),$link);
    $xprop = 0;
}

$fecha = date('Y-m-d');
$status = 'Pendiente de pago';
$hash = hash("sha256",$card.$idsocio.$idproveedor.$monto.$moneda.$status);

$query = "INSERT INTO giftcards (card, nombres, apellidos, telefono, email, saldo, moneda, fechacompra, status, socio, id_socio, id_proveedor, hash) VALUES ('".$card."','".$nombres."','".$apellidos."','".$telefono."','".$email."',".$monto.",'".$moneda."','".$fecha."','".$status."',".$xprop.",".$idsocio.",".$idproveedor.",'".$hash."')";
if ($result = mysqli_query($link,$query)) {
    $respuesta = '{"exito":"SI","card":"'.$card.'"}';
} else {
    $respuesta = '{"exito":"NO","card":""}';
}

echo $respuesta;

function generacodigo($letra,$link) {
    $query = "select codigo from _codigo where valor='".$letra."'";
    $result = mysqli_query($link, $query);
    if ($row = mysqli_fetch_array($result)) {
        $codigo = $row["codigo"];
    } else {
        $query = "select codigo from _codigo where valor='?'";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);
        $codigo = $row["codigo"];
    }
    return $codigo;
}
?>