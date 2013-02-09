jQuery(document).ready(function($) {   
    $( "#tabs" ).tabs();
    var thickDims, tbWidth, tbHeight; 
    thickDims = function() {
        var tbWindow = $('#TB_window'), H = $(window).height(), W = $(window).width(), w, h;
    
        w = (tbWidth && tbWidth < W - 90) ? tbWidth : W - 90;
        h = (tbHeight && tbHeight < H - 60) ? tbHeight : H - 60;
        if ( tbWindow.size() ) {
            tbWindow.width(w).height(h);
            $('#TB_iframeContent').width(w).height(h - 27);
            tbWindow.css({'margin-left': '-' + parseInt((w / 2),10) + 'px'});
            if ( typeof document.body.style.maxWidth != 'undefined' )
                tbWindow.css({'top':'30px','margin-top':'0'});
        }
    };

    $('a.thickbox-preview').click( function() {
        
        var previewLink = this;
        
        var $inputs = $('#addthis_settings :input');

        var values = {};
        $.each($('#addthis_settings').serializeArray(), function(i, field) {
            
            var thisName = field.name
            if (thisName.indexOf("addthis_settings[") != -1 )
            {
                thisName = thisName.replace("addthis_settings[", '');
                thisName = thisName.replace("]", '');
            }
            
            values[thisName] = field.value;
        });

        var stuff = $.param(values, true);

        var data = {
            action: 'at_save_transient',
            value : stuff
        };
        

        jQuery.post(ajaxurl, data, function(response) {
            // Fix for WP 2.9's version of lightbox 
            if ( typeof tb_click != 'undefined' &&  $.isFunction(tb_click.call))
            {
               tb_click.call(previewLink); 
            }
            var href = $(previewLink).attr('href');
            var link = '';


        if ( tbWidth = href.match(/&tbWidth=[0-9]+/) ) 
            tbWidth = parseInt(tbWidth[0].replace(/[^0-9]+/g, ''), 10); 
        else 
            tbWidth = $(window).width() - 90; 

        if ( tbHeight = href.match(/&tbHeight=[0-9]+/) ) 
            tbHeight = parseInt(tbHeight[0].replace(/[^0-9]+/g, ''), 10);
        else
            tbHeight = $(window).height() - 60;
            
        $('#TB_title').css({'background-color':'#222','color':'#dfdfdf'}); 
        $('#TB_closeAjaxWindow').css({'float':'left'}); 
        $('#TB_ajaxWindowTitle').css({'float':'right'}).html(link); 

        $('#TB_iframeContent').width('100%'); 

        thickDims(); 

        });
        return false;
    });

   /**
    * Handle enable disable top and bottom share buttons
    */
   $("#enable_above, #enable_below").click(enableShareIconsClickHandler);
  
   function enableShareIconsClickHandler(){
       toggleShareIconsContainer($(this));
   }
  function toggleShareIconsContainer(element){
       var animationContainer = element.closest("td").find(".select_row");
       if (!element.attr("checked")) {
          animationContainer.css("opacity", 0.4);
          animationContainer.find("input").attr("disabled", true);
       } else {
          animationContainer.css("opacity", 1);
          animationContainer.find("input").attr("disabled", false);
       }
   }
   toggleShareIconsContainer($("#enable_above"));
   toggleShareIconsContainer($("#enable_below"));
   
    var show_above =  $('input[name="addthis_settings[show_above]"]');
    var show_below = $('input[name="addthis_settings[show_below]"]');
    if ( show_above.prop('checked') != "undefined" && show_above.prop('checked') == true)
    {
        $('.above_option').toggleClass('hide');
    }
   
    if ( show_below.prop('checked') != "undefined" && show_below.prop('checked') == true)
    {
        $('.below_option').toggleClass('hide');
    }
   
    $('input[name="addthis_settings[show_above]"]').change( function() {
        $('.above_option').toggleClass('hide');
    });

    $('input[name="addthis_settings[show_below]"]').change( function() {
        $('.below_option').toggleClass('hide');
    });

    var aboveCustom = $('#above_custom_button'); 
    var aboveCustomShow = function(){
        if ( aboveCustom.prop('checked') != 'undefined' &&  aboveCustom.prop('checked') == true)
        {
            $('.above_option_custom').removeClass('hidden');
        }
        else
        {
            $('.above_option_custom').addClass('hidden');
        }
    };

    var belowCustom = $('#below_custom_button'); 
    var belowCustomShow = function(){
        if ( belowCustom.prop('checked') != 'undefined' &&  belowCustom.prop('checked') == true)
        {
            $('.below_option_custom').removeClass('hidden');
        }
        else
        {
            $('.below_option_custom').addClass('hidden');
        }
    };

    var aboveCustomString = $('#above_custom_string'); 
    var aboveCustomStringShow = function(){
        if ( aboveCustomString.prop('checked') != 'undefined' &&  aboveCustomString.prop('checked') == true)
        {
            $('.above_custom_string_input').removeClass('hidden');
        }
        else
        {
            $('.above_custom_string_input').addClass('hidden');
        }
    };

    var belowCustomString = $('#below_custom_string'); 
    var belowCustomStringShow = function(){
        if ( belowCustomString.prop('checked') != 'undefined' &&  belowCustomString.prop('checked') == true)
        {
            $('.below_custom_string_input').removeClass('hidden');
        }
        else
        {
            $('.below_custom_string_input').addClass('hidden');
        }
    };

    aboveCustomShow();
    belowCustomShow();
    aboveCustomStringShow();
    belowCustomStringShow();

    $('input[name="addthis_settings[above]"]').change( function(){aboveCustomShow(); aboveCustomStringShow();} );
    $('input[name="addthis_settings[below]"]').change( function(){belowCustomShow(); belowCustomStringShow();} );

    /**
     * Hide Theming and branding options when user selects version 3.0 or above
     */   
    var ATVERSION_250 = 250;
    var AT_VERSION_300 = 300;
    var MANUAL_UPDATE = -1;
    var AUTO_UPDATE = 0;
    var REVERTED = 1;
    var atVersionUpdateStatus = $("#addthis_atversion_update_status").val();
    if (atVersionUpdateStatus == REVERTED) {
        $(".classicFeature").show();
    } else {
        $(".classicFeature").hide();
    }
    
    /**
     * Revert to older version after the user upgrades
     */
    $(".addthis-revert-atversion").click(function(){
       $("#addthis_atversion_update_status").val(REVERTED);
       $("#addthis_atversion_hidden").val(ATVERSION_250);
       $(this).closest("form").submit();
       return false;
    });
   /**
    * Update to a newer version
    */ 
   $(".addthis-update-atversion").click(function(){
       $("#addthis_atversion_update_status").val(MANUAL_UPDATE);
       $("#addthis_atversion_hidden").val(AT_VERSION_300);
       $(this).closest("form").submit();
       return false;
   });
   
   var addthis_credential_validation_status = $("#addthis_credential_validation_status");
   var addthis_validation_message = $("#addthis-credential-validation-message");
   var addthis_profile_validation_message = $("#addthis-profile-validation-message");
   //Validate the Addthis credentials
   window.skipValidationInternalError = false;
   function validate_addthis_credentials() {
        $.ajax(
            {"url" : addthis_option_params.wp_ajax_url,
             "type" : "post",
             "data" : {"action" : addthis_option_params.addthis_validate_action,
                      "addthis_profile" : $("#addthis_profile").val(),
                      "addthis_username" : $("#addthis_username").val(),
                      "addthis_password" : $("#addthis_password").val()
                  },
             "dataType" : "json",
             "beforeSend" : function() {
                 $(".addthis-admin-loader").show();
                 addthis_validation_message.html("").next().hide();
                 addthis_profile_validation_message.html("").next().hide();
             },
             "success": function(data) {
                 addthis_validation_message.show();
                 addthis_profile_validation_message.show();

                 if (data.credentialmessage == "error" || (data.profileerror == "false" && data.credentialerror == "false")) {
                     if (data.credentialmessage != "error") {
                         addthis_credential_validation_status.val(1);
                     } else {
                         window.skipValidationInternalError = true;
                     }
                     $("#addthis_settings").submit();
                 } else {
                     addthis_validation_message.html(data.credentialmessage);
                     addthis_profile_validation_message.html(data.profilemessage);
                     if (data.profilemessage != "") {
                         $('html, body').animate({"scrollTop":0}, 'slow');
                     }
                 }

             },
             "complete" :function(data) {
                 $(".addthis-admin-loader").hide();
             },
             "error" : function(jqXHR, textStatus, errorThrown) {
                 console.log(textStatus, errorThrown);
             }
         });
    }
    //Prevent default form submission
    $("#addthis_settings").submit(function(){
        if(window.skipValidationInternalError) {
            return true;
        }
        var isProfileEmpty = $.trim($("#addthis_profile").val()) == "";
        var isUsernameEmpty = $.trim($("#addthis_username").val()) == "";
        var isPasswordEmpty = $.trim($("#addthis_password").val()) == "";
        var isAnyFieldEmpty = isProfileEmpty || isUsernameEmpty || isPasswordEmpty;
        var validationRequired = addthis_credential_validation_status.val() == 0;
        
        if(isUsernameEmpty != isPasswordEmpty) {
            var emptyLabel = isUsernameEmpty ? "username" : "password";
            addthis_validation_message.html("&#x2716; AddThis " + emptyLabel + " is required to view analytics").next().hide();
            return false;
        } else if (isProfileEmpty && !isUsernameEmpty && !isPasswordEmpty) {
            addthis_profile_validation_message.html("&#x2716; AddThis profile ID is required to view analytics").next().hide();
            $('html, body').animate({"scrollTop":0}, 'slow');
            return false;
        } else if (!validationRequired || isAnyFieldEmpty) {
            return true;
        } else if(!isAnyFieldEmpty && validationRequired) {
            validate_addthis_credentials();
            return false;
        }
    });
    
    $("#addthis_username, #addthis_password, #addthis_profile").change(function(){
       addthis_credential_validation_status.val(0);
       if($.trim($("#addthis_profile").val()) == "") {
            addthis_profile_validation_message.next().hide();
       }
       if(($.trim($("#addthis_username").val()) == "") || ($.trim($("#addthis_password").val()) == "")) {
            addthis_validation_message.next().hide();
       }
    });
});
