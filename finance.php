<?php
session_start();
require_once 'header.php';
require_once 'setup.php';
$start_date=(date("D")=="Mon" ? strtotime("today") : strtotime("last monday"));
?>

<!DOCTYPE HTML>
<html>
<?php echo $header; ?>
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
