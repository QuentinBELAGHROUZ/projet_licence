<?php session_start();
include '../includes/config_bdd.php';
if(isset($_POST['valider_commentaire']))
{
  $commentaire = htmlspecialchars($_POST['commentaire']);
  date_default_timezone_set('Europe/Paris');
  $date_commentaire = date('Y-m-d H:i:s');
  $id_oeuvre = htmlspecialchars($_POST['id_oeuvre']);
  $insert_com = $bdd->prepare('INSERT INTO COMMENTAIRE(TEXTE_COM, DATE_COM, NUM_UTI, ID_OEUVRE)
  VALUES(?, ?, ?, ?)');
  $insert_com -> execute(array($commentaire, $date_commentaire, $_SESSION['user_id'], $id_oeuvre));

  header('location:index.php?p=consultation&action=detail&id_oeuvre='.$id_oeuvre);
}

?>

<script>
$(function() {
  //autocomplete
  $("#search").autocomplete({
    source: "searchKeyWord.php",
    minLength: 1
  });
});
</script>

<style>
body{margin-top:50px;}
.glyphicon { margin-right:10px; }
.panel-body { padding:0px; }
.panel-body table tr td { padding-left: 15px }
.panel-body .table {margin-bottom: 0px; }
.user_name{
  font-size:14px;
  font-weight: bold;
}
.comments-list .media{
  border-bottom: 1px dotted #ccc;
}
.btn-glyphicon { padding:8px; background:#ffffff; margin-right:4px; }
.icon-btn { padding: 1px 15px 3px 2px; border-radius:50px;}
</style>

<?php include '../includes/templates/header.php'; ?>

<br />
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-3">

      <?php
      $query_category = "SELECT * FROM CATEGORIE ORDER BY ID_CATEGORIE DESC";
      foreach($bdd->query($query_category) as $row)
      { ?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><span class="glyphicon glyphicon-folder-close">
              </span> <?= $row['LIBELLE_CATEGORIE']; ?></a>
            </h4>
          </div>

          <?php
          $query_sub_category = $bdd->prepare("SELECT * FROM SOUS_CATEGORIE WHERE ID_CATEGORIE = ?");
          $query_sub_category -> execute(array($row['ID_CATEGORIE']));
          $infos = $query_sub_category->fetch();

          ?>

          <div id="collapseOne" class="panel-collapse collapse in">
            <div class="panel-body">
              <table class="table">
                <tr>
                  <td>
                    <a href="index.php?p=consultation&id_sous_cat=<?= $infos['ID_SOUS_CATEGORIE'] ?>" data-toggle="tooltip"><?= $infos['LIBELLE_SOUS_CATEGORIE']; ?></a>
                  </td>
                </tr>

              </table>
            </div>
          </div>
        </div>

      <?php } ?>

    </div>
    <div class="col-sm-9">

      <?php
      if(isset($_GET['id_sous_cat']) OR isset($_GET['motcle']))
      {
        $sql = 'SELECT O.ID_OEUVRE, O.COTE_OEUVRE, O.TITRE_OEUVRE, A.NOM_AUTEUR, A.PRENOM_AUTEUR, O.ANNEE_PARUTION FROM AUTEUR as A INNER JOIN OEUVRE as O on A.ID_AUTEUR = O.ID_AUTEUR INNER JOIN';
        if(isset($_GET['id_sous_cat']))
        {
          $sql .= ' SOUS_CATEGORIE as SC on O.ID_SOUS_CATEGORIE = SC.ID_SOUS_CATEGORIE where SC.ID_SOUS_CATEGORIE = ?';
          $array = array($_GET['id_sous_cat']);
        }
        else if(isset($_GET['motcle']))
        {
          $sql .= ' MOT_CLE_OEUVRE as MCO on O.COTE_OEUVRE = MCO.COTE_OEUVRE INNER JOIN MOT_CLE as MC on MCO.ID_MOT_CLE = MC.ID_MOT_CLE WHERE MC.LIBELLE_MOT_CLE = ?';
          $array = array($_GET['motcle']);
        }
        $query_livres = $bdd->prepare($sql);
        if( $query_livres -> execute($array))
        {
          while( $row = $query_livres -> fetch())
          {

            $tmp = $row['TITRE_OEUVRE']; $tmp = str_replace(' ', '', $tmp); ?>
            <div class="row">
              <div class="col-sm-4">
                <img src="../images/oeuvres/<?= strtolower($tmp) ?>.jpg" style="width:50%">
              </div>
              <div class="col-sm-8">
                <h4><?= $row['TITRE_OEUVRE']; ?></h4>
                <br>
                <p>
                  Auteur : <?= $row['PRENOM_AUTEUR']. ' ' . $row['NOM_AUTEUR']; ?>
                </p>
                <p>
                  Année de parution : <?= $row['ANNEE_PARUTION']; ?>
                </p >
                <?php
                $find_nb_livres = $bdd -> prepare('SELECT * FROM LIVRE WHERE ID_OEUVRE = ?');
                $find_nb_livres -> execute(array($row['ID_OEUVRE']));
                $count_livres = $find_nb_livres -> rowCount();
                ?>
                <p>
                  Disponibilité : <i style="color:#2ecc71"><?= $count_livres ?> exemplaires disponibles</i>
                </p>

                <a href="index.php?p=consultation&action=detail&id_oeuvre=<?= $row['ID_OEUVRE'] ?>">En savoir plus</a>
                <br />
              </div>

            </div>


            <hr />


          <?php } ?>

        <?php  }

      }
      else if(isset($_GET['id_oeuvre']) && isset($_GET['action']))
      {
        if($_GET['action'] == 'detail')
        { ?>
          <div class="container">
            <div class="row">
              <div class="col-md-8">
                <div class="page-header">

                  <?php
                  $informations_oeuvre = $bdd -> prepare('SELECT * FROM OEUVRE as O
                    LEFT JOIN LIVRE as L
                    ON O.id_oeuvre = L.ID_OEUVRE
                    INNER JOIN AUTEUR as A
                    ON O.id_auteur = A.ID_AUTEUR
                    INNER JOIN CATEGORIE as C
                    ON O.id_categorie = C.ID_CATEGORIE
                    INNER JOIN SOUS_CATEGORIE as SC
                    ON O.id_sous_categorie = SC.ID_SOUS_CATEGORIE
                    WHERE O.id_oeuvre = ?');

                    $informations_oeuvre -> execute(array($_GET['id_oeuvre']));
                    $result_oeuvre = $informations_oeuvre -> fetch();
                    ?>
                    <img src="../images/oeuvres/<?= $result_oeuvre['illustration'] ?>" style="width:50%">
                    <h3 style="color:#2c3e50"><?= strtoupper($result_oeuvre['titre_oeuvre']) ?></h3>
                    <p><i style="color:#34495e"><?= $result_oeuvre['LIBELLE_CATEGORIE'] . '>' . $result_oeuvre['LIBELLE_SOUS_CATEGORIE'] ?></i></p>
                    <p><i style="color:#34495e"><?= $result_oeuvre['PRENOM_AUTEUR'] . ' ' . $result_oeuvre['NOM_AUTEUR'], ', paru en ' . $result_oeuvre['annee_parution'] ?></i></p>
                    <br />
                    <p style="text-indent: 15px;"><?= $result_oeuvre['DESCRIPTION'] ?></p>
                    <br />
                    <br />

                    <a class="btn icon-btn btn-info" href="#"><span class="glyphicon btn-glyphicon glyphicon-plus img-circle text-info"></span>Demande de réservation</a>


                    <?php
                    if(isset($_SESSION['user_id']))
                    { ?>
                      <h4><small class="pull-right" data-toggle="collapse" data-target="#demo">Ajouter un commentaire</small> Commentaires </h4>
                    <?php }
                    else
                    { ?>
                      <h4>Commentaires</h4>
                    <?php }
                    ?>

                    <div id="demo" class="collapse">

                      <form action="consultation.php" method="post">
                        <div class="form-group">
                          <?php   echo '<input type="hidden" class="form-control" name = "id_oeuvre"  value='.$_GET['id_oeuvre'].'>' ?>
                          <label for="comment">Votre commentaire:</label>
                          <textarea class="form-control" rows="5" id="comment" name="commentaire"></textarea>
                        </div>
                        <button type="submit" id="com" name="valider_commentaire" class="btn btn-default" onclick="myFunction()">Valider</button>
                      </form>

                      <script>
                      function myFunction() {
                        alert("<b>Votre commentaire a été envoyé!</b> Il est maintenant en attente de modération ");
                      }
                      </script>
                    </div>
                  </div>
                  <div class="comments-list">

                    <?php
                    $find_comments = $bdd->prepare('SELECT U.NUM_UTI, U.NOM_UTI, U.PRENOM_UTI, U.AVATAR_UTI, C.ID_COM, C.TEXTE_COM, C.ID_OEUVRE, C.DATE_COM FROM UTILISATEUR as U INNER JOIN COMMENTAIRE as C on U.NUM_UTI = C.NUM_UTI WHERE C.ID_OEUVRE = ? AND C.STATUT = 1');
                    $find_comments->execute(array($_GET['id_oeuvre']));

                    while( $row = $find_comments -> fetch())
                    { ?>
                      <div class="media">
                        <p class="pull-right"><small><?= $row['DATE_COM'] ?></small></p>
                        <a class="media-left" href="#">
                          <img src="../../images/utilisateurs/<?= $row['AVATAR_UTI']; ?>" style="width: 20px;" >
                        </a>
                        <div class="media-body">

                          <h4 class="media-heading user_name"><?= $row['PRENOM_UTI'] . ' ' . $row['NOM_UTI'] ?></h4>
                          <?= $row['TEXTE_COM'] ?>
                        </div>


                      </div>
                      <br />
                      <br />
                    <?php }

                    ?>

                  </div>
                </div>



              </div>
            </div>
          </div>
        <?php }
      } ?>

    </div>


  </div>
</div>
