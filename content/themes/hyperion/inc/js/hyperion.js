// Superfish Menu
$(document).ready(function() { 
	$('ul.hp-menu').superfish({
     animation:   {opacity:'show',height:'show'}
  })
}); 

// Switch view option portfolio
$(document).ready(function(){

    $("a.switch_thumb").toggle(function(){
        $(this).addClass("swap");
        $("ul.display").fadeOut("fast", function() {
            $(this).fadeIn("fast").addClass("thumb_view");
        });
    }, function () {
        $(this).removeClass("swap");
        $("ul.display").fadeOut("fast", function() {
            $(this).fadeIn("fast").removeClass("thumb_view");
        });
    });

});

// Hover fade rollover for images and video
$(document).ready(function(){
$(".portfolio-item a.hover-zoom-video, .hover-zoom, .hover-zoom-video-home, #video a.hover-zoom-video-single, .gallery1 a.hover-zoom-video").append("<span></span>"); 
    $(".portfolio-item a.hover-zoom-video, .hover-zoom, .hover-zoom-video-home, #video a.hover-zoom-video-single, .gallery1 a.hover-zoom-video").hover(function(){
	 $(this).children("span").hide();										   
        $(this).children("span").stop().fadeTo(800, 0.45);
    },function(){
        $(this).children("span").stop().fadeTo(400, 0);
    });
});

// Hover fade for buttons
$(document).ready(function() {
		$('.intouch-button, .more-link').append('<span class="buttonhover"></span>').each(function () {
	  		var $span = $('> span.buttonhover', this).css('opacity', 0);
	  		$(this).hover(function () {
	    		$span.stop().fadeTo(500, 1);
	 		}, function () {
	   	$span.stop().fadeTo(500, 0);
	  		});
		});
	})
