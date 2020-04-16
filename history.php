<?php
session_start();
require_once 'setup.php';


$ordhist=DB::query('SELECT previous_orders FROM users WHERE username=%s', $_SESSION['username']);
$ordhist=array_reverse(json_decode($ordhist[0]['previous_orders'], true));
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
	echo '<h1>Je eerder geplaatste bestellingen</h1>';
	foreach ($ordhist as $date => $contents){
		if(empty($contents)){continue;}
		echo '<div class="order">';
		echo '<h2>' . date("d F Y", strtotime($date)) . '</h2>';
		$subtotal=0;
		$bez=0;
		$total=0;
		foreach($contents as $id => $prod){
				echo '<div class="product">';
				$am=$prod['bestelling_amount'] ?? 1;
				echo '<p id="desc">'.$prod['description'].'</p>';
				echo '<p id="amount">'.$prod['bestelling_amount'].' stuks</p>';
				echo '<p id="priceLabel">€'.$prod['priceLabel']['now'].'</p>';
				$subtotal += $prod['priceLabel']['now']*$am; // when items don't have an ID, the random value that is returned seems to be constant so it just works
				$total=round(1.1*$subtotal,2);
				$bez=$total-$subtotal;
				echo '</div>';
		}
		echo '<div class="order-prices">';
		echo '<p>Subtotaal: €'.number_format($subtotal,2,"."," ").'</p>';
		echo '<p>Bezorgkosten: €'.number_format($bez,2,"."," ").'</p>';
		echo '<p>Totaal: €'.number_format($total,2,"."," ").'</p>';
		echo '</div>';
		echo '</div>';
	}
?>
</div>
</div>
</body>
</html>
