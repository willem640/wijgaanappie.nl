<?php session_start(); include 'setup.php' ;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
var_dump($_GET);
if(isset($_GET['email']) && isset($_GET['username'])){
    $user = DB::queryFirstRow('SELECT `username`,`email` FROM `users` WHERE `username` = %s0 AND `email` = %s1', $_GET['email'], $_GET['username']);
    $user = ['username' => 'test_die_niet_bestaat', 'email' => 'test-twkwk760z@srv1.mail-tester.com'];
    if(isset($user)){
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
           wijgaanappie.nl/wachtwoord_vergeten.php?username=" . urlencode($user['email']) . '&token=' . $token;
        $mail->send();
        header('Location: wachtwoord_vergeten.php?confirm=confirm');

    } else {
        // proper error handling
    }
}
?>

<html>
    <head>
        <?php include 'header_material.php' ?>
        <script type="text/javascript">
        $(document).ready(() => {

        });
        </script>
    </head>
    <body>
        <?php include 'mobile_banner.php' ?>
        <form>
            <input name="email">
            <input name="username">
        </form>

    </body>
</html>