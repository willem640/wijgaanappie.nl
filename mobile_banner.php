<?php
session_start();
?>

<script type="text/javascript">
    $(document).ready(() => {
        var buttons = $('.mdc-button');
        for (var i = 0; i < buttons.length; ++i) {
            mdc.ripple.MDCRipple.attachTo(buttons[i]);
        }
        
        $('#circle').click(() => {
 
            if (!$('#circle').hasClass('open')) {
                $('body > *').not('.top-mobile-banner').not('.circle').not('script').velocity("fadeOut");
                $('.top-mobile-banner').velocity({height: "100vh"});
                $('.circle').velocity({top: '-=15vh'});
                $('.banner-links').velocity("fadeIn");
                $('#circle').addClass('open');
            } else {
                $('body > *').not('.top-mobile-banner').not('.circle').not('script').velocity("fadeIn");
                setTimeout(() => {$('body > *').not('.top-mobile-banner').not('.circle').not('script').css("display", "");}, 450);
                $('.top-mobile-banner').velocity({height: "50vh"});
                $('.circle').velocity({top: '+=15vh'});
                $('.banner-links').velocity("fadeOut");
                $('#circle').removeClass('open');
            }
        });
    });
</script>
<div class="top-mobile-banner">
    <div class="banner-links">
        <div class ="banner-links-left banner-links-element">
            <button class="mdc-button banner-button mdc-button--raised" onclick="document.location.href = 'index.php'">
                <div class="mdc-button__ripple"></div>
                <span class="mdc-button__label">
                    <i class="material-icons-round" aria-hidden="true">home</i>
                    <br>
                    Home
                </span>
            </button>
            <button class="mdc-button banner-button mdc-button--raised" onclick="document.location.href = 'bonus.php'">
                <div class="mdc-button__ripple"></div>
                <span class="mdc-button__label">
                    <i class="material-icons-round" aria-hidden="true">euro</i>
                    <br>
                    Bonus
                </span>
            </button>
            <button class="mdc-button banner-button mdc-button--raised" onclick="document.location.href = 'contact.php'">
                <div class="mdc-button__ripple"></div>
                <span class="mdc-button__label">
                    <i class="material-icons-round" aria-hidden="true">contact_support</i>
                    <br>
                    Contact
                </span>
            </button>
        </div>
        <div class ="banner-links-right banner-links-element">
            <button class="mdc-button banner-button mdc-button--raised" onclick="document.location.href = 'zoeken.php'">
                <div class="mdc-button__ripple"></div>
                <span class="mdc-button__label">
                    <i class="material-icons-round" aria-hidden="true">search</i>
                    <br>
                    Zoek
                </span>
            </button>
            <button class="mdc-button banner-button mdc-button--raised" onclick="document.location.href = 'bestelling.php'">
                <div class="mdc-button__ripple"></div>
                <span class="mdc-button__label">
                    <i class="material-icons-round" aria-hidden="true">shopping_cart</i>
                    <br>
                    Winkelmandje
                </span>
            </button>
            <button class="mdc-button banner-button mdc-button--raised" onclick="document.location.href = '<?= ($_SESSION['loggedin'] ? 'profile.php' : 'login.php') ?>'">
                <div class="mdc-button__ripple"></div>
                <span class="mdc-button__label">
                    <i class="material-icons-round" aria-hidden="true"><?= ($_SESSION['loggedin'] ? 'account_circle' : 'lock_open') ?></i>
                    <br>
                    <?= ($_SESSION['loggedin'] ? 'Profiel' : 'Inloggen') ?>
                </span>
            </button>
        </div>
    </div>       
</div>
<div class="circle" id="circle">
    <img src="assets/android-chrome-512x512.png">
</div>