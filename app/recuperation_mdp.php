<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include '../includes/config_bdd.php';

if(isset($_POST['submit_mail_recup']))
{
  $mail = htmlspecialchars($_POST['email']);
  $mail_exist = $bdd -> prepare('SELECT * FROM UTILISATEUR WHERE MAIL_UTI = ?');
  $mail_exist -> execute(array($mail));
  $result_utilisateur = $mail_exist -> fetch();
  $_SESSION['NUM_UTI'] = $result_utilisateur['NUM_UTI'];


  $count = $mail_exist -> rowCount();

  if($count > 0)
  {
    $result = $mail_exist -> fetch();
    $_SESSION['recup_mail'] = $mail;
    $code_recup = "";

    for($i = 0; $i < 8; $i++)
    {
      $code_recup .= mt_rand(0, 9);
    }

    $_SESSION['recup_code'] = $code_recup;

    $check_mail_recup = $bdd -> prepare('SELECT * FROM RECUPERATION_MDP WHERE MAIL_RECUPERATION = ?');
    $check_mail_recup -> execute(array($_SESSION['recup_mail']));
    $count_mail = $check_mail_recup -> rowCount();

    if($count_mail > 0)
    {
      $update_code = $bdd -> prepare('UPDATE RECUPERATION_MDP SET CODE_RECUPERATION = ? WHERE MAIL_RECUPERATION = ?');
      $update_code -> execute(array($_SESSION['recup_code'], $_SESSION['recup_mail']));

    }
    else
    {
      $insert = $bdd -> prepare('INSERT INTO RECUPERATION_MDP(MAIL_RECUPERATION, CODE_RECUPERATION) VALUES(?,?)');
      $insert -> execute(array($_SESSION['recup_mail'], $_SESSION['recup_code']));
    }


    require '../vendor/autoload.php';

    $mail = new PHPMailer;
    $mail->isSMTP();                            // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';             // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                     // Enable SMTP authentication
    $mail->Username = 'qbelaghrouz@gmail.com';          // SMTP username
    $mail->Password = 'quentin1997'; // SMTP password
    $mail->SMTPSecure = 'tls';                  // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                          // TCP port to connect to

    $mail->setFrom('qbelaghrouz@gmail.com', 'La BU');
    $mail->addReplyTo('qbelaghrouz@gmail.com', 'La BU');
    $mail->addAddress($_SESSION['recup_mail']);   // Add a recipient


    $mail->isHTML(true);  // Set email format to HTML

    $bodyContent = '<p>Bonjour <b>' . $result_utilisateur['PRENOM_UTI']. '</b>, voici votre code de récupération de mot de passe.</p>';
    $bodyContent .= '<p>Code : <b>' . $_SESSION['recup_code'] . '</b></p>';

    $mail->Subject = 'Recuperation mot de passe';
    $mail->Body    = $bodyContent;

    if(!$mail->send()) {
      $erreur =  'Le message n\'a pas pu être envoyé';
      echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
      header('location:index.php?p=recuperation_mdp&action=code');
    }

  }
  else {
    $erreur = 'Adresse mail inconnue !';
  }
}
else if(isset($_POST['submit_code_recup']))
{
  $code_recup = htmlspecialchars($_POST['code_recup']);

  $check_code = $bdd -> prepare('SELECT * FROM RECUPERATION_MDP WHERE CODE_RECUPERATION = ?');
  $check_code -> execute(array($code_recup));

  $code_exist = $check_code -> rowCount();

  if($code_exist == 1)
  {
    header('location:index.php?p=recuperation_mdp&action=nouveaumdp');
  }
  else
  {
    $erreur =  "Le code saisie ne correspond pas !";
  }
}
else if(isset($_POST['submit_nouveau_mdp']))
{
  $mdp1 = htmlspecialchars($_POST['mdp1']);
  $mdp2 = htmlspecialchars($_POST['mdp2']);

  if($mdp1 == $mdp2)
  {
    $mdp = sha1($mdp1);
    $update_mdp = $bdd -> prepare('UPDATE UTILISATEUR SET MDP_UTI = ? WHERE NUM_UTI = ? ');
    $update_mdp -> execute(array($mdp, $_SESSION['NUM_UTI']));

    header('location:index.php?p=connexion');
  }
  else
  {
    $erreur = 'Les mots de passes ne sont pas identiques !';
  }
}


include '../includes/templates/header.php';

if(isset($_GET['action']))
{
  if($_GET['action'] == 'code')
  { ?>
    <div class="container">

      <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info" >
          <div class="panel-heading">
            <div class="panel-title">Code de récupération</div>
          </div>

          <div style="padding-top:30px" class="panel-body" >
            <?php
            if(isset($erreur))
            {?>
              <div id="erreur"><b><?= $erreur; ?></b></div>
            <?php } ?>

            <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

            <form method="post">

              <div style="margin-bottom: 25px" class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-cog"></i></span>
                <input type="text" class="form-control" name="code_recup" required  placeholder="saisissez le code envoyé par mail">
              </div>

              <div class="input-group">

                <div style="margin-top:10px" class="form-group">

                  <div class="col-sm-12 controls">
                    <button type="submit" name="submit_code_recup" class="btn btn-info btn-block">Valider</button>
                  </div>
                </div>

              <?php }

              else if($_GET['action'] == 'nouveaumdp')
              { ?>
                <div class="container">

                  <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                    <div class="panel panel-info" >
                      <div class="panel-heading">
                        <div class="panel-title">Nouveau mot de passe</div>
                      </div>

                      <div style="padding-top:30px" class="panel-body" >
                        <?php
                        if(isset($erreur))
                        {?>
                          <div id="erreur"><b><?= $erreur; ?></b></div>
                        <?php } ?>

                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

                        <form method="post">

                          <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-refresh"></i></span>
                            <input type="password" class="form-control" name="mdp1" required  placeholder="saisissez un nouveau mot de passe">
                          </div>
                          <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-refresh"></i></span>
                            <input type="password" class="form-control" name="mdp2" required  placeholder="confirmer le nouveau mot de passe">
                          </div>

                          <div class="input-group">

                            <div style="margin-top:10px" class="form-group">

                              <div class="col-sm-12 controls">
                                <button type="submit" name="submit_nouveau_mdp" class="btn btn-info btn-block">Valider</button>
                              </div>
                            </div>

                          <?php }
                        }
                        else
                        { ?>
                          <div class="container">

                            <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                              <div class="panel panel-info" >
                                <div class="panel-heading">
                                  <div class="panel-title">Récupération de votre mot de passe</div>
                                </div>

                                <div style="padding-top:30px" class="panel-body" >
                                  <?php
                                  if(isset($erreur))
                                  {?>
                                    <div id="erreur"><b><?= $erreur; ?></b></div>
                                  <?php } ?>

                                  <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

                                  <form method="post">

                                    <div style="margin-bottom: 25px" class="input-group">
                                      <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                      <input id="login-username" type="email" class="form-control" name="email" required  placeholder="saisissez votre email">
                                    </div>

                                    <div class="input-group">

                                      <div style="margin-top:10px" class="form-group">

                                        <div class="col-sm-12 controls">
                                          <button type="submit" name="submit_mail_recup" class="btn btn-info btn-block" onclick="myFunction()">Valider</button>
                                        </div>
                                      </div>

                                      <script>
                                      function myFunction() {
                                        alert("Un mail vous a été envoyé pour récupérer votre mot de passe !");
                                      }
                                      </script>

                                    </form>

                                  </div>
                                </div>
                              </div>

                            </div>

                          <?php }
                          ?>
