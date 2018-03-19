<?php
include '../includes/config_bdd.php';

if(isset($_POST['submit']))
{
  $email = htmlspecialchars($_POST['email']);
  $password = htmlspecialchars($_POST['password']);

  if(isset($_POST['email']) AND isset($_POST['password']))
  {
    $query = $bdd->prepare("SELECT * FROM UTILISATEUR WHERE MAIL_UTI = ? AND MDP_UTI = SHA1(?)");
    $query -> execute(array($email, $password));
    $user = $query -> rowCount();

    if($user > 0)
    {
      $user_infos = $query->fetch();

      session_start();
      $_SESSION['user_id'] = $user_infos['NUM_UTI'];
      $_SESSION['user_firstname'] = $user_infos['PRENOM_UTI'];
      $_SESSION['user_name'] = $user_infos['NOM_UTI'];
      $_SESSION['user_password'] = $user_infos['MDP_UTI'];
      $_SESSION['user_admin'] = $user_infos['ADMINISTRATEUR'];
      $_SESSION['user_paymentdate'] = $user_infos['DATE_PAIEMENT'];
      $_SESSION['user_postalcode'] = $user_infos['CODE_POSTAL'];
      $_SESSION['user_city'] = $user_infos['VILLE'];
      $_SESSION['user_mail'] = $user_infos['MAIL_UTI'];

      if($_SESSION['user_admin'] == 1)
      header('location:index.php?p=administration');
      else
      header("location:index.php?p=consultation");
    }
    else
    {
      $erreur = "Adresse mail et/ou mot de passe incorrect ! ";
    }
  }
}

?>

<?php include '../includes/templates/header.php'; ?>

<div class="container">

  <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
    <div class="panel panel-info" >
      <div class="panel-heading">
        <div class="panel-title">CONNEXION</div>
        <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="index.php?p=recuperation_mdp">Mot de passe oublié?</a></div>
      </div>

      <div style="padding-top:30px" class="panel-body" >
        <?php
        if(isset($erreur))
        {?>
          <div id="erreur"><b><?= $erreur; ?></b></div>
        <?php } ?>

        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

        <form id="loginform" class="form-horizontal" role="form" action="connexion.php" method="post">

          <div style="margin-bottom: 25px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input id="login-username" type="email" class="form-control" name="email" required  placeholder="saisissez votre email">
          </div>

          <div style="margin-bottom: 25px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input id="login-password" type="password" class="form-control" name="password" required  placeholder="saisissez votre mot de passe">
          </div>



          <div class="input-group">

            <div style="margin-top:10px" class="form-group">

              <div class="col-sm-12 controls">
                <button type="submit" name="submit" class="btn btn-info btn-block">Valider</button>
              </div>
            </div>


            <div class="form-group">
              <div class="col-md-12 control">
                <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                  Vous n'avez pas de compte ?
                  <a href="index.php?p=inscription">Inscrivez vous ici !</a>
                </div>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>

  </div>
