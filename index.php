<?php
session_start();
require_once 'setup.php';
?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php include 'header_material.php'?>
       <script>
            var check = false;
            (function(a){if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true; })(navigator.userAgent || navigator.vendor || window.opera);
            if (check){console.log("You're on a mobile device"); }
            else{console.log("You're on a non-mobile device"); }
        </script>
        
        
  
    </head>
    <body>
        <?php include 'mobile_banner.php' ?>
        <div class="wrapper">
            <div id="card">
                <h1 class="mdc-typography--headline1">Welkom bij Wijgaanappie.nl</h1>
                <p class="mdc-typography--body1">Wij zijn Willem en Robin, en wij heten je welkom op de site voor onze lokale bezorgservice waarbij wij jou dé spullen bezorgen die jij nodig hebt! Wij bezorgen van alles
                    om en nabij de school en direct vanaf jouw favoriete Albert Heijn op de Pottenbakkerssingel<br>Wil jij ook wat bestellen? Registreer je dan eerst en kies jouw producten
                    uit het ruime assortiment.<br><br><br> </p>
            </div>
            <div class="status">
                <h1 class="mdc-typography--headline1">Status</h1>
                <?php
                $query = DB::query('SELECT * FROM komt_chobin_naar_de_appie')[0]; //should only be one row, or someone decided to be a dick
                $komt_chobin = $query['komt hij'];
                if ($komt_chobin === '1') {
                    echo '<p "mdc-typography--body1">Vandaag bezorgen we!</p>';
                    echo '<div class="status_circle" id="green">';
                    include("assets/radio_button_checked-24px.svg");
                    echo '<svg class="outer" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/></svg>';
                    echo '</div>';
                } elseif ($komt_chobin === '0' || empty($komt_chobin)) {
                    echo '<p "mdc-typography--body1">Vandaag bezorgen we helaas niet, je kan gewoon bestellen, je bestelling blijft dan staan<p>';
                    echo '<div class="status_circle" id="red">';
                    include("assets/radio_button_checked-24px.svg");
                    echo '<svg class="outer" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/></svg>';
                    echo '</div>';
                } elseif ($komt_chobin === '2') { //special status
                    echo '<p class="mdc-typography--body1">' . $query['special_status'] . '</p>';
                    echo '<div class="status_circle" id="orange">';
                    include("assets/radio_button_checked-24px.svg");
                    echo '<svg class="outer" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/></svg>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <script>
            var glower = $('.status_circle');
            window.setInterval(function() {
            glower.toggleClass('active');
            }, 2000);
        </script>
    </body>

</html>
