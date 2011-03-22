<?php if (get_option('disable_page_search', 1) == '1'): ?>
<!-- begin search -->
<form method="get" id="searchform" action="<?php bloginfo('siteurl'); ?>" >
<input type="text" name="s" id="s" class="search_text" />
</form>
<!-- end begin search -->
<?php endif; ?>