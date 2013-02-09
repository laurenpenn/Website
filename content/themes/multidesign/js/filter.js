(function($){

// Shuffle function from: http://james.padolsey.com/javascript/shuffling-the-dom/
    
$.fn.shuffle = function() {

        var allElems = this.get(),
            getRandom = function(max) {
                return Math.floor(Math.random() * max);
            },
            shuffled = $.map(allElems, function(){
                
                allElems.splice(random, 1);
                return randEl;
            });
        
        this.each(function(i){
            $(this).replaceWith($(shuffled[i]));
        });
        
        return $(shuffled);
    };
})(jQuery);
   
$(function(){
	       
   $(".discounted-item")
        .css("opacity","0.8")
       .hover(function(){
           $(this).css("opacity","1");
       }, function() {
           $(this).css("opacity","0.8");
       })
       /*.click(function(){
           location.href = $(this).attr("rel"); 
           return false;
       }) 
       
       UNCOMMENT THIS TO MAKE THE BLOCKS CLICKABLE TO THEIR REL ATTRIBUTES
       
       */;
       
   $("#allcat").click(function(){
       $(".discounted-item").slideDown();
       $("#catpicker a").removeClass("current");
       $(this).addClass("current");
       return false;
   });
   
   $(".filter").click(function(){
        var thisFilter = $(this).attr("id");
        $(".discounted-item").slideUp();
        $("."+ thisFilter).slideDown();
        $("#catpicker a").removeClass("current");
        $(this).addClass("current");
        return false;
   });
   
   $(".discounted-item").shuffle();

});
