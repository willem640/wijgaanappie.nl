<?php
session_start();
require_once 'setup.php';
$perm_level = DB::query("SELECT perm_level FROM users WHERE username = %s", $_SESSION['username'])[0]['perm_level'];
if($perm_level != 2){header("Location: login.php");}
$contents=(isset($_GET['date']) ? DB::query("SELECT all_orders FROM finance WHERE date=%s", $_GET['date']) : []);
print_r($contents);
print_r(gettype($_GET['date']));
?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php include 'header_material.php'?>
</head>
<body>
    <?php include 'mobile_banner.php'?>
<div class="wrapper">
<form method="get">
    <input type="date" value="<?php echo $_POST['date'] ?? date('Y-m-d')?>" name="date">
    <input type="submit">
</form>
<div id="card">
    <?php
    //var_dump($contents);
    ?>
</div>
</div>
</body>

</html>
