<?php session_start();?>
<!DOCTYPE html>
<html>
    <head>
    <?php include 'header_material.php'?>
    </head>
    <body>
        <?php include 'mobile_banner.php' ?>
        <script type="text/javascript">
            var num_products = 1;
            var current_product_index = 0;
            var dialog;
            var select;
            var icon_buttons_dialog = [];
            var success_snackbar;
            var error_snackbar;
            var nologin_snackbar;
            var cancel_success_snackbar;
            $(document).ready(function () {
                var ripple_surfaces = $('.ripple-surface');
                for (var i = 0; i < ripple_surfaces.length; ++i) {
                    mdc.ripple.MDCRipple.attachTo(ripple_surfaces[i]);
                }
                dialog = new mdc.dialog.MDCDialog($('.bonus-product-dialog')[0]);
                select = new mdc.select.MDCSelect($('.sort-select')[0]);
                success_snackbar = new mdc.snackbar.MDCSnackbar($('#bonus-snackbar-order-success')[0]);
                error_snackbar = new mdc.snackbar.MDCSnackbar($('#bonus-snackbar-order-error')[0]);
                nologin_snackbar = new mdc.snackbar.MDCSnackbar($('#bonus-snackbar-not-logged-in')[0]);
                cancel_success_snackbar = new mdc.snackbar.MDCSnackbar($('#bonus-snackbar-order-cancel-success')[0]);
                const url_parameters = new URLSearchParams(window.location.search);
                if (url_parameters.has('sort')) {
                    current_sort = url_parameters.get('sort');
                    current_sort_index = $('div.bonus-sort-select ul li[data-value="' + current_sort + '"]')
                            .index();
                    select.selectedIndex = current_sort_index;
                } else {
                    select.selectedIndex = 0;
                }
                dialog.listen('MDCDialog:closed', function (action) {
                    if (action.detail.action === 'order') {
                        orderProduct(current_product_index, num_products);
                    }
                });

                select.listen('MDCSelect:change', () => {
                    document.location.href = window.location.pathname + '?sort=' + select.value;
                });
            });
            
            function buyProductDialog(title, price_was, price_now, unit_size, discount, index) {
                current_product_index = index;
                $('#bonus-product-dialog-title')[0].innerHTML = title;
                if (price_was === '') {
                    $('#bonus-product-dialog-content')[0].innerHTML =
                            `Voor: ${price_now}<br>`.concat(
                                    `Korting: ${discount}<br>`,
                                    `${unit_size}
                                        `);
                } else {
                    $('#bonus-product-dialog-content')[0].innerHTML =
                            `Van: €${price_was}<br>`.concat(
                                    `Voor: €${price_now}<br>`,
                                    `Korting: ${discount}<br>`,
                                    `${unit_size}`);
                }
                ;
                setProductAmount(1);
                dialog.open();
                if (!icon_buttons_dialog.length) {
                    var icon_buttons_html = $('.mdc-icon-button');
                    for (var i = 0; i < icon_buttons_html.length; ++i) {
                        icon_buttons_dialog[i] = new mdc.ripple.MDCRipple(icon_buttons_html[i]);
                        icon_buttons_dialog[i].unbounded = true;
                    }
                }
            }

            function setProductAmount(new_num_products) {
                if (new_num_products <= 1) {
                    $('#bonus-product-dialog-content-stuks-buttons-stuks')[0].innerHTML = 1 + " Stuks";
                    num_products = 1;
                } else {
                    $('#bonus-product-dialog-content-stuks-buttons-stuks')[0].innerHTML = new_num_products + " Stuks";
                    num_products = new_num_products;
                }
                return num_products;
            }

            function addProduct() {
                setProductAmount(num_products + 1);
            }

            function removeProduct() {
                setProductAmount(num_products - 1);
            }

            function orderProduct(index, amount) {
                $.ajax({
                    type: "POST",
                    url: '/bestelling.php',
                    data: {product: index, amount: amount},
                    success: buyProductPostSuccess,
                    error: buyProductPostError,
                    dataType: 'text'
                });
            }

            function buyProductPostSuccess(data_returned, text_status, xhr) {
                success_snackbar.open();
            }

            function buyProductPostError(xhr, text_status, error_thrown) {
                if(xhr.status === 403){
                    nologin_snackbar.open();
                } else {
                    $('#bonus-snackbar-order-error div div.mdc-snackbar__label')[0].innerHTML
                            = 'Je product is niet besteld: ' + xhr.responseText;
                    error_snackbar.open();
                }
            }

            function retryOrder() {
                orderProduct(current_product_index, num_products);
            }
            
            function undoOrder(){
                $.ajax({
                    type: "POST",
                    url: '/bestelling.php',
                    data: {product: current_product_index, amount: -num_products},
                    success: cancelProductPostSuccess,
                    error: cancelProductPostError,
                    dataType: 'text'
                });
            } 
            function cancelProductPostSuccess(data_returned, text_status, xhr) {
                cancel_success_snackbar.open();
            }

            function cancelProductPostError(xhr, text_status, error_thrown) {
                $('#bonus-snackbar-order-error div div.mdc-snackbar__label')[0].innerHTML
                        = 'Je product is niet geannuleerd: ' + xhr.responseText;
                error_snackbar.open();
                
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
                    <div class="mdc-dialog__content" id="bonus-product-dialog-content-stuks-buttons">
                        <button class="mdc-icon-button material-icons-round" onclick="removeProduct()">remove</button> <div id="bonus-product-dialog-content-stuks-buttons-stuks">1 Stuks</div> <button class="mdc-icon-button material-icons-round" onclick="addProduct()">add</button>
                    </div>
                    <footer class="mdc-dialog__actions">
                        <button type="button" class="mdc-button mdc-dialog__button" data-mdc-dialog-action="cancel">
                            <div class="mdc-button__ripple"></div>
                            <span class="mdc-button__label">Annuleren</span>
                        </button>
                        <button type="button" class="mdc-button mdc-dialog__button mdc-button--raised" data-mdc-dialog-action="order" data-mdc-dialog-button-default>
                            <div class="mdc-button__ripple"></div>
                            <span class="mdc-button__label">Bestellen</span>
                        </button>
                    </footer>
                </div>
            </div>
            <div class="mdc-dialog__scrim"></div>
        </div>
        <div class="mdc-snackbar bonus-snackbar-after-order" id="bonus-snackbar-order-success">
            <div class="mdc-snackbar__surface">
                <div class="mdc-snackbar__label"
                     role="status"
                     aria-live="polite">
                    Je product is besteld
                </div>
                 <div class="mdc-snackbar__actions">
                    <button type="button" class="mdc-button mdc-snackbar__action ripple-surface" onclick="undoOrder()">
                        <div class="mdc-button__ripple"></div>
                        <span class="mdc-button__label" id="bonus-post-error__label">Annuleren</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="mdc-snackbar bonus-snackbar-after-order" id="bonus-snackbar-order-error">
            <div class="mdc-snackbar__surface">
                <div class="mdc-snackbar__label"
                     role="status"
                     aria-live="polite">
                    Je product is niet besteld.
                </div>
                <div class="mdc-snackbar__actions">
                    <button type="button" class="mdc-button mdc-snackbar__action ripple-surface" onclick="retryOrder()">
                        <div class="mdc-button__ripple"></div>
                        <span class="mdc-button__label" id="bonus-post-error__label">Opnieuw</span>
                    </button>
                </div>
            </div>
        </div>
                <div class="mdc-snackbar bonus-snackbar-after-order" id="bonus-snackbar-not-logged-in">
            <div class="mdc-snackbar__surface">
                <div class="mdc-snackbar__label"
                     role="status"
                     aria-live="polite">
                    Om een product te bestellen moet je eerst ingelogd zijn. 
                </div>
                <div class="mdc-snackbar__actions">
                    <button type="button" class="mdc-button mdc-snackbar__action ripple-surface" onclick="window.location.href = 'login.php?return=bonus.php'">
                        <div class="mdc-button__ripple"></div>
                        <span class="mdc-button__label" id="bonus-post-error__label">Inloggen</span>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="mdc-snackbar bonus-snackbar-after-order" id="bonus-snackbar-order-cancel-success">
            <div class="mdc-snackbar__surface">
                <div class="mdc-snackbar__label"
                     role="status"
                     aria-live="polite">
                    Je bestelling is geannuleerd.
                </div>
            </div>
        </div>


        <div class="wrapper" style="width:96%; left:2%"> 
            <div id="card" style="background-color: gainsboro">

                <div class="mdc-select sort-select">
                    <div class="mdc-select__anchor bonus-sort-select ripple-surface">
                        <i class="mdc-select__dropdown-icon"></i>
                        <div class="mdc-select__selected-text" id="select-sort-selected-text"></div>
                        <span class="mdc-floating-label">Sorteer</span>
                        <div class="mdc-line-ripple bonus-sort-select-line-ripple"></div>
                    </div>

                    <div class="mdc-select__menu mdc-menu mdc-menu-surface bonus-sort-select">
                        <ul class="mdc-list">

                            <li class="mdc-list-item ripple-surface" data-value="alphabetical">
                                Alfabetisch (A-z)
                            </li>
                            <li class="mdc-list-item ripple-surface" data-value="reverse alphabetical">
                                Omgekeerd Alfabetisch (z-A)
                            </li>
                            <li class="mdc-list-item ripple-surface" data-value="price">
                                Op prijs (oplopend)
                            </li>
                            <li class="mdc-list-item ripple-surface" data-value="reverse price">
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
