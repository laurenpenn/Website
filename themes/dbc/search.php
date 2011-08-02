<?php
/**
 * Template Name: Search
 *
 * The search template is loaded when a visitor uses the search form to search for something
 * on the site.
 *
 * @package Prototype
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // prototype_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // prototype_open_content ?>
 
		<div class="hfeed">
			
			<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>
			
			<div id="cse" style="width: 100%;">Loading</div>
			<script src="http://www.google.com/jsapi" type="text/javascript"></script>
			<script type="text/javascript"> 
			  function parseQueryFromUrl () {
			    var queryParamName = "s";
			    var search = window.location.search.substr(1);
			    var parts = search.split('&');
			    for (var i = 0; i < parts.length; i++) {
			      var keyvaluepair = parts[i].split('=');
			      if (decodeURIComponent(keyvaluepair[0]) == queryParamName) {
			        return decodeURIComponent(keyvaluepair[1].replace(/\+/g, ' '));
			      }
			    }
			    return '';
			  }
			  google.load('search', '1', {language : 'en'});
			  var _gaq = _gaq || [];
			  _gaq.push(["_setAccount", "UA-10285065-2"]);
			  function _trackQuery(control, searcher, query) {
			    var gaQueryParamName = "s";
			    var loc = document.location;
			    var url = [
			      loc.pathname,
			      loc.search,
			      loc.search ? '&' : '?',
			      gaQueryParamName == '' ? 'q' : encodeURIComponent(gaQueryParamName),
			      '=',
			      encodeURIComponent(query)
			    ].join('');
			    _gaq.push(["_trackPageview", url]);
			  }
			  google.setOnLoadCallback(function() {
			    var customSearchControl = new google.search.CustomSearchControl('005661340557523912723:kolwlhunn_0');
			    customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
			    customSearchControl.setSearchStartingCallback(null, _trackQuery);
			    var options = new google.search.DrawOptions();
			    options.setAutoComplete(true);
			    customSearchControl.setLinkTarget(google.search.Search.LINK_TARGET_SELF);
			    customSearchControl.draw('cse', options);
			    var queryFromUrl = parseQueryFromUrl();
			    if (queryFromUrl) {
			      customSearchControl.execute(queryFromUrl);
			    }
			  }, true);
			</script>
			<link rel="stylesheet" href="http://www.google.com/cse/style/look/default.css" type="text/css" /> 

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // prototype_close_content ?>

		<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // prototype_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>