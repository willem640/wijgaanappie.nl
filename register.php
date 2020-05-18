<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
session_start();
require('setup.php');
if ($_SESSION['loggedin'] ?? false) {
    header('Location: /');
} elseif (!empty($_POST['username'])) {
    if (!isset(DB::query('SELECT username FROM users WHERE username=%s', $_POST['username'])[0]['username'])) {
        DB::insert('users', ['username' => $_POST['username'], 'password' => password_hash($_POST['password_0'], PASSWORD_DEFAULT), 'email' => $_POST['email'], 'phone' => $_POST['phone']]);
        $token = bin2hex(openssl_random_pseudo_bytes(127));
        DB::insert('email_activate', ['username' => $_POST['username'], 'email' => $_POST['email'], 'token' => $token]);
        $mail = new PHPMailer(true);
        $mail->setFrom('noreply@wijgaanappie.nl', 'no-reply');
        $mail->addAddress($_POST['email']);
        $mail->Subject = 'Activeer je account';
        $mail->isHTML(false);
        $mail->Body = 'Dag ' . $_POST['username'] . "!\n Je account is bijna geactiveerd, klik op onderstaande link om gebruik te kunnen maken van je account.\n
           wijgaanappie.nl/activeer.php?email=" . $_POST['email'] . '&token=' . $token;
        $mail->send();
        if(isset($_GET['return'])){
            header('Location: login.php?return='.$_GET['return']);
        } else {
        header('Location: /login.php');
        }
    } else {
        $error = 'Die gebruikersnaam is al bezet';
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
                                $(event.currentTarget).closest('.mdc-text-field').children('.mdc-text-field-helper-line').children('.mdc-text-field-helper-text--validation-msg').css({opacity: 0});
                            } else {
                                $(event.currentTarget).closest('.mdc-text-field').children('.mdc-text-field-helper-line').children('.mdc-text-field-helper-text--validation-msg').css({opacity: 1});
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
                <h1 class="mdc-typography--headline1">Registreren</h1>
                <form method="post" id="register-form">
                    <label class="mdc-text-field mdc-text-field--outlined material-textfield" id="email-input">
                        <input type="text" name="email" class="mdc-text-field__input" aria-labelledby="email-input-label" required value="<?=$_POST['email'] ?? ''?>" pattern="([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})">
                        <span class="mdc-notched-outline">
                            <span class="mdc-notched-outline__leading"></span>
                            <span class="mdc-notched-outline__notch">
                                <span class="mdc-floating-label" id="email-input-label">Email</span>
                            </span>
                            <span class="mdc-notched-outline__trailing"></span>
                        </span>
                        <div class="mdc-text-field-helper-line">
                            <div class="mdc-text-field-helper-text mdc-text-field-helper-text--validation-msg" aria-hidden="true">Voer een correct emailadres in</div>
                        </div>
                    </label><br>
                    <label class="mdc-text-field mdc-text-field--outlined material-textfield" id="username-input">
                        <input type="text" name="username" class="mdc-text-field__input" aria-labelledby="username-input-label" required value="<?=$_POST['username'] ?? ''?>" pattern="[A-Za-z0-9]{1,64}">
                        <span class="mdc-notched-outline">
                            <span class="mdc-notched-outline__leading"></span>
                            <span class="mdc-notched-outline__notch">
                                <span class="mdc-floating-label" id="username-input-label">Gebruikersnaam</span>
                            </span>
                            <span class="mdc-notched-outline__trailing"></span>
                        </span>
                        <div class="mdc-text-field-helper-line">
                            <div class="mdc-text-field-helper-text mdc-text-field-helper-text--validation-msg" aria-hidden="true">Voer een gebruikersnaam in met alleen letters en cijfers</div>
                        </div>
                    </label><br>
                    <label class="mdc-text-field mdc-text-field--outlined material-textfield" id="phone-input">
                        <input type="text" name="phone" class="mdc-text-field__input" aria-labelledby="phone-input-label" required value="<?=$_POST['phone'] ?? ''?>" pattern="^[+]?[(]?[0-9]{1,4}[)]?[-\s\./0-9]*$">
                        <span class="mdc-notched-outline">
                            <span class="mdc-notched-outline__leading"></span>
                            <span class="mdc-notched-outline__notch">
                                <span class="mdc-floating-label" id="phone-input-label">Telefoonnummer</span>
                            </span>
                            <span class="mdc-notched-outline__trailing"></span>
                        </span>
                        <div class="mdc-text-field-helper-line">
                            <div class="mdc-text-field-helper-text mdc-text-field-helper-text--validation-msg" aria-hidden="true">Voer in correct telefoonnummer in</div>
                        </div>
                    </label><br>
                    <label class="mdc-text-field mdc-text-field--outlined material-textfield" id="password_0-input">
                        <input type="password" class="mdc-text-field__input" aria-labelledby="password_0-input-label" required onchange="this.setCustomValidity(this.validity.patternMismatch ? '' : '');
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
                            <div class="mdc-text-field-helper-text mdc-text-field-helper-text--validation-msg" aria-hidden="true">Voer een wachtwoord in</div>
                        </div>
                    </label><br>
                    <label class="mdc-text-field mdc-text-field--outlined material-textfield" id="password_1-input">
                        <input type="password" class="mdc-text-field__input" aria-labelledby="password_1-input-label" pattern="" required>
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
                </form>
                <?php if (isset($error)) {
                    echo '<p class="mdc-typography--body1 login-error">' . $error . '</p>';
                } ?>
                <div class="mdc-touch-target-wrapper">
                    <button onclick="$('#register-form').submit()" class="mdc-button mdc-button--touch material-button submit-register-button">
                        <div class="mdc-button__ripple"></div>
                        <span class="mdc-button__label">Registreren</span>
                        <div class="mdc-button__touch"></div>
                    </button>
                </div>
            </div>

        </div>
    </body>
</html>
