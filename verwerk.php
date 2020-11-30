<?php
  require_once('setup.php');
  $naam = $_POST['naam'];
  $email = $_POST['email'];
  $tekst = $_POST['tekst'];
  echo($naam, $tekst, $email);
?>
