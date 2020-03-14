<?php session_start(); 
require_once 'header.php';
require_once 'setup.php';?>

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

$response = file_get_contents('https://www.googleapis.com/customsearch/v1?'
        . 'key='.    $customSearch['apiKey']
        . '&cx='.    $customSearch['searchID']
        . '&q=' .    urlencode($_GET['query'])
        . '&start='. (int)($_GET['start']??1));
$parse_producten = json_decode($response, true);

foreach ($parse_producten['items'] as $parse_prod){
    if(strpos($parse_prod['link'], '/producten/product/')!==false){
        $pos = strpos($parse_prod['link'], '/producten/product');
        $producten[] = substr($parse_prod['link'],$pos, strlen($parse_prod['link']) - $pos);
    }
}

echo '<table id="products">';
echo '<tr><th>Product</th><th>Afbeelding</th><th>Prijs</th><th style="text-align:left;">Aantal</th><th></th></tr>';
$max = 20;
$i = 0;
foreach($producten as $product){
    if($i >= $max){
        $i = 0;
        break;
    } $i += 1;
    //curl_setopt($ch, CURLOPT_URL, 'https://www.ah.nl/service/rest/delegate?url='.urlencode($product));
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
if(!isset($_GET['query'])){
    goto no_arrows;
}
?>
<div id="arrows" style="width:100%">
    <form method="get">
        <a href="#" onclick="this.closest('form').elements['start'].value = <?php echo (($_GET['start']??1)-10>0?($_GET['start']??1)-10:1); ?>;this.closest('form').submit()">
            <img src="assets/TriangleArrow-Left.svg" alt="" style="width:10%; float: left; margin:50px">
        </a>
        <a href="#" onclick="this.closest('form').elements['start'].value = <?php echo (($_GET['start']??1)+10<100?($_GET['start']??1)+10:($_GET['start']??1)); ?>;this.closest('form').submit()">
            <img src="assets/TriangleArrow-Right.svg" alt="" style="width:10%; float: right; margin:50px">
        </a>
        <input type="hidden" name="start">
        <input type="hidden" name="query" value="<?php echo $_GET['query']?>">
    </form>
</div>
<?php no_arrows:?>
    </body>
</html>
