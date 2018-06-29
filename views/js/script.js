/*(function($) {
//change settings
//$('.mk-alert').css("background-color", $('.mk-alert').data('color') );
//$('.mk-alert').css("color", $('.mk-alert').data('letter') );


//show animation
var animationin=$('.mk-alert').data('animationin');

var animationout=$('.mk-alert').data('animationout');
$('.mk-alert').addClass('animated '+ animationin);
animationout='bounceOut';

//close
//$('.close').addClass('animated bounce');

$('.close').click(function() {
$('.mk-alert').removeAttr('class').addClass("mk-alert");
  $('.mk-alert').addClass('animated '+ animationout);
});




})( jQuery );*/