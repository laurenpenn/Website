function setCookie(c_name,value,exdays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
    document.cookie=c_name + "=" + c_value;
}

function getCookie(c_name)
{
    var i,x,y,ARRcookies=document.cookie.split(";");
    for (i=0;i<ARRcookies.length;i++)
    {
        x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
        y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
        x=x.replace(/^\s+|\s+$/g,"");
        if (x==c_name)
        {
            return unescape(y);
        }
    }
}

jQuery(document).ready(function($){
    $('.wpb_settings_accordion').accordion({
        active: (getCookie('wpb_js_composer_settings_group_tab') ? getCookie('wpb_js_composer_settings_group_tab') : false),
        collapsible: true,
        change: function(event, ui) {
            if(ui.newHeader.attr('id')!=undefined)
                setCookie('wpb_js_composer_settings_group_tab', '#' + ui.newHeader.attr('id'), 365*24*60*60);
            else
                setCookie('wpb_js_composer_settings_group_tab', '', 365*24*60*60);

        }
    });
    $('.wpb-settings-select-all-shortcodes').click(function(e){
        e.preventDefault();
        $(this).parent().parent().find('[type=checkbox]').attr('checked', true);
        });
    $('.wpb-settings-select-none-shortcodes').click(function(e){
        e.preventDefault();
        $(this).parent().parent().find('[type=checkbox]').removeAttr('checked');
        });
});