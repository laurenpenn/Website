// change the menu position based on the scroll positon
window.onscroll = function()
{
    if( window.XMLHttpRequest ) {
        if (document.documentElement.scrollTop > 140 || self.pageYOffset > 140) {
            jQuery('#floatMenu').css('position','fixed');
            jQuery('#floatMenu').css('top','5px');
        } else if (document.documentElement.scrollTop < 90 || self.pageYOffset < 90) {
            jQuery('#floatMenu').css('position','absolute');
            jQuery('#floatMenu').css('top','90px');
        }
    }
}