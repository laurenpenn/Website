jQuery(document).ready(function(){
// get the initial (full) list
var jQueryfilterList = jQuery('ul.portfolio-list');
// add unique id's
// i don't like having to write these all in the code
// so i wrote a script to id these for me
for(var i=0; i<jQuery('ul.portfolio-list li').length; i++){
jQuery('ul.portfolio-list li:eq(' + i + ')').attr('id','flitem' + i);
}
// clone first collection to get a second collection
var jQuerydata = jQueryfilterList.clone();
// handle trigger clicks
jQuery('#filterButtons a').click(function(e) {
if(jQuery(this).attr('rel') == 'all') {
// get a group of all items
var jQueryfilteredData = jQuerydata.find('li');
} else {
// get a group of items of a particular class
var jQueryfilteredData = jQuerydata.find('li.' + jQuery(this).attr('rel'));
}
// call quicksand
jQuery('ul.portfolio-list').quicksand(jQueryfilteredData, {
duration: 500,
attribute: function(v) {
// this is the unique id attribute we created above
return jQuery(v).attr('id');
}
}, function() {
		jQuery(document).ready(function(){
			jQuery("a[rel^='prettyPhoto']").prettyPhoto({
                    theme: 'facebook'
                    });
            jQuery('.postThumbWrapFW img, .postThumbWrap img, .portfolio_wrap img, .portfolio_wrap_small img').hover(function(){
	        jQuery(this).stop().fadeTo(300, 0.7);
	        }, function() {
                jQuery(this).stop().fadeTo(500, 1);
	        });                       
                        });

                
});
e.preventDefault();
});
});