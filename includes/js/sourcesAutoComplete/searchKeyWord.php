<?php
header('Content-Type: text/html; charset=iso-8859-1');
include '../config_bdd.php';  //On inclue le fichier permettant de se connecter à la BDD

$term = $_GET['term'];

$query = $bdd -> prepare('SELECT * FROM MOT_CLE WHERE LIBELLE_MOT_CLE LIKE :term');

$query -> execute(array('term' => '%' . $term . '%'));

$array = array();

while($row = $query -> fetch())
{
    array_push($array, $row['LIBELLE_MOT_CLE']);
}

echo json_encode($array);
?>