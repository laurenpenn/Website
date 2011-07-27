jQuery(document).ready(function($) {
//initial the menus
$("ul.menu li").mouseover(function() {
$(this).find("ul.sub-menu").filter(':not(:animated)').animate({"height": "show", "opacity": "show"}, 10, "swing");

$(this).hover(function() {}, function(){
$(this).find("ul.sub-menu").delay(1200).animate({"height": "hide", "opacity": "hide"}, 0, "swing");
});
});
});

/*jQuery(document).ready(function($) {
//initial the menus
$("ul.menu li a").mouseover(function() {
$(this).find("ul.sub-menu").filter(':not(:animated)').animate({"height": "show", "opacity": "show"}, 0, "swing");
});

$("ul.menu li a").mouseleave( function() {
$(this).find("ul.sub-menu").delay(600).animate({"height": "hide", "opacity": "hide"}, 0, "swing");
});

});*/