<?php
session_start();
require_once 'setup.php';
$start_date=(date("D")=="Mon" ? strtotime("today") : strtotime("last monday"));
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
<form method="get">
    <input type="date" value="<?php echo $_POST['date'] ?? date('Y-m-d')?>" name="date">
</form>
<?php
$perm_level = DB::query("SELECT perm_level FROM users WHERE username = %s", $_SESSION['username'])[0]['perm_level'];
if($perm_level >= 2){
    $all_users = DB::query('SELECT * FROM users');
    
    foreach($all_users as $user){
		//Check welke dagen user in heeft gekocht deze week
		$orders=json_decode($user['previous_orders'], true);
		$days=[];
		$week_totaal=0;
		for($i=0;$i<7;$i++){
			$day=$start_date+$i*24*3600;
			$date=date("d-m-Y", $day);
			if(array_key_exists($date, $orders)){
				if(!empty($orders[$date])){
				array_push($days, date("N", $day)-1);
				}
			}
		}
		if(empty($days)){
			continue;
		}
		echo "<h1>" . $user['realname'] . "</h1>";
		foreach($days as $day){
			$date=date("d-m-Y",$start_date+$day*24*3600);
			$order = $orders[$date];
			if(empty($order)){
				continue;
			}
			echo "<h2>" . date("l" , $start_date+$day*24*3600) . "</h2>";
			
			//var_dump($order);
			echo "<table><tr><th>Product</th><th>Aantal</th><th>Verwijder</th><th>Prijs</th></tr>";
			$dag_totaal=0;
			foreach($order as $prod){
				echo "<tr><td>" . $prod["description"] . "</td><td>" . $prod["bestelling_amount"] . "</td><td></td><td>" . round(1.1*$prod["bestelling_amount"]*$prod["priceLabel"]["now"],2) . "</td></tr>";
				$dag_totaal+=1.1*$prod["bestelling_amount"]*$prod["priceLabel"]["now"];
			}
			echo "</table>";
			echo "<p>Dagtotaal: &euro;" . round($dag_totaal, 2) . "</p>";
			$week_totaal+=$dag_totaal;
		}
		echo "<p><b>Weektotaal: &euro;" . round($week_totaal, 2) . "</b></p>";
    }
}  

?>
</body>

</html>
