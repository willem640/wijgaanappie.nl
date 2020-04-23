<?php session_start(); 
require_once 'header.php';?>

<!DOCTYPE html>
<html>
<?php echo $header;?>
        <form method="get">
            <select id="top" name="sort" onchange="if(this.value != 0) { this.form.submit(); }">
                <?php
                
                echo '<option value="alphabetical" '.(($_GET['sort'] != "alphabetical")?: 'selected' ).'>A-z</option>';
                echo '<option value="reverse alphabetical" '.(($_GET['sort'] != "reverse alphabetical")?: 'selected' ).'>Z-a</option>';
                echo '<option value="price" '.(($_GET['sort'] != "price")?: 'selected' ).'>Op prijs(oplopend)</option>';
                echo '<option value="reverse price" '.(($_GET['sort'] != "reverse price")?: 'selected' ).'>Op prijs(aflopend)</option>';
                
                    ?>
            </select>
        </form>
        <div class="spacer"></div>
<div class="grid">
<?php
require_once 'setup.php';
require_once 'simple_html_dom.php';
$bonus = json_decode(file_get_contents('https://www.ah.nl/service/rest/bonus'),true);

$products = [];
foreach($bonus['_embedded']['lanes'] as $lane){
    if(($lane['type'] ?? '') == 'ProductLane' && ($lane['_embedded']['items'][0]['label'] ?? '') != 'Extra online aanbiedingen'){
        $products = array_merge($products,$lane['_embedded']['items']);
        
} }
        $products = array_filter($products, function($val){ return ((($val['resourceType'] ?? '') == 'Product') 
                                                                    && ($val['promotionTheme'] ?? '') == 'ah'
                                                                    && !empty($val['_embedded']['product']['priceLabel']['now'])
                                                                    );});

        
        if(strpos(($_GET['sort'] ?? ''), 'reverse') !== false){ // if string is in string
            $sortdir = SORT_DESC;
        } else {
            $sortdir = SORT_ASC;
        }
        if (strpos(($_GET['sort'] ?? ''), 'price') !== false) {
            $col = array_column(
                    array_column(
                            array_column(
                                    array_column($products,
                                    '_embedded'),
                            'product'),
                    'priceLabel'),
                   'now');
        } else { // sort alphabetically if something weird was selected, such as 'alphabetical'
            $col = array_column(
                    array_column(
                            array_column($products,
                            '_embedded'),
                    'product'),
                   'description'); // access column with desc, nested bc php is dumb
        } 
        //print_r($products);
        array_multisort($col,$sortdir,$products);
        foreach($products as $el){
            $prod = $el['_embedded']['product'];
                echo '<form method="post" action="bestelling.php">'
                        . '<input name="product" type="hidden" value=\''.str_replace("'",'',json_encode($prod)).'\'>'
                        . '<a id="unstyle" href="#" onclick="this.closest(\'form\').submit();">'
                        . '<div class="grid-item">'
                        . '<p>'.($prod['description'] ?? '').'</p>'
                        .'<br>'
                        . '<img id="tableImg" src="'.($prod['images'][0]['link']['href'] ?? '').'">'
                        . '<br>'
                        . '<div class="leftGrid">'
                        . (
                                isset($prod['priceLabel']['was']) ?
                                    '<del>€'.$prod['priceLabel']['was'].'</del>' 
                                :   ''
                            )
                        . (
                                isset($prod['priceLabel']['was'])?
                                    ' €'.($prod['priceLabel']['now'] ?? '')
                                  : ''
                            )
                        . '</div>'
                        . '<div class="rightGrid">'
                        . ucfirst(strtolower($prod['discount']['label'] ?? '')).''
                        . '</div></div></a></form>';
    }
    

        ?>
</div>
    </body>
</html>
