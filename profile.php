<?php
session_start();
require_once 'header.php';
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
<?php echo $header;?>
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
    echo '<a id="fancy_a" href="lijstje.php">Boodschappenlijstje</a>';
    echo '<br><br><form method="post">'
    . '<input type="submit" name="naar appie" value="vandaag naar de appie">'
    . '<input type="submit" name="niet naar appie" value="vandaag niet naar de appie">'
    . '<input type="submit" name="speciaal bericht" value="speciaal bericht">'
    . '<input type="text" name="speciaal bericht tekst" placeholder="speciaal bericht"></form>';
}
?>
</html>
