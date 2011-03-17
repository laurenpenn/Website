
<div id="whats-happening" class="aside">

	<h3>What's Happening - <a href="http://dentonbible.org/about-us/publications/">see more</a></h3>
	
	<?php 
	$args = array (
		'posts_per_page' => 1,
		'post_type' => 'publication',
		'publication-type' => 'first-cup'
	);
	
	query_posts( $args );
	while ( have_posts() ) : the_post(); ?>
	
		<div id="post-<?php the_ID(); ?>" class="first <?php hybrid_entry_class(); ?>">

			<a href="<?php echo dbc_get_post_pdf(); ?>"><?php get_the_image( array( 'default_image' => THEME_URI .'/library/images/first-cup.gif', 'link_to_post' => false ) ); ?></a>
	
		</div><!-- .hentry -->
	
	<?php endwhile; wp_reset_query();
	
	$args = array (
		'posts_per_page' => 1,
		'post_type' => 'publication',
		'publication-type' => 'common-ground'
	);
	
	query_posts( $args );
	while ( have_posts() ) : the_post(); ?>
	
		<div id="post-<?php the_ID(); ?>" class="second <?php hybrid_entry_class(); ?>">
	
			<a href="<?php echo dbc_get_post_pdf(); ?>"><?php get_the_image( array( 'default_image' => THEME_URI .'/library/images/common-ground.gif', 'link_to_post' => false ) ); ?></a>
	
		</div><!-- .hentry -->
	
	<?php endwhile; wp_reset_query();

	$args = array (
		'posts_per_page' => 1,
		'post_type' => 'publication',
		'publication-type' => 'starting-point'
	);
	
	query_posts( $args );
	while ( have_posts() ) : the_post(); ?>
	
		<div id="post-<?php the_ID(); ?>" class="third <?php hybrid_entry_class(); ?>">
	
			<a href="<?php echo dbc_get_post_pdf(); ?>"><?php get_the_image( array( 'default_image' => THEME_URI .'/library/images/starting-point.gif', 'link_to_post' => false ) ); ?></a>
	
		</div><!-- .hentry -->
	
	<?php endwhile; wp_reset_query();  ?>
	
</div>

