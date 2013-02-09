<?php
/**
 * Search Form Template
 *
 * The search form template displays the search form.
 *
 * @package Prototype
 * @subpackage Template
 */
?>
			<div class="search">

				<div id="cse-search-form" style="width: 100%;">Loading</div>
				<script src="http://www.google.com/jsapi" type="text/javascript"></script>
				<script type="text/javascript"> 
				  google.load('search', '1', {language : 'en'});
				  google.setOnLoadCallback(function() {
				    var customSearchControl = new google.search.CustomSearchControl('005661340557523912723:kolwlhunn_0');
				    customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
				    var options = new google.search.DrawOptions();
				    options.setAutoComplete(true);
				    options.enableSearchboxOnly("http://dbcm.org/", "s");    
				    customSearchControl.draw('cse-search-form', options);
				  }, true);
				</script>
				<link rel="stylesheet" href="http://www.google.com/cse/style/look/default.css" type="text/css" />

			</div><!-- .search -->	
