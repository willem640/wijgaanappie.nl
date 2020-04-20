<?php session_start();
require_once 'setup.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-153875032-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'UA-153875032-1');
        </script>
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <!-- Google Tag Manager -->
        <script>(function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({'gtm.start':
                            new Date().getTime(), event: 'gtm.js'});
                var f = d.getElementsByTagName(s)[0],
                        j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-5GJ825S');</script>
        <!-- End Google Tag Manager -->
        <meta charset="UTF-8">
        <title>Zoek</title>
        <link rel='stylesheet' media='only screen and (max-width: 1080px)' href='style_smallscreen.css' />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    <body>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5GJ825S"
                          height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <div class="anim">
        </div>
        <div class="banner-mobile">
            <div class="links">
                <div class="link">
                    <a href="index.php">
<?php include("assets/home-24px.svg"); ?>
                        <p>Home</p>
                    </a>
                </div>
                <div class="link">
                    <a href="zoeken.php">
<?php include("assets/search-24px.svg"); ?>
                        <p>Zoek</p>
                    </a>
                </div>
                <div class="link">
                    <a href="bonus.php">
<?php include("assets/euro_symbol-24px.svg"); ?>
                        <p>Bonus</p>
                    </a>
                </div>
                <div class="link">
                    <a href="bestelling.php">
<?php include("assets/shopping_cart-24px.svg"); ?>
                        <p>Winkelmandje</p>
                    </a>
                </div>
                <div class="link">
                    <a href="contact.php">
<?php include("assets/contact_support-24px.svg"); ?>
                        <p>Contact</p>
                    </a>
                </div>
                <?php
                $logged_in = (isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false);
                if ($logged_in) {
                    echo '<div class="link">
			  <a href="profile.php">';
                    include("assets/person_outline-24px.svg");
                    echo '<p>Profiel</p>
		      </a>
	          </div>';
                } else {
                    echo '<div class="link">
			  <a href="login.php">';
                    include("assets/lock_open-24px.svg");
                    echo '<p>Login</p>
		      </a>
			  </div>';
                }
                ?>

            </div>
        </div>
        <div class="circle">
            <img src="assets/android-chrome-512x512.png">
        </div>

    </div>

    <script>
        var i = 0;
        $('.links').fadeOut(0);
        $('.circle').click(function () {
            i += 1;
            if (i % 2 != 0) {
                $(".wrapper").fadeOut();
                $('.banner-mobile').animate({height: "100vh"});
                $('.circle').animate({top: '-=15vh'});
                $('.links').fadeIn();
            } else {
                $(".wrapper").fadeIn();
                $('.banner-mobile').animate({height: "50vh"});
                $('.circle').animate({top: '+=15vh'});
                $('.links').fadeOut();
            }
        });
    </script>
    <div class="wrapper">
        <div id="card">
            <form method="get">
                <input type="search" name="query" placeholder="Zoek hier naar uw product...">
            </form>
            <?php
            require_once 'setup.php';
            require_once 'simple_html_dom.php';
            if (!empty($_GET['query'])) {

                $response = file_get_contents('https://www.googleapis.com/customsearch/v1?'
                        . 'key=' . $customSearch['apiKey']
                        . '&cx=' . $customSearch['searchID']
                        . '&q=' . urlencode($_GET['query']));

                $parse_producten = json_decode($response, true);

                foreach ($parse_producten['items'] as $parse_prod) {
                    if (strpos($parse_prod['link'], '/producten/product/') !== false) {
                        $pos = strpos($parse_prod['link'], '/producten/product');
                        $producten[] = substr($parse_prod['link'], $pos, strlen($parse_prod['link']) - $pos);
                    }
                }

                echo '<table id="products">';
                echo '<tr><th>Product</th><th>Afbeelding</th><th>Prijs</th><th style="text-align:left;">Aantal</th><th></th></tr>';
                $max = 20;
                $i = 0;
                foreach ($producten as $key => $product) {
                    if ($i >= $max) {
                        $i = 0;
                        break;
                    } $i += 1;
                    $response = json_decode(file_get_contents('https://www.ah.nl/service/rest/delegate?url=' . urlencode($product)), true); // true is for array, false for stdClass
                    foreach ((array) $response['_embedded']['lanes'] as $lane) {
                        if ($lane['type'] == 'ProductDetailLane') {
                            $prod = $lane['_embedded']['items'][0]['_embedded']['product'];
                            $_SESSION['orderable_array'][$key] = $prod;
                            echo '<tr><td>' . $prod['description'] . '</td>'
                            . '<td style="width:10%"><img id="tableImg" src="' . $prod['images'][0]['link']['href'] . '"></td>'
                            . '<td>€' . $prod['priceLabel']['now'] . '</td>'
                            . '<td><form id="form_' . $prod['id'] . '" method="post" action="bestelling.php">'
                            . '<input id="aantal" type="number" name="amount" min=1 value="1" placeholder="aantal">'
                            . '<input name="product" type="hidden" value=\'' . str_replace("'", '', $key) . '\'>'
                            . '<a id="fancy_a" onclick="document.getElementById(\'form_' . $prod['id'] . '\').submit();">Bestellen</a></form><td><tr>';
                        }
                    }
                }
                echo '</table>';
            }
            ?>
        </div>
    </div>
</body>
</html>