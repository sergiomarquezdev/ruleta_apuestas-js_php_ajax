<?php

$num = $_GET[ 'num' ];

$apostado = array();
$apostado[ 'negro' ] = $_GET[ 'eu_negro' ];
$apostado[ 'rojo' ] = $_GET[ 'eu_rojo' ];
$apostado[ 'par' ] = $_GET[ 'eu_par' ];
$apostado[ 'impar' ] = $_GET[ 'eu_impar' ];


$color = dimeColor( $num );

if ( $num % 2 ) {
  $paridad = 'impar';
} else {
  $paridad = 'par';
}

$datos_finales = array();
$contador = 0;
$total_apostado = 0;
$ganancias = 0;

foreach ( $apostado as $key => $val ) {
  if ( $val > 0 ) {
    $datos_finales[ $contador ][ 'msj' ] = 'Apostado al ' . $key . ': ';
    $datos_finales[ $contador ][ 'valor' ] = $val;
    $contador++;
    $total_apostado += $val;

    if ( ( $color == $key || $paridad == $key ) && $num > 0 ) {
      $datos_finales[ $contador ][ 'msj' ] = 'GANADO al ' . $key . ': ';
      $datos_finales[ $contador ][ 'valor' ] = $val * 2;
      $contador++;
      $ganancias += $val * 2;
    }
  }
}
$datos_finales[ $contador ][ 'msj' ] = '::: APOSTADO: ';
$datos_finales[ $contador ][ 'valor' ] = $total_apostado;
$contador++;
$datos_finales[ $contador ][ 'msj' ] = '::: GANADO: ';
$datos_finales[ $contador ][ 'valor' ] = $ganancias;
$contador++;

$total = $ganancias - $total_apostado;
if ( $total >= 0 ) {
  $datos_finales[ $contador ][ 'msj' ] = 'EN TOTAL HAS GANADO: ';
  $datos_finales[ $contador ][ 'valor' ] = $total;
} else {
  $datos_finales[ $contador ][ 'msj' ] = 'EN TOTAL HAS PERDIDO: ';
  $datos_finales[ $contador ][ 'valor' ] = abs( $total );
}

echo json_encode( $datos_finales );

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
    $color = 'rojo';
  } else {
    $color = 'negro';
  }

  return $color;
}