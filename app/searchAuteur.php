<?php
header('Content-Type: text/html; charset=iso-8859-1');
include '../includes/config_bdd.php';  //On inclue le fichier permettant de se connecter à la BDD

if(isset($_GET['term']))
{
    $return_arr = array();
    $term = $_GET['term'];

    $query = $bdd -> prepare('SELECT * FROM AUTEUR WHERE NOM_AUTEUR LIKE :term');

    $query -> execute(array('term' => '%' . $term . '%'));

    $array = array();

    while($row = $query -> fetch())
    {
        $return_arr[] = $row['NOM_AUTEUR'] . ' ' . $row['PRENOM_AUTEUR'];
    }

    echo json_encode($return_arr);
}
?>
