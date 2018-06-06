<!DOCTYPE HTML>
<!--
Arcana by HTML5 UP
html5up.net | @ajlkn
Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../css/main.css" />
  <link rel="shortcut icon" href="../images/icon.ico">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BIBLIOTHEQUE</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/themes/smoothness/jquery-ui.css" />
</head>

<body>
<style>
.navbar-login
{
    width: 305px;
    padding: 10px;
    padding-bottom: 0px;
}

.navbar-login-session
{
    padding: 10px;
    padding-bottom: 0px;
    padding-top: 0px;
}

.icon-size
{
    font-size: 87px;
}
</style>
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container"> 
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span> 
            </button>
            <a target="_blank" href="#" class="navbar-brand">BIBLIOTHEQUE</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="#">MENU 1</a></li>
                <li class="active"><a href="http://bootsnipp.com/snippets/featured/nav-account-manager" target="_blank">MENU 2</a></li>
                 <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">MENU 3
                    <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Option 1 </a></li>
                        <li>a href="#">Option 2</a></li>
                    </ul>
                 </li>              
             </ul>

             <!--LOGIN-->
            <?php 
            if(isset($_SESSION['user_id']))
            { ?>
              <ul class="nav navbar-nav navbar-right">
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      <span class="glyphicon glyphicon-user"></span> 
                      <strong><?= ucfirst($_SESSION['user_firstname']); ?></strong>
                      <span class="glyphicon glyphicon-chevron-down"></span>
                  </a>
                  <ul class="dropdown-menu">
                      <li>
                          <div class="navbar-login">
                              <div class="row">
                                  <div class="col-lg-4">
                                      <p class="text-center">
                                          <span class="glyphicon glyphicon-user icon-size"></span>
                                      </p>
                                  </div>
                                  <div class="col-lg-8">
                                      <p class="text-left"><strong><?= ucfirst($_SESSION['user_firstname']) . ' ' . strtoupper($_SESSION['user_name']); ?></strong></p>
                                      <p class="text-left small"><?= $_SESSION['user_mail']; ?></p>
                                      <p class="text-left small"><?= strtoupper($_SESSION['user_city']) . ' - ' . $_SESSION['user_postalcode']; ?> </p>
                                      <p class="text-left">
                                          <a href="#" class="btn btn-primary btn-block btn-sm">Modifier informations</a>
                                      </p>
                                  </div>
                              </div>
                          </div>
                      </li>
                      <li class="divider"></li>
                      <li>
                          <div class="navbar-login navbar-login-session">
                              <div class="row">
                                  <div class="col-lg-12">
                                      <p>
                                          <a href="../fonctions/deconnexion.php" onclick="return(confirm('Etes-vous sur de vouloir vous déconnecter ?'))" class="btn btn-danger btn-block">Deconnexion</a>
                                      </p>
                                  </div>
                              </div>
                          </div>
                      </li>
                  </ul>
              </li>
          </ul>
          <?php  }
          else
          { ?>
     <ul class="nav navbar-nav navbar-right">
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      <span class="glyphicon glyphicon-user"></span> 
                      <span class="glyphicon glyphicon-chevron-down"></span>
                  </a>
                  <ul class="dropdown-menu">
                  <div class="navbar-login navbar-login-session">
                              <div class="row">
                                  <div class="col-lg-12">
                                      <p>
                                          <a href="index.php?p=connexion" class="btn btn-info btn-block">Connexion</a>
                                      </p>
                                  </div>
                              </div>
                          </div>
                      <li class="divider"></li>
                      <li>
                      <div class="col-lg-12">
                        <p>
                          <a href="index.php?p=inscription">Pas encore inscris?</a>
                        </p>
                      </div>
                      </li>
                  </ul>
              </li>
          </ul>
         <?php } ?>

           
        </div>
    </div>
</div>
<br><br>
<div class="container">

