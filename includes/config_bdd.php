<?php
define ('DB_HOST', 'localhost'); //chemin vers le serveur
define ('DB_PORT', '8888');
define ('DB_DATABASE', 'BIBLIOTHEQUE'); //bdd utilisée
define ('DB_USERNAME', 'root'); //utilisateur autorisé
define ('DB_PASSWORD','root'); //mot de passe user

try {
	$bdd = new PDO('mysql:host=' .DB_HOST. ';port=' .DB_PORT. ';dbname=' .DB_DATABASE, DB_USERNAME , DB_PASSWORD);
	$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$bdd->exec("SET NAMES 'utf8'");

} catch(Exception $e) {
	echo $e->getMessage();
}

