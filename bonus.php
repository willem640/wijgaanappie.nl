<?php session_start();
require_once 'header_material.php';
?>


<!DOCTYPE html>
<html>
<?php echo $header; ?>
</style>
<link rel='stylesheet' media='only screen and (max-width: 1080px)' href='style_smallscreen.css' />
<link type=\"text/css\" rel=\"stylesheet\" media=\"only screen and (min-width: 1080px)\" href=\"style.css\">
<script type="text/javascript">
    var dialog;
    var select;
    window.onload = function () {
        var ripple_surfaces = $('.ripple-surface');
        for (var i = 0; i < ripple_surfaces.length; ++i) {
            mdc.ripple.MDCRipple.attachTo(ripple_surfaces[i]);
        }
        dialog = new mdc.dialog.MDCDialog(document.querySelector('.bonus-product-dialog'));
        select = new mdc.select.MDCSelect(document.querySelector('.sort-select'));
        const url_parameters = new URLSearchParams(window.location.search);
        if (url_parameters.has('sort')) {
            current_sort = url_parameters.get('sort');
            current_sort_index = $('div.bonus-sort-select ul li[data-value="' + current_sort + '"]')
                    .index();
            select.selectedIndex = current_sort_index;
        } else {

        }


        select.listen('MDCSelect:change', () => {
            document.location.href = window.location.pathname + '?sort=' + select.value;
        });
    };
    function buyProductDialog(title, price_was, price_now, unit_size, discount, index) {
        $('#bonus-product-dialog-title')[0].innerHTML = title;
        if (price_was === '') {
            $('#bonus-product-dialog-content')[0].innerHTML =
                    `Voor: ${price_now}<br>`.concat(
                            `Korting: ${discount}<br>`,
                            `${unit_size}`);
        } else {
            $('#bonus-product-dialog-content')[0].innerHTML =
                    `Van: €${price_was}<br>`.concat(
                            `Voor: €${price_now}<br>`,
                            `Korting: ${discount}<br>`,
                            `${unit_size}`)
        }
        ;
        dialog.open();
    }
</script>

<div class="mdc-dialog bonus-product-dialog">
    <div class="mdc-dialog__container">
        <div class="mdc-dialog__surface"
             role="alertdialog"
             aria-modal="true"
             aria-labelledby="bonus-dialog-title"
             aria-describedby="bonus-dialog-content">
            <h2 class="mdc-dialog__title" id="bonus-product-dialog-title">

            </h2>
            <div class="mdc-dialog__content" id="bonus-product-dialog-content">

            </div>
            <footer class="mdc-dialog__actions">
                <button type="button" class="mdc-button mdc-dialog__button" data-mdc-dialog-action="no">
                    <div class="mdc-button__ripple"></div>
                    <span class="mdc-button__label">Annuleren</span>
                </button>
                <button type="button" class="mdc-button mdc-dialog__button mdc-button--raised" data-mdc-dialog-action="yes">
                    <div class="mdc-button__ripple"></div>
                    <span class="mdc-button__label">Bestellen</span>
                </button>
            </footer>
        </div>
    </div>
    <div class="mdc-dialog__scrim"></div>
</div>
<div class="wrapper" style="width:96%; left:2%"> 
    <div id="card" style="background-color: gainsboro">

        <div class="mdc-select sort-select">
            <div class="mdc-select__anchor bonus-sort-select">
                <i class="mdc-select__dropdown-icon"></i>
                <div class="mdc-select__selected-text" id="select-sort-selected-text"></div>
                <span class="mdc-floating-label">Sorteer</span>
                <div class="mdc-line-ripple bonus-sort-select-line-ripple"></div>
            </div>

            <div class="mdc-select__menu mdc-menu mdc-menu-surface bonus-sort-select">
                <ul class="mdc-list">

                    <li class="mdc-list-item" data-value="alphabetical">
                        Alfabetisch (A-z)
                    </li>
                    <li class="mdc-list-item" data-value="reverse alphabetical">
                        Omgekeerd Alfabetisch (z-A)
                    </li>
                    <li class="mdc-list-item" data-value="price">
                        Op prijs (oplopend)
                    </li>
                    <li class="mdc-list-item" data-value="reverse price">
                        Op prijs (aflopend)
                    </li>
                </ul>
            </div>
        </div>
        <ul class="mdc-image-list bonus-image-list mdc-image-list--with-text-protection">
            <?php
            require_once 'setup.php';
            require_once 'simple_html_dom.php';
            $bonus = json_decode(file_get_contents('https://www.ah.nl/service/rest/bonus'), true);

            $products = [];
            foreach ($bonus['_embedded']['lanes'] as $lane) {
                if (($lane['type'] ?? '') == 'ProductLane' && ($lane['_embedded']['items'][0]['label'] ?? '') != 'Extra online aanbiedingen') {
                    $products = array_merge($products, $lane['_embedded']['items']);
                }
            }
            $products = array_filter($products, function($val) {
                return ((($val['resourceType'] ?? '') == 'Product') && ($val['promotionTheme'] ?? '') == 'ah' && !empty($val['_embedded']['product']['priceLabel']['now'])
                        );
            });


            if (strpos(($_GET['sort'] ?? ''), 'reverse') !== false) { // if string is in string
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
            array_multisort($col, $sortdir, $products);
            foreach ($products as $key => $el) {
                $prod = $el['_embedded']['product'];
                $_SESSION['orderable_array'][$key] = $prod; // array met producten en dan kan je op basis van de index iets kopen ipv het hele object

                echo('<li class="mdc-image-list__item bonus-image-list__item ripple-surface" '
                // function buyProductDialog(title, price_was, price_now, unit_size, discount)
                . 'onclick="buyProductDialog(\'' . addslashes($prod["description"]) . '\', \'' . $prod["priceLabel"]["was"] . '\', \'' . $prod["priceLabel"]["now"] . '\', \'' . $prod["unitSize"] . '\', \'' . ucfirst(strtolower($prod["discount"]["type"]["name"])) . '\',\'' . $key . '\')" >'
                . '<div class="mdc-image-list__image-aspect-container">'
                . '<img class="mdc-image-list__image" src="' . ($prod["images"][0]["link"]["href"] ?? "") . '">'
                . '</div>'
                . '<div class="mdc-image-list__supporting bonus-image-list__supporting">'
                . '<span class="mdc-image-list__label bonus-image-list__label">' . ($prod["description"] ?? "") . '</span>'
                . '</div>'
                . '<div class="price-label"><span>€' . $prod["priceLabel"]["now"] . '</span></div>'
                . '</li>');
            }
            ?>
        </ul>
    </div>
</div>
</body>
</html>
