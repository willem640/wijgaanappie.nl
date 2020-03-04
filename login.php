<?php
session_start();
require_once 'setup.php';
$logged_in=($_SESSION['loggedin'] ?? false);
if($logged_in){
    header('Location: index.php');
}
$username = ($_COOKIE['username'] ?? '');
$token = DB::query('SELECT * FROM `cookie users` WHERE username = %s',$username);
if(isset($token[0]['token']) && $token[0]['token'] == $_COOKIE['logintoken']){
    // found session, is it valid?
    $date = new DateTime($token[0]['login time']);
    $dif = $date->diff(new DateTime);
    if($dif->days <= 30){//yay, the token is less than thirty days old
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $token[0]['username'];
        echo('<script type="text/javascript">window.location="index.php"</script>');
        exit();
    } else { // token is invalid
        DB::delete('cookie users','username = %s',$token[0]['username']);
}}
$error = '';
if(isset($_POST['username']) && isset($_POST['password'])){
    $user = DB::query('SELECT * FROM users WHERE username = %s',$_POST['username']);
    $activ = DB::query('SELECT * FROM email_activate WHERE username=%s', $_POST['username']);
    $isActiv = (isset($activ[0]['token']) ? false : true);
    if(password_verify($_POST['password'],$user[0]['password'])){
        if($isActiv){
            if($_POST['remember_me']??false){
            $token = bin2hex(openssl_random_pseudo_bytes(127));
            setcookie('logintoken', $token, time() + (86400*30), '/');
            setcookie('username',$_POST['username'], time() + (86400*30),'/');
            DB::insert('cookie users',['username' => $user[0]['username'], 'token' => $token, 'login time' => date("Y-m-d H:i:s")]);
            }
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user[0]['username'];
            if(!empty($_GET['return'])){
               echo('<script type="text/javascript">window.location="'.urldecode($_GET['return']).'"</script>');
            } else {
               echo('<script type="text/javascript">window.location="index.php"</script>');
            }
        } else {$error = 'Je email is nog niet geactiveerd, als je geen mail hebt ontvangen kan je ons <a href="contact.php">appen of een mailtje sturen</a>';}
    } else echo 'password incorrect';
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
<div class="back_stripe"></div>
        <form class="form" method="post">
            <h1>Inloggen</h1>
<label for="username">Gebruikersnaam:</label> <input type="text" id="username" name="username"><br><br><br><br><br>
            <label for="password">Wachtwoord:</label> <input type="password" id="password" name="password"><br><br><br><br><br>
            <?php echo '<center><p>'.$error.'</p></center>'; ?>
            <input type="checkbox" name="remember_me" id="remember_me">   <label for="remember_me">Onthoud mij voor 30 dagen</label><br><br>
            <input id="fancy_a" type="submit" value="Inloggen"><br><br>
            </form>
<form action="/register.php" method="get" class="form"><p><label for="register_btn">Geen account? </label><input type="submit" id="register_btn" value="Registreren"/></form>
    </body>
</html>
