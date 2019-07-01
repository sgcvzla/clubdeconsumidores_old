<?php 
include_once("../_config/conexion.php");
include_once("funciones.php");

$tabla = 'resultado_encuesta';
// $fecha = date("Y")."-".date("m")."-".sprintf("%'02d",(date("d")-1));

$fech1 = date('Y-m-d');
$fecha = strtotime('-2 day', strtotime ($fech1));
$fecha = date ('Y-m-d', $fecha);

// $fech3 = strtotime('-5 day', strtotime ($fech1));
// $fech3 = date ('Y-m-d', $fech3);

$campos = array();
$tipos = array();
$quer2 = "select * from information_schema.columns where table_schema='".$database."' and table_name='".$tabla."'";
$resul2 = mysqli_query($link,$quer2);
while($row = mysqli_fetch_array($resul2)) {
    $indice = $row["COLUMN_NAME"];
    $campos[] = $indice;
    $x = $row["DATA_TYPE"];
    $tipos[] = $x;
}

$query = "SELECT * FROM resultado_encuesta where fechaencuesta<'".$fecha."' order by ide,idd";
$result = mysqli_query($link, $query);
$cuerpo = '';
while ($row = mysqli_fetch_array($result)) {
    $cuerpo .= '<tr>';
        foreach ($campos as $key => $value) {
            $cuerpo .= '<td>';
                    $cuerpo .= $row[$key];
            $cuerpo .= '</td>';
        }
    $cuerpo .= '</tr>';
}

$asunto = 'Encuestas del ';
$asunto .= substr($fecha,8,2).'/'.substr($fecha,5,2).'/'.substr($fecha,0,4);

$texto = '<p><u>'.$asunto.'</u></p>';
$texto .= '<table border="1">';
    $texto .= '<thead>';
        $texto .= '<tr>';
            foreach ($campos as $key => $value) {
                $texto .= '<th>';
                        $texto .= $value;
                $texto .= '</th>';
            }
        $texto .= '</tr>';
    $texto .= '</thead>';
    $texto .= '<tbody>';
        $texto .= $cuerpo;
    $texto .= '</tbody>';
$texto .= '</table>';

echo $texto;

// $asunto = "Transacciones del dia: ".substr($fecha,8,2).'/'.substr($fecha,5,2).'/'.substr($fecha,0,4);
$mensaje = $texto;
$cabeceras = 'Content-type: text/html;';
// mail("soluciones2000@gmail.com",$asunto,$mensaje,$cabeceras);
?>
