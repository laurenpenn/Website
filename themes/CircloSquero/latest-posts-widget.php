<?php
class CS_Widget_Recent_Posts extends WP_Widget {

	function CS_Widget_Recent_Posts() {
		$widget_ops = array('classname' => 'cs_recent_entries', 'description' => __( "The most recent posts on your site with thumbnails. Made for CircloSquero WP Theme.") );
		$this->WP_Widget('cs-recent-posts', __('CircloSquero Recent Posts'), $widget_ops);
		$this->alt_option_name = 'cs_widget_recent_entries';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('cs_widget_recent_entries', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('CircloSquero Recent Posts') : $instance['title'], $instance, $this->id_base);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;

		$r = new WP_Query(array('showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1));
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul class="CS-lastest-posts">
		<?php  while ($r->have_posts()) : $r->the_post(); ?>
		<?php
		$postimageurl280x225 = get_post_meta(get_the_ID(), 'post-thumb-280x225', true); 
                $postimageurl600x225 = get_post_meta(get_the_ID(), 'post-thumb-600x225', true);
		if ($postimageurl280x225) {$thumburl = $postimageurl280x225;} else {$thumburl = $postimageurl600x225;};
		?>
		<?php if ($thumburl) { ?>
				<li>
				
				<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
				<img src="<?php bloginfo('template_url'); ?>/thumbnails/timthumb.php?src=<?php echo $thumburl; ?>&h=43&amp;w=57&amp;zc=1" />
				</a>
				<span class="LPlink"> <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a>
				<br/>
				<?php the_time('M j, Y') ?> </span>
				<div class="clear"></div>
				</li>

		<?php } else { ?>
				<li class="CS-lastest-posts">
				<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a>
				</li>						
		<?php }; ?>
		<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('cs_widget_recent_entries', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_entries']) )
			delete_option('widget_recent_entries');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('cs_widget_recent_entries', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("CS_Widget_Recent_Posts");'));
?>
