<?php
    // Preparamos la llamada CURL con el primer argumento pasado por
    // consola al ejecutar el cliente.
    $ch = curl_init($argv[1]);
    
    // Guardamos el Token de autenticación que será el segundo argumento
    // pasado por consola al ejecutar el cliente
    $token = array_key_exists(2, $argv) ? $argv[2] : '';
    
    // Agregamos una opción a la llamada CURL con el Token de autenticación
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X_Token: {$token}"]);
    
    // Agregamos la opciön de retorno
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Ejecutamos la llamada CURL
    $response = curl_exec($ch);
    // Obtenemos el codigo que devuelve el servidor a nuestra petición
    
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // Manejamos los codigos HTTP
    
    switch($http_code){
        case 200:
            echo 'Todo Bien'.PHP_EOL;
            echo $response.PHP_EOL;
            break;
        case 400:
            echo 'Pedido incorrecto'.PHP_EOL;
            break;
        case 401:
            echo 'Fallo de autenticación'.PHP_EOL;
            break;
        case 404:
            echo 'Recurso no encontrado'.PHP_EOL;
            break;
        case 500:
            echo 'El servidor falló'.PHP_EOL;
            break;
    }
?>