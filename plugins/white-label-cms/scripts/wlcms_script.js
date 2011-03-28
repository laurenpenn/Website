    jQuery(document).ready(function(){
    
    	jQuery('#footer-left').remove();
    
		jQuery('.wlcms_options').slideUp();
		
		jQuery('.video-h').hover(function() {
			jQuery(this).addClass('pretty-hover');
		}, function() {
			jQuery(this).removeClass('pretty-hover');
		});

		var showHideWelcome;
		showHideWelcome = jQuery('.wlcms_opts form #form-show-welcome input:radio:checked').val();
		if(showHideWelcome == 0) {
			jQuery('.video-h').hide();
		}
		
		 jQuery('.wlcms_opts form #form-show-welcome input:radio').click(function() {
		 	showHideWelcome = jQuery('.wlcms_opts form #form-show-welcome input:radio:checked').val();
			if(showHideWelcome == 0) {
				jQuery('.video-h').hide();
			} else {
				jQuery('.video-h').show();
			}
		 });
		
  
		jQuery('.wlcms_section h3').click(function(){		
			if(jQuery(this).parent().next('.wlcms_options').css('display')=='none')
				{	jQuery(this).removeClass('inactive');
					jQuery(this).addClass('active');
					jQuery(this).children('img').removeClass('inactive');
					jQuery(this).children('img').addClass('active');
					
				}
			else
				{	jQuery(this).removeClass('active');
					jQuery(this).addClass('inactive');		
					jQuery(this).children('img').removeClass('active');			
					jQuery(this).children('img').addClass('inactive');
				}
				
			jQuery(this).parent().next('.wlcms_options').slideToggle('slow');	
		});
		
		jQuery('#radioWebsite').click(function() {
			jQuery('input[name=wlcms_o_hide_posts]').attr('checked', true);
			jQuery('input[name=wlcms_o_hide_media]').attr('checked', false);
			jQuery('input[name=wlcms_o_hide_links]').attr('checked', true);
			jQuery('input[name=wlcms_o_hide_pages]').attr('checked', false);			
			jQuery('input[name=wlcms_o_hide_comments]').attr('checked', true);
			jQuery('input[name=wlcms_o_hide_users]').attr('checked', true);			
			jQuery('input[name=wlcms_o_hide_tools]').attr('checked', true);
			jQuery('input[name=wlcms_o_hide_separator2]').attr('checked', true);		
			jQuery('input[name=wlcms_o_show_appearance]').attr('checked', false);		
			jQuery('input[name=wlcms_o_show_widgets]').attr('checked', false);					
		});

		jQuery('#radioBlog').click(function() {
			jQuery('input[name=wlcms_o_hide_posts]').attr('checked', false);
			jQuery('input[name=wlcms_o_hide_media]').attr('checked', false);
			jQuery('input[name=wlcms_o_hide_links]').attr('checked', true);
			jQuery('input[name=wlcms_o_hide_pages]').attr('checked', false);			
			jQuery('input[name=wlcms_o_hide_comments]').attr('checked', false);
			jQuery('input[name=wlcms_o_hide_users]').attr('checked', true);			
			jQuery('input[name=wlcms_o_hide_tools]').attr('checked', true);
			jQuery('input[name=wlcms_o_hide_separator2]').attr('checked', true);			
			jQuery('input[name=wlcms_o_show_appearance]').attr('checked', false);		
			jQuery('input[name=wlcms_o_show_widgets]').attr('checked', false);					
		});

		jQuery('#radioCustom').click(function() {
			if (jQuery('#wlcms_o_hide_posts').is('.wlcms_remChecked')) { jQuery('input[name=wlcms_o_hide_posts]').attr('checked', true); } else { jQuery('input[name=wlcms_o_hide_posts]').attr('checked', false); }
			if (jQuery('#wlcms_o_hide_media').is('.wlcms_remChecked')) { jQuery('input[name=wlcms_o_hide_media]').attr('checked', true); } else { jQuery('input[name=wlcms_o_hide_media]').attr('checked', false); }
			if (jQuery('#wlcms_o_hide_links').is('.wlcms_remChecked')) { jQuery('input[name=wlcms_o_hide_links]').attr('checked', true); } else { jQuery('input[name=wlcms_o_hide_links]').attr('checked', false); }
			if (jQuery('#wlcms_o_hide_pages').is('.wlcms_remChecked')) { jQuery('input[name=wlcms_o_hide_pages]').attr('checked', true); } else { jQuery('input[name=wlcms_o_hide_pages]').attr('checked', false); }
			if (jQuery('#wlcms_o_hide_comments').is('.wlcms_remChecked')) { jQuery('input[name=wlcms_o_hide_comments]').attr('checked', true); } else { jQuery('input[name=wlcms_o_hide_comments]').attr('checked', false); }
			if (jQuery('#wlcms_o_hide_users').is('.wlcms_remChecked')) { jQuery('input[name=wlcms_o_hide_users]').attr('checked', true); } else { jQuery('input[name=wlcms_o_hide_users]').attr('checked', false); }
			if (jQuery('#wlcms_o_hide_tools').is('.wlcms_remChecked')) { jQuery('input[name=wlcms_o_hide_tools]').attr('checked', true); } else { jQuery('input[name=wlcms_o_hide_tools]').attr('checked', false); }			
			if (jQuery('#wlcms_o_hide_separator2').is('.wlcms_remChecked')) { jQuery('input[name=wlcms_o_hide_separator2]').attr('checked', true); } else { jQuery('input[name=wlcms_o_hide_separator2]').attr('checked', false); }			
			if (jQuery('#wlcms_o_show_appearance').is('.wlcms_remChecked')) { jQuery('input[name=wlcms_o_show_appearance]').attr('checked', true); } else { jQuery('input[name=wlcms_o_show_appearance]').attr('checked', false); }			
			if (jQuery('#wlcms_o_show_widgets').is('.wlcms_remChecked')) { jQuery('input[name=wlcms_o_show_widgets]').attr('checked', true); } else { jQuery('input[name=wlcms_o_show_widgets]').attr('checked', false); }			

		});
		
		
		
});