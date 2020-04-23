
<?php
$search=(isset($_POST['zoek']) ?? '');
var_dump($_POST);
echo $search;
session_start();
require_once 'setup.php';
$query=DB::query("SELECT * FROM products WHERE MATCH(title) AGAINST(%s) ORDER BY MATCH(title) AGAINST(%s) DESC", $search, $search);
var_dump($query);
?>

<form>
    <input type="text" name="zoek"/>
    <input type="submit"/>
</form>