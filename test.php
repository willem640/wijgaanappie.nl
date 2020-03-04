<html>
<?php
require_once 'setup.php';
require_once 'simple_html_dom.php';
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$list = [];
$start = microtime(true);
for($i=10;$i<200;$i++){
	curl_setopt($ch, CURLOPT_URL, 'https://www.ah.nl/producten/product/wi' . $i);
	$data=curl_exec($ch);
	$parse = new simple_html_dom();
	$parse->load($data);
	if(empty($parse->find('main'))){
		$list['wi' . $i]=array('name'=>'leeg');
	} else {
		$title = $parse->find('span.line-clamp');
		$list['wi' . $i]=array('name'=>$title);
	}
	
	
}
curl_close($ch);
$end= microtime(true);

$time=$end-$start;
echo $time;
print_r($list);
//$title = $parse->find('span.line-clamp'); //titel van product
//$check = empty($parse->find('main')); //check of product bestaat
// enige wat je nu moet doen is dit alles in loop zetten en voor elk ding bijbehorende data in database zetten
// kolommen idee : prod_id, image_link, title, price_now, (en nog iets met bonus)
?>

</html>