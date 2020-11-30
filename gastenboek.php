<?php
require_once('setup.php');
$results = DB::query('SELECT * FROM gastenboek ORDER BY id DESC');
foreach($results as $result){
  foreach($result as $line){
    echo($line);
  }
  echo '<hr>';
}
?>
