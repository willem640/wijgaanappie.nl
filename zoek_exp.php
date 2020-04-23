
<?php
$search=$_GET['zoek'] ?? '';
echo $search;
session_start();
require_once 'setup.php';
$query=DB::query("SELECT * FROM products WHERE MATCH(title) AGAINST(%s) ORDER BY MATCH(title) AGAINST(%s) DESC", $search, $search);
?>

<form method="get" action="zoek_exp.php">
    <input type="text" name="zoek"/>
    <input type="submit"/>
</form>

<?php

echo '<ul>';
foreach($query as $result){
    echo '<li>' . $result['title'] . ' ' . $result['priceNow'] . ' ' . $result['unitSize'] . '</li>';
}
echo '</ul>';
?>