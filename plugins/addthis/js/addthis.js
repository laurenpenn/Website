jQuery(document).ready(function($) {  
    
    var data = {action: "at_show_dashboard_widget"};

    $.post(ajaxurl, data, function(response){
        $( "#dashboard_addthis > .inside > .widget-loading").replaceWith(response);
        $( "#at_tabs").tabs();
    });


});
