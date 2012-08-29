<p>You can use this page to set up, configure and customise this (Post Type Archives) plugins behaviour. This plugin won't explain all the options (you can learn all about it in the plugins homepage or plugins page in WordPress directory - links are at the bottom of the page) but I will try to tell the priority lists and variables you can use as field values.</p>

<h2>Priority Lists</h2>

<h3>Rewrite Slug Priority List</h3>

<p><em>Note that this priority list will only be active if 'Use Rewrite Slug' option is checked. If it's not checked then rewrite slug will be the post type name and plugin won't even look at other options.</em></p>

<ul>
	<li>Custom 'Rewrite Slug' value that can be specified for every 'Enabled Custom Post Type' (this field won't be visible if 'Use Rewrite Slug' is unchecked). If left blank then will skip to next step.</li>
	<li>Rewrite slug that's specified when you register your custom post type. If you don't specify then will skip to next step.</li>
	<li>Uses the custom post type name.</li>
</ul>

<h3>Title Priority List</h3>

<ul>
	<li>Custom 'Title' value that can be specified for every 'Enabled Custom Post Type'. If left blank then will continue to next step.</li>
	<li>'Title' options value that doesn't specifically belong to any custom post type. If left blank then will continue to next step.</li>
	<li>Default title that WordPress thinks should be there. It will probably be blank.</li>
</ul>

<h2>Variables in field values</h2>

<p>You can use variables inside the option fields that will be repalced with the values that are specific to the place you're currently on. Capitalized, underscore seperated words wrapped in figure brackets are the variables you can put in option fields value.</p>

<h3>Title</h3>

<p><em>These variables will be both available for global 'Title' field and 'Title' field that can be specified for every 'Enabled Custom Post Type'</em></p>

<ul>
	<li><strong>{POST_TYPE_NAME}</strong> - post type label in plural</li>
	<li><strong>{POST_TYPE_SINGULAR_NAME}</strong> - post type label in singular</li>
	<li><strong>{SEP}</strong> - seperator that you specify for wp_title() function</li>
	<li><strong>{SEP_LEFT_SPACE}</strong> - seperator prefixed with whitespace (' $sep')</li>
	<li><strong>{SEP_RIGHT_SPACE}</strong> - seperator suffixed with whitespace ('$sep ')</li>
	<li><strong>{SEP_SPACED}</strong> - seperator wrapped in whitespaces (' $sep ')</li>
</ul>

<h3>Template Pattern</h3>

<ul>
	<li><strong>{POST_TYPE}</strong> - post type name</li>
</ul>

<h2>Useful links</h2>

<ul>
	<li><a href="http://ratvars.com/custom-post-type-archives/">Plugins Homepage</a></li>
	<li><a href="http://wordpress.org/extend/plugins/custom-post-type-archives/">Plugins Page on WordPress Plugin Directory</a></li>
</ul>