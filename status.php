<?php
session_start();
require_once 'setup.php';
require_once 'header.php';
$query = DB::query('SELECT * FROM komt_chobin_naar_de_appie')[0];
$komt_chobin = $query['komt hij'];

$array = [];
$array['status'] = $komt_chobin;
$json = json_encode($array);
echo($json);
?>
