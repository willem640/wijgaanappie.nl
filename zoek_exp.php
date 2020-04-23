
<?php
$search=(isset($_GET['zoek']) ?? '');
var_dump($_GET);
echo $search;
session_start();
require_once 'setup.php';
$query=DB::query("SELECT * FROM products WHERE MATCH(title) AGAINST(%s) ORDER BY MATCH(title) AGAINST(%s) DESC", $search, $search);
var_dump($query);
?>

<form method="get" action="zoek_exp.php">
    <input type="text" name="zoek"/>
    <input type="submit"/>
</form>