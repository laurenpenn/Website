

Cufon.replace('.site_menu .current_page_item', {  color: '#fff', hover: { color: '#fff'}});
Cufon.replace('.menu_container li a', {  color: '#555', hover: { color: '#fff'}});
Cufon.replace('.site_menu .current_page_item', {  color: '#fff'});
Cufon.replace('.site_menu ul .current_page_item', {  color: '#555', hover: { color: '#fff'}});
Cufon.replace('.headlines_menu li a', {  color: '#fff', hover: { color: '#555'}});
Cufon.replace('.ah, .some_title, h1, h2, h3, h4, h5, .title, .widgets_title, .recent_inside_title, .simple_page_title, .custom_title');

Cufon.replace('.page_categories ul li.current_page_item', {  color: '#23bff6', hover: { color: '#23bff6'}});
Cufon.replace('.page_categories ul li', {  color: '#555', hover: { color: '#23bff6'}});
Cufon.replace('.page_categories ul li.current_page_item', {  color: '#23bff6'});


Cufon.replace('.page_navigation_container ul li.current_page_item', {  color: '#23bff6', hover: { color: '#23bff6'}});
Cufon.replace('.page_navigation_container ul li', {  color: '#555', hover: { color: '#23bff6'}});
Cufon.replace('.page_navigation_container ul li.current_page_item', {  color: '#23bff6'});


	
$(document).ready(function(){

	$("a[rel^='prettyPhoto']").prettyPhoto({theme:'dark_square'});

	$('#success').hide();
	$('#error').hide();
	
	$('#myform').FormValidate({
		phpFile:"mail.php",
		ajax:true
	});
	
	
		$('.portfolio_box_anime').hover(function(){
		$(".portfolio_zoom", this).fadeIn('500');
	
	}, function() {
		$(".portfolio_zoom", this).fadeOut('500');
	});
	
	
		$(".activefocus").focus(function () {
			if ($(this).attr("value") == $(this).attr("defaultValue")) {
					$(this).attr("value", '');
			}

	});

	$(".activefocus").blur(function () {
			if ($(this).attr("value") == '') {
					$(this).attr("value", $(this).attr("defaultValue"));
			}

	});
	
	
	
});


jQuery.iFormValidate = {
	build : function(options)
	{
		var defaults = {
			phpFile:"mail.php",
			ajax: true
		};
		var options = $.extend(defaults, options); 
		return $(this).each(
			function() {
			$inputs = $(this).find(":input").filter(":not(:submit)");
			$(this).submit(function(){
				var isValid = jQuery.iFormValidate.validateForm($inputs);
				if(!isValid){
					$('#error').fadeIn("slow");
					$('#success').fadeOut("slow");
					return false;
				};
				if(options.ajax){
					var data = {};
					$inputs.each(function(){
						data[this.name] = this.value
					});
					$inputs.each(function(){
						data[this.name] = this.value
						
					});
					
						$('#error').fadeOut("slow");
						$('#success').load(options.phpFile, data, function(){
						$('#success').fadeIn("slow");
						
						
						$(':input','#myform')
						 .not(':button, :submit, :reset, :hidden')
						 .val('')
						 .removeAttr('checked')
						 .removeAttr('selected');

					});
					return false;
				}else{
					return true;
				}
			});
			
			$inputs.bind("keyup", jQuery.iFormValidate.validate);
			$inputs.filter("select").bind("change", jQuery.iFormValidate.validate);
		});
	},
	validateForm : function($inputs)
	{
		var isValid = true; //benifit of the doubt?
		$inputs.filter(".is_required").each(jQuery.iFormValidate.validate);
		if($inputs.filter(".is_required").hasClass("invalid")){isValid=false;}
		return isValid;
	},
		
	validate : function(){
		var $val = $(this).val();
		var isValid = true;
		

		
		if($(this).hasClass('vdate')){
			var Regex = /^([\d]|1[0,1,2]|0[1-9])(\-|\/|\.)([0-9]|[0,1,2][0-9]|3[0,1])(\-|\/|\.)\d{4}$/;
			isValid = Regex.test($val);
		}else if($(this).hasClass('vemail')){
			var Regex =/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if(!Regex.test($val)){isValid = false;};
		}else if($(this).hasClass('vname')){
			if($val == "name" || $val == ""){
				isValid = false;
				$('.vname').val('name');
				$('.vsemail').val('email address');
				$('.vsubject').val('subject');
				$('.vmessage').val('message');
			};
		}else if($(this).hasClass('vsemail')){
			if($val == "email address" || $val == ""){
				isValid = false;
			};
		}else if($(this).hasClass('vsubject')){
			if($val == "subject" || $val == ""){
				isValid = false;
			};
		}else if($(this).hasClass('vmessage')){
			if($val == "message" || $val == ""){
				isValid = false;
			};
		}else if($(this).hasClass('vphone')){
			var Regex =/^([0-9a-zA-Z]+([_+.-]?[0-9a-zA-Z]+)*@[0-9a-zA-Z]+[0-9,a-z,A-Z,.,-]*(.){1}[a-zA-Z]{2,4})+$/;
			if(!Regex.test($val)){isValid = false;}
		}else if($val.length == 0){
			isValid = false;
		}
		
		if(isValid){
			$(this).removeClass("invalid");
			$(this).addClass("valid");
		}else{
			$(this).removeClass("valid");
			$(this).addClass("invalid");
		}
		return isValid;
	}	
}
jQuery.fn.FormValidate = jQuery.iFormValidate.build;









         $(function() {
                $('.headlines_content').tabs({ fxFade: true, fxSpeed: 'fast' });
            });

   
    function formatText(index, panel) {
	  return index + "";
    }
    
    $(function () {
        
		$('.anythingSlider').anythingSlider({
            easing: "easeInOutExpo",        // Anything other than "linear" or "swing" requires the easing plugin
            autoPlay: true,                 // This turns off the entire FUNCTIONALY, not just if it starts running or not.
            delay: 3000,                    // How long between slide transitions in AutoPlay mode
            startStopped: false,            // If autoPlay is on, this can force it to start stopped
            animationTime: 600,             // How long the slide transition takes
            hashTags: true,                 // Should links change the hashtag in the URL?
            buildNavigation: true,          // If true, builds and list of anchor links to link to each slide
        	pauseOnHover: true,             // If true, and autoPlay is enabled, the show will pause on hover
			startText: null,             // Start text
			stopText: null,               // Stop text
			navigationFormatter: function(){}   
		});
            
        $("#slide-jump").click(function(){
            $('.anythingSlider').anythingSlider(1);
        });
    
    });