<?php
class CS_Widget_Featured_Works extends WP_Widget {

	function CS_Widget_Featured_Works() {
		$widget_ops = array('classname' => 'cs_featured_works', 'description' => __( "Display your porfolio items. Made for CircloSquero Theme.") );
		$this->WP_Widget('cs-featured-posts', __('CircloSquero Featured Works'), $widget_ops);
		$this->alt_option_name = 'cs_widget_featured_works';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('cs_widget_featured_works', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('CircloSquero Featured Works') : $instance['title'], $instance, $this->id_base);
		$portfolioID = explode(",", $instance['number']);

			
		$args = array( 'post_type' => 'portfolio', 'post__in'  => $portfolioID ); 
		$r = new WP_Query( $args );

		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<?php $count = 1; ?>
		<div class="widget_works_wrap">
		
		<?php  while ($r->have_posts()) : $r->the_post(); ?>
		<div id="featuredworks<?php echo $count?>">
			<h4><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h4>
			<?php $portfoliothumbnail = get_post_meta(get_the_ID(), 'thumbnailurl', true); ?>
			<a href="<?php the_permalink() ?>"> <img src="<?php bloginfo('template_url'); ?>/thumbnails/timthumb.php?src=<?php echo $portfoliothumbnail; ?>&amp;h=100&w=230&amp;zc=1" alt="<?php the_title(); ?>"  /> </a>
			<img src="<?php bloginfo('template_url'); ?>/images/shadow.jpg" alt="<?php the_title(); ?>" class="shadowportfoliowidget"  />
			<p><?php $portfoliodescription = get_post_meta(get_the_ID(), 'portfoliodesc', true); echo $portfoliodescription;  ?></p>
		</div>
		<?php $count++; ?>
		<?php endwhile; ?>
		<?php if ($count > 1) { ?>
		<div class="featuredworksnav">
			<img src="<?php bloginfo('template_url'); ?>/images/arrow_left.png" alt="Previous" id='avaa-prev'/> <img src="<?php bloginfo('template_url'); ?>/images/arrow_right.png" alt="Next" id='avaa-next'/>
		</div><div class="clear"></div>
		<?php }; ?>
		</div>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('cs_widget_featured_works', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_featured_works']) )
			delete_option('widget_featured_works');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('cs_widget_featured_works', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		if ( !isset($instance['number']) || !$number = $instance['number'] )
			$number = 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Portfolio Items IDs'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>"class="widefat" />
		<br />
			<small><?php _e( 'Portfolio items IDs, separated by commas.' ); ?></small>
		</p>
<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("CS_Widget_Featured_Works");'));
?>