<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
}
require_once 'setup.php';
require_once 'simple_html_dom.php';
if ($_SESSION['loggedin'] === true && !empty($_POST['product'])) {
    
    $prod = json_decode($_POST['product'], true);
    $prod['bestelling_amount'] = ($_POST['amount'] ?? 1);
    $cart = json_decode(DB::query('SELECT cart FROM users WHERE username = %s', $_SESSION['username'])[0]['cart'], true);
    $cart = json_decode(DB::query('SELECT cart FROM users WHERE username = %s', $_SESSION['username'])[0]['cart'], true);
    foreach($cart as $key => $cart_prod){
        if($prod['id'] === $cart_prod['id'] && $prod['description'] === $cart_prod['description']){
            $cart[$key]['bestelling_amount'] += $prod['bestelling_amount'];
            goto inCart;
        }
    }
    $cart[] = $prod;
    inCart:
    array_filter($cart, function ($var) {
        return count($var) !== 0;
    }); // filter empty arrays
    array_filter($cart);

    DB::update('users', ['cart' => json_encode($cart)], 'username = %s', $_SESSION['username']);
    header('Location: bestelling.php');
}
if(isset($_POST['place_order'])){
    $cq = DB::query('SELECT cart,realname FROM users WHERE username = %s', $_SESSION['username']);
    $cart = json_decode($cq[0]['cart'] ?? '[]', true);
    if(!empty($cart)){
    $query = DB::query('SELECT * FROM current_orders WHERE username = %s', $_SESSION['username']);
	$orders = json_decode($query[0]['contents'] ?? '[]',true);
    
    $ids = array_column($cart, 'id');
    $prices = array_column(array_column($cart, 'priceLabel'),'now');
    $descriptions = array_column($cart,'description'); // test
	$amounts = array_column($cart, 'bestelling_amount');
    $cart_trimmed = array_map(function($id, $price, $description, $amount){return ['id' => $id, 'priceLabel' => ['now' => $price], 'description'=>$description, 'bestelling_amount'=>$amount];}, $ids, $prices, $descriptions, $amounts);
    if(empty($query[0]['username']) ){
            DB::insert('current_orders',['contents' => json_encode($cart_trimmed), 'username' => $_SESSION['username'], 'realname' => $cq[0]['realname']]);
    } else {
    $orders = array_merge($orders,$cart_trimmed);
    array_filter($orders, function ($var){return count($var) !== 0;}); // filter empty arrays
    //echo '<pre>'.var_dump($orders).'</pre>';
    DB::update('current_orders',['contents' => json_encode($orders)], 'username = %s',$_SESSION['username']);
    }
    DB::update('users', ['cart' => '{}'],'username = %s', $_SESSION['username']);
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
?>

<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153875032-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-153875032-1');
</script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5GJ825S');</script>
<!-- End Google Tag Manager -->
    <meta charset="utf-8">
    <meta name="google-site-verification" content="uvDiVjrbFuwiF-ME9NPjbmsZsRXQGzNzGdJUElgM7DQ" />
    <link rel='stylesheet' href='style_smallscreen.css' />
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
			<?php include("assets/home-24px.svg");?>
			<p>Home</p>
		</a>
	</div>
	<div class="link">
		<a href="zoeken.php">
			<?php include("assets/search-24px.svg");?>
			<p>Zoek</p>
		</a>
	</div>
	<div class="link">
		<a href="bonus.php">
			<?php include("assets/euro_symbol-24px.svg");?>
			<p>Bonus</p>
		</a>
	</div>
	<div class="link">
		<a href="bestel.php">
			<?php include("assets/shopping_cart-24px.svg");?>
			<p>Winkelmandje</p>
		</a>
	</div>
	<div class="link">
		<a href="contact.php">
			<?php include("assets/contact_support-24px.svg");?>
			<p>Contact</p>
		</a>
	</div>
	<?php 
		$logged_in=(isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false);
		if($logged_in){
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
	$('.circle').click(function(){
		i+=1;
		if(i%2!=0){
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
    <?php
    if($logged_in){
        $cart = json_decode(DB::query('SELECT cart FROM users WHERE username = %s',$_SESSION['username'])[0]['cart'],true);
        if(empty($cart)){	
                echo '<h1>Uw winkelmandje is op dit moment leeg!</h1>';
        } else {
            echo '<div class="card">';
            $subtotal=0;
            $bez=0;
            $total=0;
            foreach((array)$cart as $prod){
                    $am=$prod['bestelling_amount'] ?? 1;
                    echo '  <div class="product-card">
                            <img src="assets/placeholder-card.jpg">
                            <div class="card-content">
                            <h3 id="title">'.$order['description'].'</h3>
                            <h4 id="price">€'.$order['priceLabel']['now'].'</h4>
                            <h4 id="amount">'.$order['bestelling_amount'].' Stuks</h4>
                            <div class="buttons">
                            <form method="post" id="cancel_order_'.$order['id'].'">
                            <input type="hidden" name="cancel_order" value="'.$order['id'].'">
                            <button onclick="document.getElementById(\'cancel_order_'.$order['id'].'\').submit();" id="remove" style="float:left">Verwijder</button>
                            </form>
                            <button id="up" style="float:right">+</button>
                            <button id="down" style="float:right">-</button>
                            </div>
                            </div>
                            </div>';
                    $subtotal += $prod['priceLabel']['now']*$am; // when items don't have an ID, the random value that is returned seems to be constant so it just works
                    $total=round(1.1*$subtotal,2);
                    $bez=$total-$subtotal;   
            }
		echo '<div class="prices">';
                echo '<p>Subtotaal: €'.number_format($subtotal,2,"."," ").'</p>';
		echo '<p>Bezorgkosten: €'.number_format($bez,2,"."," ").'</p>';
		echo '<p>Totaal: €'.number_format($total,2,"."," ").'</p>';
		echo '</div>';
        }
    }
    ?>
</div>
</div>
</body>

</html>
