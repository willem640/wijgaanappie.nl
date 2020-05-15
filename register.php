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
        header('Location: /login.php');
    } else {
        $_POST['error_msg'] = 'Gebruikersnaam is al bezet';
    }
}
?>
<html>
    <head>
<?php include 'header_material.php' ?>
    </head>
    <body>
        <?php include 'mobile_banner.php' ?>
        <div class="wrapper">
            <center><h1>Registreren</h1></center>

            <form method="post" class='form'>
<?php
echo '<label>Email</label> <input type="email" name="email" value="' . ($_POST['email'] ?? '') . '" required><br><br><br>'
 . '<label>Gebruikersnaam</label> <input type="text" name="username" value="' . ($_POST['username'] ?? '') . '" required pattern="[A-Za-z0-9]{1,64}" title="Gebruikersnaam mag alleen letters en cijfers bevatten"><br><br><br>'
 . '<label>Telefoonnummer</label> <input type="tel" name="phone" value="' . ($_POST['phone'] ?? '') . '" required pattern="^[+]?[(]?[0-9]{1,4}[)]?[-\s\./0-9]*$" title="Vul een correct telefoonnummer in"><br><br><br>';
?>
                <label>Wachtwoord</label><input id="password_0" name="password_0" type="password" onchange="this.setCustomValidity(this.validity.patternMismatch ? '' : ''); if (this.checkValidity())
                            form.password_1.pattern = this.value;" required><br><br><br>

                <label>Wachtwoord bevestigen</label><input id="password_1" name="password_1" type="password" pattern="" title="Wachtwoorden moeten hetzelfde zijn" required><br><br><br>
                <input type="submit" value="Registreren">
            </form>
<?php echo '<br><br><br><center>' . ($_POST['error_msg'] ?? '') . '</center>'; ?>
        </div></body>
</html>
