<?php

require_once 'AnalizadorPrincipal.php';

/*
$a = new Analizador();

$a->metodoPrincipal();
*/

$ap = new AnalizadorPrincipal("/var/www/analizador/");

$ap->iniciarAnalisisCompleto();

?>