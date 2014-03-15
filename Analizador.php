<?php

$file_path = "/var/www/analizador/ejemplo.php";

$source = file_get_contents($file_path);

$tokens = token_get_all($source);

$variables = array();
$funciones = array();

//var_dump($tokens);

foreach($tokens as $key => $token) {
	if(is_array($token)) {
		if($token[0] === T_VARIABLE) {
				
			$variables[$token[1]] = 0;
			//var_dump($token);
		}
	
		//Para funciones y clases!
		if($token[0] === T_FUNCTION || $token[0] === T_CLASS) {
			$funciones[$key] = array();
			$funciones[$key]['inicioLinia'] =  $token[2];
			//$funciones[$key]['inicioLlaveArreglo'] =  $key; //No hace falta porque es la llave del arreglo principal.
			$funciones[$key]['fin'] =  null;
			$funciones[$key]['cantidadAperturas'] = 0;
			$funciones[$key]['cantidadCierres'] = 0;
			$funciones[$key]['variables'] = array();
		}
		
	} else {
		if($token === '{') {
			$funciones[max(array_keys($funciones))]['cantidadAperturas']++;
		}
		
		if($token === '}') {
			foreach(array_reverse($funciones, true) as $k => $v) {
				if($v['cantidadAperturas'] != $v['cantidadCierres']) {
					$funciones[$k]['cantidadCierres']++;
					
					if($funciones[$k]['cantidadAperturas'] === $funciones[$k]['cantidadCierres']) {
						$funciones[$k]['fin'] = $key;
					}
					
					break;
				}
			}
		}
	}
}

var_dump($funciones);
var_dump($variables);
?>