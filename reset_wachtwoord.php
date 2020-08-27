<?php
session_start();
include 'setup.php';

if(isset($_GET['username']) && isset($_GET['token'])){
    $token = DB::QueryFirstRow('SELECT token FROM forgot_password WHERE email=%s', $_GET['username'])['token'];
    $timestamp = strtotime(DB::QueryFirstRow('SELECT valid_till FROM forgot_password WHERE email=%s', $_GET['username'])['valid_till']);
    $now = new DateTime();
    $valid = ($timestamp > $now);
    if($now < $timestamp && $_GET['token'] == $token){
        //TODO: Cleanup
    } else {
        header('Location: /wachtwoord_vergeten.php?error=1');
    }
    
}

if(isset($_POST['pass0']) && isset($_POST['pass1'])){
    if($_POST['pass0'] != $_POST['pass1']){
        $error = "Wachtwoorden matchen niet";
    } else {
        DB::update('users', ['password' => password_hash($_POST['pass0'], PASSWORD_DEFAULT)], 'email=%s', $_POST['username']);
        DB::delete('forgot_password', 'email=%s', $_POST['username']);
        header('Location: /login.php');
    }
}
?>

<html>
    <head>
        <?php include 'header_material.php' ?>
        <script type="text/javascript">
var textfield_objects = [];
            $(document).ready(() => {
                var textfields = $('.mdc-text-field');

                for (var i = 0; i < textfields.length; ++i) {
                    var tf = new mdc.textField.MDCTextField(textfields[i]);
                    textfield_objects.push(tf);
                }
                $('.mdc-text-field > input')
                        .on('input', function (event) {
                            var inputs = $(event.currentTarget.form).children('label').children('input');
                            var index = inputs.index($(event.currentTarget));
                            if (textfield_objects[index].valid) {
                                $(event.currentTarget).closest('.mdc-text-field').children('.mdc-text-field-helper-line').children('.mdc-text-field-helper-text--validation-msg').css({display: "none"});
                            } else {
                                $(event.currentTarget).closest('.mdc-text-field').children('.mdc-text-field-helper-line').children('.mdc-text-field-helper-text--validation-msg').css("display","").css({opacity:1});
                            }

                        });
                var helpertexts = $('.mdc-text-field-helper-text');

                for (var i = 0; i < helpertexts.length; ++i) {
                    var ht = new mdc.textField.MDCTextFieldHelperText(helpertexts[i]);

                }
            });
        </script>
    </head>
    <body>
        <?php include 'mobile_banner.php' ?>
        <div class="wrapper">
            <div id="card">
                <h1 class="mdc-typography--headline1">Wachtwoord reset</h1>
<!--                <h3 class="mdc-typography--headline3"></h3>-->
                    <form method="post" id="password-reset-form" action="reset_wachtwoord.php">
                        <input type="hidden" name="username" value="<?php echo($_GET['username'])?>">
                        <input type="hidden" name="token" value="<?php echo($_GET['token'])?>">
                    <label class="mdc-text-field mdc-text-field--outlined material-textfield" id="password_0-input">
                        <input name="pass0" type="password" class="mdc-text-field__input" aria-labelledby="password_0-input-label" required onchange="this.setCustomValidity(this.validity.patternMismatch ? '' : '');
                                if (this.checkValidity())
                                    $('#password_1-input > input')[0].pattern = this.value;">
                        <span class="mdc-notched-outline">
                            <span class="mdc-notched-outline__leading"></span>
                            <span class="mdc-notched-outline__notch">
                                <span class="mdc-floating-label" id="password_0-input-label">Wachtwoord</span>
                            </span>
                            <span class="mdc-notched-outline__trailing"></span>
                        </span>
                        <div class="mdc-text-field-helper-line">
                            <div class="mdc-text-field-helper-text mdc-text-field-helper-text--validation-msg" aria-hidden="true">Voer een  nieuwwachtwoord in</div>
                        </div>
                    </label><br>
                    <label class="mdc-text-field mdc-text-field--outlined material-textfield" id="password_1-input">
                        <input name="pass1" type="password" class="mdc-text-field__input" aria-labelledby="password_1-input-label" pattern="" required>
                        <span class="mdc-notched-outline">
                            <span class="mdc-notched-outline__leading"></span>
                            <span class="mdc-notched-outline__notch">
                                <span class="mdc-floating-label" id="password_1-input-label">Bevestig je wachtwoord</span>
                            </span>
                            <span class="mdc-notched-outline__trailing"></span>
                        </span>
                        <div class="mdc-text-field-helper-line">
                            <div class="mdc-text-field-helper-text mdc-text-field-helper-text--validation-msg" aria-hidden="true">Zorg dat de wachtwoorden matchen</div>
                        </div>
                    </label><br>
                </form><br>
                <?php
                if (isset($error)) {
                    echo '<p class="mdc-typography--body1 login-error">' . $error . '</p>';
                }
                ?>
                <div class="mdc-touch-target-wrapper">
                    <button onclick="function(){
                            if($('#password_1-input > input').is(':valid')){
                                $('#password-reset-form').submit();
                            }
                        }" class="mdc-button mdc-button--touch material-button submit-register-button">
                        <div class="mdc-button__ripple"></div>
                        <span class="mdc-button__label">reset</span>
                        <div class="mdc-button__touch"></div>
                    </button>
                </div>
            </div>

        </div>

    </body>
</html>
