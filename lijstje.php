<?php
session_start();
require_once 'setup.php';


if (!($_SESSION['loggedin'] ?? false)) {
    header('Location: login.php?return=lijstje.php');
}
?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php include 'header_material.php' ?>
    </head>
    <body>
        <?php include 'mobile_banner.php' ?>
        <?php
        $perm_level = DB::query('SELECT perm_level FROM users WHERE username = %s', $_SESSION['username'])[0]['perm_level'];
        if ($perm_level >= 2) {
            $all_orders = DB::query('SELECT * FROM current_orders');
            $j = 0; //Zo doe k ff index snap key=>value nie. fight me
            echo '<div class="wrapper">';
            foreach ($all_orders as $orders) {
                $contents = json_decode($orders['contents'], true);
                $order_history = json_decode(DB::query('SELECT previous_orders FROM users WHERE username=%s', $orders['username'])[0]['previous_orders'], true);
                $order_history[date("d-m-Y")] = array_merge(($order_history[date("d-m-Y")] ?? []), $contents);
                $order_history_json = json_encode($order_history);
                if (isset($_POST['clear'])) {
                    DB::update('users', ['previous_orders' => $order_history_json], 'username=%s', $orders['username']);
                }
                //$order_history = array_merge($test,$order_history);
                //print_r($order_history);
                if (empty($contents)) {
                    goto end_loop;
                } //Zo krijg ik niet telkens lege orders te zien was best verwarrend   
                echo '<div class="mdc-card material-card">';
                echo '<h3>' . (empty($orders['realname']) ? $orders['username'] : $orders['realname']) . '</h3>';
                echo '<ul class="mdc-list mdc-list--two-line">';
                $subtotal = $bez = $total = 0;
                foreach ((array) $contents as $prod) {
                    $am = $prod['bestelling_amount'];
                    echo '<li class="mdc-list-item" tabindex="0">'; //List item
                    echo '<span class="mdc-list-item__text">'; //Span for texts and meta tag
                    echo '<span class="mdc-list-item__primary-text">' . $prod['description'] . '</span>'; //Primary text
                    echo '<span class="mdc-list-item__secondary-text">' . $prod['bestelling_amount'] . '</span>'; //Secondary text                    
                    echo '</span>';
                    echo '<span class="mdc-list-item__meta">€' . $prod['priceLabel']['now'] . '</span>'; //Meta tag
                    $subtotal += $prod['priceLabel']['now'] * $am; // when items don't have an ID, the random value that is returned seems to be constant so it just works
                    $total = round(1.1 * $subtotal, 2);
                    $bez = $total - $subtotal;
                }
                echo '</ul>';
                echo '<hr>'; //Vertical row
                echo '<span>Subtotaal:€' . $subtotal . '</span>'; //Subtotal

                echo '<span>Bezorgkosten:€' . $bez . '</span>'; //Shipping
                echo '<span>Totaal:€' . round($total, 2) . '</span>'; //Total
                end_loop:
                $j++;
                echo '</div>';
            }
            if (isset($_POST['clear'])) {
                DB::query('DELETE FROM current_orders');
                echo('<script type="text/javascript">window.location.href=lijstje.php</script>');
            }
        }
        echo '</div>';
        ?>
        <br><br><br><br><form method="post" onsubmit="return confirm('weet je zeker dat je alles hebt?')"><input type="submit"  value="lijst leeghalen" name="clear"></form>
</body>
</html>
