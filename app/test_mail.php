<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
session_start();
include '../includes/config_bdd.php';
include '../includes/templates/header.php';
require '../vendor/autoload.php'; ?>

<h1>TEST d'envoie d'un mail</h1>

<?php
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

$bodyContent = '<h1>Voici votre code de récupération de mot de passe</h1>';
$bodyContent .= '<p>code : <b>' . $_SESSION['recup_code'] . '</b></p>';

$mail->Subject = 'Récupération mot de passe';
$mail->Body    = $bodyContent;

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
?>

?>
