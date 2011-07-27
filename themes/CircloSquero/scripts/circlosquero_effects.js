jQuery(document).ready(function(){
     //SubMenu color change
    	   jQuery('#navigation ul li ul li').hover(function(){
	        jQuery(this).stop().animate({backgroundColor: '#ffffff'},{queue:false,duration:120});
                jQuery('a', this).stop().animate({color: '#424242'},{queue:false,duration:120});
	        }, function() {
	        jQuery(this).stop().animate({backgroundColor: '#424242'},{queue:false,duration:120});
                jQuery('a', this).stop().animate({color: '#ffffff'},{queue:false,duration:120});
	        });
    //readmore button effect
            jQuery('#rm_button, .bigbutton').hover(function(){
	        jQuery(this).stop().animate({backgroundPosition: '0px -45px'},{queue:false,duration:120});            
	        }, function() {
	        jQuery(this).stop().animate({backgroundPosition: '0px 0px'},{queue:false,duration:120});
	        }); 
            
     //post thumbnail effect
            jQuery('.postThumbWrapFW img, .postThumbWrap img, .portfolio_wrap img, .portfolio_wrap_small img').hover(function(){
	        jQuery(this).stop().fadeTo(300, 0.7);
	        }, function() {
                jQuery(this).stop().fadeTo(500, 1);
	        });
    //all tweets button effect
            jQuery('#tw_button').hover(function(){
	        jQuery(this).stop().animate({backgroundPosition: '0px -36px'},{queue:false,duration:100});            
	        }, function() {
	        jQuery(this).stop().animate({backgroundPosition: '0px 0px'},{queue:false,duration:100});
	        }); 
            
     jQuery('.slidingContentWrapCSCS').find('.slidingContentTitleCSCS').click(function () {
     jQuery(this).parent().find('.slidingContentContentCSCS').slideToggle("slow");
     });
     jQuery('.slidingContentWrapCSCS2').find('.slidingContentTitleCSCS2').click(function () {
     jQuery(this).parent().find('.slidingContentContentCSCS2').slideToggle("slow");
     });
     jQuery('.slidingContentWrapCSCS3').find('.slidingContentTitleCSCS3').click(function () {
     jQuery(this).parent().find('.slidingContentContentCSCS3').slideToggle("slow");
     });

});