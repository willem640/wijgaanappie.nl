<?php session_start();
//header('Location: zoeken.php');
require_once 'header.php';
?>

<!DOCTYPE html>
<html>
<?php echo $header;?>
        <form method="get">
            <input type="search" name="query" placeholder="Zoek hier naar uw product...">
        </form>
<?php
require_once 'setup.php';
require_once 'simple_html_dom.php';
if(!empty($_GET['query'])){
$response = file_get_contents('https://www.ah.nl/zoeken?query='.$_GET['query']);
$parse = new simple_html_dom($response);
//$parse->load($response);
$parse_producten = $parse->find('article div a');
//var_dump($parse_producten);
foreach ($parse_producten as $parse_prod){
    $producten[] = $parse_prod->href;
}
foreach (range(1, count($producten), 2) as $key) { // for some reason every link is doubled, so this does something magical and removes every second element
  unset($producten[$key]);
}
$producten = array_merge($producten); // reset keys

echo '<table id="products">';
echo '<tr><th>Product</th><th>Afbeelding</th><th>Prijs</th><th style="text-align:left;">Aantal</th><th></th></tr>';
$max = 20;
$i = 0;
foreach($producten as $product){
    if($i >= $max){
        $i = 0;
        break;
    } $i += 1;
    $response = json_decode(file_get_contents('https://www.ah.nl/service/rest/delegate?url='.urlencode($product)),true); // true is for array, false for stdClass
    foreach((array)$response['_embedded']['lanes'] as $lane){
        if($lane['type'] == 'ProductDetailLane'){
            $prod = $lane['_embedded']['items'][0]['_embedded']['product'];
            echo      '<tr><td>'.$prod['description'].'</td>'
                    . '<td style="width:10%"><img id="tableImg" src="'.$prod['images'][0]['link']['href'].'"></td>'
                    . '<td>€'.$prod['priceLabel']['now'].'</td>'
                    . '<td><form id="form_'.$prod['id'].'" method="post" action="bestelling.php">'
                    . '<input id="aantal" type="number" name="amount" min=1 value="1" placeholder="aantal">'
					. '<input name="product" type="hidden" value=\''.str_replace("'",'',json_encode($prod)).'\'>'
                    . '<a id="fancy_a" onclick="document.getElementById(\'form_'.$prod['id'].'\').submit();">Bestellen</a></form><td><tr>';
        }
    }
}
echo '</table>';
}

?>

    </body>
</html>
