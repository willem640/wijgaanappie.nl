<?php
  session_start();
  require_once 'setup.php';
?>
<html>
  <h1>Leerlingadministratie Breeweg</h1>
  <hr>
  <table border="1" cellpadding="5" cellspacing="5" style="width: 100%;">
      <tr>
        <th>Voornaam</th>
        <th>Achternaam</th>
        <th>Leerlingnummer</th>
        <th>Klas</th>
        <th>Dag</th>
        <th>Maand</th>
        <th>Jaar</th>
        <th>Geslacht</th>
        <th>Straat</th>
        <th>Huisnummer</th>
        <th>Postcode</th>
        <th>Stad</th>
        <th>Pakket</th>
      </tr>
    <?php
    $results = DB::query('SELECT * FROM lijst');
    foreach($results as $row){
      echo '<tr>';
      foreach($row as $cell){
        echo '<td>' . $cell . '</td>';
      }
      echo '</tr>';
    }
    ?>
  </table>
</html>
