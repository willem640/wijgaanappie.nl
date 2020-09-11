<?php
$start=microtime(true);
session_start();
require_once 'setup.php';

if (!($_SESSION['loggedin'] ?? false)) {
    //header('Location: login.php?return=lijstje.php');
}

//Check if user has permission to get to page
$perm_level = DB::query('SELECT perm_level FROM users WHERE username = %s', $_SESSION['username'])[0]['perm_level'];
if($perm_level<2){header('Location: index.php');}

//Initiate vital variables
$all_orders = DB::query('SELECT * FROM current_orders');
$now=date("Y-m-d");
$finance_contents=[];
$boodschappenlijst=[];
$bezorglijst=[];

//Iterate through orders
foreach($all_orders as $order){
    $finance_contents[$order["username"]]=json_decode($order["contents"], true);
    $contents = json_decode($order['contents'], true);
           
    //Update order history van mensen
    $order_history = json_decode(DB::query('SELECT previous_orders FROM users WHERE username=%s', $order['username'])[0]['previous_orders'], true);
    $order_history[$now] = array_merge(($order_history[$now] ?? []), $contents);
    $order_history_json = json_encode($order_history);
    if (isset($_POST['clear'])) {
        DB::update('users', ['previous_orders' => $order_history_json], 'username=%s', $order['username']);
    }
    
    //Fill boodschappenlijst array
    foreach($contents as $product){
        $added = false;
        foreach($boodschappenlijst as $key => $dupe){
            if($product['id']==$dupe['id']){
                $boodschappenlijst[$key]['bestelling_amount']+=$product['bestelling_amount'];
                $added=true;
            }
        }
        if(!$added){array_push($boodschappenlijst, $product);}
    }
    
    //Fill bezorglijst array
    $user=(empty($order['realname']) ? $order['username'] : $order['realname']);
    $bezorglijst[$user]=[];
    foreach($contents as $product){
        $j=['desc'=>$product['description'], 'amount'=>$product['bestelling_amount']];
        array_push($bezorglijst[$user], $j);
    }
}

//If orders are cleared push them all to finance and clear current orders
if (isset($_POST['clear'])) {
                
                DB::insert('finance', ['date'=> $now, 'all_orders'=> json_encode($finance_contents, true)]);
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
                                    foreach($boodschappenlijst as $product){
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
                                foreach($bezorglijst as $user){
                                    echo '<center class="mdc-typography--headline5" id="user">' . $user . '</center>';
                                    echo '<ul class="mdc-list">';
                                    foreach($user as $product){
                                        echo '<li class="mdc-list-item" tabindex="0">'; //List item
                                        echo '<span class="mdc-list-item__text">' . $product['desc'] . '</span>'; //Primary text   
                                        echo '<span class="mdc-list-item__meta">' . $prod['amount'] . '</span>'; //Meta tag         
                                        echo '</li>';
                                    }
                                    echo '</ul>';
                                }
                            ?>
                        </div>
                    </div>
                            
        <?php
            //Code voor de bezorglijst
            /*echo '<div class="swiper-slide">';
            echo '<div class="mdc-card material-card lijst" id="bezorg">';
            echo '<center class="mdc-typography--headline5">Bezorglijst</center>';
            foreach ($all_orders as $orders) {
                $contents = json_decode($orders['contents'], true);
                
                echo '<center class="mdc-typography--headline5">' . (empty($orders['realname']) ? $orders['username'] : $orders['realname']) . '</center>';
                echo '<ul class="mdc-list">';
                foreach ((array) $contents as $prod) {
                    $am = $prod['bestelling_amount'];
                    echo '<li class="mdc-list-item" tabindex="0">'; //List item
                    echo '<span class="mdc-list-item__text">' . $prod['description'] . '</span>'; //Primary text   
                    echo '<span class="mdc-list-item__meta">' . $prod['bestelling_amount'] . '</span>'; //Meta tag                    
                }
                echo '</ul>';
                
                
            }
            echo '</div>';
            echo '</div>';
            */
            //Code voor de tikkielijst
            echo '<div class="swiper-slide">';
            echo '<div class="mdc-card material-card lijst" id="tikkie">';
            echo '<center class="mdc-typography--headline5">Tikkielijst</center>';
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
            echo '</div>';
            
            echo '</div>';
            echo '<div class="prev"><span class="material-icons">navigate_before</span></div>';
            echo '<div class="next"><span class="material-icons">navigate_next</span></div>';
            echo '</div>';
        
        echo '</div>';
        $end=microtime(true);
        $time=$end-$start;
        echo($time);
        ?>
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
