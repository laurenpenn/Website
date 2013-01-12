<?php if (get_option('disable_shortcut', 1) == '1') : ?>
<div class="page_shortcut">
<?php 
	global $wp_query;
 
	if ( !is_home() ){
 
		echo '<a href="'. get_settings('home') .'">'. get_bloginfo('name') .'</a>';
 
		if ( is_single() ) 
		{
			$category = get_the_category();
			$category_id = get_cat_ID( $category[0]->cat_name );
			echo ' &raquo; '. get_category_parents( $category_id, TRUE, " &raquo; " );
			echo the_title('','', FALSE);
		}
		
		elseif ( is_page() ) 
		{
			$post = $wp_query->get_queried_object();
			if ( $post->post_parent == 0 ){
 
				echo " &raquo; ".the_title('','', FALSE);
 
			} else {
				$title = the_title('','', FALSE);
				$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
				array_push($ancestors, $post->ID);
 
				foreach ( $ancestors as $ancestor ){
					if( $ancestor != end($ancestors) ){
						echo ' &raquo; <a href="'. get_permalink($ancestor) .'">'. strip_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ) .'</a>';
					} else {
						echo ' &raquo; '. strip_tags(apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ) .'';
					}
				}
			}
		}
	}
?>
</div>
<?php endif; ?>
