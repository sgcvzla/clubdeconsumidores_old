<?php
function mensajes($archivojson,$texto){
    $parametros = json_decode(file_get_contents($archivojson),true);
    $mensaje = '[';
    for ($i = 0; $i < count($parametros["mensajes"][$texto]); $i++) {
        $mensaje .= '"' . $parametros["mensajes"][$texto][$i] . '"';
        if (count($parametros["mensajes"][$texto]) > 1 && $i + 1 < count($parametros["mensajes"][$texto])) {
            $mensaje .= ',';
        }
    }
    $mensaje .= ']';
    return $mensaje;
}
?>