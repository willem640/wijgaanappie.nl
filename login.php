<?php
session_start();
require_once 'setup.php';
$logged_in = ($_SESSION['loggedin'] ?? false);
if ($logged_in) {
    header('Location: index.php');
}
$username = ($_COOKIE['username'] ?? '');
$token = DB::query('SELECT * FROM `cookie users` WHERE username = %s', $username);
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
            $(document).ready(() => {
                document
                        .querySelectorAll('.mdc-text-field__input:-webkit-autofill')
                        .forEach(el => {
                            const textField = el.parentNode;
                            const label = textField.querySelector('.mdc-floating-label');
                            if (label) {
                                label.classList.add('mdc-floating-label--float-above');
                            }
                            if (textField.MDCTextField) {
                                textField.MDCTextField.foundation_.notchOutline(true);
                            }
                        });
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
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label for="username">Gebruikersnaam</label>
                    </div>
                    <div class="text-field">
                        <input type="password" id="password" name="password" required>
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label for="password">Wachtwoord</label>
                    </div>
                    <div id="remember">
                        <input type="checkbox" name="remember_me" id="remember_me">
                        <label for="remember_me">Onthoud mij voor 30 dagen</label>
                    </div>
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
                <button onclick="url_params = new URLSearchParams(window.location.search);
                        window.location.href = 'register.php' + (url_params.has('return') ? '?return=' + url_params.get('return') : '')" class="mdc-button mdc-button--touch material-button register-button">
                    <div class="mdc-button__ripple"></div>
                    <span class="mdc-button__label">Registreren</span>
                    <div class="mdc-button__touch"></div>
                </button>
            </div>	
        </div>

    </body>
</html>
