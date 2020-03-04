<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
session_start();
require('setup.php');
if($_SESSION['loggedin'] ?? false){
    header('Location: /');
} elseif(!empty($_POST['username'])){
    if(!isset(DB::query('SELECT username FROM users WHERE username=%s',$_POST['username'])[0]['username'])){
    DB::insert('users',['username' => $_POST['username'], 'password' => password_hash($_POST['password_0'], PASSWORD_DEFAULT), 'email'=>$_POST['email'], 'phone'=>$_POST['phone']]);
    $token = bin2hex(openssl_random_pseudo_bytes(127));
    DB::insert('email_activate',['username' => $_POST['username'],'email'=>$_POST['email'],'token'=>$token]);
    $mail = new PHPMailer(true);
    $mail->setFrom('noreply@wijgaanappie.nl', 'no-reply');
    $mail->addAddress($_POST['email']);
    $mail->Subject='Activeer je account';
    $mail->isHTML(false);
    $mail->Body='Dag ' . $_POST['username'] . "!\n Je account is bijna geactiveerd, klik op onderstaande link om gebruik te kunnen maken van je account.\n
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
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-153875032-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-153875032-1');
</script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5GJ825S');</script>
<!-- End Google Tag Manager -->
        <link rel='stylesheet' media='only screen and (max-width: 1080px)' href='style_smallscreen.css' />
        <link type="text/css" rel="stylesheet" media="only screen and (min-width: 1080px)" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5GJ825S"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
        <div class="banner">
	<a class="left active" id="home" href="index.php">Robins AH Bestelservice</a>
        <a class="right" id="banner_a" href="zoek.php">Zoek</a>
	<a class="right" id="banner_a" href="bonus.php">Bonus</a>
	<a class="right" id="banner_a" href="bestelling.php">Bestel</a>
	<a class="right" id="banner_a" href="contact.php">Contact</a>
        <?php 
		$logged_in=(isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false);
		if($logged_in){
        echo '<a class="right" id="banner_a" href="profile.php">Profiel</a>';
        } else {
        echo '<a class="right" id="banner_a" href="login.php">Inloggen</a>';
        } ?>
</div>
<div class="banner-mobile">
  <div class="dropdown">
    <button class="dropbtn"> Robins AH Bestelservice
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
	<ul>
      <li><a id="mobile_banner_a" href="index.php">Home</a>
      <li><a id="mobile_banner_a" href="zoek.php">Zoek</a>
      <li><a id="mobile_banner_a" href="bonus.php">Bonus</a>
      <li><a id="mobile_banner_a" href="bestelling.php">Bestel</a>
      <li><a id="mobile_banner_a" href="contact.php">Contact</a>
      <?php if($logged_in){
        echo '<li><a id="mobile_banner_a" href="profile.php">Profiel</a>';
        } else {
        echo '<li><a id="mobile_banner_a" href="login.php">Inloggen</a>';
        } ?>
	</ul>
    </div>
  </div>
</div>
<script>
	$('.dropbtn').on('touchstart', function (event) {
    $(".dropdown-content").slideToggle(200, "swing");
	});
</script>
    </head>
    <body>
    <center><h1>Registreren</h1></center>
        
        <form method="post" class='form'>
            <?php
            echo  '<label>Email</label> <input type="email" name="email" value="'.($_POST['email']??'').'" required><br><br><br>'
                . '<label>Gebruikersnaam</label> <input type="text" name="username" value="'.($_POST['username']??'').'" required pattern="[A-Za-z0-9]{1,64}" title="Gebruikersnaam mag alleen letters en cijfers bevatten"><br><br><br>'
                . '<label>Telefoonnummer</label> <input type="tel" name="phone" value="'.($_POST['phone']??'').'" required pattern="^[+]?[(]?[0-9]{1,4}[)]?[-\s\./0-9]*$" title="Vul een correct telefoonnummer in"><br><br><br>';
            ?>
            <label>Wachtwoord</label><input id="password_0" name="password_0" type="password" onchange="this.setCustomValidity(this.validity.patternMismatch ? '' : ''); if(this.checkValidity()) form.password_1.pattern = this.value;" required><br><br><br>

            <label>Wachtwoord bevestigen</label><input id="password_1" name="password_1" type="password" pattern="" title="Wachtwoorden moeten hetzelfde zijn" required><br><br><br>
            <input type="submit" value="Registreren">
        </form>
        <?php echo '<br><br><br><center>'.($_POST['error_msg']??'').'</center>';?>
    </body>
</html>
