<?php
session_start();
session_destroy();
header('location: ../app/index.php?p=home');
exit;
?>
