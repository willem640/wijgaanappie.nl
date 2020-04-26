<?php
session_start();
require_once 'header_material.php';
require_once 'setup.php';
require_once 'simple_html_dom.php';
?>
<form method="get" action="zoek_exp.php">
    <input type="text" name="query"/>
    <input type="submit"/>
</form>

<?php
echo 'Resulaten Aantal Gevonden: ' . count($query);
echo '<ul>';
$search=$_GET['query'] ?? '';
$query=DB::query("SELECT * FROM `products-with-noprice` WHERE MATCH(title) AGAINST(%s) ORDER BY MATCH(title) AGAINST(%s) DESC", $search, $search);
$mh = curl_multi_init();
foreach($query as $result){
    echo '<li>'.$result['title'].'</li>';
    $url="https://www.ah.nl/service/rest" . substr($result['link'], 17, strlen($result['link'])-17);
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $curlHandles[$url]=$ch;
    
    curl_multi_add_handle($mh, $ch);
    
}
do {
    $status= curl_multi_exec($mh, $active);
    if($active){
    curl_multi_select($mh);
    }
} while ($active && $status == CURLM_OK);

foreach($curlHandles as $url=>$ch){
    $content=json_decode(curl_multi_getcontent($ch), true);
    $prod=$content['_embedded']['lanes'][4]['_embedded']['items'][0]['_embedded']['product'];
    echo '<pre>';
    echo '<img src="'.$prod['images'][0]['link'].'">';
    echo '</pre>';
}
curl_multi_close($mh);
echo '</ul>';
?>