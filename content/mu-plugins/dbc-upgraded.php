<?php
function get_content_in_wp_pointer() {
	$pointer_content = '<h3>' . __( 'We just upgraded!', 'dbc' ) . '</h3>';
	$pointer_content .= '<p>' . __( 'The Denton Bible web server was recently upgraded. If you experience any issues please email <a href="mailto:webmaster@dentonbible.org">webmaster@dentonbible.org</a>.', 'dbc' ) . '</p>';
	?>
	<style type="text/css">
		#wp-pointer-0 .wp-pointer-arrow {
			display: none;
		}
		#wp-pointer-0 .wp-pointer-content h3 {
			background: red;
			background-image: -moz-linear-gradient(center bottom , red 0pt, red 100%);
		}
	</style>
	<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function($) {
		$('#wpadminbar').pointer({
			content: '<?php echo $pointer_content;?>',
			position: {
				my: 'left top',
				at: 'center bottom',
				offset: '-25 0'
			},
			close: function() {
				setUserSetting( 'p1', '1' );
			}
		}).pointer('open');
	});
	//]]>
	</script>
	<?php
}
function fb_enqueue_wp_pointer( $hook_suffix ) {
	$enqueue = FALSE;
	$admin_bar = get_user_setting( 'p1', 0 ); // check settings on user
	// check if admin bar is active and default filter for wp pointer is true
	if ( ! $admin_bar && apply_filters( 'show_wp_pointer_admin_bar', TRUE ) ) {
		$enqueue = TRUE;
		add_action( 'admin_print_footer_scripts', 'get_content_in_wp_pointer' );
	}
	// in true, include the scripts
	if ( $enqueue ) {
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_script( 'utils' ); // for user settings
	}
}
add_action( 'admin_enqueue_scripts', 'fb_enqueue_wp_pointer' );
