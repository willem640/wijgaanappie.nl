<?php session_start(); 
require_once 'header_material.php';?>

<!DOCTYPE html>
<html>
<?php echo $header;?>
        <link rel='stylesheet' media='only screen and (max-width: 1080px)' href='style_smallscreen.css' />
        <link type=\"text/css\" rel=\"stylesheet\" media=\"only screen and (min-width: 1080px)\" href=\"style.css\">
        <script type="text/javascript">
        var dialog;
        window.onload = function() {
            var ripple_surfaces = $('.ripple-surface');
            for(var i = 0; i < ripple_surfaces.length; ++i){
                mdc.ripple.MDCRipple.attachTo(ripple_surfaces[i]);
            }
            dialog = new mdc.dialog.MDCDialog(document.querySelector('.bonus-product-dialog'));
        }
        function buyProductDialog(title, price_was, price_now, unit_size, discount, index){
            $('#bonus-product-dialog-title')[0].innerHTML = title;
            if(price_was === ''){
                $('#bonus-product-dialog-content')[0].innerHTML = 
                    `Voor: ${price_now}<br>`.concat(
                    `Korting: ${discount}<br>`,
                    `${unit_size}`)
            } else {
                $('#bonus-product-dialog-content')[0].innerHTML = 
                    `Van: ${price_was}<br>`.concat(
                    `Voor: ${price_now}<br>`,
                    `Korting: ${discount}<br>`,
                    `${unit_size}`)
                }
            dialog.open();
        }
        </script>
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
        
<div class="mdc-dialog bonus-product-dialog">
  <div class="mdc-dialog__container">
    <div class="mdc-dialog__surface"
      role="alertdialog"
      aria-modal="true"
      aria-labelledby="my-dialog-title"
      aria-describedby="my-dialog-content">
        <h2 class="mdc-dialog__title" id="bonus-product-dialog-title">
            
        </h2>
      <div class="mdc-dialog__content" id="bonus-product-dialog-content">
          
      </div>
      <footer class="mdc-dialog__actions">
        <button type="button" class="mdc-button mdc-dialog__button" data-mdc-dialog-action="no">
          <div class="mdc-button__ripple"></div>
          <span class="mdc-button__label">No</span>
        </button>
        <button type="button" class="mdc-button mdc-dialog__button" data-mdc-dialog-action="yes">
          <div class="mdc-button__ripple"></div>
          <span class="mdc-button__label">Yes</span>
        </button>
      </footer>
    </div>
  </div>
  <div class="mdc-dialog__scrim"></div>
</div>

<ul class="mdc-image-list bonus-image-list mdc-image-list--with-text-protection">
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
        foreach($products as $key => $el){
            $prod = $el['_embedded']['product'];
            $_SESSION['orderable_array'][$key] = $prod; // array met producten en dan kan je op basis van de index iets kopen ipv het hele object
                                                                                          
            echo('<li class="mdc-image-list__item bonus-image-list__item ripple-surface" '
                        // function buyProductDialog(title, price_was, price_now, unit_size, discount)
                        . 'onclick="buyProductDialog(\''.addslashes($prod["description"]).'\', \''.$prod["priceLabel"]["was"].'\', \''.$prod["priceLabel"]["now"].'\', \''.$prod["unitSize"].'\', \''. ucfirst(strtolower($prod["discount"]["type"]["name"])).'\',\''.$key.')" >'
                        . '<div class="mdc-image-list__image-aspect-container">'
                            . '<img class="mdc-image-list__image" src="'.($prod["images"][0]["link"]["href"] ?? "").'">'
                        . '</div>'
                        . '<div class="mdc-image-list__supporting">'
                            . '<span class="mdc-image-list__label">'.($prod["description"] ?? "").'</span>'
                        . '</div>'
                  . '</li>');
        }

        ?>
</ul>
</body>
</html>
