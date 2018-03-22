<?php session_start();
include '../includes/config_bdd.php';
include '../fonctions/images.php';
require '../fonctions/mails.php';
if(isset($_POST['valider_oeuvre']))
{
  //contre les injections sql
  $titre_oeuvre = htmlspecialchars($_POST['titre_oeuvre']);
  $annee_parution = htmlspecialchars($_POST['annee_parution']);
  $prenom_auteur = htmlspecialchars($_POST['prenom_auteur']);
  $nom_auteur = htmlspecialchars($_POST['nom_auteur']);
  $categorie = htmlspecialchars($_POST['categorie']);
  $sous_categorie = htmlspecialchars($_POST['sous_categorie']);

  //on check si l'auteur existe déjà dans la BDD
  $check_auteur = $bdd->prepare('SELECT * FROM AUTEUR WHERE PRENOM_AUTEUR = ? AND NOM_AUTEUR = ?');
  $check_auteur -> execute(array($prenom_auteur, $nom_auteur));
  $count = $check_auteur -> rowCount();

  if($count == 0) //s'il n'existe pas, on l'insere dans la table AUTEUR
  {
    $insert_auteur = $bdd->prepare('INSERT INTO AUTEUR(prenom_auteur, nom_auteur) VALUES(?, ?)');
    $insert_auteur -> execute(array($prenom_auteur, $nom_auteur));
  }

  //on refait une recherche pour récupérer l'id de l'auteur
  $find_auteur = $bdd->prepare('SELECT * FROM AUTEUR WHERE prenom_auteur = ? AND nom_auteur = ?');
  $find_auteur -> execute(array($prenom_auteur, $nom_auteur));
  $result_auteur = $find_auteur->fetch();

  //récupération de l'id max de la colonne id_oeuvre pour créer une cote personnalisée
  $find_max_id = $bdd->query('SELECT MAX(ID_OEUVRE) FROM OEUVRE');
  $row = $find_max_id->fetch();

  if($categorie == 1) //categorie = divertissement
  {
    $cote_oeuvre = 'LDI00' . $row['MAX(ID_OEUVRE)'];
  }
  else if($categorie == 2) //categorie = sciences
  {
    $cote_oeuvre = 'LSC00' . $row['MAX(ID_OEUVRE)'];
  }

  //categorie = santé (cote: LSA00)

  //categorie = arts, lettres, langues (cote: LAL00)

  //upload de l'image téléchargée
  $tmp = $titre_oeuvre; $tmp = str_replace(' ', '', $tmp);
  $upload_image = upload('illustration', '../images/oeuvres/'.$tmp.'.jpg', FALSE, array('jpg', 'jpeg'));
  $illustration = $tmp.'.jpg';

  //insertion des données dans la table OEUVRE
  $insert_oeuvre = $bdd->prepare('INSERT INTO OEUVRE(COTE_OEUVRE, TITRE_OEUVRE, ANNEE_PARUTION, ID_AUTEUR, ID_CATEGORIE, ID_SOUS_CATEGORIE, ILLUSTRATION)
  VALUES(?, ?, ?, ?, ?, ?, ?)');
  $insert_oeuvre -> execute(array($cote_oeuvre, $titre_oeuvre, $annee_parution, $result_auteur['ID_AUTEUR'], $categorie, $sous_categorie, $illustration));

}
else if(isset($_POST['valider_categorie']))
{
  $categorie = htmlspecialchars($_POST['libelle_categorie']);
  $insert_categorie = $bdd->prepare('INSERT INTO CATEGORIE(LIBELLE_CATEGORIE) VALUES(?)');
  $insert_categorie->execute(array($categorie));
}
else if(isset($_POST['valider_sous_categorie']))
{
  $categorie = htmlspecialchars($_POST['categorie']);
  $sous_categorie = htmlspecialchars($_POST['sous_categorie']);

  $insert_sous_categorie = $bdd->prepare("INSERT INTO SOUS_CATEGORIE(LIBELLE_SOUS_CATEGORIE, ID_CATEGORIE) VALUES(?, ?)");
  $insert_sous_categorie->execute(array($sous_categorie, $categorie));
}
else if(isset($_POST['valider_achat_livre']))
{
  $titre_oeuvre = htmlspecialchars($_POST['titre_oeuvre']);
  $find_id_oeuvre = $bdd -> prepare('SELECT * FROM OEUVRE WHERE TITRE_OEUVRE = ?');
  $find_id_oeuvre -> execute(array($titre_oeuvre));
  $row = $find_id_oeuvre -> fetch();
  $id_oeuvre = $row['id_oeuvre'];
  date_default_timezone_set('Europe/Paris');
  $date_achat = date('Y-m-d');
  $date_achat;
  $insert_livre = $bdd -> prepare('INSERT INTO LIVRE(ID_OEUVRE, DATE_ACHAT) VALUES(?, ?)');
  $insert_livre -> execute(array($id_oeuvre, $date_achat));
  header('location:index.php?p=administration&action=achat_livre');
}

if(isset($_GET['action']))
{
  if($_GET['action'] == 'valider_commentaire')
  {
    $valider_com = $bdd -> prepare('UPDATE COMMENTAIRE SET STATUT = 1 WHERE ID_COM = ?');
    $valider_com -> execute(array($_GET['id_com']));
    header('location:index.php?p=administration&action=commentaire');
  }
  else if($_GET['action'] == 'supprimer_commentaire')
  {
    $supprimer_com = $bdd -> prepare('DELETE FROM COMMENTAIRE WHERE ID_COM = ?');
    $supprimer_com -> execute(array($_GET['id_com']));
    header('location:index.php?p=administration&action=commentaire');
  }
  else if($_GET['action'] == 'accepter_pret' OR $_GET['action'] == 'refuser_pret')
  {
    $find_id_livre = $bdd -> prepare('SELECT L.ID_LIVRE, O.TITRE_OEUVRE, U.PRENOM_UTI, U.MAIL_UTI FROM EMPRUNT AS E INNER JOIN LIVRE AS L ON E.ID_LIVRE = L.ID_LIVRE INNER JOIN OEUVRE AS O ON L.ID_OEUVRE = O.ID_OEUVRE INNER JOIN UTILISATEUR AS U ON E.NUM_UTI = U.NUM_UTI WHERE ID_EMPRUNT = ?');
    $find_id_livre -> execute(array($_GET['id_emprunt']));
    $row = $find_id_livre -> fetch();

    if($_GET['action'] == 'accepter_pret')
    {
      $update_statut_livre = $bdd -> prepare('UPDATE LIVRE SET STATUT = 1 WHERE ID_LIVRE = ?');
      $update_statut_livre -> execute(array($row['ID_LIVRE']));

      date_default_timezone_set('Europe/Paris');
      $date_pret = date('Y-m-d');
      $date_retour = date('Y-m-d', strtotime($date_pret.' + 1 month'));

      $update_emprunt = $bdd->prepare('UPDATE EMPRUNT SET DATE_PRET = ?, DATE_RETOUR = ?, STATUT = 1 WHERE ID_EMPRUNT = ?');
      $update_emprunt -> execute(array($date_pret, $date_retour, $_GET['id_emprunt']));

      envoieMailAcceptation($row['TITRE_OEUVRE'], $row['PRENOM_UTI'], $row['MAIL_UTI']);

      header('location:index.php?p=administration&action=pret');
    }
    else if($_GET['action'] == 'refuser_pret')
    {
      $delete_emprunt = $bdd -> prepare('DELETE FROM EMPRUNT WHERE ID_EMPRUNT = ?');
      $delete_emprunt -> execute(array($_GET['id_emprunt']));
      envoieMailRefus($row['TITRE_OEUVRE'], $row['PRENOM_UTI'], $row['MAIL_UTI']);

      header('location:index.php?p=administration&action=pret');
    }
  }
  else if($_GET['action'] == 'retour_livre')
  {
    echo 'procédure de retour d\'un livre';
  }



}
?>

<?php include '../includes/templates/header.php'; ?>

<script>
$(function() {
  //autocomplete
  $("#titre_oeuvre").autocomplete({
    source: "searchKeyWord.php",
    minLength: 1
  });
});
</script>

<script>
$(document).ready(function () {
  $('#id_cat').on('change', function () {
    var id_cat = $(this).val();
    if (id_cat) {
      $.ajax({
        type: 'POST',
        url: 'ajaxData.php',
        data: 'ID_CATEGORIE=' + id_cat,
        success: function (html) {
          $('#id_sous_cat').html(html);
        }
      });
    } else {
      $('#id_sous_cat').html('<option value="">Saisissez une catégorie au préalable</option>');
    }
  });

  $('#id_sous_cat').on('change', function () {
    var id_sous_cat = $(this).val();
    if (id_sous_cat) {
      $.ajax({
        type: 'POST',
        url: 'ajaxData.php',
        data: 'ID_SOUS_CATEGORIE=' + id_sous_cat,
        success: function (html) {
        }
      });
    }
  });
});
</script>

<style>
body{margin-top:50px;}
.glyphicon { margin-right:10px; }
.panel-body { padding:0px; }
.panel-body table tr td { padding-left: 15px }
.panel-body .table {margin-bottom: 0px; }
</style>

<div class="container">
  <div class="row">
    <div class="col-sm-3 col-md-3">
      <div class="panel-group" id="accordion">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><span class="glyphicon glyphicon-folder-close">
              </span>Nouveau Contenu</a>
            </h4>
          </div>
          <div id="collapseOne" class="panel-collapse collapse in">
            <div class="panel-body">
              <table class="table">
                <tr>
                  <td>
                    <span class="glyphicon glyphicon-pencil text-primary"></span><a href="index.php?p=administration&action=oeuvre">Oeuvre</a>
                  </td>
                </tr>
                <tr>
                  <td>
                    <span class="glyphicon glyphicon-flash text-success"></span><a href="index.php?p=administration&action=categorie">Catégorie</a>
                  </td>
                </tr>
                <tr>
                  <td>
                    <span class="glyphicon glyphicon-file text-info"></span><a href="index.php?p=administration&action=sous_categorie">Sous catégorie</a>
                  </td>
                </tr>
                <tr>
                  <td>
                    <span class="glyphicon glyphicon-euro text-info"></span><a href="index.php?p=administration&action=achat_livre">Achat livre</a>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><span class="glyphicon glyphicon-folder-close">
              </span>Modération</a>
            </h4>
          </div>
          <div id="collapseOne" class="panel-collapse collapse in">
            <div class="panel-body">
              <table class="table">
                <tr>
                  <td>
                    <span class="glyphicon glyphicon-pencil text-primary"></span><a href="index.php?p=administration&action=commentaire">Commentaires</a>
                  </td>
                </tr>
                <tr>
                  <td>
                    <span class="glyphicon glyphicon-flash text-success"></span><a href="index.php?p=administration&action=pret">Prêts</a>
                  </td>
                </tr>
                <tr>
                  <td>
                    <span class="glyphicon glyphicon-file text-info"></span><a href="index.php?p=administration&action=sous_categorie">##</a>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>



      </div>
    </div>



    <div class="col-sm-9 col-md-9">



      <?php
      if(isset($_GET['action']))
      {
        if($_GET['action'] == 'oeuvre')
        { ?>
          <div class="well">
            <h1>Ajout d'une nouvelle oeuvre</h1>
          </div>

          <form action="administration.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label for="titre">Titre de l'oeuvre</label>
              <input type="text" class="form-control" name="titre_oeuvre" placeholder="saisissez le titre de l'oeuvre">
            </div>
            <div class="form-group">
              <label for="annee_parution">Année de parution</label>
              <input type="text" class="form-control" name="annee_parution" placeholder="saisissez l'année de parution (format: YYYY)">
            </div>
            <div class="form-group">
              <label for="prenom_auteur">Prenom auteur</label>
              <input type="text" class="form-control" name="prenom_auteur" placeholder="saisissez le prénom de l'auteur">
            </div>
            <div class="form-group">
              <label for="nom_auteur">Nom auteur</label>
              <input type="text" class="form-control" name="nom_auteur" placeholder="saisissez le nom de l'auteur">
            </div>

            <?php
            $query = $bdd->query('SELECT ID_CATEGORIE, LIBELLE_CATEGORIE FROM CATEGORIE');

            $count = $query->rowCount();
            ?>
            <div class="form-group">
              <label for="categorie">Categorie</label>
              <select class="form-control" id="id_cat" name="categorie">
                <option>
                  <?php
                  if($count > 0)
                  {
                    echo "Sélectionnez une catégorie";
                    while($row = $query->fetch())
                    {
                      echo '<option value="' . $row['ID_CATEGORIE'] . '">' . $row['LIBELLE_CATEGORIE'] . '</option>';
                    }
                  }
                  ?>
                </option>
              </select>
            </div>

            <div class="form-group">
              <label for="sous_categorie">Sous catégorie</label>
              <select class="form-control" id="id_sous_cat" name="sous_categorie">
                <option value="">Saisissez d'abord une catégorie.</option>
              </select>
            </div>

            <div class="form-group">
              <label for="illustration">Illustration (.jpg)</label>
              <input type="file" class="form-control" name="illustration">
            </div>

            <button type="submit" class="btn btn-default" name="valider_oeuvre">Valider</button>
          </form>


        <?php }
        else if($_GET['action'] == 'categorie')
        { ?>
          <div class="well">
            <h1>Ajout d'une nouvelle catégorie</h1>
          </div>
          <form action="administration.php" method="post">
            <div class="form-group">
              <label for="libelle_categorie">Libelle catégorie</label>
              <input type="text" class="form-control" name="libelle_categorie" placeholder="saisissez le libelle de la catégorie">
            </div>

            <button type="submit" class="btn btn-default" name="valider_categorie">Valider</button>
          </form>
        <?php }
        else if($_GET['action'] == 'sous_categorie')
        { ?>
          <div class="well">
            <h1>Ajout d'une nouvelle sous catégorie</h1>
          </div>
          <form action="administration.php" method="post">

            <?php
            $query = $bdd->query('SELECT ID_CATEGORIE, LIBELLE_CATEGORIE FROM CATEGORIE');

            $count = $query->rowCount();
            ?>

            <div class="form-group">
              <div class="form-group">
                <label for="categorie">Categorie</label>
                <select class="form-control" id="id_cat" name="categorie">
                  <option>
                    <?php
                    if($count > 0)
                    {
                      echo "Sélectionnez une catégorie";
                      while($row = $query->fetch())
                      {
                        echo '<option value="' . $row['ID_CATEGORIE'] . '">' . $row['LIBELLE_CATEGORIE'] . '</option>';
                      }
                    }
                    ?>
                  </option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="libelle_sous_categorie">Libelle sous catégorie</label>
              <input type="text" class="form-control" name="sous_categorie" placeholder="saisissez le libelle de la catégorie">
            </div>

            <button type="submit" class="btn btn-default" name="valider_sous_categorie">Valider</button>
          </form>
        <?php  }
        else if($_GET['action'] == 'achat_livre')
        { ?>
          <div class="well">
            <h1>Achat d'un livre</h1>
          </div>

          <form action="administration.php" method="post">
            <div class="form-group">
              <label for="oeuvre">Titre oeuvre</label>
              <input type="text" class="form-control" name="titre_oeuvre" id="titre_oeuvre" placeholder="saisissez le titre de l'eouvre">
            </div>

            <button type="submit" class="btn btn-default" name="valider_achat_livre">Valider</button>

            <hr />

            <i>Oeuvre non référencée? <a href="index.php?p=administration&action=oeuvre">Cliquez ici !</a></i>
          </form>



        <?php }
        else if($_GET['action'] == 'commentaire')
        { ?>
          <div class="well">
            <h1>Modération des commentaires</h1>
          </div>

          <table class="table table-striped">
            <thead>
              <tr>
                <th>
                  ID_COM
                </th>
                <th>OEUVRE</th>
                <th>COMMENTAIRE</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>

              <?php
              $query_com = $bdd->query('SELECT * FROM OEUVRE INNER JOIN COMMENTAIRE ON OEUVRE.id_oeuvre = COMMENTAIRE.ID_OEUVRE WHERE STATUT = 0');
              while( $row = $query_com -> fetch())
              { ?>
                <tr>
                  <td>
                    <?= $row['ID_COM'] ?>
                  </td>
                  <td><?= strtoupper($row['titre_oeuvre']) ?></td>
                  <td><?= $row['TEXTE_COM'] ?></td>
                  <td class="text-center"><a class='btn btn-info btn-xs' href='index.php?p=administration&action=valider_commentaire&id_com=<?= $row['ID_COM'] ?>'><span class="glyphicon glyphicon-ok"></span> Valider</a> <a href='index.php?p=administration&action=supprimer_commentaire&id_com=<?= $row['ID_COM'] ?>' class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> Supprimer</a></td>
                </tr>
              <?php } ?>

            </tbody>
          </table>

        <?php }
        else if($_GET['action'] == 'pret')
        { ?>
          <h2 style="color: #2980b9">Demandes de prêts</h4>

            <table class="table table-striped">
              <thead>
                <tr>
                  <th>
                    UTILISATEUR
                  </th>
                  <th>
                    TITRE OEUVRE
                  </th>
                  <th>ID LIVRE</th>
                  <th>STOCK</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>

                <?php
                $query_pret = $bdd->query('SELECT E.ID_EMPRUNT,U.NOM_UTI, U.PRENOM_UTI, O.titre_oeuvre, O.id_oeuvre, L.ID_LIVRE
                  FROM OEUVRE AS O
                  INNER JOIN LIVRE AS L
                  ON O.id_oeuvre = L.ID_OEUVRE
                  INNER JOIN EMPRUNT as E
                  ON L.ID_LIVRE = E.ID_LIVRE
                  INNER JOIN UTILISATEUR as U
                  on E.NUM_UTI = U.NUM_UTI
                  WHERE E.STATUT = 0');
                  while( $row = $query_pret -> fetch())
                  { ?>
                    <tr>
                      <td>
                        <?= $row['PRENOM_UTI'] . ' ' . $row['NOM_UTI'] ?>
                      </td>
                      <td><?= strtoupper($row['titre_oeuvre']) ?></td>
                      <td><?= $row['ID_LIVRE'] ?></td>
                      <?php
                      $stock = $bdd -> prepare('SELECT * FROM OEUVRE AS O INNER JOIN LIVRE AS L ON O.id_oeuvre = L.ID_OEUVRE WHERE L.STATUT = 0 AND O.id_oeuvre = ? ');
                      $stock -> execute(array($row['id_oeuvre']));
                      $nb_dispo = $stock -> rowCount();
                      ?>
                      <td><?= $nb_dispo ?></td>
                      <td class="text-center"><a class='btn btn-info btn-xs' href='index.php?p=administration&action=accepter_pret&id_emprunt=<?= $row['ID_EMPRUNT'] ?>'><span class="glyphicon glyphicon-ok"></span> Accepter</a> <a href='index.php?p=administration&action=refuser_pret&id_emprunt=<?= $row['ID_EMPRUNT'] ?>' class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> Refuser</a></td>
                    </tr>
                  <?php } ?>

                </tbody>
              </table>

              <br />
              <br />
              <h2 style="color: #2980b9">Gestion des retours</h4>

                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>
                        UTILISATEUR
                      </th>
                      <th>
                        TITRE OEUVRE
                      </th>
                      <th>ID LIVRE</th>
                      <th>DATE RETOUR MAX</th>
                      <th>ACTION</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $query_pret = $bdd->query('SELECT E.ID_EMPRUNT, E.DATE_RETOUR, U.NOM_UTI, U.PRENOM_UTI, O.titre_oeuvre, O.id_oeuvre, L.ID_LIVRE
                      FROM OEUVRE AS O
                      INNER JOIN LIVRE AS L
                      ON O.id_oeuvre = L.ID_OEUVRE
                      INNER JOIN EMPRUNT as E
                      ON L.ID_LIVRE = E.ID_LIVRE
                      INNER JOIN UTILISATEUR as U
                      on E.NUM_UTI = U.NUM_UTI
                      WHERE E.STATUT = 1');
                      while( $row = $query_pret -> fetch())
                      { ?>
                        <tr>
                          <td>
                            <?= $row['PRENOM_UTI'] . ' ' . $row['NOM_UTI'] ?>
                          </td>
                          <td><?= strtoupper($row['titre_oeuvre']) ?></td>
                          <td><?= $row['ID_LIVRE'] ?></td>
                          <td><?= $row['DATE_RETOUR'] ?></td>
                          <td class="text-center"><a class='btn btn-info btn-xs' href='index.php?p=administration&action=retour_livre&id_emprunt=<?= $row['ID_EMPRUNT'] ?>'><span class="glyphicon glyphicon-ok"></span>Retour livre</a> </td>
                        </tr>
                      <?php } ?>

                  </tbody>
                </table>
              <?php }
            }
            ?>



          </div>
        </div>
      </div>
