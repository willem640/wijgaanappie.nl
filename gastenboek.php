<?php
require_once('setup.php');
$results = DB::query('SELECT * FROM gastenboek ORDER BY id DESC');
var_dump($results);
?>
