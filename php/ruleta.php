<?php

session_start();

$datos = '';

$nums_ruleta = array( 0, 32, 15, 19, 4, 21, 2, 25, 17, 34, 6, 27, 13, 36, 11, 30, 8, 23, 10, 5, 24, 16, 33, 1, 20, 14, 31, 9, 22, 18, 29, 7, 28, 12, 35, 3, 26 );

if ( !empty( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ) {
  $ip = $_SERVER[ 'HTTP_CLIENT_IP' ];
} elseif ( !empty( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ) {
  $ip = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
} else {
  $ip = $_SERVER[ 'REMOTE_ADDR' ];
}
$file = str_replace( array( ':', '.' ), '-', $ip ) . '.json';

if ( isset( $_GET[ 'go' ] ) && $_GET[ 'go' ] == 1 ) {
  $datos = array();
  $datos[ 'indice' ] = rand( 0, 36 );
  $datos[ 'num' ] = $nums_ruleta[ $datos[ 'indice' ] ];
  $datos[ 'vueltas' ] = rand( 90, 110 );
  $datos[ 'vuelta_actual' ] = 1;
} else {
  if ( file_exists( $file ) ) {
    $pf = fopen( $file, "r" );
    $datos = fread( $pf, filesize( $file ) );
    $datos = json_decode( $datos, true );
    fclose( $pf );
    $datos[ 'vuelta_actual' ]++;

    $datos[ 'indice' ]++;
    if ( $datos[ 'indice' ] > 36 ) {
      $datos[ 'indice' ] = 0;
    }
    $datos[ 'num' ] = $nums_ruleta[ $datos[ 'indice' ] ];
  } else {
    $datos_json = array();
    $datos_json[ 'status' ] = 'ko';
    $datos_json[ 'datos' ] = 'Primero, hay que inicializar la ruleta.';

    echo json_encode( $datos_json );
    die;
  }

}

if ( $datos[ 'vuelta_actual' ] > $datos[ 'vueltas' ] ) {
  $datos_json = array();
  $datos_json[ 'status' ] = 'ko';
  $datos_json[ 'datos' ] = 'Ya se han terminado las vueltas a dar.';

  unlink( $file );

  echo json_encode( $datos_json );
  die;
}

if ( ( $datos[ 'vueltas' ] - $datos[ 'vuelta_actual' ] ) > 10 ) {
  $sleep = 30000;
} elseif ( ( $datos[ 'vueltas' ] - $datos[ 'vuelta_actual' ] ) > 5 ) {
  $sleep = 90000;
} else {
  $sleep = ( ( 5 - ( $datos[ 'vueltas' ] - $datos[ 'vuelta_actual' ] ) ) * 100000 ) + 90000;
}

$datos[ 'sleep' ] = $sleep;

usleep( $sleep );


//var_dump($datos);

$datos_json = array();
$datos_json[ 'status' ] = 'ok';
$datos_json[ 'datos' ] = $datos;
$datos_json[ 'datos' ][ 'fin' ] = ( $datos[ 'vuelta_actual' ] == $datos[ 'vueltas' ] ) ? true : false;

$pf = fopen( $file, "w" );
fwrite( $pf, json_encode( $datos ) );
fclose( $pf );

$num = $datos[ 'num' ];

$color = dimeColor( $num );

if ( $num % 2 ) {
  $paridad = 'impar';
} else {
  $paridad = 'par';
}
$datos_json[ 'datos' ][ 'color' ] = $color;
$datos_json[ 'datos' ][ 'paridad' ] = $paridad;

echo json_encode( $datos_json );

die;


function dimeColor( $num ) {

  if ( $num == 0 ) {
    return 'green';
  }

  if ( $num == 10 || $num == 28 ) {
    return 'black';
  }

  while ( strlen( $num ) > 1 ) {

    $array_temp = str_split( $num );
    $num = 0;
    foreach ( $array_temp as $i => $caracter ) {
      $num += $caracter;
    }
  }

  if ( $num % 2 ) {
    $color = 'red';
  } else {
    $color = 'black';
  }

  return $color;
}