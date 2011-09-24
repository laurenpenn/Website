<?php
/**
 * Story Template
 *
 * This is the default story template.  It is used when a more specific template can't be found to display
 * singular views of the 'story' post type.
 *
 * @package DBC
 * @subpackage Template
 */

 	$events = $wpdb->get_results($querystr, OBJECT);
	
	// - declare fresh day -
	$daycheck = null;
	
	// - loop -
	if ($events):
	global $post;
	foreach ($events as $post):
	setup_postdata($post);
	
	// - custom variables -
	$custom = get_post_custom(get_the_ID());
	$sd = $custom["event_startdate"][0];
	$ed = $custom["event_enddate"][0];

	// - determine if it's a new day -
	$longdate = date("l, M j, Y", $sd);
		
	if ( $sd != $ed ) {
		$longdate = date("M j, Y", $sd) .' - ' . date("M j, Y", $ed);
	}

	
	// - local time format -
	$time_format = get_option('time_format');
	$stime = date($time_format, $sd);
	$etime = date($time_format, $ed);

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // dbc_before_content ?>
	
	<?php breadcrumb_trail(); ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>

		<div class="hfeed">

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php do_atomic( 'before_entry' ); // dbc_before_entry ?>

					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // dbc_open_entry ?>

						<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>

						<?php echo apply_atomic_shortcode( 'byline', '<div class="byline">' . __( '[entry-published format="F Y"] [entry-edit-link before=" | "]', hybrid_get_textdomain() ) . '</div>' ); ?>

						<div class="entry-content">
							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', hybrid_get_textdomain() ) ); ?>
							<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', hybrid_get_textdomain() ), 'after' => '</p>' ) ); ?>
						</div><!-- .entry-content -->

						<?php do_atomic( 'close_entry' ); // dbc_close_entry ?>

					</div><!-- .hentry -->

					<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
						<p>
							<a class="addthis_button_preferred_1"></a>
							<a class="addthis_button_preferred_2"></a>
							<a class="addthis_button_preferred_3"></a>
							<a class="addthis_button_preferred_4"></a>
							<a class="addthis_button_preferred_5"></a>
							<a class="addthis_button_preferred_6"></a>
						</p>
					</div>
					
					<?php do_atomic( 'after_entry' ); // dbc_after_entry ?>

					<?php get_sidebar( 'after-singular' ); // Loads the sidebar-after-singular.php template. ?>

					<?php do_atomic( 'after_singular' ); // dbc_after_singular ?>
					
				<?php endwhile; ?>

			<?php endif; ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // dbc_close_content ?>
		
		<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>