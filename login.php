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
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
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
        <link rel='stylesheet' href='style_smallscreen.css' />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script
  src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
  integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
  crossorigin="anonymous"></script>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5GJ825S"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="anim">
  </div>
<div class="banner-mobile">
<div class="links">
	<div class="link">
		<a href="index.php">
			<?php include("assets/home-24px.svg");?>
			<p>Home</p>
		</a>
	</div>
	<div class="link">
		<a href="zoeken.php">
			<?php include("assets/search-24px.svg");?>
			<p>Zoek</p>
		</a>
	</div>
	<div class="link">
		<a href="bonus.php">
			<?php include("assets/euro_symbol-24px.svg");?>
			<p>Bonus</p>
		</a>
	</div>
	<div class="link">
		<a href="bestel.php">
			<?php include("assets/shopping_cart-24px.svg");?>
			<p>Winkelmandje</p>
		</a>
	</div>
	<div class="link">
		<a href="contact.php">
			<?php include("assets/contact_support-24px.svg");?>
			<p>Contact</p>
		</a>
	</div>
	<?php 
		$logged_in=(isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false);
		if($logged_in){
        echo '<div class="link">
			  <a href="profile.php">';
		include("assets/person_outline-24px.svg");
		echo '<p>Profiel</p>
		      </a>
	          </div>';
        } else {
        echo '<div class="link">
			  <a href="login.php">';
		include("assets/lock_open-24px.svg");
		echo '<p>Login</p>
		      </a>
			  </div>';
        }
	?>
	
</div>
</div>
  <div class="circle">
  <img src="assets/android-chrome-512x512.png">
  </div>

</div>
  
<script>
	var i = 0;
	$('.links').fadeOut(0);
	$('.circle').click(function(){
		i+=1;
		if(i%2!=0){
			$(".wrapper").fadeOut();
			$('.banner-mobile').animate({height: "100vh"});
			$('.circle').animate({top: '-=15vh'});
			$('.links').fadeIn();
		} else {
			$(".wrapper").fadeIn();
			$('.banner-mobile').animate({height: "50vh"});
			$('.circle').animate({top: '+=15vh'});
			$('.links').fadeOut();
		}
	});
</script>
<div class="wrapper">
	<div id="card">
		<form class="form" method="post">
		<h1>Inloggen</h1>
		<div class="text-field">
			<input type="text" id="username" name="username" required>
			<span class="highlight"></span>
			<span class="bar"></span>
			<label>Gebruikersnaam</label>
		</div>
		<div class="text-field">
			<input type="password" id="password" name="password" required>
			<span class="highlight"></span>
			<span class="bar"></span>
			<label>Wachtwoord</label>
		</div>
		<?php echo '<center style="width:100%; float:right;"><p>'.$error.'</p></center>'; ?>
		<div id="remember">
			<input type="checkbox" name="remember_me" id="remember_me">
			<label for="remember_me">Onthoud mij voor 30 dagen</label>
		</div>
		<input id="fancy_a" type="submit" value="Inloggen">
		</form>
	</div>
	<a id="register" href="/register.php">Nog niet geregistreerd? Maak een account aan!</a>	
</div>

</body>
</html>
