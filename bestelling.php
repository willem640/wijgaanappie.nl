<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
}
require_once 'header.php';
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
 <?php echo $header;?>
    <?php
    if($logged_in){
		
        
        $cart = json_decode(DB::query('SELECT cart FROM users WHERE username = %s',$_SESSION['username'])[0]['cart'],true);
		if(empty($cart)){
			
			echo '<h1>Uw winkelmandje is op dit moment leeg!</h1>';
		
		} else{
		echo '<table id="products" style="width:100%">';
        echo '<tr><th>Product</th><th>Afbeelding</th><th>Prijs</th><th>Aantal</th><th></th></tr>';	
		$subtotal=0;
		$bez=0;
		$total=0;
			foreach((array)$cart as $prod){
				$am=$prod['bestelling_amount'];
				echo '<tr><td>'.$prod['description'].'</td>'
						. '<td style="width:10%"><img id="tableImg" src="'.$prod['images'][0]['link']['href'].'"></td>'
						. '<td>€'.$prod['priceLabel']['now'].'</td>'
						. '<td>'.$prod['bestelling_amount'].'</td>'
						. '<td><form method="post" id="delete_'.$prod['id'].'">'
						. '<input type="hidden" name="delete" value="'.$prod['id'].'">'
						. '<a onclick="document.getElementById(\'delete_'.$prod['id'].'\').submit();" href="#"><img style="width: 10vh" src="assets/cross.svg"></a></form></td></tr>';
				$subtotal += $prod['priceLabel']['now']*$am; // when items don't have an ID, the random value that is returned seems to be constant so it just works
				$total=round(1.1*$subtotal,2);
				$bez=$total-$subtotal;        
			}
			echo '<tr><td><b>Subtotaal:</b></td><td></td><td></td><td>€'.number_format($subtotal,2,"."," ").'</td><td></td></tr>';

			echo '<tr><td><b>Bezorgkosten:</b></td><td></td><td></td><td>€'. number_format($bez,2,"."," ") .'</td><td></td></tr>';
			echo '<tr><td><b>Totaal:</b></td><td></td><td></td><td>€'.number_format($total,2,"."," ").'</td><td></td></tr>';
			echo '</table>'
                           . '<center><p>De prijzen op de website dienen puur ter referentie en kunnen hoger of lager zijn dan hier aangegeven</p></center>';
			echo '<form method="post" onsubmit="ga(\'send\', \'event\', \'Bestelling\', \'Bestelling\', \'Bestelling\')">'
				. '<center><input id="confirm" type="submit" value="Bestellen" name="place_order"></center>'
				. '</form><br><br><br><br>';
		}
       
    }
    
    ?>

    </body>

</html>
