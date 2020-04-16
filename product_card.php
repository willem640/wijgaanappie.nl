<!DOCTYPE HTML>
<html>
<head>
	<link rel='stylesheet' href='style_smallscreen.css' />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
</head>
<body>
<div class="product-card">
	<img src="assets/placeholder-card.jpg">
	<div class="card-content">
	<h3 id="title">Title</h3>
	<h4 id="price">Prijs: </h4>
	<h4 id="amount">Stuks </h4>
	<div class="buttons">
	<button id="remove" style="float:left">Verwijder</button>
	<button id="up" style="float:right">+</button>
	<button id="down" style="float:right">-</button>
	</div>
	</div>
</div>
<script>
	$(".buttons button").click(function(e){
	
	$(".ripple").remove();

  // Setup
  var posX = $(this).offset().left,
      posY = $(this).offset().top,
      buttonWidth = $(this).width(),
      buttonHeight = $(this).height();

  // Add the element
  $(this).prepend("<span class='ripple'></span>");

  // Make it round!
  if (buttonWidth >= buttonHeight) {
    buttonHeight = buttonWidth;
  } else {
    buttonWidth = buttonHeight;
  }

  // Get the center of the element
  var x = e.pageX - posX - buttonWidth / 2;
  var y = e.pageY - posY - buttonHeight / 2;

  // Add the ripples CSS and start the animation
  $(".ripple").css({
    width: buttonWidth,
    height: buttonHeight,
    top: y + 'px',
    left: x + 'px'
  }).addClass("rippleEffect");
	});
</script>
</body>
</html>