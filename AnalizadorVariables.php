<?php

class AnalizadorVariables {
	
	public function metodoPrincipal() {
		$file_path = "/var/www/analizador/ejemplo.php";
		
		$source = file_get_contents($file_path);
		
		$tokens = token_get_all($source);
		
		$this->buscarVariablesNoDefinidas($tokens);
	}

	/**
	 * Busca en el código (tokens del archivo) la utilización de variables
	 * no definidas.
	 * 
	 * @param array() $tokens
	 */
	private function buscarVariablesNoDefinidas($tokens) {		
		$funciones = array();
		
		//var_dump($tokens);
		
		foreach($tokens as $key => $token) {
			if(is_array($token)) {
				if($token[0] === T_VARIABLE) {
					if(! in_array($token[1], $funciones[max(array_keys($funciones))]['variables'])) {
						if($this->esDefinicionDeVariable($key, $tokens)) {
							$funciones[max(array_keys($funciones))]['variables'][] = $token[1];
						} else {
							if($token[1] !== '$this') { //TODO: por ahora los $this no los tenemos en cuenta, habría que fijarse si está definido en la clase.
								var_dump('Error: Variable ' . $token[1] . ' no definida (linea ' . $token[2] . ').');
							}
						}
					}
					
				}
			
				//Para funciones y clases!
				if($token[0] === T_FUNCTION || $token[0] === T_CLASS) {
					$funciones[$key] = array();
					$funciones[$key]['tipo'] = $token[0];
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
							
							//Termina la clase o función!
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
	}


	/**
	 * Retorna true Si el token que está en la posición $nroToken es una variable de clase
	 * en el momento en que está se define, o si es un parametro de un método de una clase,
	 * o si es na variable a la que se le está asignando un valor. Falso en caso contrario.
	 * @param unknown $nroToken
	 * @param unknown $tokens
	 * @return boolean
	 */
	private function esDefinicionDeVariable($nroToken, $tokens) {
		return $this->esT_VAR($nroToken, $tokens) || 
					$this->esParametro($nroToken, $tokens) || 
					$this->esAsignacionDeVariable($nroToken, $tokens);
	}
	
	
	/**
	 * Si el token que está en la posición $nroToken es una variable de clase
	 * en el momento en que está se define, retorna true, falso en caso contrario.
	 *
	 * @param int $nroToken
	 * @param array() $tokens
	 * @return boolean
	 */
	private function esT_VAR($nroToken, $tokens) {
		
		$tokenActual = $tokens[$nroToken];
		
		if(is_array($tokenActual) && $tokenActual[0] == T_VARIABLE) {
			
			$tokenAnterior2 = $tokens[$nroToken - 2];
			
			if(is_array($tokenAnterior2) && $tokenAnterior2[0] === T_VAR) {
				return true;
			}	
		}
		
		return false;
	}
	
	/**
	 * Si el token que está en la posición $nroToken es un parametro del método
	 *  retorna true, falso en caso contrario.
	 *  
	 *  TODO: Esto fallaría en el caso de las variables de clase, pero como me fijo antes si
	 *  es una variable de clase, no hay problema. Pero habría que arreglarlo.
	 *
	 * @param int $nroToken
	 * @param array() $tokens
	 * @return boolean
	 */
	private function esParametro($nroToken, $tokens) {
		$tokenActual = $tokens[$nroToken];
		
		if(is_array($tokenActual) && $tokenActual[0] == T_VARIABLE) {
			$i = $nroToken - 1;
			
			while($tokens[$i]) {
				if(is_array($tokens[$i])) {
					if($tokens[$i][0] === T_FUNCTION) {
						return true;
					}
				} else {
					if($tokens[$i] === '{') {
						return false;
					}
				}
				
				$i--;
			}
		}
		
		return false;
	}
	
	/**
	 * Si el token que está en la posición $nroToken se le está asignando un valor
	 * ($variable = valor), retorna true, falso en caso contrario.
	 * 
	 * @param int $nroToken
	 * @param array() $tokens
	 * @return boolean
	 */
	private function esAsignacionDeVariable($nroToken, $tokens) {
		$tokenActual = $tokens[$nroToken];
		
		if(is_array($tokenActual) && $tokenActual[0] == T_VARIABLE) {
			$tokenPosterior = $tokens[$nroToken + 1];
			$tokenPosterior2 = $tokens[$nroToken + 2];
			
			if(! is_array($tokenPosterior) && $tokenPosterior === '=') {
				return true;
			}
			
			if(is_array($tokenPosterior) && ! is_array($tokenPosterior2) && trim($tokenPosterior[1]) === '' && $tokenPosterior2 === '=') {
				return true;
			}
		}
		
		return false;
	}
}
?>