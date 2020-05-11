<?php
session_start();
require_once 'header_material.php';
require_once 'setup.php';
require_once 'simple_html_dom.php';
?>


<!DOCTYPE html>
<html>
    <head>
        <?= $header ?>
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
            var search_input;
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
                search_input = new mdc.textField.MDCTextField($('.search-box')[0]);
                $('#search-input-form').bind('submit', doSearch);

                const url_parameters = new URLSearchParams(window.location.search);
                if (url_parameters.has('sort')) {

                    var current_sort = url_parameters.get('sort');

                    var current_sort_index = $('div.bonus-sort-select ul li[data-value="' + current_sort + '"]')
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
                    var url_params = new URLSearchParams(window.location.search);
                    var q;
                    if (url_params.has('q')) {
                        q = url_params.get('q');
                    } else {
                        q = '';
                    }
                    document.location.href = window.location.pathname + '?sort=' + select.value + '&q=' + q;
                });
            });

            function buyProductDialog(title, price_was, price_now, unit_size, discount, index, description = '') {
                current_product_index = index;
                $('#bonus-product-dialog-title')[0].innerHTML = title;
                var $content = '';
                if (price_was !== '') {
                    $content = $content.concat(`Van: €${$price_was}<br>`);
                }
                if (price_now !== '' && price_was === '') {
                    $content = $content.concat(`Prijs: €${price_now}<br>`);
                } else if (price_now !== '') {
                    $content = $content.concat(`Voor: €${price_now}<br>`);
                }
                if (unit_size !== '') {
                    $content = $content.concat(`${unit_size}<br>`);
                }
                if (description !== '') {
                    $content = $content.concat(`${description}<br>`);
                }
                $('#bonus-product-dialog-content')[0].innerHTML = $content;
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
                if (xhr.status === 403) {
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

            function undoOrder() {
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

            function doSearch(event) {
                event.preventDefault();
                console.log(event);
            }

        </script>

    </head>
    <body>
        <?php
        // put your code here
        ?>
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

        <div class="wrapper jscroll">
            <div class="mdc-card search-result-card search-top-card">
                <label class="mdc-text-field mdc-text-field--outlined search-box">
                    <form id="search-input-form">
                        <input type="text" onblur="this.form.submit();" class="mdc-text-field__input" aria-labelledby="search-input">
                    </form>
                    <span class="mdc-notched-outline">
                        <span class="mdc-notched-outline__leading"></span>
                        <span class="mdc-notched-outline__notch">
                            <span class="mdc-floating-label" id="search-input">Zoek</span>
                        </span>
                        <span class="mdc-notched-outline__trailing"></span>
                    </span>
                </label>
                <div class="mdc-select sort-select">
                    <div class="mdc-select__anchor bonus-sort-select ripple-surface">
                        <i class="mdc-select__dropdown-icon"></i>
                        <div class="mdc-select__selected-text" id="select-sort-selected-text"></div>
                        <span class="mdc-floating-label">Sorteer</span>
                        <div class="mdc-line-ripple bonus-sort-select-line-ripple"></div>
                    </div>

                    <div class="mdc-select__menu mdc-menu mdc-menu-surface bonus-sort-select">
                        <ul class="mdc-list">

                            <li class="mdc-list-item ripple-surface" data-value="relevance">
                                Op relevantie
                            </li>
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
            </div>
            <!--
                only applies to div.loader and children
            
                Copyright (c) <Year Unknown> - Andy/@agtoaaa - https://codepen.io/agtoaaa/pen/brNLVx

                Permission is hereby granted, free of charge, to any person 
                obtaining a copy of this software and associated documentation 
                files (the "Software"), to deal in the Software without restriction,
                including without limitation the rights to use, copy, modify, 
                merge, publish, distribute, sublicense, and/or sell copies of 
                the Software, and to permit persons to whom the Software is 
                furnished to do so, subject to the following conditions:

                The above copyright notice and this permission notice shall 
                be included in all copies or substantial portions of the Software.

                THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
                EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES 
                OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
                NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
                HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
                WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
                OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
                DEALINGS IN THE SOFTWARE.

            -->
            <div class="loader">
                <svg class="circular">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10"></circle>
                </svg>
            </div>
            <?php
            $search = $_GET['q'] ?? '';
            $sort = $_GET['sort'] ?? '';
            if (strpos($sort, 'reverse') !== false) {
                $sort_direction = 'DESC';
            } else {
                $sort_direction = 'ASC';
            }
            if (strpos(($_GET['sort'] ?? ''), 'price') !== false) {
                $query = DB::query("SELECT * FROM `products` WHERE MATCH(title) AGAINST(%s0) ORDER BY priceNow " . $sort_direction, $search);
            } else if (strpos(($_GET['sort'] ?? ''), 'alphabetical') !== false) {
                $query = DB::query("SELECT * FROM `products` WHERE MATCH(title) AGAINST(%s0) ORDER BY title " . $sort_direction, $search);
            } else { //not price, not alphabetical, so sort by relevance
                $query = DB::query("SELECT * FROM `products` WHERE MATCH(title) AGAINST(%s0) ORDER BY MATCH(title) AGAINST(%s0) " . $sort_direction, $search);
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
            ?>


        </div>
    </body>
</html>
