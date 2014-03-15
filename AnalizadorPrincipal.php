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
		$dirContent = $this->scanearDirectorio($this->directorioAAnalizar);
		
		$analizadorVariables = new AnalizadorVariables();
		
		foreach($dirContent as $file) {
			$pathCompleto = $this->directorioAAnalizar . $file;
						
			if(! is_dir($pathCompleto)) {
				if(preg_match($this->extensionesAAnalizar, $pathCompleto)) {
					$source = file_get_contents($pathCompleto);
		
					$tokens = token_get_all($source);
					
					$analizadorVariables->buscarVariablesNoDefinidas($tokens);
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