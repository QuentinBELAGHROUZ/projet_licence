<?php session_start();
include '../includes/config_bdd.php';

include '../includes/templates/header.php';
?>
<style>
h4 {
  color: #636e72;
  position:relative;
  overflow:hidden;
}
h4:after {
  content:'';
  display:inline-block;
  vertical-align:middle;
  margin-top:0.5em;
  height:0;
  border-top:groove 2px;
  width:100%;
  position:absolute;
}
p {
  color: #636e72;
}
</style>

<div class="container">
  <div class="row">
    <div class="col-sm-4">
      <br />
    <img src="../images/utilisateurs/<?= $_SESSION['user_avatar'] ?>" class="img-rounded" style="width: 75%">

    </div>
    <div class="col-sm-8">
      <br />
      <h4 data-toggle="collapse" data-target="#demo1">Mes informations </h4>
      <div id="demo1" class="collapse">
        <p><b>Prénom :</b> <?= $_SESSION['user_firstname'] ?></p>
        <p><b>Nom :</b> <?= $_SESSION['user_name'] ?></p>
        <p><b>Mail :</b> <?= $_SESSION['user_mail'] ?></p>
        <p><b>Adresse :</b> <?= $_SESSION['user_postalcode'] . ' - ' . strtoupper($_SESSION['user_city']) ?></p>
        <p><b>Date d'inscription :</b> <?= $_SESSION['user_dateinsc'] ?></p>
      </div>

      <br />

      <h4 data-toggle="collapse" data-target="#demo2">Mes prêts en cours </h4>
      <div id="demo2" class="collapse">
        <table class="table table-striped">
    <thead>
      <tr>
        <th>TITRE OEUVRE</th>
        <th>AUTEUR</th>
        <th>DATE PRET</th>
        <th>DATE RENDU</th>
      </tr>
    </thead>
    <tbody>

      <?php
      $find_prets = $bdd -> prepare('SELECT U.NUM_UTI, O.TITRE_OEUVRE, A.PRENOM_AUTEUR, A.NOM_AUTEUR, E.STATUT, E.DATE_PRET, E.DATE_RETOUR, E.EST_TERMINE FROM UTILISATEUR AS U INNER JOIN EMPRUNT AS E
                                     ON U.NUM_UTI = E.NUM_UTI
                                     INNER JOIN LIVRE AS L
                                     ON E.ID_LIVRE = L.ID_LIVRE
                                     INNER JOIN OEUVRE AS O
                                     ON L.ID_OEUVRE = O.ID_OEUVRE
                                     INNER JOIN AUTEUR AS A
                                     ON O.ID_AUTEUR = A.ID_AUTEUR
                                     WHERE U.NUM_UTI = ?');
      $find_prets -> execute(array($_SESSION['user_id']));
      while($row = $find_prets -> fetch())
      { ?>
        <tr>
          <td><?= strtoupper($row['TITRE_OEUVRE']) ?></td>
          <td><?= $row['PRENOM_AUTEUR'] . ' ' . $row['NOM_AUTEUR'] ?></td>
          <td><?= $row['DATE_PRET'] ?></td>
          <td><?= $row['DATE_RETOUR'] ?></td>
        </tr>
      <?php }



       ?>
    </tbody>
  </table>
      </div>

      <br />

      <h4 data-toggle="collapse" data-target="#demo3">Mes réservations </h4>
      <div id="demo3" class="collapse">
        <i>tableau avec réservations</i>
      </div>

    </div>
  </div>
</div>
