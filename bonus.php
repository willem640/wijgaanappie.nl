<?php session_start(); ?>

<!DOCTYPE html>
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
        <title>Bonus</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <link rel='stylesheet' media='only screen and (max-width: 1080px)' href='style_smallscreen.css' />
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
      <li><a id="mobile_banner_a" href="zoeken.php">Zoek</a>
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
            $col = array_column(array_column(array_column(array_column($products,'_embedded'),'product'),'priceLabel'),'now');
        } else { // sort alphabetically if something weird was selected, such as 'alphabetical'
            $col = array_column(array_column(array_column($products,'_embedded'),'product'),'description'); // access column with desc, nested bc php is dumb
        } 
        //print_r($products);
        array_multisort($col,$sortdir,$products);
        foreach($products as $el){
            $prod = $el['_embedded']['product'];
                echo '<form id="form_'.$prod['id'].'" method="post" action="bestelling.php">'
                        . '<input name="product" type="hidden" value=\''.str_replace("'",'',json_encode($prod)).'\'>'
                        . '<a id="unstyle" href="#" onclick="document.getElementById(\'form_'.$prod['id'].'\').submit();">'
                        . '<div class="grid-item">'. '<p>'.($prod['description'] ?? '').'</p>'.'<br>'
                        . '<img id="tableImg" src="'.($prod['images'][0]['link']['href'] ?? '').'"><br>'
                        . '<div class="leftGrid">'
                        . '<del>€'.($prod['priceLabel']['was'] ?? '').'</del>'
                        . ' €'.($prod['priceLabel']['now'] ?? '')
                        . '</div><div class="rightGrid">'
                        . ($prod['discount']['label'] ?? '').''
                        . '</div></div></a></form>';
    }
    

        ?>
</div>
    </body>
</html>
