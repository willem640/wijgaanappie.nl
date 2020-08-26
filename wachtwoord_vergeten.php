<?php
session_start();
include 'setup.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
var_dump($_POST);
if (isset($_POST['email']) && isset($_POST['username'])) {
    $user = DB::queryFirstRow('SELECT `username`,`email` FROM `users` WHERE `username` = %s0 AND `email` = %s1', $_GET['email'], $_GET['username']);
    if (isset($user)) {
        $token = bin2hex(openssl_random_pseudo_bytes(127));
        $valid_till = new DateTime();
        $valid_till->modify('+1 day');
        DB::insert('forgot_password', [
            'token' => $token,
            'username' => $user['username'],
            'email' => $user['email'],
            'valid_till' => $valid_till
        ]);
        $mail = new PHPMailer(true);
        $mail->setFrom('noreply@wijgaanappie.nl', 'no-reply');
        $mail->addAddress($user['email']);
        $mail->Subject = 'Wachtwoord vergeten';
        $mail->isHTML(false);
        $mail->Body = 'Dag ' . ($user['realname'] ?? $user['username']) . "!\n Je hebt aangegeven dat je je wachtwoord vergeten bent.\n
           wijgaanappie.nl/reset_wachtwoord.php?username=" . urlencode($user['email']) . '&token=' . $token;
        $mail->send();
        header('Location: bevestig_wachtwoord_vergeten.php?confirm=confirm');
    } else {
        // proper error handling
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
                <h1 class="mdc-typography--headline1">Wachtwoord vergeten</h1>
<!--                <h3 class="mdc-typography--headline3"></h3>-->
                <form method="post" id="forgot-password-form">
                    <label class="mdc-text-field mdc-text-field--outlined material-textfield" style="width: 100%" id="email-input">
                        <input type="text" name="email" class="mdc-text-field__input" aria-labelledby="email-input-label" required value="<?= $_POST['email'] ?? '' ?>" pattern="([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})">
                        <span class="mdc-notched-outline">
                            <span class="mdc-notched-outline__leading"></span>
                            <span class="mdc-notched-outline__notch">
                                <span class="mdc-floating-label" id="email-input-label">Email</span>
                            </span>
                            <span class="mdc-notched-outline__trailing"></span>
                        </span>
                        <div class="mdc-text-field-helper-line">
                            <div class="mdc-text-field-helper-text mdc-text-field-helper-text--validation-msg" aria-hidden="true">Voer een geldig emailadres in</div>
                        </div>
                    </label><br>
                    <label class="mdc-text-field mdc-text-field--outlined material-textfield" style="width: 100%" id="username-input">
                        <input type="text" name="username" class="mdc-text-field__input" aria-labelledby="username-input-label" required value="<?= $_POST['username'] ?? '' ?>">
                        <span class="mdc-notched-outline">
                            <span class="mdc-notched-outline__leading"></span>
                            <span class="mdc-notched-outline__notch">
                                <span class="mdc-floating-label" id="username-input-label">Gebruikersnaam</span>
                            </span>
                            <span class="mdc-notched-outline__trailing"></span>
                        </span>
                        <div class="mdc-text-field-helper-line">
                            <div class="mdc-text-field-helper-text mdc-text-field-helper-text--validation-msg" aria-hidden="true">Voer een geldig emailadres in</div>
                        </div>
                    </label><br>
                </form><br>
                <?php
                if (isset($error)) {
                    echo '<p class="mdc-typography--body1 login-error">' . $error . '</p>';
                }
                ?>
                <div class="mdc-touch-target-wrapper">
                    <button onclick="$('#forgot-password-form').submit()" class="mdc-button mdc-button--touch material-button submit-register-button">
                        <div class="mdc-button__ripple"></div>
                        <span class="mdc-button__label">aanvragen</span>
                        <div class="mdc-button__touch"></div>
                    </button>
                </div>
            </div>

        </div>

    </body>
</html>