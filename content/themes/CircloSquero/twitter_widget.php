<?php
class CS_Widget_Twitter extends WP_Widget {

	function CS_Widget_Twitter() {
		$widget_ops = array('classname' => 'cs_featured_works', 'description' => __( "Display your latest tweets. Made for Circlosquero Theme.") );
		$this->WP_Widget('cs-twitter', __('CircloSquero Twitter Widget'), $widget_ops);
		$this->alt_option_name = 'cs_widget_twitter';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('CS_Widget_Twitter ', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('CircloSquero Featured Works') : $instance['title'], $instance, $this->id_base);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;

			

?>
		<?php echo $before_widget; ?>

		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul id="twitter_update_listCS"></ul>
		<?php echo $after_widget; ?>
<?php
$unamed = $instance['tw_username'];
$nr = $instance['number'];
echo '
      <script type="text/javascript">
         jQuery(document).ready(function() {
            GetTwitterFeedIncRT("'. $unamed .'", "'. $nr .'",  "twitter_update_listCS", 1 );
	 });
      </script>';
?>

<?php



		// Reset the global $the_post as this query will have stomped on it


		

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('cs_widget_twitter', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['tw_username'] = strip_tags($new_instance['tw_username']);
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_featured_works']) )
			delete_option('widget_featured_works');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('cs_widget_twitter', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$tw_username = isset($instance['tw_username']) ? esc_attr($instance['tw_username']) : '';
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('tw_username'); ?>"><?php _e('Twitter Username:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('tw_username'); ?>" name="<?php echo $this->get_field_name('tw_username'); ?>" type="text" value="<?php echo $tw_username; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of Tweets'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
		<br />
			<small><?php _e( 'Number of tweets you would like to display' ); ?></small>
		</p>
<?php
	}
}


add_action('widgets_init', create_function('', 'return register_widget("CS_Widget_Twitter");'));
?>
