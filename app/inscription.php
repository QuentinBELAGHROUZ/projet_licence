<?php
session_start();
include '../includes/config_bdd.php';
include '../fonctions/images.php';
if(isset($_POST['submit']))
{
    $name = htmlspecialchars($_POST['name']);
    $firstname = htmlspecialchars($_POST['firstname']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $password2 = htmlspecialchars($_POST['password2']);
    $postalcode = htmlspecialchars($_POST['postalcode']);
    $city = htmlspecialchars($_POST['city']);
    $contribution_date = date("y-m-d");
    $admin = 0;

    //Verification adresse mail existante
    $mail = $bdd->prepare("SELECT * FROM UTILISATEUR WHERE MAIL_UTI = ?");
    $mail->execute(array($email));
    $check = $mail->rowCount();

    if($check == 0)
    {
        if($password == $password2)
        {
            $password_tmp = sha1($password);

            $tmp = $firstname.$name;

            $upload_image = upload('avatar', '../images/utilisateurs/'.$tmp.'.jpg', FALSE, array('jpg', 'jpeg'));
            $avatar = $tmp.'.jpg';

            $insertion = $bdd -> prepare("INSERT INTO UTILISATEUR(NOM_UTI, PRENOM_UTI, MDP_UTI, ADMINISTRATEUR, CODE_POSTAL, VILLE, MAIL_UTI, AVATAR_UTI)
            VALUES(?, ?, ?, ?, ?, ?, ?, ?);");
            $insertion -> execute(array($name, $firstname, $password_tmp, $admin, $postalcode, $city, $email, $avatar));

            $query = $bdd->prepare("SELECT * FROM UTILISATEUR WHERE MAIL_UTI = ?");
            $query -> execute(array($email));
            $user = $query -> rowCount();

            if($user > 0)
            {
                $user_infos = $query->fetch();

                $_SESSION['user_id'] = $user_infos['NUM_UTI'];
                $_SESSION['user_firstname'] = $user_infos['PRENOM_UTI'];
                $_SESSION['user_name'] = $user_infos['NOM_UTI'];
                $_SESSION['user_password'] = $user_infos['MDP_UTI'];
                $_SESSION['user_admin'] = $user_infos['ADMINISTRATEUR'];
                $_SESSION['user_paymentdate'] = $user_infos['DATE_PAIEMENT'];
                $_SESSION['user_postalcode'] = $user_infos['CODE_POSTAL'];
                $_SESSION['user_city'] = $user_infos['VILLE'];
                $_SESSION['user_mail'] = $user_infos['MAIL_UTI'];
                $_SESSION['user_avatar'] = $user_infos['AVATAR_UTI'];

                header("location:index.php?p=consultation");
            }
        }
        else
            $erreur = "Les mots de passes ne sont pas identiques !";
    }
    else
        $erreur = "Adresse mail déjà utilisée !";

}
?>

<?php include '../includes/templates/header.php'; ?>

<div class="container">
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">INSCRIPTION</div>
                    </div>

                    <div style="padding-top:30px" class="panel-body" >
                    <?php
                        if(isset($erreur))
                        {?>
                            <div id="erreur"><b><?= $erreur; ?></b></div>
                        <?php } ?>

                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

                        <form id="loginform" class="form-horizontal" role="form" action="inscription.php" method="post" enctype="multipart/form-data">

                            <div style="margin-bottom: 25px" class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                 <input id="login-username" type="text" class="form-control" name="name"  placeholder="saisissez votre nom">
                            </div>

                            <div style="margin-bottom: 25px" class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                 <input id="login-username" type="text" class="form-control" name="firstname" placeholder="saisissez votre prenom">
                            </div>

                            <div style="margin-bottom: 25px" class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                 <input id="login-username" type="email" class="form-control" name="email" placeholder="saisissez votre email">
                            </div>

                            <div style="margin-bottom: 25px" class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                 <input id="login-password" type="password" class="form-control" name="password" placeholder="saisissez votre mot de passe">
                             </div>

                             <div style="margin-bottom: 25px" class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                 <input id="login-password" type="password" class="form-control" name="password2" placeholder="confirmez votre mot de passe">
                             </div>

                             <div style="margin-bottom: 25px" class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                                 <input id="login-password" type="text" class="form-control" name="postalcode" placeholder="saisissez votre code postal">
                             </div>

                             <div style="margin-bottom: 25px" class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                                 <input id="login-password" type="text" class="form-control" name="city" placeholder="saisissez votre ville">
                             </div>

                             <div style="margin-bottom: 25px" class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-picture"></i></span>
                                 <input type="file" class="form-control" name="avatar">
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
                                            Vous avez déjà un compte ?
                                        <a href="index.php?p=inscription">Connectez vous ici !</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
        </div>

    </div>
