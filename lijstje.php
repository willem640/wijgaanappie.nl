<?php
session_start();
require_once 'setup.php';

if (!($_SESSION['loggedin'] ?? false)) {
    //header('Location: login.php?return=lijstje.php');
}

//Check if user has permission to get to page
$perm_level = DB::query('SELECT perm_level FROM users WHERE username = %s', $_SESSION['username'])[0]['perm_level'];
if ($perm_level < 2) {
    header('Location: index.php');
}

//Initiate vital variables
$all_orders = DB::query('SELECT * FROM current_orders');
$now = date("Y-m-d");
$finance_contents = [];
$boodschappenlijst = [];
$bezorglijst = [];
$tikkielijst = [];

//Iterate through orders
foreach ($all_orders as $order) {
    $finance_contents[$order["username"]] = json_decode($order["contents"], true);
    $contents = json_decode($order['contents'], true);

    //Update order history van mensen
    $order_history = json_decode(DB::query('SELECT previous_orders FROM users WHERE username=%s', $order['username'])[0]['previous_orders'], true);
    $order_history[$now] = array_merge(($order_history[$now] ?? []), $contents);
    $order_history_json = json_encode($order_history);
    if (isset($_POST['clear'])) {
        DB::update('users', ['previous_orders' => $order_history_json], 'username=%s', $order['username']);
    }

    //Fill boodschappenlijst array
    foreach ($contents as $product) {
        $added = false;
        foreach ($boodschappenlijst as $key => $dupe) {
            if ($product['id'] == $dupe['id']) {
                $boodschappenlijst[$key]['bestelling_amount'] += $product['bestelling_amount'];
                $added = true;
            }
        }
        if (!$added) {
            array_push($boodschappenlijst, $product);
        }
    }

    //Fill bezorglijst array
    $user = (empty($order['realname']) ? $order['username'] : $order['realname']);
    $bezorglijst[$user] = [];
    foreach ($contents as $product) {
        $j = ['desc' => $product['description'], 'amount' => $product['bestelling_amount']];
        array_push($bezorglijst[$user], $j);
    }

    //Fill tikkielijst array
    $tot = 0;
    foreach ($contents as $product) {
        $tot += 1.1 * ($product['priceLabel']['now'] * $product['bestelling_amount']);
    }
    $tikkielijst[$user] = round($tot, 2);
}
//If orders are cleared push them all to finance and clear current orders
if (isset($_POST['clear'])) {

    DB::insert('finance', ['date' => $now, 'all_orders' => json_encode($finance_contents, true)]);
    //TODO Check if date already has orders in it
    DB::query('DELETE FROM current_orders');
    echo('<script type="text/javascript">window.location.href="lijstje.php"</script>');
}
?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php include 'header_material.php' ?>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    </head>
    <body>
        <style>
        .app-fab--absolute {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            z-index: 1;
        }
        center {
            margin: 1vh 0;
        }
        .lijst {
            min-height: 30vh;
        }
        .prev {
            position: absolute;
            top: 1vh;
            left: 1vw;
            z-index: 10;
        }
        .next {
            position: absolute;
            top: 1vh;
            right: 1vw;
            z-index: 10;
        }
        </style>
        <form method="post" id="clear_sweep">
            <input type="hidden" name="clear" value="lijst leeghalen">
            <button type="button" class="mdc-fab app-fab--absolute material-fab" aria-label="delete_sweep" onclick="let a = confirm('weet je zeker dat je alles hebt?');if(a){ this.closest('form').submit();}">
                <div class="mdc-fab__ripple"></div>
                <span class="mdc-fab__icon material-icons">delete_sweep</span>
            </button>
        </form>
        <?php include 'mobile_banner.php' ?>
        <div class="wrapper">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="mdc-card material-card lijst" id="boodschappen">
                            <center class="mdc-typography--headline5">Boodschappenlijst</center>
                            <ul class="mdc-list mdc-list--two-line">
                                <?php
                                //Show the boodschappenlijst
                                foreach ($boodschappenlijst as $product) {
                                    echo '<li class="mdc-list-item" tabindex="0">'; //List item
                                    echo '<span class="mdc-list-item__text">'; //Span for texts and meta tag
                                    echo '<span class="mdc-list-item__primary-text">' . $product['description'] . '</span>'; //Primary text
                                    echo '<span class="mdc-list-item__secondary-text">€' . $product['priceLabel']['now'] . '</span>'; //Secondary text                    
                                    echo '</span>';
                                    echo '<span class="mdc-list-item__meta">' . $product['bestelling_amount'] . '</span>'; //Meta tag
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="mdc-card material-card lijst" id="bezorg">
                            <center class="mdc-typography--headline5">Bezorglijst</center>
                            <?php
                            //Show the bezorglijst
                            foreach ($bezorglijst as $user => $order) {
                                echo '<center class="mdc-typography--headline5" id="user">' . $user . '</center>';
                                echo '<ul class="mdc-list">';
                                foreach ($order as $product) {
                                    echo '<li class="mdc-list-item" tabindex="0">'; //List item
                                    echo '<span class="mdc-list-item__text">' . $product['desc'] . '</span>'; //Primary text   
                                    echo '<span class="mdc-list-item__meta">' . $product['amount'] . '</span>'; //Meta tag         
                                    echo '</li>';
                                }
                                echo '</ul>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="mdc-card material-card lijst" id="boodschappen">
                            <center class="mdc-typography--headline5">Tikkielijst</center>
                            <ul class="mdc-list">
                                <?php
                                //Show the tikkielijst
                                foreach ($tikkielijst as $user => $amount) {
                                    echo '<li class="mdc-list-item" tabindex="0">'; //List item
                                    echo '<span class="mdc-list-item__text">' . $user . '</span>'; //Primary text   
                                    echo '<span class="mdc-list-item__meta">' . $amount . '</span>'; //Meta tag
                                    echo '</li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="prev"><span class="material-icons">navigate_before</span></div>
                <div class="next"><span class="material-icons">navigate_next</span></div>
            </div>
        </div>
        <script>
            var mySwiper = new Swiper('.swiper-container', {
                // Optional parameters
                direction: 'horizontal',
                loop: true,

                // Navigation arrows
                navigation: {
                  nextEl: '.next',
                  prevEl: '.prev',
                },
            })
        </script>
    </body>
</html>
