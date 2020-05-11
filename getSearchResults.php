<?php

$search = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? '';
$from = (int)($_GET['from'] ?? 0);
$to = (int)($_GET['to'] ?? 10);
$num = abs($to - $from);

if (strpos($sort, 'reverse') !== false) {
    $sort_direction = 'DESC';
} else {
    $sort_direction = 'ASC';
}
if (strpos(($_GET['sort'] ?? ''), 'price') !== false) {
    $query = DB::query("SELECT * FROM `products` WHERE MATCH(title) AGAINST(%s0) ORDER BY priceNow " . $sort_direction . " LIMIT " . $from . "," . $num, $search);
} else if (strpos(($_GET['sort'] ?? ''), 'alphabetical') !== false) {
    $query = DB::query("SELECT * FROM `products` WHERE MATCH(title) AGAINST(%s0) ORDER BY title " . $sort_direction . " LIMIT " . $from . "," . $num, $search);
} else { //not price, not alphabetical, so sort by relevance
    $query = DB::query("SELECT * FROM `products` WHERE MATCH(title) AGAINST(%s0) ORDER BY MATCH(title) AGAINST(%s0) " . $sort_direction . " LIMIT " . $from . "," . $num, $search);
}

$mh = curl_multi_init();

foreach ($query as $result) {
    $ch = curl_init();
    $url = "https://www.ah.nl/service/rest" . substr($result['link'], 17, strlen($result['link']) - 17);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $curlHandles[$url] = $ch;
    curl_multi_add_handle($mh, $ch);
}
do {
    $status = curl_multi_exec($mh, $active);
    if ($active) {
        curl_multi_select($mh);
    }
} while ($active && $status == CURLM_OK);
$key = 0;
foreach ($curlHandles as $handle_url => $ch) {
    $content = json_decode(curl_multi_getcontent($ch), true);
    if (!isset($content)) {
        curl_setopt($ch, CURLOPT_URL, $handle_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = json_decode(curl_exec($ch), true); // try again
        if (!isset($content)) {
            continue; // er zal wel iets met de key zijn
        }
    }
    $detail_lanes = array_filter($content['_embedded']['lanes'], function ($lane) {
        return isset($lane['_embedded']['items'][0]['_embedded']['product']);
    });
    $detail_lane = array_values($detail_lanes)[0];
    //echo json_encode($detail_lane);
    $prod = $detail_lane['_embedded']['items'][0]['_embedded']['product'];
    /* if(!isset($prod)) {
      continue;
      } */
    $_SESSION['orderable_array'][$key] = $prod;
    ++$key;
    echo '<div class="mdc-card search-result-card">'
    . ' <div class="mdc-card__primary-action ripple-surface" onclick="buyProductDialog(\'' . addslashes($prod["description"]) . '\', \'' . $prod["priceLabel"]["was"] . '\', \'' . $prod["priceLabel"]["now"] . '\', \'' . $prod["unitSize"] . '\', \'' . ucfirst(strtolower($prod["discount"]["label"] ?? $prod["discount"]["type"]["name"])) . '\',\'' . $key . '\')">'
    . '<div class="mdc-card__media search-result-card__media" style="background-image: url(' . $prod['images'][0]['link']['href'] . ')"></div>'
    . '<h5 class="mdc-typography--headline5 search-result-card__title">'
    . $prod["description"]
    . '</h5>'
    . '<p class="mdc-typography--body1 search-result-card__content">'
    . '€' . ($prod["priceLabel"]["now"] ?? ($prod["discount"]["label"] ?? '')) . ' - ' . $prod["unitSize"]
    . '</p>'
    . '</div>'
    . '</div>';
}
curl_multi_close($mh);
