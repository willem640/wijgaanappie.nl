<?php
session_start();
require_once 'setup.php';
require_once 'header.php';
?>

<!DOCTYPE HTML>
<html>
<?php echo $header;?>
<div class="content">
<div class="status">
	<h1>Status</h1>
	    <?php
    $query = DB::query('SELECT * FROM komt_chobin_naar_de_appie')[0]; //should only be one row, or someone decided to be a dick
    $komt_chobin = $query['komt hij'];
    if($komt_chobin === '1'){
        echo '<p style="color: #02d10c">Vandaag bezorgen we!</p>';
    } elseif($komt_chobin === '0' || empty($komt_chobin)){
        echo '<p style="color: #ad0c00">Vandaag bezorgen we helaas niet, je kan gewoon bestellen, je bestelling blijft dan staan<p>';
    } elseif($komt_chobin === '2'){ //special status
       echo $query['special_status'];
    }
    ?>
</div>
<h1>Welkom bij Wijgaanappie.nl</h1>
<p>Wij zijn Willem en Robin, en wij heten je welkom op de site voor onze lokale bezorgservice waarbij wij jou dé spullen bezorgen die jij nodig hebt! Wij bezorgen van alles
om en nabij de school en direct vanaf jouw favoriete Albert Heijn op de Pottenbakkerssingel<br>Wil jij ook wat bestellen? Registreer je dan eerst en kies jouw producten
uit het ruime assortiment.
<a href="https://wijgaanappie.nl/zoeken.php?query=wi451137">Klik hier om een focaccia Mozarella te bestellen</a><br><br><br> </p>
</div>

</body>

</html>
