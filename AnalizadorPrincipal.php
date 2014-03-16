<?php

require_once 'AnalizadorVariables.php';

class AnalizadorPrincipal {

	private $directorioAAnalizar; //Path completo al directorio que se quiere analizar.
	
	private $analisisRecursivo;
	
	private $extensionesAAnalizar;
	
	function __construct($directorioAAnalizar, $analisisRecursivo = true) {
		$this->directorioAAnalizar = $directorioAAnalizar;
		
		$this->analisisRecursivo = $analisisRecursivo;
		
		$this->extensionesAAnalizar = "/(php\z)/i"; // "/(php\z|txt\z)/i"
	}
	
	/**
	 * En principio se analizarán solo archivos *.php
	 */
	public function iniciarAnalisisCompleto() {
		$this->analizarDirectorio($this->directorioAAnalizar);
	}
	
	private function analizarDirectorio($pathAlDirectorio) {
		$dirContent = $this->scanearDirectorio($pathAlDirectorio);
		
		$analizadorVariables = new AnalizadorVariables(); //TODO: esto está mal acá, porque lo crea cada vez que lo llama la recursión, por ahí puede ser singleton.
		
		foreach($dirContent as $file) {
			$pathCompleto = $pathAlDirectorio . $file;
		
			if(! is_dir($pathCompleto)) {
				if(preg_match($this->extensionesAAnalizar, $pathCompleto)) {
					$source = file_get_contents($pathCompleto);
		
					$tokens = token_get_all($source);
						
					$analizadorVariables->buscarVariablesNoDefinidas($tokens);
				}
			} else {
				if($this->analisisRecursivo && ! preg_match('/^\./', $file)) { //No entro en directorios que empiezan con un .
					$this->analizarDirectorio($pathCompleto . '/');
				}
			}
		}
	}
	
	private function scanearDirectorio($pathAlDirectorio) {
		$dir = scandir($pathAlDirectorio);
		
		return $dir;
	}
}
?>