<?php
session_start();
if(!$_SESSION['loggedin']){
	echo '<script>window.location.href = "login.php?return=profile.php"</script>';
}
require_once 'setup.php';
$perm_level = DB::query('SELECT perm_level FROM users WHERE username = %s', $_SESSION['username'])[0]['perm_level'];
if($perm_level >= 2){
if(isset($_POST['naar_appie'])){
    DB::update('komt_chobin_naar_de_appie',['komt hij' => 1],'id = 0');
}
if(isset($_POST['niet_naar_appie'])){
    DB::update('komt_chobin_naar_de_appie',['komt hij' => 0],'id = 0');
}
if(isset($_POST['speciaal_bericht']) && !empty($_POST['speciaal_bericht_tekst'])){
    DB::update('komt_chobin_naar_de_appie',['komt hij' => 2, 'special_status' => $_POST['speciaal_bericht_tekst']],'id = 0');
}
}
if (isset($_POST['cancel_order']) && $_SESSION['loggedin'] === true) {
    $orders = json_decode(DB::query('SELECT contents FROM current_orders WHERE username = %s', $_SESSION['username'])[0]['contents'], true);
    foreach ($orders as $key => $prod) {
        if ($prod['id'] === $_POST['cancel_order']) {
            unset($orders[$key]);
            break;
        }
    }
    $orders = array_merge($orders); // reset keys
    DB::update('current_orders', ['contents' => json_encode($orders)], 'username = %s', $_SESSION['username']);
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
    header('Location: profile.php');
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
    header('Location: profile.php');
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
		<a href="bestelling.php">
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
<?php
	$curr_order=DB::query('SELECT contents FROM current_orders WHERE username= %s', $_SESSION['username']);
	$curr_order=json_decode($curr_order[0]['contents'], true);
	if(empty($curr_order)){
		echo '<div id="card">';
		echo '<h1>Je huidige bestelling</h1>';
		echo '</div>';
	} else {
		echo '<div id="card">';
		echo '<h1>Je huidige bestelling</h1>';
                $subtotal=0;
		$bez=0;
		$total=0;
		foreach($curr_order as $order){
                    $am=$order['bestelling_amount'] ?? 1;
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
                            <form method="post" id="add'.$prod['id'].'">
                                <input type="hidden" name="add" value="'.$prod['id'].'">
                                <button id="up" onclick="document.getElementById(\'add'.$prod['id'].'\').submit();" style="float:right">+</button>
                            </form>
                            <form method="post" id="subs'.$prod['id'].'">
                                <input type="hidden" name="subs" value="'.$prod['id'].'">
                                <button id="down" onclick="document.getElementById(\'subs'.$prod['id'].'\').submit();" style="float:right">-</button>
                            </form>
                            </div>
                            </div>
                            </div>';
                    $subtotal += $order['priceLabel']['now']*$am; // when items don't have an ID, the random value that is returned seems to be constant so it just works
                    $total=round(1.1*$subtotal,2);
                    $bez=$total-$subtotal;
		}
		echo '<div class="prices">';
                echo '<p>Subtotaal: €'.number_format($subtotal,2,"."," ").'</p>';
		echo '<p>Bezorgkosten: €'.number_format($bez,2,"."," ").'</p>';
		echo '<p>Totaal: €'.number_format($total,2,"."," ").'</p>';
		echo '</div>';
	}
	
?>
<div class="submenu">
	<a href="history.php" id="fancy_a">Eerdere Bestellingen</a>
	<a href="logout.php" id="fancy_a">Uitloggen</a>
	</a>
</div>

</div>
<script>
	$(".buttons button").click(function(e){
	
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
</body>
<?php
$perm_level = DB::query('SELECT perm_level FROM users WHERE username = %s', $_SESSION['username'])[0]['perm_level'];
if($perm_level >= 2){
    echo '<a id="fancy_a" href="lijstje.php">Boodschappenlijstje</a>';
    echo '<br><br><form method="post">'
    . '<input type="submit" name="naar appie" value="vandaag naar de appie">'
    . '<input type="submit" name="niet naar appie" value="vandaag niet naar de appie">'
    . '<input type="submit" name="speciaal bericht" value="speciaal bericht">'
    . '<input type="text" name="speciaal bericht tekst" placeholder="speciaal bericht"></form>';
}
?>
</html>
