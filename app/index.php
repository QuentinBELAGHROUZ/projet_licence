<?php

if(isset($_GET['p'])) {
    $p = $_GET['p'];
}
else {
    $p = 'home';
}


if($p === 'home') {
    require 'home.php';
}
elseif ($p === 'connexion') {
    require 'connexion.php';
}
elseif ($p == 'inscription') {
    require 'inscription.php';
}
elseif ($p == 'consultation') {
    require 'consultation.php';
}
elseif ($p == 'administration') {
    require 'administration.php';
}


