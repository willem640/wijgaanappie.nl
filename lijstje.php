<?php
session_start();
require_once 'setup.php';


if(!($_SESSION['loggedin'] ?? false)){
    header('Location: login.php?return=lijstje.php');
}

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
        <link rel='stylesheet' media='only screen and (max-width: 1080px)' href='style_smallscreen.css' />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5GJ825S"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
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
</body>
<center>
    <?php
    $perm_level = DB::query('SELECT perm_level FROM users WHERE username = %s', $_SESSION['username'])[0]['perm_level'];
    if($perm_level >= 2){
        $all_orders = DB::query('SELECT * FROM current_orders');
		$j = 0; //Zo doe k ff index snap key=>value nie. fight me
           
        foreach($all_orders as $orders){
			$contents = json_decode($orders['contents'], true);
			$order_history = json_decode(DB::query('SELECT previous_orders FROM users WHERE username=%s', $orders['username'])[0]['previous_orders'], true);
			$order_history[date("d-m-Y")] = array_merge(($order_history[date("d-m-Y")]??[]),$contents);
			$order_history_json=json_encode($order_history);
			if(isset($_POST['clear'])){
				DB::update('users', ['previous_orders' => $order_history_json], 'username=%s', $orders['username']);
			}
			//$order_history = array_merge($test,$order_history);
			//print_r($order_history);
                  if(empty($contents)){goto end_loop;} //Zo krijg ik niet telkens lege orders te zien was best verwarrend   
  
            echo '<h3>'.(empty($orders['realname']) ? $orders['username'] : $orders['realname']).'</h3>';
            echo '<table class="products">';
            echo '<tr><th>Product</th><th>Prijs</th><th>Hoeveelheid</th></tr>';
           $subtotal = $bez = $total = 0;
            foreach ((array) $contents as $prod) {
                $am = $prod['bestelling_amount'];
                echo '<tr><td>' . $prod['description'] . '</td>'
                . '<td>€' . $prod['priceLabel']['now'] . '</td>'
                . '<td>'.$prod['bestelling_amount'].'</td></tr>';
                $subtotal += $prod['priceLabel']['now'] * $am; // when items don't have an ID, the random value that is returned seems to be constant so it just works
                $total = round(1.1 * $subtotal, 2);
                $bez = $total - $subtotal;
            }
            echo '<tr><td><b>Subtotaal:</b></td><td></td><td>€' . $subtotal . '</td><td></td></tr>';

            echo '<tr><td><b>Bezorgkosten:</b></td><td></td><td>€' . $bez . '</td><td></td></tr>';
            echo '<tr><td><b>Totaal:</b></td><td></td><td>€' . round($total, 2) . '</td><td></td></tr>';
            echo '</table>';
            echo '</table>';
			end_loop:
			$j++;
        }
        if(isset($_POST['clear'])){
			 DB::query('DELETE FROM current_orders');
     echo('<script type="text/javascript">window.location.href=lijstje.php</script>');
	}
    }

    ?>
    <br><br><br><br><form method="post" onsubmit="return confirm('weet je zeker dat je alles hebt?')"><input type="submit"  value="lijst leeghalen" name="clear"></form>
</center>
</html>
