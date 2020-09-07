<?php
session_start();
require_once 'setup.php';
$start_date=(date("D")=="Mon" ? strtotime("today") : strtotime("last monday"));
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
<?php
$perm_level = DB::query("SELECT perm_level FROM users WHERE username = %s", $_SESSION['username'])[0]['perm_level'];
if($perm_level != 2){header("Location: login.php");}
?>
</div>
</body>

</html>
