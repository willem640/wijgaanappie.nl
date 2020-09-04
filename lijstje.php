<?php
session_start();
require_once 'setup.php';


if (!($_SESSION['loggedin'] ?? false)) {
    //header('Location: login.php?return=lijstje.php');
}
if (isset($_POST['clear'])) {
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
            <button class="mdc-fab app-fab--absolute material-fab" aria-label="delete_sweep" onclick="let a = confirm('weet je zeker dat je alles hebt?');console.log(a); if(a){ this.closest('form').submit();console.log('true');}">
                <div class="mdc-fab__ripple"></div>
                <span class="mdc-fab__icon material-icons">delete_sweep</span>
            </button>
        </form>
        <?php include 'mobile_banner.php' ?>
        <?php
        $perm_level = DB::query('SELECT perm_level FROM users WHERE username = %s', $_SESSION['username'])[0]['perm_level'];
        if ($perm_level >= 2) {
            $all_orders = DB::query('SELECT * FROM current_orders');
            $j = 0; //Zo doe k ff index snap key=>value nie. fight me
            echo '<div class="wrapper">';
            foreach ($all_orders as $orders) {
                $contents = json_decode($orders['contents'], true);
                
                //Update order history van mensen
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
                echo '<div class="mdc-card material-card" style="display:none">'; //TODO weghalen oude shizzle en niet op de luie manier hier 
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
                echo '<ul class="mdc-list">';

                // Three list items for subtotal, shipping and total
                
                echo '<li class="mdc-list-item" tabindex="0">';
                echo '<span class="mdc-list-item__text">Subtotaal:</span>'; //Subtotal
                echo '<span class="mdc-list-item__meta">€' . round($subtotal,2) . "</span>"; 
                echo '</li>';
                
                
                echo '<li class="mdc-list-item" tabindex="0">';
                echo '<span class="mdc-list-item__text">Bezorgkosten:</span>'; //Shipping
                echo '<span class="mdc-list-item__meta">€' . round($bez,2) . "</span>"; 
                echo '</li>';
                
                
                echo '<li class="mdc-list-item" tabindex="0">';
                echo '<span class="mdc-list-item__text">Totaal:</span>'; //Total
                echo '<span class="mdc-list-item__meta">€' . round($total, 2) . "</span>"; 
                echo '</li>';
                

                echo '</ul>';
                end_loop:
                $j++;
                echo '</div>';
                
            }
            echo '<div class="swiper-container">';
            echo '<div class="swiper-wrapper">';
            //Code voor de boodschappenlijst
            echo '<div class="swiper-slide">';
            echo '<div class="mdc-card material-card lijst" id="boodschappen">';
            $boodschappenlijst = [];
            foreach($all_orders as $order){
                $order_content = json_decode($order['contents'], true);
                foreach($order_content as $product){
                    $added = false;
                    foreach($boodschappenlijst as $key => $dupe){
                        if($product['id']==$dupe['id']){
                            $boodschappenlijst[$key]['bestelling_amount']+=$product['bestelling_amount'];
                            $added=true;
                        }
                    }
                    if(!$added){array_push($boodschappenlijst, $product);}
                }
            }
            echo '<center class="mdc-typography--headline5">Boodschappenlijst</center>';
            echo '<ul class="mdc-list mdc-list--two-line">';
            foreach($boodschappenlijst as $product){
                echo '<li class="mdc-list-item" tabindex="0">'; //List item
                echo '<span class="mdc-list-item__text">'; //Span for texts and meta tag
                echo '<span class="mdc-list-item__primary-text">' . $product['description'] . '</span>'; //Primary text
                echo '<span class="mdc-list-item__secondary-text">€' . $product['priceLabel']['now'] . '</span>'; //Secondary text                    
                echo '</span>';
                echo '<span class="mdc-list-item__meta">' . $product['bestelling_amount'] . '</span>'; //Meta tag
            }
            echo '</ul>';
            echo '</div>';
            echo '</div>';
            //Code voor de bezorglijst
            echo '<div class="swiper-slide">';
            echo '<div class="mdc-card material-card lijst" id="bezorg">';
            echo '<center class="mdc-typography--headline5">Bezorglijst</center>';
            foreach ($all_orders as $orders) {
                $contents = json_decode($orders['contents'], true);
                
                echo '<center class="mdc-typography--headline5">' . (empty($orders['realname']) ? $orders['username'] : $orders['realname']) . '</center>';
                echo '<ul class="mdc-list">';
                $subtotal = $bez = $total = 0;
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
        }
        echo '</div>';
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
