<?php

session_start();
require_once 'setup.php';
require_once 'simple_html_dom.php';

$search = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? '';
$from = (int) ($_GET['from'] ?? 0);
$to = (int) ($_GET['to'] ?? 10);
$num = abs($to - $from);

if (strpos($sort, 'reverse') !== false) {
    $sort_direction = 'DESC';
} else {
    $sort_direction = 'ASC';
}
if (strpos(($sort ?? ''), 'price') !== false) {
    $query = DB::query("SELECT * FROM `products` WHERE MATCH(title) AGAINST(%s0) ORDER BY priceNow " . $sort_direction . " LIMIT " . $from . "," . $num, $search);
    $count = DB::query("SELECT COUNT(*) FROM `products` WHERE MATCH(title) AGAINST(%s0) ORDER BY priceNow " . $sort_direction, $search);
} else if (strpos(($sort ?? ''), 'alphabetical') !== false) {
    $query = DB::query("SELECT * FROM `products` WHERE MATCH(title) AGAINST(%s0) ORDER BY title " . $sort_direction . " LIMIT " . $from . "," . $num, $search);
    $count = DB::query("SELECT COUNT(*) FROM `products` WHERE MATCH(title) AGAINST(%s0) ORDER BY title " . $sort_direction, $search);
} else { //not price, not alphabetical, so sort by relevance
    $query = DB::query("SELECT * FROM `products` WHERE MATCH(title) AGAINST(%s0) ORDER BY MATCH(title) AGAINST(%s0) " . $sort_direction . " LIMIT " . $from . "," . $num, $search);
    $count = DB::query("SELECT COUNT(*) FROM `products` WHERE MATCH(title) AGAINST(%s0) ORDER BY MATCH(title) AGAINST(%s0) " . $sort_direction, $search);
}
$count = (int) ($count[0]['COUNT(*)']);
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
    $prod = $detail_lane['_embedded']['items'][0]['_embedded']['product'];
    $_SESSION['orderable_array'][$key + $from] = $prod;
    echo '<div class="mdc-card material-card">'
    . ' <div class="mdc-card__primary-action ripple-surface" onclick="buyProductDialog(\'' . addslashes($prod["description"]) . '\', \'' . $prod["priceLabel"]["was"] . '\', \'' . $prod["priceLabel"]["now"] . '\', \'' . $prod["unitSize"] . '\', \'' . ucfirst(strtolower($prod["discount"]["label"] ?? $prod["discount"]["type"]["name"])) . '\',\'' . ($key +$from). '\')">'
    . '<div class="mdc-card__media material-card__media" style="background-image: url(' . $prod['images'][0]['link']['href'] . ')"></div>'
    . '<h5 class="mdc-typography--headline5 material-card__title">'
    . $prod["description"]
    . '</h5>'
    . '<p class="mdc-typography--body1 material-card__content">'
    . '€' . ($prod["priceLabel"]["now"] ?? ($prod["discount"]["label"] ?? '')) . ' - ' . $prod["unitSize"]
    . '</p>'
    . '</div>'
    . '</div>';
    ++$key;
}
curl_multi_close($mh);
if ($to < $count && $from < $count) {
    if ($to + $num > $count) {
        $to = $count;
    } else {
        $to += $num;
    }
    if ($from + $num > $count) {
        die();
    } else {
        $from += $num;
    }
    echo '<a style="display:none" href="getSearchResults.php?q=' . $search . '&sort=' . $sort . '&to=' . $to . '&from=' . $from . '"></a>';
}
