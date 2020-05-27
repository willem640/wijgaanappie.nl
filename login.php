<?php
session_start();
require_once 'setup.php';
$logged_in = ($_SESSION['loggedin'] ?? false);
if ($logged_in) {
    header('Location: index.php');
}
$username = ($_COOKIE['username'] ?? '');
$token = DB::query('SELECT * FROM `cookie users` WHERE username = %s', $username); // NIET COMMITTEN
if (isset($token[0]['token']) && $token[0]['token'] == $_COOKIE['logintoken']) {
    // found session, is it valid?
    $date = new DateTime($token[0]['login time']);
    $dif = $date->diff(new DateTime);
    if ($dif->days <= 30) {//yay, the token is less than thirty days old
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $token[0]['username'];
        echo('<script type="text/javascript">window.location="index.php"</script>');
        exit();
    } else { // token is invalid
        DB::delete('cookie users', 'username = %s', $token[0]['username']);
    }
}
$error = '';
if (isset($_POST['username']) && isset($_POST['password'])) {
    $user = DB::query('SELECT * FROM users WHERE username = %s', $_POST['username']);
    $activ = DB::query('SELECT * FROM email_activate WHERE username=%s', $_POST['username']);
    $isActiv = (isset($activ[0]['token']) ? false : true);
    if (password_verify($_POST['password'], $user[0]['password'])) {
        if ($isActiv) {
            if ($_POST['remember_me'] ?? false) {
                $token = bin2hex(openssl_random_pseudo_bytes(127));
                setcookie('logintoken', $token, time() + (86400 * 30), '/');
                setcookie('username', $_POST['username'], time() + (86400 * 30), '/');
                DB::insert('cookie users', ['username' => $user[0]['username'], 'token' => $token, 'login time' => date("Y-m-d H:i:s")]);
            }
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user[0]['username'];
            if (!empty($_GET['return'])) {
                echo('<script type="text/javascript">window.location="' . urldecode($_GET['return']) . '"</script>');
            } else {
                echo('<script type="text/javascript">window.location="index.php"</script>');
            }
        } else {
            $error = 'Je email is nog niet geactiveerd, als je geen mail hebt ontvangen kan je ons <a href="contact.php">appen of een mailtje sturen</a>';
        }
    } else {
        $error = 'Je wachtwoord klopt niet, probeer het nog een keer';
    }
}
?>
<html>
    <head>
        <?php include 'header_material.php' ?>
        <script type="text/javascript">
            var checkbox;
            var formField;
            $(document).ready(() => {
                checkbox = new mdc.checkbox.MDCCheckbox(document.querySelector('.mdc-checkbox'));
                formField = new mdc.formField.MDCFormField(document.querySelector('.mdc-form-field'));
                formField.input = checkbox;
            });



        </script>
    </head>
    <body>
        <?php include 'mobile_banner.php' ?>

        <div class="wrapper">
            <div id="card">
                <h1 class="mdc-typography--headline1">Inloggen</h1>
                <form class="form" method="post">

                    <div class="text-field">
                        <input type="text" id="username" name="username" required>
                        <span class="bar"></span>
                        <label for="username">Gebruikersnaam</label>
                    </div>
                    <div class="text-field">
                        <input type="password" id="password" name="password" required>
                        <span class="bar"></span>
                        <label for="password">Wachtwoord</label>
                    </div>
                    <!--<div id="remember">-->
                        <div class="mdc-form-field material-form-field">
                            <div class="mdc-touch-target-wrapper">
                                <div class="mdc-checkbox mdc-checkbox--touch material-checkbox">
                                    <input type="checkbox"
                                           class="mdc-checkbox__native-control"
                                           id="checkbox-1"
                                           name="remember_me">
                                    <div class="mdc-checkbox__background">
                                        <svg class="mdc-checkbox__checkmark"
                                             viewBox="0 0 24 24">
                                        <path class="mdc-checkbox__checkmark-path"
                                              fill="none"
                                              d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
                                        </svg>
                                        <div class="mdc-checkbox__mixedmark"></div>
                                    </div>
                                    <div class="mdc-checkbox__ripple"></div>
                                </div>
                            </div>
                            <label for="remember_me">Onthoud mij voor 30 dagen</label>
                        </div>
                    <!--</div>-->
                    <?php
                    if (isset($error)) {
                        echo '<p class="mdc-typography--body1 login-error">' . $error . '</p>';
                    }
                    ?>


                    <div class="mdc-touch-target-wrapper">
                        <button onclick="if ($(this).closest('form').valid) {
                                    $(this).closest('form').submit();
                                }" class="mdc-button mdc-button--touch material-button login-button">
                            <div class="mdc-button__ripple"></div>
                            <span class="mdc-button__label">Inloggen</span>
                            <div class="mdc-button__touch"></div>
                        </button>
                    </div>
                </form>
            </div>
            <div class="mdc-touch-target-wrapper">
                <button onclick="url_params = new URLSearchParams(window.location.search);window.location.href = 'register.php' + (url_params.has('return') ? '?return=' + url_params.get('return') : '')" class="mdc-button mdc-button--touch material-button register-button">
                    <div class="mdc-button__ripple"></div>
                    <span class="mdc-button__label">Registreren</span>
                    <div class="mdc-button__touch"></div>
                </button>
            </div>	
        </div>

    </body>
</html>
