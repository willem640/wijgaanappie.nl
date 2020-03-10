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
    //header('Location: profile.php');
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
        <link rel='stylesheet' media='only screen and (max-width: 1080px)' href='style_smallscreen.css' />
        <link type="text/css" rel="stylesheet" media="only screen and (min-width: 1080px)" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5GJ825S"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->       <div class="banner">
	<a class="left active" id="home" href="index.php">Robins AH Bestelservice</a>
        <a class="right" id="banner_a" href="zoeken.php">Zoek</a>
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
      <li><a id="mobile_banner_a" href="zoeken.php">Zoek</a>
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
<?php
	$curr_order=DB::query('SELECT contents FROM current_orders WHERE username= %s', $_SESSION['username']);
	$curr_order=json_decode($curr_order[0]['contents'], true);
	if(empty($curr_order)){
		echo '<div class="order" style="width: 100%;">';
		echo '<h1 style="display:inline; float:left">Je hebt nog niks besteld</h1>';
		
	} else {
		echo '<div class="order" style="width: 100%;">';
		echo '<h1 style="display:inline; float:left">Bestellingen</h1>';
		echo '<table id="products" style="width:70%">';
        echo '<tr><th>Product</th><th>Prijs</th><th>Aantal</th><th></th></tr>';
		$i=0;
                	$subtotal=0;
		$bez=0;
		$total=0;
		foreach($curr_order as $order){
                        $am=$order['bestelling_amount'] ?? 1;
			echo '<tr>';
			echo '<td>'.$order['description'].'</td>';
			echo '<td>'.$order['priceLabel']['now'].'</td>';
			echo '<td>'.$order['bestelling_amount'].'</td>'
						. '<td><form method="post" id="cancel_order_'.$order['id'].'">'
						. '<input type="hidden" name="cancel_order" value="'.$order['id'].'">'
						. '<a onclick="document.getElementById(\'cancel_order_'.$order['id'].'\').submit();" href="#"><img style="width: 10vh" src="assets/cross.svg"></a></form></td></tr>';
                        $subtotal += $order['priceLabel']['now']*$am; // when items don't have an ID, the random value that is returned seems to be constant so it just works
                        $total=round(1.1*$subtotal,2);
			$bez=$total-$subtotal;
			$i++;
		}
                	echo '<tr><td><b>Subtotaal:</b></td><td></td><td></td><td>€'.number_format($subtotal,2,"."," ").'</td><td></td></tr>';

		echo '<tr><td><b>Bezorgkosten:</b></td><td></td><td></td><td>€'. number_format($bez,2,"."," ") .'</td><td></td></tr>';
		echo '<tr><td><b>Totaal:</b></td><td></td><td></td><td>€'.number_format($total,2,"."," ").'</td><td></td></tr>';
		echo '</table>'; 
		echo '</div>';
	}
	
?>
<script>
	$('.dropbtn').on('touchstart', function (event) {
    $(".dropdown-content").slideToggle(200, "swing");
	});
</script>
<div class="submenu" style="float:right">
	<a href="history.php" id="fancy_a" style="float:right;">Eerdere <br>Bestellingen</a>
	<br>
	<a href="logout.php" id="fancy_a" style="float:right;">Uitloggen</a>
	</a>
</div>
</body>
<?php
$perm_level = DB::query('SELECT perm_level FROM users WHERE username = %s', $_SESSION['username'])[0]['perm_level'];
if($perm_level >= 2){
    echo '<a href="lijstje.php">Boodschappenlijstje</a>';
    echo '<br><br><form method="post">'
    . '<input type="submit" name="naar appie" value="vandaag naar de appie">'
    . '<input type="submit" name="niet naar appie" value="vandaag niet naar de appie">'
    . '<input type="submit" name="speciaal bericht" value="speciaal bericht">'
    . '<input type="text" name="speciaal bericht tekst" placeholder="speciaal bericht"></form>';
}
?>
</html>
