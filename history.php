<?php
session_start();
require_once 'setup.php';


$ordhist=DB::query('SELECT previous_orders FROM users WHERE username=%s', $_SESSION['username']);
$ordhist=json_decode($ordhist[0]['previous_orders'], true);
?>

<!DOCTYPE HTML>
<html>
<head>
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
        <link rel='stylesheet' media='only screen and (max-width: 1080px)' href='style_smallscreen.css' />
        <link type="text/css" rel="stylesheet" media="only screen and (min-width: 1080px)" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5GJ825S"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <div class="banner">
	<a class="left active" id="home" href="index.php">Robins AH Bestelservice</a>
        <a class="right" id="banner_a" href="zoek.php">Zoek</a>
	<a class="right" id="banner_a" href="bonus.php">Bonus</a>
	<a class="right" id="banner_a" href="bestelling.php">Bestel</a>
	<a class="right" id="banner_a" href="contact.php">Contact</a>
        <?php 
		$logged_in=(isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false);
		if($logged_in){
        echo '<a class="right" id="banner_a" href="profile.php">Profiel</a>';
        } else {
        echo '<a class="right" id="banner_a" href="login.php">Inloggen</a>';
        } ?>
</div>
<div class="banner-mobile">
  <div class="dropdown">
    <button class="dropbtn"> Robins AH Bestelservice
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
	<ul>
      <li><a id="mobile_banner_a" href="index.php">Home</a>
      <li><a id="mobile_banner_a" href="zoek.php">Zoek</a>
      <li><a id="mobile_banner_a" href="bonus.php">Bonus</a>
      <li><a id="mobile_banner_a" href="bestelling.php">Bestel</a>
      <li><a id="mobile_banner_a" href="contact.php">Contact</a>
      <?php if($logged_in){
        echo '<li><a id="mobile_banner_a" href="profile.php">Profiel</a>';
        } else {
        echo '<li><a id="mobile_banner_a" href="login.php">Inloggen</a>';
        } ?>
	</ul>
    </div>
  </div>
</div>
<script>
	$('.dropbtn').on('touchstart', function (event) {
    $(".dropdown-content").slideToggle(200, "swing");
	});
</script>
<?php
	echo '<h1>Je eerder geplaatste bestellingen</h1>';
	foreach ($ordhist as $date => $contents){
		if(empty($contents)){continue;}
		echo '<div class="order">';
		echo '<h2>' . date("d F Y", strtotime($date)) . '</h2>';
		echo '<table id="products" style="width:70%">';
		echo '<tr><th>Product</th><th>Prijs</th><th>Aantal</th><th></th></tr>';
		$subtotal=0;
		$bez=0;
		$total=0;
		foreach($contents as $id => $prod){
				$am=$prod['bestelling_amount'] ?? 1;
				echo '<tr>';
				echo '<td>'.$prod['description'].'</td>';
				echo '<td>'.$prod['priceLabel']['now'].'</td>';
				echo '<td>'.$prod['bestelling_amount'].'</td>';
				echo '<td></td>';
				echo '</tr>';
				$subtotal += $prod['priceLabel']['now']*$am; // when items don't have an ID, the random value that is returned seems to be constant so it just works
				$total=round(1.1*$subtotal,2);
				$bez=$total-$subtotal;
		}
		echo '<tr><td><b>Subtotaal:</b></td><td></td><td></td><td>€'.number_format($subtotal,2,"."," ").'</td></tr>';
		echo '<tr><td><b>Bezorgkosten:</b></td><td></td><td></td><td>€'. number_format($bez,2,"."," ") .'</td></tr>';
		echo '<tr><td><b>Totaal:</b></td><td></td><td></td><td>€'.number_format($total,2,"."," ").'</td></tr>';
		echo '</table>'; 
		echo '</div>';
		echo '<br>';
	}
?>
</body>
</html>