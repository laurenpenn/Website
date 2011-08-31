<?php 
/*
 *  Plugin Name: DBC
 *  Description: This plugin contains globally relavant information for all DBC network sites and is required for all DBC websites.
 *  Version: 0.3
 *  Author: Patrick Daly
 *  Author URI: http://developdaly.com/
 * 
 *  Version 0.2 makes way for the WordPress 3.1 admin bar
 *  Version 0.3 has been renamed to "DBC" and is now more of general global DBC plugin
 *  
 */
 
 /* ACS API connection. We won't use this until we've built anything that needs it. */
//require_once( trailingslashit( WP_CONTENT_DIR ) . 'mu-plugins/dbc/acs.php' );

 /* Inserts Javascript. */
add_action( 'template_redirect', 'dbc_plugin_load_scripts' );

 /* Inserts CSS. */
add_action( 'wp_print_styles', 'dbc_plugin_load_styles' );

/* Modifies WordPress admin bar. */
add_action( 'admin_bar_menu', 'dbc_admin_bar_menu', 95 );

/* Adds the public global bar. */
add_action( 'wp_footer', 'dbc_global_bar', 12 );

/* Inserts the Google Analytics script into the footer. */
add_action( 'wp_footer', 'dbc_analytics', 13 );

/* switch_to_blog() bug fix. */
add_action( 'switch_blog', 'dbc_switch_blog', null, 2 );

/* Co-authors plugin fix. */
add_action( 'init', 'cap_register_taxonomy_for_pages' );

/**
 * Queues Javascript.
 *
 * @since 0.3
 */
function dbc_plugin_load_scripts() {
	
	wp_enqueue_script( 'dbc-global-bar', get_bloginfo('url') . '/wp-content/mu-plugins/dbc/js/scripts.js', array( 'jquery' ), '0.3', true );	
	
}

/**
 * Queues CSS.
 *
 * @since 0.3
 */
function dbc_plugin_load_styles() {
	
	wp_enqueue_style( 'dbc-global-bar', get_bloginfo('url') . '/wp-content/mu-plugins/dbc/style.css' );
	
}

/**
 * Adds the "Church Admin" link to the WordPress Admin bar.
 *
 * @since 0.3
 */
function dbc_admin_bar_menu() {

	global $wp_admin_bar;

	$wp_admin_bar->add_menu( array( 'id' => 'admin', 'title' => __( 'Church Admin' ), 'href' => 'http://admin.dentonbible.org') );
}

/**
 * Global navigation bar.
 *
 * @since 0.1
 */
function dbc_global_bar(){
	global $blog_id;
	
?>

<div id="global-wrapper">

	<div id="ministry-guide" class="sidebar aside">
	
		<p>The <em>Ministry Guide</em> is a comprehensive list of ministries at Denton Bible Church. Many of these links may take you away from this site.</p>
		<?php
			//This pulls the menu from the main site (1)
			global $switched;
			switch_to_blog(1);
			wp_nav_menu( array( 'menu' => 'ministry-guide', 'container_class' => 'menu', 'menu_class' => '', 'fallback_cb' => '' ) );
			restore_current_blog();
		?>
		
		<?php dynamic_sidebar( 'ministry-guide' ); ?>
	
	</div><!-- #ministry-guide .aside -->
	
	<div id="global-bar">
			
		<ul>
			<?php
			
			if ( $blog_id == 1 ) { // if main site
				echo '';
			} else {
				echo '<li id="dbc-small"><a href="http://dentonbible.org/" title="Denton Bible Main Site Home Page">DBC Main Site</a></li>';
			}
			
			?>			
			
			<li id="ministry-guide-link"><a href="">Ministry Guide</a></li>
			<li id="contact"><a href="http://dentonbible.org/about-us/contact-us/">Contact DBC</a></li>			
		</ul>
			
	</div>

</div>

<?php }

/**
 * Inserts Google Analytics script.
 *
 * @since 0.3
 */
function dbc_analytics() {
	
	// If the current, logged in user is a Super Admin (i.e. has 'update_core' abilities)
	// then don't display the script
	if ( !current_user_can('update_core')) { ?>
		<script type="text/javascript">
		
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-10285065-2']);
		  _gaq.push(['_setDomainName', '.dentonbible.org']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		
		</script>
	<?php
	}
}

/**
 * Hooks the WP MS action switch_blog to switch some caches cache when a blog
 * is switched to. Fixes bug that prevented switch_to_blog() from returning the
 * home_url of the parent site and instead gave the current child site.
 *
 * @param int $blog_id The ID of the blog which has been switched to 
 * @param int $prev_blog_id The ID of the blog we were in before switching 
 * @return void
 **/
function dbc_switch_blog( $blog_id, $prev_blog_id ) {
	// Save the previous alloptions cache
	$prev_alloptions = wp_cache_get( 'alloptions', 'options' );
	// Do we have a previously cached alloptions for the new blog? If so,
	// replace the current alloptions cache otherwise delete it.
	if ( $alloptions = wp_cache_get( "alloptions_$blog_id", 'options' ) )
		wp_cache_replace( "alloptions", $alloptions, 'options' );
	else
		wp_cache_delete( 'alloptions', 'options' );
}

/**
 * Register the taxonomy with pages so the Co-Authors Plus permissions lookup works properly
 */
function cap_register_taxonomy_for_pages() {
	register_taxonomy( 'author', 'page' );
}

?>