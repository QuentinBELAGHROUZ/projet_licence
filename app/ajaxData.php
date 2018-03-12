<?php include '../includes/config_bdd.php';

if(isset($_POST['ID_CATEGORIE']))
{
  echo $_POST['ID_CATEGORIE'];
  $sql = "SELECT C.ID_CATEGORIE, C.LIBELLE_CATEGORIE, SC.ID_SOUS_CATEGORIE, SC.LIBELLE_SOUS_CATEGORIE
  FROM CATEGORIE as C
  INNER JOIN SOUS_CATEGORIE as SC
  ON C.ID_CATEGORIE = SC.ID_CATEGORIE
  WHERE C.ID_CATEGORIE = ".$_POST['ID_CATEGORIE']." ";

  $query = $bdd->query($sql);

  $count = $query->rowCount();

  if($count > 0)
  {
    echo '<option value="">Sélectionnez une sous catégorie</option>';

    while( $row = $query -> fetch())
    {
        echo '<option value="' . $row['ID_SOUS_CATEGORIE'] . '">' . $row['LIBELLE_SOUS_CATEGORIE'] . ' </option>';
    }
  }
}

 ?>
