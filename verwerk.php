<?php
  require_once('setup.php');
  $naam = $_POST['naam'];
  $email = $_POST['email'];
  $tekst = $_POST['tekst'];
  
  DB::insert('gastenboek', [
    'naam' => $naam,
    'email' => $email,
    'tekst' => $tekst
  ]);

  header("Location: gastenboek.php");
?>
