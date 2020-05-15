<?php
session_start();
require_once 'setup.php';


$ordhist = DB::query('SELECT previous_orders FROM users WHERE username=%s', $_SESSION['username']);
$ordhist = array_reverse(json_decode($ordhist[0]['previous_orders'], true));
?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php include 'header_material.php' ?>
    </head>
    <body>
        <?php include 'mobile_banner.php' ?>
        <div class="wrapper">
            <div id="card">
                <?php
                echo '<h1>Je eerder geplaatste bestellingen</h1>';
                foreach ($ordhist as $date => $contents) {
                    if (empty($contents)) {
                        continue;
                    }
                    echo '<div class="order">';
                    echo '<h2>' . date("d F Y", strtotime($date)) . '</h2>';
                    $subtotal = 0;
                    $bez = 0;
                    $total = 0;
                    foreach ($contents as $id => $prod) {
                        echo '<div class="product">';
                        $am = $prod['bestelling_amount'] ?? 1;
                        echo '<p id="desc">' . $prod['description'] . '</p>';
                        echo '<p id="amount">' . $prod['bestelling_amount'] . ' stuks</p>';
                        echo '<p id="priceLabel">€' . $prod['priceLabel']['now'] . '</p>';
                        $subtotal += $prod['priceLabel']['now'] * $am; // when items don't have an ID, the random value that is returned seems to be constant so it just works
                        $total = round(1.1 * $subtotal, 2);
                        $bez = $total - $subtotal;
                        echo '</div>';
                    }
                    echo '<div class="order-prices">';
                    echo '<p>Subtotaal: €' . number_format($subtotal, 2, ".", " ") . '</p>';
                    echo '<p>Bezorgkosten: €' . number_format($bez, 2, ".", " ") . '</p>';
                    echo '<p>Totaal: €' . number_format($total, 2, ".", " ") . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </body>
</html>
