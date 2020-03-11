<?php session_start(); 
require_once 'setup.php';?>

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
        <meta charset="UTF-8">
        <title>Zoek</title>
        <link rel='stylesheet' media='only screen and (max-width: 1080px)' href='style_smallscreen.css' />
        <link type="text/css" rel="stylesheet" media="only screen and (min-width: 1080px)" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    <body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5GJ825S"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
        <div class="banner">
	<a class="left active" id="home" href="index.php">Robins AH Bestelservice</a>
        <a class="right" id="banner_a" href="zoek.php">Zoek</a>
	<a class="right" id="banner_a" href="bonus.php">Bonus</a>
	<a class="right" id="banner_a" href="bestelling.php">Bestel</a>
	<a class="right" id="banner_a" href="contact.php">Contact</a>
        <?php 
		$logged_in=(isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false);
		if($logged_in){
        echo '<a class="right" id="banner_a" href="profile.php">Profiel</a>';
        } else {
        echo '<a class="right" id="banner_a" href="login.php">Inloggen</a>';
        }
		?>
</div>
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
