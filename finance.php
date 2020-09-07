<?php
session_start();
require_once 'setup.php';
$perm_level = DB::query("SELECT perm_level FROM users WHERE username = %s", $_SESSION['username'])[0]['perm_level'];
if($perm_level != 2){header("Location: login.php");}
$all_orders=(isset($_GET['date']) ? json_decode(DB::queryFirstRow("SELECT all_orders FROM finance WHERE date=%s", $_GET['date'])['all_orders'], true) : []);
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
    <h1><?php echo $_GET['date']?>
    <?php
    echo '<div class="mdc-card material-card lijst" id="tikkie">';
            echo '<ul class="mdc-list">';
            foreach($all_orders as $order){
                $user = (empty($order['realname']) ? $order['username'] : $order['realname']);
                $contents=json_decode($order['contents'], true);
                $tot = 0;
                foreach($contents as $prod){
                    $am = $prod['bestelling_amount'];
                    $tot+=1.1*($prod['priceLabel']['now']*$am);
                }
                echo '<li class="mdc-list-item" tabindex="0">'; //List item
                echo '<span class="mdc-list-item__text">' . $user . '</span>'; //Primary text   
                echo '<span class="mdc-list-item__meta">' . round($tot, 2) . '</span>'; //Meta tag
            }
            echo '</ul>';
            echo '</div>';
    ?>
</div>
</div>
</body>

</html>
