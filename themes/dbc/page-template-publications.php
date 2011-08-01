<?php
/**
 * Template Name: Publications
 *
 * This is the page template for Publications. It is used to list the publication post_type.
 *
 * @package DBC
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // dbc_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // dbc_open_content ?>

		<div class="hfeed">

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php do_atomic( 'before_entry' ); // dbc_before_entry ?>

					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

						<?php do_atomic( 'open_entry' ); // dbc_open_entry ?>

						<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>

						<div class="entry-content">
							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', hybrid_get_textdomain() ) ); ?>
							<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', hybrid_get_textdomain() ), 'after' => '</p>' ) ); ?>
						</div><!-- .entry-content -->

						<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">[entry-edit-link]</div>' ); ?>

						<?php do_atomic( 'close_entry' ); // dbc_close_entry ?>

					</div><!-- .hentry -->

					<?php do_atomic( 'after_entry' ); // dbc_after_entry ?>

					<?php get_sidebar( 'after-singular' ); // Loads the sidebar-after-singular.php template. ?>

					<?php do_atomic( 'after_singular' ); // dbc_after_singular ?>

					<?php comments_template( '/comments.php', true ); // Loads the comments.php template. ?>

				<?php endwhile; ?>

			<?php endif; ?>

		</div><!-- .hfeed -->
		
		<div id="starting-point" class="publications">

			<?php
			$args = array (
				'posts_per_page' => 1,
				'post_type' => 'publication',
				'publication-type' => 'starting-point'
			);
			
			query_posts( $args );
			while ( have_posts() ) : the_post(); 
				$args = array(
					'post_type' => 'attachment',
					'numberposts' => -1, // get all attachments
					'post_status' => null,
					'post_parent' => $post->ID
					); 
				$attachments = get_posts($args);
				if ($attachments) {
					
					foreach ($attachments as $attachment) {
						if ( $attachment->post_mime_type == 'application/pdf')
							$link = $attachment->guid;
					}
				}											
				?>
			
				<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
			
					<p><img src="http://dentonbible.org/wp-content/themes/dbc/library/images/starting-point-cover.jpg" alt="Starting Point" class="alignleft" height="320" width="250" /></p>
					
					<h2>Starting Point - Quarterly</h2>
									
					<p>If you're looking for <strong>an easy guide to find your way around Denton Bible's many ministries</strong> start here. You'll find information on virtually all ministries of Denton Bible Church and contact information to get started.</p>
					
					<p>You can always find an up to date copy at any informaton booth throughout the DBC lobby.</p>
					
					<p><a href="<?php echo $link; ?>" class="link-out">View Starting Point</a></p>
			
				</div><!-- .hentry -->
				
				<?php endwhile; ?>
						
				<?php wp_reset_query(); ?>
				
		</div>									
			
		<div class="clear"></div>

		<div id="publication-archive">

			<div id="first-cup" class="publications">

				<p><img src="http://dentonbible.org/wp-content/themes/dbc/library/images/first-cup-cover.gif" alt="First Cup" class="alignleft" height="320" width="250" /></p>
								
				<h2>First Cup - Weekly</h2>
	
				<p>First Cup is the weekly Sunday bulletin providing brief announcements for upcoming events. This bulletin is distributed before each Sunday service.</p>			
												
				<ul id="first-cup-publications-inner">
					<?php
					$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
					$args = array (
						'paged' => $paged,
						'posts_per_page' => 5,
						'post_type' => 'publication',
						'publication-type' => 'first-cup'
					);
					
					query_posts( $args );
					while ( have_posts() ) : the_post(); 
						$args = array(
							'post_type' => 'attachment',
							'numberposts' => 51,
							'post_status' => null,
							'post_parent' => $post->ID
							); 
						$attachments = get_posts($args);
						if ($attachments) {
							foreach ($attachments as $attachment) {
								if ( $attachment->post_mime_type == 'application/pdf')
									$link = $attachment->guid;
							}
						}											
						?>
					
						<li id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
					
							<a href="<?php echo $link; ?>" class="date"><?php the_title_attribute(); ?></a>
					
						</li><!-- .hentry -->
					
					<?php endwhile; ?>	
							
					<?php wp_reset_query(); ?>
				</ul>
			</div><!-- #first-cup -->
			
			<div id="common-ground" class="publications">

				<p><img src="http://dentonbible.org/wp-content/themes/dbc/library/images/common-ground-cover.gif" alt="Common Ground" class="alignleft" height="320" width="250" /></p>
								
				<h2>Common Ground - Monthly</h2>
	
				<p>Common Ground is a monthly magazine providing a variety of ministry events and opportunities during the month it is published: It also includes articles about ministries, people, or current events relevant to our lives today. A new edition of the magazine is distributed around the first full week of each month.</p>			
												
				<ul id="common-ground-publications-inner">
					<?php
					$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
					$args = array (
						'paged' => $paged,
						'posts_per_page' => 5,
						'post_type' => 'publication',
						'publication-type' => 'common-ground'
					);
					
					query_posts( $args );
					while ( have_posts() ) : the_post(); 
						$args = array(
							'post_type' => 'attachment',
							'numberposts' => 51,
							'post_status' => null,
							'post_parent' => $post->ID
							); 
						$attachments = get_posts($args);
						if ($attachments) {
							foreach ($attachments as $attachment) {
								if ( $attachment->post_mime_type == 'application/pdf')
									$link = $attachment->guid;
							}
						}											
						?>
					
						<li id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
					
							<a href="<?php echo $link; ?>" class="date"><?php the_time('F Y'); ?></a>
					
						</li><!-- .hentry -->
					
					<?php endwhile; ?>	
							
					<?php wp_reset_query(); ?>
				</ul>
			</div><!-- #commoun-ground -->
			
		</div><!-- #publication-archive -->

		<?php do_atomic( 'close_content' ); // dbc_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // dbc_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>