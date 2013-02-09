        <!--Show related posts-->
        <?php $show_related = get_option('hp_show_related'); ?>
         <?php if ($show_related=='true') : ?>
        <div id="related-posts">
        <h5>Related Articles</h5>
        <?php  //for use in the loop, list 5 post titles related to first tag on current post
		  $backup = $post;  // backup the current object
		  $tags = wp_get_post_tags($post->ID);
		  $tagIDs = array();
		  if ($tags) {
			$tagcount = count($tags);
			for ($i = 0; $i < $tagcount; $i++) {
			  $tagIDs[$i] = $tags[$i]->term_id;
			}
			$args=array(
			  'tag__in' => $tagIDs,
			  'post__not_in' => array($post->ID),
			  'showposts'=>4,
			  'caller_get_posts'=>1
			);
			$my_query = new WP_Query($args);
			if( $my_query->have_posts() ) {
			  while ($my_query->have_posts()) : $my_query->the_post(); ?>
              <div class="related-entry">
              <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/inc/timthumb.php?src=<?php echo get_post_meta( $post->ID, "post_image", true ); ?>&amp;w=128&amp;h=82&amp;zc=1" alt="<?php the_title(); ?>" class="related-thumb"/></a>
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                </div>
			  <?php endwhile;
			} else { ?>
			  <h2>No related posts found!</h2>
			<?php }
		  }
		  $post = $backup;  // copy it back
		  wp_reset_query(); // to use the original query again
		?>
        </div>
        <?php endif; ?>
