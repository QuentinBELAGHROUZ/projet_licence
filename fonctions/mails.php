<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;


  require '../vendor/autoload.php';

  function envoieMailAcceptation($titre_oeuvre)
  {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'qbelaghrouz@gmail.com';
    $mail->Password = 'quentin1997';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('qbelaghrouz@gmail.com', 'La BU');
    $mail->addReplyTo('qbelaghrouz@gmail.com', 'La BU');
    $mail->addAddress($_SESSION['user_mail']);   // Add a recipient


    $mail->isHTML(true);  // Set email format to HTML

    $bodyContent = '<p>Bonjour <b>' . ucfirst($_SESSION['user_firstname']). '</b>, Votre prêt concernant le livre <b>' . ' ' . strtoupper($titre_oeuvre) .' </b> a été accepté.</p>';
    $bodyContent .= '<p>Vous pouvez donc venir le récupérer danas votre bibliothéque</p>';
    $bodyContent .= '<br /><br /><p>Cordialement, votre bibliothécaire.</p>';

    $mail->Subject = 'Pret de livre';
    $mail->Body    = $bodyContent;

    if(!$mail->send()) {
      $erreur =  'Le message n\'a pas pu être envoyé';
      echo 'Mailer Error: ' . $mail->ErrorInfo;
    }

  }

  function envoieMailRefus($titre_oeuvre, $prenom_dest, $mail_dest)
  {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'qbelaghrouz@gmail.com';
    $mail->Password = 'quentin1997';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('qbelaghrouz@gmail.com', 'La BU');
    $mail->addReplyTo('qbelaghrouz@gmail.com', 'La BU');
    $mail->addAddress($mail_dest);   // Add a recipient


    $mail->isHTML(true);  // Set email format to HTML

    $bodyContent = '<p>Bonjour <b>' . ucfirst($prenom_dest). '</b>, Votre prêt concernant le livre <b>' . ' ' . strtoupper($titre_oeuvre) .' </b> a été refusé.</p>';
    $bodyContent .= '<p>En effet, le livre souhaité n\'est plus en sstock. Vous pouvez malgrès tout faire une demande de réservation sur la page du livre souhaité</p>';
    $bodyContent .= '<br /><br /><p>Cordialement, votre bibliothécaire.</p>';

    $mail->Subject = 'Pret de livre';
    $mail->Body    = $bodyContent;

    if(!$mail->send()) {
      $erreur =  'Le message n\'a pas pu être envoyé';
      echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
  }


?>
