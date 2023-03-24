<?php

/*Autenticación vía hash
if (
    !array_key_exists('HTTP_X_HASH', $_SERVER) ||
    !array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) ||
    !array_key_exists('HTTP_X_UID', $_SERVER)
){
  die;
}

list( $hash, $uid, $timestamp ) = [
    $_SERVER['HTTP_X_HASH'],
    $_SERVER['HTTP_X_UID'],
    $_SERVER['HTTP_X_TIMESTAMP']
];

$secret = 'Sh!! No se lo cuentes a nadie!';

$newHash = sha1($uid.$timestamp.$secret);

if ( $newHash !== $hash ) {
  die;
}*/

if( !array_key_exists ( 'HTTP_X_TOKEN', $_SERVER ) ) {

  die;
}

$url = 'http://localhost:8001';

$ch = curl_init( $url );
curl_setopt(
  $ch,
  CURLOPT_HTTPHEADER,
  [
    "X-Token: {$_SERVER['HTTP_X_TOKEN']}"
  ]
  );
curl_setopt(
  $ch,
  CURLOPT_RETURNTRANSFER,
  true
);

$ret = curl_exec( $ch );

if ( $ret !== 'true' ){
  
  die;
}

//Avisamos al cliente que le vamos a estar enviando JSON
header('Content-Type: application/json');

//Definimos los recursos disponibles y consultables desde el exterior 
$allowedResourceTypes = [
  'books',
  'authors',
  'genres',
];

//Validamos que el recurso este disponible
$resourceType = $_GET['resource_type'];
if (!in_array($resourceType, $allowedResourceTypes)) {
    die;
}

//Definimos los recursos 
$books = [
  1 => [
    'titulo' => 'Lo que el viento se llevo',
    'id_autor' => 2,
    'id_genero' => 2,
  ],
  2 => [
    'titulo' => 'La Iliada',
    'id_autor' => 1,
    'id_genero' => 1,
  ],
  3 => [
    'titulo' => 'La Odisea',
    'id_autor' => 1,
    'id_genero' => 1,
  ],
];

/*Levantamos el ID del recurso buscado
$resourceId: variable que deberia venir desde la URL
array_key_exists: validador de PHP para saber si la peticion del ID CORRESPONDE al contenido del array*/
$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : ''; 
$method = $_SERVER['REQUEST_METHOD'];

//Generamos la respuesta asumiendo que el pedido es correcto
switch ( strtoupper($_SERVER['REQUEST_METHOD']) ) {
  case 'GET': //Peticiones al server
    if ( empty( $resourceId ) ) {
        echo json_encode( $books ); //para toda la coleccion
    } else {
        if ( array_key_exists( $resourceId, $books ) ) { //para registros especificos
            echo json_encode( $books[ $resourceId ] );
        }
    }
  break;
  case 'POST': //con esto permitimos que una entidad externa cree un nuevo libro
    
    //Tomamos la entrada "cruda" el usuario nos envia un texto con la misma estructura de nuestro array en formato JSON
    $json = file_get_contents('php://input'); 
    
    //Transformamos el json recibido a un nuevo elemento del array
    $books[] = json_decode($json, true); //true para decir que esto se haga en forma de array
    
    /*Devolvemos el ID que se a generado para el nuevo elemento (libro) dentro del array
    echo array_keys( $books )[ count($books) - 1 ];*/

    echo json_encode( $books );
    break;
  case 'PUT':
    /*Validaciones
    $resourceId: valida que el parametro no este vacio
    array_key_exists: valida que el ID corresponda a uno dentro del array con el cual contamos*/
    if (!empty($resourceId) && array_key_exists($resourceId, $books)) {
      
      //Tomamos la entrada cruda
      $json = file_get_contents('php://input');
      
      //La clave que recibimos nos dira cual sera el recurso a reemplazar
      $books[$resourceId] = json_decode($json, true);
      
      //retornamos la coleccion completa con la ultima modificacion
      echo json_encode($books);
    }
    break;
  case 'DELETE':
    /*Validaciones
    $resourceId: valida que el parametro no este vacio
    array_key_exists: valida que el ID corresponda a uno dentro del array con el cual contamos*/
    if (!empty($resourceId) && array_key_exists($resourceId, $books)) { 
      unset($books[$resourceId]); //funcion para eliminar el elemento del array

      //Tomamos la entrada cruda
      $json = file_get_contents('php://input');
    }
    
    echo json_encode($books);
    break;
}