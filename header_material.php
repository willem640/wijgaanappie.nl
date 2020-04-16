<?php
$header = "
<!-- material.io stylesheet and js library -->
<link href=\"https://unpkg.com/material-components-web@v4.0.0/dist/material-components-web.min.css\" rel=\"stylesheet\">
<script src=\"https://unpkg.com/material-components-web@v4.0.0/dist/material-components-web.min.js\"></script>    

<!-- google material icons -->
<link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/icon?family=Material+Icons\">

<!-- main stylesheet with material.io class definitions, compiled Sass from assets/scss -->
<link rel=\"stylesheet\" type=\"text/css\" href=\"assets/css/App.css\">

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=UA-153875032-1\"></script>
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
    <meta charset=\"utf-8\">
    <meta name=\"google-site-verification\" content=\"uvDiVjrbFuwiF-ME9NPjbmsZsRXQGzNzGdJUElgM7DQ\" />
		<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js\"></script>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=GTM-5GJ825S\"
height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <div class=\"banner\">
	<a class=\"left active\" id=\"home\" href=\"index.php\">Robins AH Bestelservice</a>
        <a class=\"right\" id=\"banner_a\" href=\"zoeken.php\">Zoek</a>
	<a class=\"right\" id=\"banner_a\" href=\"bonus.php\">Bonus</a>
	<a class=\"right\" id=\"banner_a\" href=\"bestelling.php\">Bestel</a>
	<a class=\"right\" id=\"banner_a\" href=\"contact.php\">Contact</a>";
$logged_in=(isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false);
if($logged_in){
    $header.='<a class="right" id="banner_a" href="profile.php">Profiel</a>';
} else {
    $header.='<a class="right" id="banner_a" href="login.php">Inloggen</a>';
}
 $header.="
</div>
<div class=\"banner-mobile\">
  <div class=\"dropdown\">
    <button class=\"dropbtn\"> Robins AH Bestelservice
      <i class=\"fa fa-caret-down\"></i>
    </button>
    <div class=\"dropdown-content\">
	<ul>
      <li><a id=\"mobile_banner_a\" href=\"index.php\">Home</a>
      <li><a id=\"mobile_banner_a\" href=\"zoeken.php\">Zoek</a>
      <li><a id=\"mobile_banner_a\" href=\"bonus.php\">Bonus</a>
      <li><a id=\"mobile_banner_a\" href=\"bestelling.php\">Bestel</a>
      <li><a id=\"mobile_banner_a\" href=\"contact.php\">Contact</a>";
if($logged_in){
    $header.='<li><a id="mobile_banner_a" href="profile.php">Profiel</a>';
} else {
    $header.='<li><a id="mobile_banner_a" href="login.php">Inloggen</a>';
}
$header.="
	</ul>
    </div>
  </div>
</div>
<script>
	$('.dropbtn').on('touchstart', function (event) {
    $(\".dropdown-content\").slideToggle(200, \"swing\");
	});
</script>
";
