<?php
session_start();
require_once 'setup.php';
require_once 'simple_html_dom.php';

if (!isset($_SESSION['loggedin']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('HTTP/1.1 403 Not Logged In');
    echo('Je bent niet ingelogd');
    die();
}
if (isset($_POST['product'])) {
    if (!isset($_SESSION['orderable_array'][$_POST['product']])) {
        header('HTTP/1.1 422 Unprocessable Entity'); // input klopt maar de server kan het niet processen
        echo('Product bestaat niet');
        die();
    }
    $prod = $_SESSION['orderable_array'][(int)$_POST['product']];
    $prod['bestelling_amount'] = (int)($_POST['amount'] ?? 1);
    $cart = json_decode(DB::query('SELECT cart FROM users WHERE username = %s', $_SESSION['username'])[0]['cart'], true);
    $cart = json_decode(DB::query('SELECT cart FROM users WHERE username = %s', $_SESSION['username'])[0]['cart'], true);
    foreach ($cart as $key => $cart_prod) {
        if ($prod['id'] === $cart_prod['id'] && $prod['description'] === $cart_prod['description']) {
            $cart[$key]['bestelling_amount'] += $prod['bestelling_amount'];
            goto inCart;
        }
    }
    $cart[] = $prod;
    inCart:
    $cart = array_filter($cart, function ($var) {
        if (count($var) === 0){
            return false;
        } else {
            return $var['bestelling_amount'] >= 1;
        };
    });     // filter empty arrays and negative indices

    $cart = array_filter($cart);

    DB::update('users', ['cart' => json_encode($cart)], 'username = %s', $_SESSION['username']);
    header('Location: bestelling.php');
}
if (isset($_POST['place_order'])) {
    $cq = DB::query('SELECT cart,realname FROM users WHERE username = %s', $_SESSION['username']);
    $cart = json_decode($cq[0]['cart'] ?? '[]', true);
    if (!empty($cart)) {
        $query = DB::query('SELECT * FROM current_orders WHERE username = %s', $_SESSION['username']);
        $orders = json_decode($query[0]['contents'] ?? '[]', true);

        $ids = array_column($cart, 'id');
        foreach($ids as $id){
            $id = intval(substr($id, 2));
            print_r($id);
            /*$weight=DB::query('SELECT weight FROM products WHERE id=%s', $id)[0];
            $weight+=1;
            DB::update('products', ['weight' => $weight], 'id=%s', $weight);*/
        }
        $prices = array_column(array_column($cart, 'priceLabel'), 'now');
        $descriptions = array_column($cart, 'description'); // test
        $amounts = array_column($cart, 'bestelling_amount');
        $images = array_column($cart, 'images');
        $cart_trimmed = array_map(function($id, $price, $description, $amount, $image) {
            return ['id' => $id, 'priceLabel' => ['now' => $price], 'description' => $description, 'bestelling_amount' => $amount, 'images' => $image];
        }, $ids, $prices, $descriptions, $amounts, $images);
        if (empty($query[0]['username'])) {
            DB::insert('current_orders', ['contents' => json_encode($cart_trimmed), 'username' => $_SESSION['username'], 'realname' => $cq[0]['realname']]);
        } else {
            $orders = array_merge($orders, $cart_trimmed);
            array_filter($orders, function ($var) {
                return count($var) !== 0;
            }); // filter empty arrays
            //echo '<pre>'.var_dump($orders).'</pre>';
            DB::update('current_orders', ['contents' => json_encode($orders)], 'username = %s', $_SESSION['username']);
        }
        DB::update('users', ['cart' => '{}'], 'username = %s', $_SESSION['username']);
    }
    header('Location: profile.php');
}

if (isset($_POST['delete']) && $_SESSION['loggedin'] === true) {
    //TODO: is robin going to the appie?
    $orders = json_decode(DB::query('SELECT cart FROM users WHERE username = %s', $_SESSION['username'])[0]['cart'], true);
    foreach ($orders as $key => $prod) {
        if ($prod['id'] === $_POST['delete']) {
            unset($orders[$key]);
            break;
        }
    }
    $orders = array_merge($orders); // reset keys
    DB::update('users', ['cart' => json_encode($orders)], 'username = %s', $_SESSION['username']);
    header('Location: bestelling.php');
}

if (isset($_POST['add']) && $_SESSION['loggedin'] === true) {
    //TODO: is robin going to the appie?
    $orders = json_decode(DB::query('SELECT cart FROM users WHERE username = %s', $_SESSION['username'])[0]['cart'], true);
    foreach ($orders as $key => $prod) {
        if ($prod['id'] === $_POST['add']) {
            $orders[$key]['bestelling_amount']++;
            break;
        }
    }
    $orders = array_merge($orders); // reset keys
    DB::update('users', ['cart' => json_encode($orders)], 'username = %s', $_SESSION['username']);
    header('Location: bestelling.php');
}

if (isset($_POST['subs']) && $_SESSION['loggedin'] === true) {
    //TODO: is robin going to the appie?
    $orders = json_decode(DB::query('SELECT cart FROM users WHERE username = %s', $_SESSION['username'])[0]['cart'], true);
    foreach ($orders as $key => $prod) {
        if ($prod['id'] === $_POST['subs']) {
            $orders[$key]['bestelling_amount']--;
            break;
        }
    }
    $orders = array_merge($orders); // reset keys
    DB::update('users', ['cart' => json_encode($orders)], 'username = %s', $_SESSION['username']);
    header('Location: bestelling.php');
}

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
}
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
            if ($_SESSION['loggedin']) {
                $cart = json_decode(DB::query('SELECT cart FROM users WHERE username = %s', $_SESSION['username'])[0]['cart'], true);
                if (empty($cart)) {
                    echo '<h1>Uw winkelmandje is op dit moment leeg!</h1>';
                } else {
                    echo '<h1>Je winkelmandje</h1>';
                    $subtotal = 0;
                    $bez = 0;
                    $total = 0;
                    foreach ((array) $cart as $key => $prod) {
                        $_SESSION['orderable_array'][$key] = $prod;
                        $am = $prod['bestelling_amount'] ?? 1;
                        echo '  <div class="product-card">
                            <img src="'.$prod['images'][0]['link']['href'].'">
                            <div class="card-content">
                            <h3 id="title" class="mdc-typography--headline3">' . $prod['description'] . '</h3>
                            <h4 id="price" class="mdc-typography--headline4">€' . $prod['priceLabel']['now'] . '</h4>
                            <h4 id="amount" class="mdc-typography--headline4">' . $prod['bestelling_amount'] . ' Stuks</h4>
                            <div class="buttons">
                            <form method="post">
                            <input type="hidden" name="product" value="' . $key . '">
                            <input type="hidden" name="amount" value="'.-$am.'">
                            <button onclick="this.closest(\'form\').submit()" id="remove" style="float:left">Verwijder</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="product" value="' . $key . '">
                                <input type="hidden" name="amount" value="1">
                                <button id="up" onclick="this.closest(\'form\').submit()" style="float:right">+</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="product" value="' . $key . '">
                                <input type="hidden" name="amount" value="-1">
                                <button id="down" onclick="this.closest(\'form\').submit()" style="float:right">-</button>
                            </form>
                            </div>
                            </div>
                            </div>';
                        $subtotal += $prod['priceLabel']['now'] * $am; // when items don't have an ID, the random value that is returned seems to be constant so it just works
                        $total = round(1.1 * $subtotal, 2);
                        $bez = $total - $subtotal;
                    }
                    echo '<div class="prices mdc-typography--body1">';
                    echo '<p>Subtotaal: €' . number_format($subtotal, 2, ".", " ") . '</p>';
                    echo '<p>Bezorgkosten: €' . number_format($bez, 2, ".", " ") . '</p>';
                    echo '<p>Totaal: €' . number_format($total, 2, ".", " ") . '</p>';
                    echo '</div>';
                }
            }
            ?>

            <div class="submenu" id="winkelmandje">
                <form method="post" onsubmit="ga('send', 'event', 'Bestelling', 'Bestelling', 'Bestelling')">
                    <input id="fancy_a" type="submit" value="Bestellen" name="place_order">
                </form>
            </div>
        </div>
        <script>
            $(".buttons button").click(function (e) {

                $(".ripple").remove();

                // Setup
                var posX = $(this).offset().left,
                        posY = $(this).offset().top,
                        buttonWidth = $(this).width(),
                        buttonHeight = $(this).height();

                // Add the element
                $(this).prepend("<span class='ripple'></span>");

                // Make it round!
                if (buttonWidth >= buttonHeight) {
                    buttonHeight = buttonWidth;
                } else {
                    buttonWidth = buttonHeight;
                }

                // Get the center of the element
                var x = e.pageX - posX - buttonWidth / 2;
                var y = e.pageY - posY - buttonHeight / 2;

                // Add the ripples CSS and start the animation
                $(".ripple").css({
                    width: buttonWidth,
                    height: buttonHeight,
                    top: y + 'px',
                    left: x + 'px'
                }).addClass("rippleEffect");
            });
        </script>
    </div>
</body>

</html>
