<?php

$method = strtoupper($_SERVER['REQUEST_METHOD']);

$token = sha1('Esto es secreto!!'); // Token generado con funcion de encriptamiento sh1 mediante un codigo secreto

if ($method === 'POST') {// Para obtenener token
  if (!array_key_exists('HTTP_X_CLIENT_ID', $_SERVER) || !array_key_exists('HTTP_X_SECRET', $_SERVER)) {//Verifica que las claves recibidas sean validas. 
    http_response_code(400);// Bad Request

    die('Faltan parametros');
  }

  // Tomar los headers
  $clientId = $_SERVER['HTTP_X_CLIENT_ID'];
  $secret = $_SERVER['HTTP_X_SECRET'];

  // Verificar credenciales
  if ($clientId !== '1' || $secret !== 'SuperSecreto!') {
    http_response_code(404);

    die("No autorizado");
  }

  echo "$token";//en tal caso, respondera con un token
} elseif ($method === 'GET') { //recibe pedidos via get, solicitudes de validacion de token
  if (!array_key_exists('HTTP_X_TOKEN', $_SERVER)) {//verifica el encabezado, contra los tokens validos
    http_response_code(400);// Bad Request

    die('Faltan parametros');
  }

  if ($_SERVER['HTTP_X_TOKEN'] == $token) { 
    echo 'true';
  } else {
    echo 'false';
  }
} else {
  echo 'false';
}