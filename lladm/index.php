<?php
  require_once('setup.php');
?>
<html>
  <h1>Leerlingadministratie Breeweg</h1>
  <hr>
  <table>
    $results = DB::query('SELECT * FROM lijst');
    foreach($results as $row){
      echo $row;
    }
  </table>
</html>
