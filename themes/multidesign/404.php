<?php
/**
 * The template for displaying 404 pages (Not Found).
 * * @package iamilkay
 * @subpackage  iamilkay
 * @since  iamilkay
 */

get_header(); ?>

<!-- Container Start -->
<div class="container_16">
	<div class="grid_16">
    <h1 style="font-size:140px; text-align:center; margin-top:220px; margin-bottom:220px;"><?php _e( '404 PAGE NOT FOUND', 'twentyten' ); ?></h1>
    </div>
</div>

	<script type="text/javascript">
		// focus on search field after it has loaded
		document.getElementById('s') && document.getElementById('s').focus();
	</script>

<?php get_footer(); ?>
